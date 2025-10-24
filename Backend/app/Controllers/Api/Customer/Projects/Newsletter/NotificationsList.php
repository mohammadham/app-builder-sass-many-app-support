<?php namespace App\Controllers\Api\Customer\Projects\Newsletter;

use App\Controllers\PrivateController;
use App\Libraries\OneSignal;
use App\Models\AppsModel;
use CodeIgniter\HTTP\ResponseInterface;

class NotificationsList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get notifications history
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));
        $offset = (int) $this->request->getGet("offset");

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
            return $this->respond(["list" => [], "total" => 0], 200);
        }

        if (!$app["one_signal_rest"]) {
            return $this->respond(["list" => [], "total" => 0], 200);
        }

        $onesignal = new OneSignal();

        $res = $onesignal->get_notifications($app["one_signal_id"], $app["one_signal_rest"], $offset);

        if (!$res["event"]) {
            return $this->respond(["message" => $res["message"]], 404);
        }

        $data = $res["data"];

        $total = $data->total_count;

        $list = [];

        foreach ($data->notifications as $notification) {
            $isEmpty = false;
            if (empty($notification->platform_delivery_stats->android)) {
                $isEmpty = true;
            }
            $list[] = [
                "content"    => $notification->contents->en,
                "image"      => $notification->global_image,
                "heading"    => $notification->headings->en,
                "successful" => !$isEmpty ? $notification->platform_delivery_stats->android->successful : 0,
                "failed"     => !$isEmpty ? $notification->platform_delivery_stats->android->failed : 0,
                "errored"    => !$isEmpty ? $notification->platform_delivery_stats->android->errored : 0,
                "converted"  => !$isEmpty ? $notification->platform_delivery_stats->android->converted : 0,
                "queued_at"  => date('d-m-Y H:i', $notification->queued_at)
            ];
        }

        return $this->respond(["list" => $list, "total" => $total], 200);
    }

}