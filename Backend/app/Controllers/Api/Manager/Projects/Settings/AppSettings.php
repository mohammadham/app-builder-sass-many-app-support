<?php namespace App\Controllers\Api\Manager\Projects\Settings;

use App\Controllers\PrivateController;
use App\Models\AppsModel;
use CodeIgniter\HTTP\ResponseInterface;

class AppSettings extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get app settings (main info)
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("deleted_at", 0)
            ->select("name,link,app_id,app_id,orientation,status,language,email,user_agent,one_signal_id,one_signal_rest")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        return $this->respond([
            "name"            => $app["name"],
            "link"            => $app["link"],
            "app_id"          => $app["app_id"],
            "user_agent"      => $app["user_agent"],
            "orientation"     => (int) $app["orientation"],
            "status"          => (int) $app["status"],
            "language"        => $app["language"],
            "email"           => $app["email"],
            "one_signal_id"   => $app["one_signal_id"],
            "one_signal_rest" => $app["one_signal_rest"]
        ], 200);
    }

}