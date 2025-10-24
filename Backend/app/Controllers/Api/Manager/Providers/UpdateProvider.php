<?php namespace App\Controllers\Api\Manager\Providers;

use App\Controllers\PrivateController;
use App\Models\DepositMethodsModel;
use CodeIgniter\HTTP\ResponseInterface;

class UpdateProvider extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update payment provider
     * @return ResponseInterface
     * @throws \ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->update_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $id = (int) $this->request->getGet("id");

        $methods = new DepositMethodsModel();

        $method = $methods
            ->where("id", $id)
            ->select("id")
            ->first();

        if (!$method) {
            return $this->respond(["message" =>  lang("Message.message_69")], 400);
        }

        $active_method = $methods
            ->where("status", 1)
            ->select("id")
            ->first();

        if ($active_method && $this->request->getJsonVar("status") == 1) {
            if ($active_method != $method["id"]) {
                $methods->update($active_method["id"], [
                    "status" => 0
                ]);
            }
        }

        $methods->update($method["id"], [
            "status"      => (int) $this->request->getJsonVar("status"),
            "name"        => esc($this->request->getJsonVar("name")),
            "api_value_1" => esc($this->request->getJsonVar("api_value_1")),
            "api_value_2" => esc($this->request->getJsonVar("api_value_2")),
            "api_value_3" => esc($this->request->getJsonVar("api_value_3")),
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for update provider
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "name"        => [
                "label" => lang("Fields.field_131"),
                "rules" => "required|max_length[1000]"
            ],
            "status"      => [
                "label" => lang("Fields.field_132"),
                "rules" => "required|in_list[0,1]"
            ],
            "api_value_1" => [
                "label" => lang("Fields.field_133"),
                "rules" => "permit_empty|max_length[1000]"
            ],
            "api_value_2" => [
                "label" => lang("Fields.field_134"),
                "rules" => "permit_empty|max_length[1000]"
            ],
            "api_value_3" => [
                "label" => lang("Fields.field_135"),
                "rules" => "permit_empty|max_length[1000]"
            ],
        ];
    }
}