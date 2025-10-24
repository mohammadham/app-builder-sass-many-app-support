<?php namespace App\Controllers\Api\Customer\Projects\Newsletter;

use App\Controllers\PrivateController;
use App\Libraries\OneSignal;
use App\Models\AppsModel;
use CodeIgniter\HTTP\ResponseInterface;

class TotalSubscribers extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get total push subscribers
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("user", $this->userId)
            ->where("deleted_at", 0)
            ->select("id,one_signal_id,one_signal_rest")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        if (!$app["one_signal_id"]) {
            return $this->respond(["all" => 0, "active" => 0, "is_prod" => false], 200);
        }

        $onesignal = new OneSignal();

        $res = $onesignal->get_total_users($app["one_signal_id"]);

        if (!$res["event"]) {
            return $this->respond(["message" => $res["message"]], 404);
        }

        return $this->respond([
            "all"     => $res["players"],
            "active"  => $res["messageable_players"],
            "is_prod" => (bool) $app["one_signal_rest"]
        ], 200);
    }

}