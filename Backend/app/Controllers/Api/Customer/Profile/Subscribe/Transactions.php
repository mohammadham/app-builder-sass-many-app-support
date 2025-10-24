<?php namespace App\Controllers\Api\Customer\Profile\Subscribe;

use App\Controllers\PrivateController;
use App\Libraries\Common;
use App\Models\AppsModel;
use App\Models\DepositMethodsModel;
use App\Models\SubscribesModel;
use App\Models\TransactionsModel;
use CodeIgniter\HTTP\ResponseInterface;

define("LIMIT", 20);

class Transactions extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get subscribe transactions
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $subscribe_uid = esc($this->request->getGet("uid"));
        $offset = (int) $this->request->getGet("offset");

        $subscribes = new SubscribesModel();

        $subscribe = $subscribes
            ->where("user_id", $this->userId)
            ->where("is_disable", 0)
            ->where("uid", $subscribe_uid)
            ->orderBy("id", "DESC")
            ->first();

        if (!$subscribe) {
            return $this->respond(["message" => lang("Message.message_87")], 404);
        }

        $transactions = new TransactionsModel();

        $transactions_list = $transactions
            ->where("subscribe_external_id", $subscribe["subscribe_external_id"])
            ->select("uid,amount,created_at,method_id,id")
            ->orderBy("id", "DESC")
            ->findAll(LIMIT, $offset);

        $items = [];

        if ($transactions_list) {
            $method_ids = [];
            foreach ($transactions_list as $item) {
                $method_ids[] = $item["method_id"];
            }

            $deposit_methods = new DepositMethodsModel();

            $methods = $deposit_methods
                ->whereIn("id", $method_ids)
                ->select("id,name,logo")
                ->findAll();

            foreach ($transactions_list as $item) {
                $method = null;
                foreach ($methods as $pay_method) {
                    if ($pay_method["id"] == $item["method_id"]) {
                        $method = $pay_method;
                        break;
                    }
                }
                $items[] = [
                    "uid" => $item["uid"],
                    "amount" => $item["amount"],
                    "method" => [
                        "name" => $method["name"],
                        "logo" => base_url("deposit/".$method["logo"])
                    ],
                    "created_at" => date('d-m-Y H:i', $item['created_at']),
                ];
            }
        }

        $total = $transactions
            ->where("subscribe_external_id", $subscribe["subscribe_external_id"])
            ->countAllResults();

        $projects = new AppsModel();

        $app = $projects
            ->where("id", $subscribe["app_id"])
            ->select("uid,name,link")
            ->first();

        $common = new Common();

        $subscribe_item = [
            "uid"        => $subscribe["uid"],
            "created_at" => date('d-m-Y H:i', $subscribe['created_at']),
            "expires_at" => date('d-m-Y H:i', $subscribe['expires_at']),
            "price"      => $subscribe["price"],
            "app"        => [
                "name" => $app["name"],
                "uid"  => $app["uid"],
                "icon" => $common->get_icon($app["uid"]),
                "link" => $app["link"]
            ],
            "is_active"  => (bool) $subscribe["is_active"]
        ];

        return $this->respond([
            "list" => $items,
            "total" => $total,
            "subscribe" => $subscribe_item
        ], 200);
    }
}