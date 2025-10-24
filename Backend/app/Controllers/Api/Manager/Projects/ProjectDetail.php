<?php namespace App\Controllers\Api\Manager\Projects;

use App\Controllers\PrivateController;
use App\Libraries\Common;
use App\Models\AppsModel;
use App\Models\SubscribesModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProjectDetail extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get short app detail
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("deleted_at", 0)
            ->select("uid,name,link,id,user")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        $app_subscribe = 0;

        $subscribes = new SubscribesModel();

        $subscribe = $subscribes
            ->where("app_id", $app["id"])
            ->where("expires_at >", time())
            ->where("is_disable", 0)
            ->select("expires_at")
            ->first();

        if ($subscribe) {
            $app_subscribe = $subscribe["expires_at"];
        }

        $common = new Common();

        return $this->respond([
            "name"      => $app["name"],
            "link"      => $app["link"],
            "icon"      => $common->get_icon($app["uid"]),
            "subscribe" => $app_subscribe,
            "subscribe_date" => !$app_subscribe ? "" : date('d-m-Y H:i', $subscribe['expires_at']),
            "user_id"   => (int) $app["user"]
        ], 200);
    }

}