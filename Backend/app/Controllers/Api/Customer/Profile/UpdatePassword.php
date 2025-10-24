<?php namespace App\Controllers\Api\Customer\Profile;

use App\Controllers\PrivateController;
use App\Libraries\Authorization\Passport;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UpdatePassword extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update user password
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->update_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $password = esc($this->request->getJsonVar("password"));
        $newPassword = esc($this->request->getJsonVar("new_password"));

        $users = new UsersModel();

        $user = $users
            ->where("id", $this->userId)
            ->select("password")
            ->first();

        $passport = new Passport(12, false);

        if (!$passport->CheckPassword($password, $user["password"])) {
            return $this->respond(["message" => lang("Message.message_1")], 400);
        }

        $users->update($this->userId, [
            "password" => $passport->HashPassword($newPassword),
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for password update
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "password"      => [
                "label" => lang("Fields.field_2"),
                "rules" => "required|max_length[100]|alpha_numeric"
            ],
            "new_password"  => [
                "label" => lang("Fields.field_77"),
                "rules" => "required|max_length[100]|alpha_numeric"
            ],
        ];
    }
}