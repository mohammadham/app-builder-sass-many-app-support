<?php namespace App\Controllers\Api\Manager\Projects\Signing;

use App\Controllers\PrivateController;
use App\Models\AppsModel;
use App\Models\SignsAndroidModel;
use App\Models\SignsIosModel;
use CodeIgniter\HTTP\ResponseInterface;

class SignaturesList extends PrivateController
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

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("deleted_at", 0)
            ->select("id")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        $ios_signatures = new SignsIosModel();

        $signs_ios = $ios_signatures
            ->where(["app_id" => $app["id"]])
            ->findAll();

        $android_signatures = new SignsAndroidModel();

        $signs_android = $android_signatures
            ->where(["app_id" => $app["id"]])
            ->findAll();

        $list = [];

        foreach ($signs_android as $sign) {
            $list[] = [
                "uid"     => $sign["uid"],
                "name"    => $sign["name"],
                "info"    => $sign["alias"],
                "type"    => "android",
                "created" => date('d-m-Y H:i', $sign['created_at']),
                "unix"    => (int) $sign['created_at']
            ];
        }

        foreach ($signs_ios as $sign) {
            $list[] = [
                "uid"     => $sign["uid"],
                "name"    => $sign["name"],
                "info"    => $sign["issuer_id"]." / ".$sign["key_id"],
                "type"    => "ios",
                "created" => date('d-m-Y H:i', $sign['created_at']),
                "unix"    => (int) $sign['created_at']
            ];
        }

        usort($list, function($a, $b) {
            return $b['unix'] <=> $a['unix'];
        });

        return $this->respond(["list" => $list], 200);
    }

}