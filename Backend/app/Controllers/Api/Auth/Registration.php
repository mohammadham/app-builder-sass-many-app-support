<?php
namespace App\Controllers\Api\Auth;

use App\Controllers\BaseController;
use App\Libraries\Authorization\Issuance;
use App\Libraries\Authorization\Passport;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Registration extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create new account
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->register_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $email = esc($this->request->getJsonVar("email"));

        $users = new UsersModel();

        if ($users->where("email", $email)->first()) {
            return $this->respond(["message" => lang("Message.message_2")], 400);
        }

        $passport = new Passport(12, false);

        $userId = $users->insert([
            "email"    => $email,
            "password" => $passport->HashPassword(
                esc($this->request->getJsonVar("password"))
            ),
        ]);

        $issuance = new Issuance();

        return $this->respond([
            "email" => $email,
            "token" => $issuance->create_auth_tokens($userId),
            "login" => true
        ], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for sign up
     * @return array
     */
    private function register_validation_type(): array
    {
        return [
            "email"        => [
                "label" => lang("Fields.field_1"),
                "rules" => "required|valid_email|max_length[100]"
            ],
            "password"     => [
                "label" => lang("Fields.field_2"),
                "rules" => "required|max_length[100]|alpha_numeric"
            ],
            "re_password"  => [
                "label" => lang("Fields.field_3"),
                "rules" => "required|required|matches[password]"
            ]
        ];
    }
}