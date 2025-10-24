<?php namespace App\Controllers\Api\Customer\Projects;

use App\Controllers\PrivateController;
use App\Libraries\Common;
use App\Libraries\Uid;
use App\Models\AppsModel;
use App\Models\DrawersModel;
use App\Models\LocalsModel;
use App\Models\SplashScreensModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class CreateProject extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create new project
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->create_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $color = esc($this->request->getJsonVar("color"));

        $common = new Common();

        if (!$common->hex_validation($color)) {
            return $this->respond(["message" => lang("Message.message_12")], 400);
        }

        $users = new UsersModel();

        $user = $users
            ->where("id", $this->userId)
            ->select("email")
            ->first();

        $uid = new Uid();

        $app_uid = $uid->create();

        $link = esc($this->request->getJsonVar("link"));
        $template = (int) $this->request->getJsonVar("template");
        $template_id = (int) $this->request->getJsonVar("template_id");

        // If template_id not provided, use default (template 1 = primary template)
        if (!$template_id) {
            $templatesModel = new \App\Models\TemplatesModel();
            $primaryTemplate = $templatesModel->where('is_primary', 1)->first();
            $template_id = $primaryTemplate ? $primaryTemplate['id'] : 1;
        }

        $apps = new AppsModel();

        $project_id = $apps->insert([
            "uid"           => $app_uid,
            "name"          => esc($this->request->getJsonVar("name")),
            "user"          => $this->userId,
            "status"        => 0,
            "link"          => $link,
            "color_theme"   => $color,
            "color_title"   => (int) $this->request->getJsonVar("theme"),
            "template"      => $template,
            "template_id"   => $template_id,
            "email"         => $user["email"],
            "language"      => strtoupper($this->request->getLocale()),
            "loader_color"  => $color,
            "app_id"        => $this->create_app_id($link),
            "icon_color"    => $color,
            "active_color"  => $color,
            "display_title" => 1
        ]);

        $drawers = new DrawersModel();

        $drawers->insert([
            "app_id" => $project_id,
            "color"  => $color,
            "mode"   => !$template ? 1 : 0
        ]);

        $locals = new LocalsModel();

        $locals->insert([
            "app_id"   => $project_id,
            "string_1" => lang("Fields.field_53"),
            "string_2" => lang("Fields.field_54"),
            "string_3" => lang("Fields.field_55"),
            "string_4" => lang("Fields.field_56"),
            "string_5" => lang("Fields.field_57"),
            "string_6" => lang("Fields.field_58"),
            "string_7" => lang("Fields.field_59"),
            "string_8" => lang("Fields.field_60"),
        ]);

        $splash = new SplashScreensModel();

        $splash->insert([
            "app_id" => $project_id,
            "delay"  => 3,
            "color"  => $color
        ]);

        return $this->respond(["uid" => $app_uid], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for create new app
     * @return array
     */
    private function create_validation_type(): array
    {
        return [
            "link"      => [
                "label" => lang("Fields.field_7"),
                "rules" => "required|min_length[3]|max_length[250]|valid_url_strict"
            ],
            "name"      => [
                "label" => lang("Fields.field_6"),
                "rules" => "required|min_length[3]|max_length[50]"
            ],
            "template"  => [
                "label" => lang("Fields.field_10"),
                "rules" => "required|in_list[0,1,2,3]"
            ],
            "color"     => [
                "label" => lang("Fields.field_8"),
                "rules" => "required|min_length[7]|max_length[7]"
            ],
            "theme"     => [
                "label" => lang("Fields.field_9"),
                "rules" => "required|in_list[0,1]"
            ],
        ];
    }

    /**
     * Create APP ID
     */
    private function create_app_id(string $link): string
    {
        $host = parse_url($link, PHP_URL_HOST);
        $site = explode('.', $host);
        $app_id = 'app.'.$site[0].'.'.$site[1];
        if (count($site) > 2) {
            $app_id = $site[2].'.'.$site[1].'.'.$site[0];
        }
        return preg_replace('/[\d\-]/', '', $app_id);
    }
}