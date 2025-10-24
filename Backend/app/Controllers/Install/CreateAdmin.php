<?php namespace App\Controllers\Install;

use App\Controllers\BaseController;
use App\Libraries\Authorization\Passport;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class CreateAdmin extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create admin account
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->admin_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $users = new UsersModel();

        $passport = new Passport(12, false);

        $users->insert([
            "email"    => esc($this->request->getJsonVar("email")),
            "password" => $passport->HashPassword(esc($this->request->getJsonVar("password"))),
            "admin"    => 1
        ]);

        return $this->respond(["code" => 200], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for create admin
     * @return array
     */
    private function admin_validation_type(): array
    {
        return [
            "email"        => [
                "label" => lang("Install.install_23"),
                "rules" => "required|valid_email|max_length[100]"
            ],
            "password"     => [
                "label" => lang("Install.install_24"),
                "rules" => "required|max_length[100]|alpha_numeric"
            ],
            "re_password"  => [
                "label" => lang("Install.install_25"),
                "rules" => "required|required|matches[password]"
            ]
        ];
    }
}