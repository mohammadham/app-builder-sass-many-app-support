<?php namespace App\Controllers\Api\Manager\Users;

use App\Controllers\PrivateController;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserDetail extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get customer detail
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $id = (int) $this->request->getGet("id");

        $users = new UsersModel();

        $user = $users
            ->where("id", $id)
            ->first();

        if (!$user) {
            return $this->respond(["message" => lang("Message.message_60")], 404);
        }

        return $this->respond(["email" => $user["email"], "admin" => (int) $user["admin"]], 200);
    }

}