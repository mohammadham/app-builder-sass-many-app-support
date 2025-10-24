<?php namespace App\Controllers\Api\Manager\Providers;

use App\Controllers\PrivateController;
use App\Models\DepositMethodsModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProvidersList extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get payment providers
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $methods = new DepositMethodsModel();

        $items = $methods->findAll();

        $list = [];

        foreach ($items as $item) {
            $list[] = [
                "id"          => $item["id"],
                "name"        => $item["name"],
                "logo"        => base_url("deposit/".$item["logo"]),
                "api_value_1" => $item["api_value_1"],
                "api_value_2" => $item["api_value_2"],
                "api_value_3" => $item["api_value_3"],
                "status"      => (int) $item["status"],
                "route"       => (int) $item["route"],
            ];
        }

        return $this->respond(["list" => $list], 200);
    }

}