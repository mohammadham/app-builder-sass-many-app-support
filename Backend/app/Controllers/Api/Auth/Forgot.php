<?php
namespace App\Controllers\Api\Auth;

use App\Controllers\BaseController;
use App\Libraries\Notification;
use App\Libraries\Settings;
use App\Libraries\Uid;
use App\Models\ResetAttemptsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Forgot extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create reset request
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->reset_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $email = esc($this->request->getJsonVar("email"));

        $users = new UsersModel();

        $user = $users
            ->where("email", $email)
            ->where("status", 0)
            ->select("id,email,status")
            ->first();

        if (!$user) {
            return $this->respond(["message" => lang("Message.message_3")], 400);
        }

        $uid = new Uid();

        $token = $uid->create();

        $resets = new ResetAttemptsModel();

        $resets->insert([
            "email" => $email,
            "token" => $token
        ]);

        $this->send_reset_email($token, $user['email']);

        return $this->respond(["message" => lang("Message.message_4")], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Send reset password link
     * @param $token
     * @param $email
     * @return void
     */
    private function send_reset_email($token, $email): void
    {
        $settings = new Settings();
        $notifications = new Notification();

        $site_url = $settings->get_config("site_url");
        $emailVariables = [
            "{LINK}",
            "{SITE_URL}",
            "{SITE_NAME}",
            "{SITE_LOGO}"
        ];
        $codeVariable = [
            $site_url."auth/reset/?token=".$token,
            $site_url,
            $settings->get_config("site_name"),
            base_url("static/".$settings->get_config("site_logo"))
        ];
        $str = file_get_contents(WRITEPATH."emails/reset.html");
        $content = str_replace($emailVariables, $codeVariable, $str);
        $subject = lang("Fields.field_4");
        $notifications->send($email, $subject, $content);
    }

    /**
     * Get validation rules for reset password
     * @return array
     */
    private function reset_validation_type(): array
    {
        return [
            "email" => [
                "label" => lang("Fields.field_1"),
                "rules" => "required|valid_email|max_length[100]"
            ]
        ];
    }
}