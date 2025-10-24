<?php
namespace App\Controllers\Api\Auth;

use App\Controllers\BaseController;
use App\Libraries\Authorization\Passport;
use App\Libraries\Notification;
use App\Libraries\Settings;
use App\Models\ResetAttemptsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Reset extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Reset password
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        $token = esc($this->request->getGet("token"));

        if (!$token) {
            return $this->respond(["message" => lang("Message.message_5")], 400);
        }

        $resets = new ResetAttemptsModel();

        $item = $resets
            ->where("token", $token)
            ->where("status", 0)
            ->first();

        if (!$item) {
            return $this->respond(["message" => lang("Message.message_6")], 400);
        }

        $current = date('Y-m-d', strtotime("-1 days"));

        if (strtotime($current) > $item['created_at']) {
            return $this->respond(["message" => lang("Message.message_7")], 400);
        }

        $users = new UsersModel();

        $user = $users
            ->where('email', $item['email'])
            ->select('id,email')
            ->first();

        if (!$user) {
            return $this->respond(["message" => lang("Message.message_3")], 400);
        }

        helper('text');
        $new_pass = random_string('alnum', 16);

        $passport = new Passport(12, false);

        $users->update($user['id'], [
            "password" => $passport->HashPassword($new_pass)
        ]);

        $resets->update($item['id'], [
            "status" => 1
        ]);

        $this->send_pass_email($new_pass, $user['email']);

        return $this->respond(["message" => lang("Message.message_8")], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Send new password
     * @param $password
     * @param $email
     * @return void
     */
    private function send_pass_email($password, $email): void
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
}