<?php namespace App\Controllers\Api\Customer\Payment\Stripe;

use App\Controllers\PrivateController;
use App\Models\DepositMethodsModel;
use App\Models\SubscribesModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

require_once(APPPATH . 'ThirdParty/Stripe/init.php');

class CancelSubscribe extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Cancel subscribe with stripe
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
            ->where("id", 1)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 404);
        }

        $stripe = new StripeClient($method["api_value_1"]);

        try {
            $stripe->subscriptions->cancel($subscribe["subscribe_external_id"], []);
            $subscribes->update($subscribe["id"], [
                "is_active" => 0
            ]);
            return $this->respond(["status" => "ok"], 200);
        } catch (ApiErrorException $e) {
            return $this->respond(["message" => $e->getMessage()], 400);
        }
    }

}