<?php namespace App\Controllers\Api\Manager\Plans;

use App\Controllers\PrivateController;
use App\Models\PlansModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class RemovePlan extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Remove plan
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
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
            "deleted_at" => time()
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

}