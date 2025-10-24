<?php
namespace App\Controllers\Api\Auth;

use App\Controllers\BaseController;
use App\Libraries\Authorization\Issuance;
use App\Libraries\Authorization\Passport;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Start sign in to account by email/password
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->login_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $users = new UsersModel();

        $user = $users
            ->where("email", esc($this->request->getJsonVar("email")))
            ->where("deleted_at", 0)
            ->select("id,email,password,admin")
            ->first();

        if (!$user) {
            return $this->respond(["message" => lang("Message.message_1")], 400);
        }

        $password = esc($this->request->getJsonVar("password"));

        $passport = new Passport(12, false);

        if (!$passport->CheckPassword($password, $user["password"])) {
            return $this->respond(["message" => lang("Message.message_1")], 400);
        }

        $issuance = new Issuance();

        return $this->respond([
            "email"         => $user['email'],
            "token"         => $issuance->create_auth_tokens($user["id"]),
            "login"         => true,
            "admin"         => (bool) $user["admin"],
        ], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for sign in
     * @return array
     */
    private function login_validation_type(): array
    {
        return [
            "email"    => [
                "label" => lang("Fields.field_1"),
                "rules" => "required|valid_email|max_length[100]"
            ],
            "password" => [
                "label" => lang("Fields.field_2"),
                "rules" => "required|max_length[100]|alpha_numeric"
            ]
        ];
    }
}