<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Models\SettingsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UpdateSiteSettings extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update website settings
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
     * Get validation rules for update website settings
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "site_name"       => [
                "label" => lang("Fields.field_83"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "site_url"        => [
                "label" => lang("Fields.field_84"),
                "rules" => "required|min_length[3]|max_length[250]"
            ],
            "currency_code"   => [
                "label" => lang("Fields.field_85"),
                "rules" => "required|min_length[3]|max_length[3]"
            ],
            "currency_symbol" => [
                "label" => lang("Fields.field_86"),
                "rules" => "required|min_length[1]|max_length[1]"
            ],
            "google_id"       => [
                "label" => lang("Fields.field_95"),
                "rules" => "max_length[100]"
            ],
            "google_enabled"  => [
                "label" => lang("Fields.field_96"),
                "rules" => "required|in_list[0,1]"
            ]
        ];
    }
}