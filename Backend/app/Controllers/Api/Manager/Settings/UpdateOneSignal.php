<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Libraries\Settings;
use App\Models\SettingsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UpdateOneSignal extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update onesignal settings
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->update_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $settings = new SettingsModel();

        foreach ($this->request->getJsonVar() as $key => $val) {
            $settings->update($key, [
                "value" => esc($val)
            ]);
        }

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for update onesignal settings
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "one_signal_auth_key"        => [
                "label" => lang("Fields.field_153"),
                "rules" => "required|min_length[3]|max_length[300]"
            ],
            "one_signal_organization_id" => [
                "label" => lang("Fields.field_154"),
                "rules" => "required|min_length[3]|max_length[300]"
            ],
        ];
    }
}