<?php namespace App\Controllers\Api\Manager\Projects;

use App\Controllers\PrivateController;
use App\Libraries\Common;
use App\Models\AppsModel;
use App\Models\SubscribesModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class DeleteProject extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Remove app project
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("deleted_at", 0)
            ->select("id,name,link,id")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        // check subscribe
        $subscribes = new SubscribesModel();

        $subscribe = $subscribes
            ->where("app_id", $app["id"])
            ->where("expires_at >", time())
            ->where("is_disable", 0)
            ->select("expires_at")
            ->first();

        if ($subscribe) {
            return $this->respond([
                "message" => lang("Message.message_89")." ".date('d-m-Y H:i', $subscribe['expires_at'])
            ], 400);
        }

        $projects->update($app["id"], [
            "deleted_at" => time()
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

}