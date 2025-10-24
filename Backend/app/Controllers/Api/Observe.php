<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\CodeMagic;
use App\Libraries\Notification;
use App\Libraries\Settings;
use App\Models\AppsModel;
use App\Models\BuildsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Observe extends BaseController
{

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get build artefacts
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        $builds = new BuildsModel();

        $build = $builds
            ->where("uid", $uid)
            ->where("status", 0)
            ->select("id,build_id,app_id")
            ->first();

        if (!$build) {
            return $this->respond(["message" => lang("Message.message_46")], 404);
        }

        $projects = new AppsModel();

        $app = $projects
            ->where("id", $build["app_id"])
            ->first();

        $users = new UsersModel();

        $customer = $users
            ->where("id", $app["user"])
            ->select("email")
            ->first();

        $codemagic = new CodeMagic();

        $res = $codemagic->check_status($build["build_id"]);

        if (!$res["event"]) {
            $this->send_notify_email($customer["email"], false, $app);
            $builds->update($build["id"], [
                "status" => 1
            ]);
            return $this->respond($res, 200);
        }

        if (!$res["data"]) {
            $this->send_notify_email($customer["email"], false, $app);
            $builds->update($build["id"], [
                "status" => 1
            ]);
            return $this->respond(["event" => false, "message" => "empty"], 200);
        }

        $builds->update($build["id"], [
            "static" => $res["data"][0]->url,
            "status" => 1
        ]);

        $this->send_notify_email($customer["email"], true, $app);

        return $this->respond(["event" => true], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Send email about change build status
     * @param string $email
     * @param bool $status
     * @param array $app
     * @return void
     */
    private function send_notify_email(string $email, bool $status, array $app): void
    {
        $settings = new Settings();
        $notification = new Notification();

        $emailVariables = [
            "{SITE_URL}",
            "{SITE_NAME}",
            "{SITE_LOGO}",
            "{LINK}",
            "{APP}",
            "{STATUS}"
        ];

        $codeVariable = [
            $settings->get_config("site_url"),
            $settings->get_config("site_name"),
            base_url("static/".$settings->get_config("site_logo")),
            $settings->get_config("site_url")."private/apps/".$app["uid"]."/build",
            $app["name"],
            $status? lang("Fields.field_122") : lang("Fields.field_123")
        ];

        $str = file_get_contents(WRITEPATH."emails/build.html");
        $content = str_replace($emailVariables, $codeVariable, $str);
        $subject = lang("Fields.field_124");
        $notification->send($email, $subject, $content);
    }
}