<?php namespace App\Controllers\Api\Customer\Projects;

use App\Controllers\PrivateController;
use App\Models\AppsModel;
use App\Models\AppConfigsModel;
use App\Models\TemplatesModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class SaveConfig extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Save or update app configuration
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        $uid = esc($this->request->getJsonVar("uid"));
        $configData = $this->request->getJsonVar("config_data");

        if (!$uid || !$configData) {
            return $this->respond(["message" => "UID and config_data are required"], 400);
        }

        // Get app
        $appsModel = new AppsModel();
        $app = $appsModel
            ->where("uid", $uid)
            ->where("user", $this->userId)
            ->where("deleted_at", 0)
            ->select("id,template_id")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        // Get template to check schema
        $templatesModel = new TemplatesModel();
        $template = $templatesModel->find($app["template_id"]);

        if (!$template || $template["status"] != 1) {
            return $this->respond(["message" => "Template not found or inactive"], 404);
        }

        $schema = json_decode($template["config_schema"], true);
        if (!$schema || !is_array($schema)) {
            return $this->respond(["message" => "Invalid template schema"], 500);
        }

        // Check if config already exists
        $configModel = new AppConfigsModel();
        $existingConfig = $configModel->getByAppId($app["id"]);

        $lockedFields = [];
        
        // If config exists and is locked, validate locked fields
        if ($existingConfig && $existingConfig["is_locked"]) {
            $existingLockedFields = json_decode($existingConfig["locked_fields"], true) ?: [];
            $existingData = json_decode($existingConfig["config_data"], true) ?: [];

            // Check if any locked field was modified
            foreach ($existingLockedFields as $fieldName) {
                if (isset($existingData[$fieldName]) && isset($configData[$fieldName])) {
                    if ($existingData[$fieldName] != $configData[$fieldName]) {
                        return $this->respond([
                            "message" => "Field '{$fieldName}' is locked and cannot be modified"
                        ], 400);
                    }
                }
            }

            $lockedFields = $existingLockedFields;
        } else {
            // First save - determine which fields should be locked
            foreach ($schema as $field) {
                if (isset($field["immutable"]) && $field["immutable"] === true) {
                    $lockedFields[] = $field["name"];
                }
            }
        }

        // Validate config data against schema
        $validationErrors = $this->validateConfigData($configData, $schema);
        if (!empty($validationErrors)) {
            return $this->respond(["message" => $validationErrors], 400);
        }

        // Save or update config
        $configToSave = [
            "app_id" => $app["id"],
            "template_id" => $app["template_id"],
            "config_data" => json_encode($configData),
            "locked_fields" => json_encode($lockedFields),
            "is_locked" => 1,
            "updated_at" => time()
        ];

        if ($existingConfig) {
            // Update
            $configModel->update($existingConfig["id"], $configToSave);
        } else {
            // Insert
            $configToSave["created_at"] = time();
            $configModel->insert($configToSave);
        }

        return $this->respond([
            "message" => "Configuration saved successfully",
            "locked_fields" => $lockedFields
        ], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Validate config data against schema
     * @param array $configData
     * @param array $schema
     * @return array Validation errors
     */
    private function validateConfigData(array $configData, array $schema): array
    {
        $errors = [];

        foreach ($schema as $field) {
            $fieldName = $field["name"];
            $fieldValue = $configData[$fieldName] ?? null;

            // Check required fields
            if (isset($field["required"]) && $field["required"] === true) {
                if ($fieldValue === null || $fieldValue === "") {
                    $label = $field["label_en"] ?? $fieldName;
                    $errors[] = "{$label} is required";
                    continue;
                }
            }

            // Type validation
            if ($fieldValue !== null && $fieldValue !== "") {
                switch ($field["type"]) {
                    case "number":
                        if (!is_numeric($fieldValue)) {
                            $errors[] = "{$fieldName} must be a number";
                        }
                        break;
                    case "url":
                        if (!filter_var($fieldValue, FILTER_VALIDATE_URL)) {
                            $errors[] = "{$fieldName} must be a valid URL";
                        }
                        break;
                    case "email":
                        if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                            $errors[] = "{$fieldName} must be a valid email";
                        }
                        break;
                    case "boolean":
                        if (!is_bool($fieldValue) && $fieldValue !== 0 && $fieldValue !== 1) {
                            $errors[] = "{$fieldName} must be a boolean";
                        }
                        break;
                }
            }
        }

        return $errors;
    }
}