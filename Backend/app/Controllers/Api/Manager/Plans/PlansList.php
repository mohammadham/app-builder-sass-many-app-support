<?php namespace App\Controllers\Api\Manager\Plans;

use App\Controllers\PrivateController;
use App\Models\PlansModel;
use CodeIgniter\HTTP\ResponseInterface;

class PlansList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get plans list
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $plans = new PlansModel();

        $items = $plans
            ->where("deleted_at", 0)
            ->orderBy("count", "ASC")
            ->findAll();

        $list = [];

        foreach ($items as $item) {
            $list[] = [
                "id"     => (int) $item["id"],
                "count"  => (int) $item["count"],
                "price"  => $item["price"],
                "save"   => $item["save"],
                "api_id" => $item["api_id"],
            ];
        }

        return $this->respond(["list" => $list], 200);
    }

}