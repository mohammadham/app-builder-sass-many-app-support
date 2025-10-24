<?php namespace App\Controllers\Api\Customer\Projects\Signing;

use App\Controllers\PrivateController;
use App\Models\SignsIosModel;
use CodeIgniter\HTTP\ResponseInterface;

class RemoveIosSignature extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Remove ios signature
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $ios_signs = new SignsIosModel();

        $item = $ios_signs
            ->where(["uid" => esc($uid), "user_id" => $this->userId])
            ->select("id,file")
            ->first();

        if (!$item) {
            return $this->respond(["message" => lang("Message.message_28")], 400);
        }

        $ios_signs->delete($item["id"]);

        unlink(WRITEPATH.'storage/ios/'.$item["file"]);

        return $this->respond(["status" => "ok"], 200);
    }

}