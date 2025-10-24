<?php namespace App\Controllers\Api\Customer\Profile;

use App\Controllers\PrivateController;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileDetail extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get main profile detail
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $users = new UsersModel();

        $user = $users
            ->where("id", $this->userId)
            ->select("email,admin")
            ->first();

        if (!$user) {
            return $this->respond(["message" => lang("Message.message_3")], 404);
        }

        return $this->respond([
            "email" => $user["email"],
            "admin" => (bool) $user["admin"],
        ], 200);
    }
}