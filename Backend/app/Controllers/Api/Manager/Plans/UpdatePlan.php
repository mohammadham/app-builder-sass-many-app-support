<?php namespace App\Controllers\Api\Manager\Plans;

use App\Controllers\PrivateController;
use App\Models\PlansModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class UpdatePlan extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Update plan
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->update_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $id = (int) $this->request->getGet("id");

        $plans = new PlansModel();

        $plan = $plans
            ->where("id", $id)
            ->select("id")
            ->first();

        if (!$plan) {
            return $this->respond(["message" => lang("Message.message_59")], 400);
        }

        $plans->update($plan["id"], [
            "count"  => (int) $this->request->getJsonVar("count"),
            "price"  => esc($this->request->getJsonVar("price")),
            "save"   => esc($this->request->getJsonVar("save")),
            "api_id" => esc($this->request->getJsonVar("api_id")),
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for update plan
     * @return array
     */
    private function update_validation_type(): array
    {
        return [
            "count" => [
                "label" => lang("Fields.field_104"),
                "rules" => "required|numeric"
            ],
            "price" => [
                "label" => lang("Fields.field_105"),
                "rules" => "required|numeric"
            ],
            "save"  => [
                "label" => lang("Fields.field_106"),
                "rules" => "required|numeric"
            ],
            "api_id"  => [
                "label" => lang("Fields.field_151"),
                "rules" => "permit_empty|max_length[1000]"
            ],
        ];
    }
}