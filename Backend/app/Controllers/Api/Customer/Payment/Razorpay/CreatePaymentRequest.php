<?php namespace App\Controllers\Api\Customer\Payment\Razorpay;

use App\Controllers\PrivateController;
use App\Models\AppsModel;
use App\Models\DepositMethodsModel;
use App\Models\PlansModel;
use App\Models\SubscribesModel;
use CodeIgniter\HTTP\ResponseInterface;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\BadRequestError;

require_once(APPPATH . 'ThirdParty/Razorpay/Razorpay.php');

class CreatePaymentRequest extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create subscribe request with Razorpay
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->create_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $uid = esc($this->request->getGet("uid"));

        $projects = new AppsModel();

        $app = $projects
            ->where("uid", $uid)
            ->where("user", $this->userId)
            ->where("deleted_at", 0)
            ->select("id")
            ->first();

        if (!$app) {
            return $this->respond(["message" => lang("Message.message_14")], 404);
        }

        $subscribes = new SubscribesModel();

        $is_active = $subscribes
            ->where("app_id", $app["id"])
            ->where("expires_at >", time())
            ->where("is_disable", 0)
            ->countAllResults();

        if ($is_active) {
            return $this->respond(["message" => lang("Message.message_86")], 400);
        }

        $plans = new PlansModel();

        $plan = $plans
            ->where("id", (int) $this->request->getJsonVar("plan_id"))
            ->where("status", 1)
            ->where("deleted_at", 0)
            ->select("id,api_id,count")
            ->first();

        if (!$plan) {
            return $this->respond(["message" => lang("Message.message_83")], 404);
        }

        $methods = new DepositMethodsModel();

        $method = $methods
            ->where("id", 6)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 400);
        }

        $api = new Api($method["api_value_1"], $method["api_value_2"]);

        try {
            $res = $api->subscription->create([
                "plan_id"         => $plan["api_id"],
                "total_count"     => 12,
                "quantity"        => 1,
                "customer_notify" => 1,
                "notes"           => [
                    "customer_id" => $this->userId,
                    "plan_id"     => $plan["id"],
                    "app_id"      => $app["id"]
                ],
            ]);
        } catch (BadRequestError $e) {
            return $this->respond(["message" => $e->getMessage()], 400);
        }

        return $this->respond(["url" => $res->short_url], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for create new payment
     * @return array
     */
    private function create_validation_type(): array
    {
        return [
            "plan_id" => [
                "label" => lang("Fields.field_148"),
                "rules" => "required|numeric"
            ],
        ];
    }
}