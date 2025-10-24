<?php namespace App\Controllers\Api\Customer\Projects\Signing;

use App\Controllers\PrivateController;
use App\Models\SignsAndroidModel;
use CodeIgniter\HTTP\ResponseInterface;

class RemoveAndroidSignature extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Remove android signature
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $android_signs = new SignsAndroidModel();

        $item = $android_signs
            ->where(["uid" => esc($uid), "user_id" => $this->userId])
            ->select("id,file")
            ->first();

        if (!$item) {
            return $this->respond(["message" => lang("Message.message_28")], 400);
        }

        $android_signs->delete($item["id"]);

        unlink(WRITEPATH.'storage/android/'.$item["file"]);

        return $this->respond(["status" => "ok"], 200);
    }

}