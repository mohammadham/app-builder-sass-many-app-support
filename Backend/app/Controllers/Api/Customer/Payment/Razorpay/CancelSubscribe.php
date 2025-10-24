<?php namespace App\Controllers\Api\Customer\Payment\Razorpay;

use App\Controllers\PrivateController;
use App\Models\DepositMethodsModel;
use App\Models\SubscribesModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\BadRequestError;
use ReflectionException;

require_once(APPPATH . 'ThirdParty/Razorpay/Razorpay.php');

class CancelSubscribe extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Cancel subscribe with Razorpay
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
            ->where("id", 6)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 404);
        }

        $api = new Api($method["api_value_1"], $method["api_value_2"]);

        try {
            $api->subscription->fetch($subscribe["subscribe_external_id"])->cancel();
        } catch (BadRequestError $e) {
            return $this->respond(["message" => $e->getMessage()], 400);
        }

        $subscribes->update($subscribe["id"], [
            "is_active" => 0
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

}