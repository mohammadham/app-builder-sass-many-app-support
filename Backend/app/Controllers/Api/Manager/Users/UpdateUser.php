<?php namespace App\Controllers\Api\Manager\Users;

use App\Controllers\PrivateController;
use App\Libraries\Authorization\Passport;
use App\Libraries\Notification;
use App\Libraries\Settings;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UpdateUser extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update customer data
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->update_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $id = (int) $this->request->getGet("id");

        $users = new UsersModel();

        $user = $users
            ->where("id", $id)
            ->first();

        if (!$user) {
            return $this->respond(["message" => lang("Message.message_60")], 404);
        }

        $email = esc($this->request->getJsonVar("email"));

        if ($email != $user["email"]) {
            $double = $users
                ->where("email", $email)
                ->countAllResults();
            if ($double) {
                return $this->respond(["message" => lang("Message.message_2")], 400);
            }
        }

        $password = esc($this->request->getJsonVar("new_password"));

        if ($password) {
            $passport = new Passport(12, false);
            $users->update($user["id"], [
                "password" => $passport->HashPassword($password),
            ]);
            $this->send_pass_email($password, $user['email']);
        }

        $users->update($user["id"], [
            "email" => $email,
            "admin" => (int) $this->request->getJsonVar("admin")
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Send new password
     * @param string $password
     * @param string $email
     * @return void
     */
    private function send_pass_email(string $password, string $email)
    {
        $settings = new Settings();
        $notifications = new Notification();

        $emailVariables = [
            '{EMAIL}',
            '{PASSWORD}',
            "{SITE_URL}",
            "{SITE_NAME}",
            "{SITE_LOGO}"
        ];
        $codeVariable = [
            $email,
            $password,
            $settings->get_config("site_url"),
            $settings->get_config("site_name"),
            base_url("static/".$settings->get_config("site_logo"))
        ];
        $str = file_get_contents(WRITEPATH."emails/password.html");
        $content = str_replace($emailVariables, $codeVariable, $str);
        $subject = lang("Fields.field_5");
        $notifications->send($email, $subject, $content);
    }

    /**
     * Get validation rules for profile update
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "email"         => [
                "label" => lang("Fields.field_1"),
                "rules" => "required|valid_email|max_length[100]"
            ],
            "new_password"  => [
                "label" => lang("Fields.field_77"),
                "rules" => "permit_empty|required_with[password]|max_length[100]|alpha_numeric"
            ],
            "admin"         => [
                "label" => lang("Fields.field_107"),
                "rules" => "required|in_list[0,1]"
            ],
        ];
    }
}