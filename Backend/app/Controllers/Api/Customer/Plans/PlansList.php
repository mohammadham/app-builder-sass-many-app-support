<?php namespace App\Controllers\Api\Customer\Plans;

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

        $list = $plans
            ->where("status", 1)
            ->where("deleted_at", 0)
            ->orderBy("price", "ASC")
            ->findAll();

        $items = [];

        foreach ($list as $item) {
            $items[] = [
                "id"    => (int) $item["id"],
                "count" => (int) $item["count"],
                "price" => (float) $item["price"],
                "save"  => (float) $item["save"],
            ];
        }

        return $this->respond($items, 200);
    }

}