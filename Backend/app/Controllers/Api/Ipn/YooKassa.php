<?php
namespace App\Controllers\Api\Ipn;

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Libraries\Uid;
use App\Models\AppsModel;
use App\Models\DepositMethodsModel;
use App\Models\PaymentIntentModel;
use App\Models\PlansModel;
use App\Models\SubscribesModel;
use App\Models\TransactionsModel;
use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\URI;
use Config\App;
use ReflectionException;

class YooKassa extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * YooKassa webhook handler
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        $methods = new DepositMethodsModel();

        $method = $methods
            ->where("id", 5)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 400);
        }

        $payload = @file_get_contents('php://input');

        $data = json_decode($payload);

        if ($data->event != "payment.succeeded") {
            // Unhandled event type
            return $this->respond(["status" => "ok"], 200);
        }

        if (!$this->checkIPRange($this->request->getIPAddress())) {
            // Invalid ip address
            return $this->respond(["status" => "ok"], 200);
        }

        $uid = new Uid();
        $subscribes = new SubscribesModel();

        $subscribe_external_id = $data->object->metadata->subscribe_id;
        $app_id = $data->object->metadata->app_id;
        $plan_id = $data->object->metadata->plan_id;
        $user_id = $data->object->metadata->customer_id;
        $amount = $data->object->amount->value;
        $invoice_id = $data->object->id;

        $plans = new PlansModel();

        $plan = $plans
            ->where("id", $plan_id)
            ->select("count")
            ->first();

        $currentDate = date('Y-m-d');
        $newDate = date('Y-m-d', strtotime('+'.$plan['count'].' month', strtotime($currentDate)));

        $subscribe = $subscribes
            ->where("subscribe_external_id", $subscribe_external_id)
            ->select("id")
            ->first();

        if ($subscribe) {
            // update subscribe
            $subscribes->update($subscribe["id"], [
                "expires_at" => strtotime($newDate),
            ]);
        } else {
            // create subscribe
            $sub_id = $subscribes->insert([
                "subscribe_external_id" => $subscribe_external_id,
                "customer_external_id"  => "",
                "plan_id"               => $plan_id,
                "user_id"               => $user_id,
                "expires_at"            => strtotime($newDate),
                "app_id"                => $app_id,
                "price"                 => $amount,
                "uid"                   => $uid->create(),
                "is_active"             => 1,
                "method_id"             => 5
            ]);

            $projects = new AppsModel();

            $projects->update($app_id, [
                "status" => 1
            ]);

            $intents = new PaymentIntentModel();

            // create payment intent
            $intents->insert([
                "subscribe_id"  => $sub_id,
                "method_id"     => 5,
                "is_pending"    => 1,
                "payment_token" => $data->object->payment_method->id,
                "planned_at"    => strtotime($newDate)
            ]);
        }

        $transactions = new TransactionsModel();

        $transactions->insert([
            "uid"                   => $uid->create(),
            "amount"                => $amount,
            "status"                => 1,
            "method_id"             => 5,
            "subscribe_external_id" => $subscribe_external_id,
            "external_uid"          => $invoice_id
        ]);

        return $this->respond(["status" => "ok"], 200);
    }

    /**
     * Create planned payment
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function payment(): ResponseInterface
    {
        $methods = new DepositMethodsModel();
        $settings = new Settings();
        $uid = new Uid();
        $subscribes = new SubscribesModel();
        $plans = new PlansModel();

        $method = $methods
            ->where("id", 5)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 400);
        }

        $intents = new PaymentIntentModel();

        $payments = $intents
            ->where("is_pending", 1)
            ->where("planned_at <", time())
            ->findAll();

        foreach ($payments as $payment) {
            $subscribe = $subscribes
                ->where("id", $payment["subscribe_id"])
                ->where("method_id", 5)
                ->first();

            $options = [
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
            ];

            $client = new CURLRequest(
                new App(),
                new URI(),
                new Response(new App()),
                $options
            );

            $res = $client
                ->setJSON([
                    "amount"              => [
                        "value"    => $subscribe["price"],
                        "currency" => $settings->get_config("currency_code")
                    ],
                    "capture"             => true,
                    "description"         => lang("Fields.field_157")." ".$settings->get_config("site_name"),
                    "payment_method_id"   => $payment["payment_token"],
                    "metadata"            => [
                        "customer_id"  => $subscribe["user_id"],
                        "plan_id"      => $subscribe["plan_id"],
                        "app_id"       => $subscribe["app_id"],
                        "subscribe_id" => $subscribe["subscribe_external_id"]
                    ],
                ])
                ->post("payments");

            if ($res->getStatusCode() == 200) {
                $plan = $plans
                    ->where("id", $subscribe["plan_id"])
                    ->first();
                $currentDate = date('Y-m-d');
                $newDate = date('Y-m-d', strtotime('+'.$plan['count'].' month', strtotime($currentDate)));

                // update intent
                $intents->update($payment["id"], [
                    "planned_at" => strtotime($newDate)
                ]);
            } else {
                // cancel subscribe
                $subscribes->update($subscribe["id"], [
                    "is_active" => 0
                ]);
                // remove intent
                $intents->update($payment["id"], [
                    "is_pending" => 0
                ]);
            }
        }

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Check ip address
     * @param string $ip
     * @return bool
     */
    private function checkIPRange(string $ip): bool
    {
        $ranges = [
            ['185.71.76.0', '185.71.76.31'],
            ['185.71.77.0', '185.71.77.31'],
            ['77.75.153.0', '77.75.153.127'],
            ['77.75.156.11', '77.75.156.11'],
            ['77.75.156.35', '77.75.156.35'],
            ['77.75.154.128', '77.75.154.255'],
            ['2a02:5180::', '2a02:5180:ffff:ffff:ffff:ffff:ffff:ffff']
        ];

        $ipLong = ip2long($ip);

        foreach ($ranges as $range) {
            $start = ip2long($range[0]);
            $end = ip2long($range[1]);
            if ($ipLong >= $start && $ipLong <= $end) {
                return true;
            }
        }

        return false;
    }
}