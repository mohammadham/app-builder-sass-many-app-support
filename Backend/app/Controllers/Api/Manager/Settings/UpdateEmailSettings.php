<?php namespace App\Controllers\Api\Manager\Settings;

use App\Controllers\PrivateController;
use App\Models\EmailConfigModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UpdateEmailSettings extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update email settings
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->update_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $emailSettings = new EmailConfigModel();

        $emailSettings->update(1, [
            "host"     => esc($this->request->getJsonVar("host")),
            "user"     => esc($this->request->getJsonVar("user")),
            "port"     => (int) $this->request->getJsonVar("port"),
            "timeout"  => (int) $this->request->getJsonVar("timeout"),
            "charset"  => esc($this->request->getJsonVar("charset")),
            "sender"   => esc($this->request->getJsonVar("sender")),
            "password" => esc($this->request->getJsonVar("password"))
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for update email settings
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "host"     => [
                "label" => lang("Fields.field_97"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "user"     => [
                "label" => lang("Fields.field_98"),
                "rules" => "required|valid_email|max_length[100]"
            ],
            "port"     => [
                "label" => lang("Fields.field_99"),
                "rules" => "required|numeric"
            ],
            "timeout"  => [
                "label" => lang("Fields.field_100"),
                "rules" => "required|numeric"
            ],
            "charset"  => [
                "label" => lang("Fields.field_101"),
                "rules" => "required|min_length[3]|max_length[10]"
            ],
            "sender"   => [
                "label" => lang("Fields.field_102"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "password" => [
                "label" => lang("Fields.field_103"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
        ];
    }
}