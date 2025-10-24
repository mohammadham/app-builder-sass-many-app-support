<?php namespace App\Controllers\Api\Manager\Projects\Signing;

use App\Controllers\PrivateController;
use App\Models\AppsModel;
use App\Models\SignsAndroidModel;
use App\Models\SignsIosModel;
use CodeIgniter\HTTP\ResponseInterface;

class SignaturesShortList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get list signatures
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));
        $target = esc($this->request->getGet("target"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("deleted_at", 0)
            ->select("id")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        if ($target == "ios") {
            $ios_signatures = new SignsIosModel();
            $signs = $ios_signatures
                ->where(["app_id" => $app["id"]])
                ->select("uid,name")
                ->findAll();
        } else {
            $android_signatures = new SignsAndroidModel();
            $signs = $android_signatures
                ->where(["app_id" => $app["id"]])
                ->select("uid,name")
                ->findAll();
        }

        $list = [];
        foreach ($signs as $sign) {
            $list[] = [
                "value" => $sign["uid"],
                "title" => $sign["name"],
            ];
        }

        return $this->respond(["list" => $list], 200);
    }

}