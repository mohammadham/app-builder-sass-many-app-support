<?php namespace App\Controllers\Api\Customer\Payment\YooKassa;

use App\Controllers\PrivateController;
use App\Models\DepositMethodsModel;
use App\Models\PaymentIntentModel;
use App\Models\SubscribesModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class CancelSubscribe extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Cancel subscribe with YooKassa
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        $subscribe_uid = esc($this->request->getGet("uid"));

        $users = new UsersModel();

        $user = $users
            ->where("id", $this->userId)
            ->select("admin")
            ->first();

        $subscribes = new SubscribesModel();

        if ($user["admin"]) {
            $subscribe = $subscribes
                ->where("is_disable", 0)
                ->where("uid", $subscribe_uid)
                ->where("is_active", 1)
                ->first();
        } else {
            $subscribe = $subscribes
                ->where("user_id", $this->userId)
                ->where("is_disable", 0)
                ->where("uid", $subscribe_uid)
                ->where("is_active", 1)
                ->first();
        }

        if (!$subscribe) {
            return $this->respond(["message" => lang("Message.message_87")], 404);
        }

        $methods = new DepositMethodsModel();

        $method = $methods
            ->where("id", 5)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 404);
        }

        $intents = new PaymentIntentModel();

        $payment = $intents
            ->where("subscribe_id", $subscribe["id"])
            ->where("is_pending", 1)
            ->first();

        if (!$payment) {
            return $this->respond(["message" => lang("Message.message_94")], 404);
        }

        // cancel subscribe
        $subscribes->update($subscribe["id"], [
            "is_active" => 0
        ]);
        // remove intent
        $intents->update($payment["id"], [
            "is_pending" => 0
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

}