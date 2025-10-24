<?php namespace App\Controllers\Api\Customer\Payment\Stripe;

use App\Controllers\PrivateController;
use App\Libraries\Settings;
use App\Models\AppsModel;
use App\Models\DepositMethodsModel;
use App\Models\PlansModel;
use App\Models\SubscribesModel;
use CodeIgniter\HTTP\ResponseInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

require_once(APPPATH . 'ThirdParty/Stripe/init.php');

class CreatePaymentRequest extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create subscribe request with Stripe
     * @return ResponseInterface
     * @throws ApiErrorException
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
            ->select("id,api_id")
            ->first();

        if (!$plan) {
            return $this->respond(["message" => lang("Message.message_83")], 404);
        }

        $methods = new DepositMethodsModel();

        $method = $methods
            ->where("id", 1)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 400);
        }

        $stripe = new StripeClient($method["api_value_1"]);

        $settings = new Settings();

        $frontUrl = $settings->get_config("site_url");

        $session = $stripe->checkout->sessions->create([
            "success_url" => $frontUrl."private/profile/subscribe",
            "cancel_url"  => $frontUrl."private/profile/subscribe",
            "mode"        => "subscription",
            "line_items"  => [[
                "price"    => $plan["api_id"],
                "quantity" => 1,
            ]],
            "metadata"    => [
                "customer_id" => $this->userId,
                "plan_id"     => $plan["id"],
                "app_id"      => $app["id"]
            ],
        ]);

        return $this->respond(["url" => $session->url], 200);
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