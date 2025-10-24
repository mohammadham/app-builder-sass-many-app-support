<?php namespace App\Controllers\Api\Manager\Transactions;

use App\Controllers\PrivateController;
use App\Libraries\Common;
use App\Models\AppsModel;
use App\Models\SubscribesModel;
use CodeIgniter\HTTP\ResponseInterface;

define("LIMIT", 20);

class UserSubscribes extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get user subscribes
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $offset = (int) $this->request->getGet("offset");
        $user_id = (int) $this->request->getGet("user_id");

        $subscribes = new SubscribesModel();

        $list = $subscribes
            ->where("user_id", $user_id)
            ->where("is_disable", 0)
            ->orderBy("id", "DESC")
            ->findAll(LIMIT, $offset);

        $items = [];

        $common = new Common();

        if ($list) {
            $app_ids = [];
            foreach ($list as $item) {
                $app_ids[] = $item["app_id"];
            }

            $projects = new AppsModel();

            $apps = $projects
                ->whereIn("id", $app_ids)
                ->select("uid,name,id")
                ->findAll();

            foreach ($list as $item) {
                $app = null;
                foreach ($apps as $app_item) {
                    if ($app_item["id"] == $item["app_id"]) {
                        $app = $app_item;
                        break;
                    }
                }
                if ($app) {
                    $items[] = [
                        "uid"        => $item["uid"],
                        "subscribe_external_id" => $item["subscribe_external_id"],
                        "created_at" => date('d-m-Y H:i', $item['created_at']),
                        "expires_at" => date('d-m-Y H:i', $item['expires_at']),
                        "price"      => $item["price"],
                        "app"        => [
                            "name" => $app["name"],
                            "uid"  => $app["uid"],
                            "icon" => $common->get_icon($app["uid"])
                        ],
                        "is_active"  => (bool) $item["is_active"]
                    ];
                }
            }
        }

        $total = $subscribes
            ->where("user_id", $user_id)
            ->countAllResults();

        return $this->respond(["list" => $items, "total" => $total], 200);
    }

}