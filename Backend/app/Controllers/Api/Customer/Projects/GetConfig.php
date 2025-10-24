<?php namespace App\Controllers\Api\Customer\Projects;

use App\Controllers\PrivateController;
use App\Models\AppsModel;
use App\Models\AppConfigsModel;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class GetConfig extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get app configuration
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getGet("uid"));

        if (!$uid) {
            return $this->respond(["message" => "UID is required"], 400);
        }

        // Get app
        $appsModel = new AppsModel();
        $app = $appsModel
            ->where("uid", $uid)
            ->where("user", $this->userId)
            ->where("deleted_at", 0)
            ->select("id,template_id,name")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        // Get template
        $templatesModel = new TemplatesModel();
        $template = $templatesModel->find($app["template_id"]);

        if (!$template) {
            return $this->respond(["message" => "Template not found"], 404);
        }

        // Get config if exists
        $configModel = new AppConfigsModel();
        $config = $configModel->getByAppId($app["id"]);

        $configData = [];
        $lockedFields = [];
        $isLocked = false;

        if ($config) {
            $configData = json_decode($config["config_data"], true) ?: [];
            $lockedFields = json_decode($config["locked_fields"], true) ?: [];
            $isLocked = (bool) $config["is_locked"];
        }

        return $this->respond([
            "app_name" => $app["name"],
            "template_id" => $app["template_id"],
            "template_name" => $template["name_en"],
            "template_name_fa" => $template["name_fa"],
            "schema" => json_decode($template["config_schema"], true),
            "config_data" => $configData,
            "locked_fields" => $lockedFields,
            "is_locked" => $isLocked
        ], 200);
    }
}