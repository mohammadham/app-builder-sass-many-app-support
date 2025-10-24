<?php namespace App\Controllers\Api\Customer\Payment\YooKassa;

use App\Controllers\PrivateController;
use App\Libraries\Settings;
use App\Libraries\Uid;
use App\Models\AppsModel;
use App\Models\DepositMethodsModel;
use App\Models\PlansModel;
use App\Models\SubscribesModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Stripe\Exception\ApiErrorException;

class CreatePaymentRequest extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create subscribe request with YooKassa
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
            ->first();

        if (!$plan) {
            return $this->respond(["message" => lang("Message.message_83")], 404);
        }

        $methods = new DepositMethodsModel();

        $method = $methods
            ->where("id", 5)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 400);
        }

        $uid = new Uid();
        $settings = new Settings();

        $frontUrl = $settings->get_config("site_url");

        $res = Services::curlrequest([
            "baseURI"     => "https://api.yookassa.ru/v3/",
            "headers"     => [
                "Content-Type"    => "application/json",
                "Idempotence-Key" => $uid->create(),
            ],
            "auth" => [
                $method["api_value_2"],
                $method["api_value_1"]
            ],
            "http_errors" => false,
        ])->setJSON([
            "amount"              => [
                "value"    => $plan["price"],
                "currency" => $settings->get_config("currency_code")
            ],
            "payment_method_data" => [
                "type" => "bank_card"
            ],
            "confirmation"        => [
                "type"       => "redirect",
                "return_url" => $frontUrl."private/profile/subscribe",
            ],
            "capture"             => true,
            "description"         => lang("Fields.field_157")." ".$settings->get_config("site_name"),
            "save_payment_method" => true,
            "metadata"            => [
                "customer_id"  => $this->userId,
                "plan_id"      => $plan["id"],
                "app_id"       => $app["id"],
                "subscribe_id" => $uid->create()
            ],
        ])->post("payments");

        $data = json_decode($res->getBody());

        if ($res->getStatusCode() != 200) {
            return $this->respond([
                "message" => !empty($data->description) ? $data->description : lang("Message.message_93")
            ], 400);
        }

        return $this->respond(["url" => $data->confirmation->confirmation_url], 200);
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