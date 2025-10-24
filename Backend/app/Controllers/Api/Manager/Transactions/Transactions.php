<?php namespace App\Controllers\Api\Manager\Transactions;

use App\Controllers\PrivateController;
use App\Models\DepositMethodsModel;
use App\Models\TransactionsModel;
use CodeIgniter\HTTP\ResponseInterface;

define("LIMIT", 20);

class Transactions extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get all transactions
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $offset = (int) $this->request->getGet("offset");

        $transactions = new TransactionsModel();

        $deposit_methods = new DepositMethodsModel();

        $methods = $deposit_methods
            ->select("id,name,logo")
            ->findAll();

        $transactions_list = $transactions
            ->select("uid,amount,created_at,method_id,id,external_uid,subscribe_external_id")
            ->orderBy("id", "DESC")
            ->findAll(LIMIT, $offset);

        $items = [];

        foreach ($transactions_list as $item) {
            $method = null;
            foreach ($methods as $pay_method) {
                if ($pay_method["id"] == $item["method_id"]) {
                    $method = $pay_method;
                    break;
                }
            }
            $items[] = [
                "uid"          => $item["uid"],
                "external_uid" => $item["external_uid"],
                "subscribe_external_id" => $item["subscribe_external_id"],
                "amount"       => $item["amount"],
                "method"       => [
                    "name" => $method["name"],
                    "logo" => base_url("deposit/".$method["logo"])
                ],
                "created_at"   => date('d-m-Y H:i', $item['created_at']),
            ];
        }

        $total = $transactions->countAllResults();

        return $this->respond(["list" => $items, "total" => $total], 200);
    }
}