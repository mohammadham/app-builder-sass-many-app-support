<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Models\SettingsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UpdateApiSettings extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update api settings
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
     * Get validation rules for update api settings
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "github_username" => [
                "label" => lang("Fields.field_87"),
                "rules" => "required|min_length[2]|max_length[100]"
            ],
            "github_token"    => [
                "label" => lang("Fields.field_88"),
                "rules" => "required|min_length[8]|max_length[100]"
            ],
            "github_repo"     => [
                "label" => lang("Fields.field_89"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "codemagic_key"   => [
                "label" => lang("Fields.field_90"),
                "rules" => "required|min_length[8]|max_length[100]"
            ],
            "codemagic_id"    => [
                "label" => lang("Fields.field_91"),
                "rules" => "required|min_length[8]|max_length[100]"
            ],
            "github_branch"   => [
                "label" => lang("Fields.field_152"),
                "rules" => "required|min_length[2]|max_length[100]"
            ]
        ];
    }
}