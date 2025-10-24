<?php namespace App\Controllers\Api\Manager\Plans;

use App\Controllers\PrivateController;
use App\Models\PlansModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class CreatePlan extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create new plan
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->create_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $plans = new PlansModel();

        $id = $plans->insert([
            "count"  => (int) $this->request->getJsonVar("count"),
            "price"  => esc($this->request->getJsonVar("price")),
            "save"   => esc($this->request->getJsonVar("save")),
            "api_id" => esc($this->request->getJsonVar("api_id")),
            "status" => 1
        ]);

        return $this->respond(["code" => 200, "id" => (int) $id], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for create plan
     * @return array
     */
    private function create_validation_type(): array
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