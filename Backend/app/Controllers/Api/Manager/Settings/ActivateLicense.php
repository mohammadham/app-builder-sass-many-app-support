<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Libraries\Flangapp;
use App\Models\SettingsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class ActivateLicense extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Activate license
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->update_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $code = esc($this->request->getJsonVar("code"));

        $flangapp = new Flangapp();

        $res = $flangapp->activation_license($code);

        if (!$res["event"]) {
            return $this->respond(["message" => $res["message"]], 400);
        }

        $settings = new SettingsModel();

        $settings->update("license", [
            "value" => $code
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for license activation
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "code"  => [
                "label" => lang("Fields.field_136"),
                "rules" => "required|min_length[3]|max_length[500]"
            ],
        ];
    }
}