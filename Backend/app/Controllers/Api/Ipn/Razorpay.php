<?php
namespace App\Controllers\Api\Ipn;

use App\Controllers\BaseController;
use App\Libraries\Uid;
use App\Models\AppsModel;
use App\Models\DepositMethodsModel;
use App\Models\SubscribesModel;
use App\Models\TransactionsModel;
use CodeIgniter\HTTP\ResponseInterface;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use ReflectionException;

require_once(APPPATH . 'ThirdParty/Razorpay/Razorpay.php');

class Razorpay extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Razorpay webhook handler
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        $methods = new DepositMethodsModel();

        if (empty($this->request->getHeaderLine("X-Razorpay-Signature"))) {
            return $this->respond(["status" => "fail"], 400);
        }

        $method = $methods
            ->where("id", 6)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 400);
        }

        $api = new Api($method["api_value_1"], $method["api_value_2"]);

        $payload = @file_get_contents('php://input');
        $sig_header = $this->request->getHeaderLine("X-Razorpay-Signature");

        $webhookSecret = $method["api_value_3"];

        try {
            $api->utility->verifyWebhookSignature($payload, $sig_header, $webhookSecret);
        } catch (SignatureVerificationError $e) {
            return $this->respond(["status" => "fail", "message" => $e->getMessage()], 400);
        }

        $subscribes = new SubscribesModel();
        $uid = new Uid();

        $data = json_decode($payload);

        file_put_contents(WRITEPATH."storage/data.json", $payload);

        switch ($data->event) {
            case 'subscription.charged':
                $subscribe_external_id = $data->payload->subscription->entity->id;
                $customer_external_id = $data->payload->subscription->entity->customer_id;
                $expires_at = $data->payload->subscription->entity->charge_at;
                $amount_paid  = $data->payload->payment->entity->amount / 100;
                $invoice_id  = $data->payload->payment->entity->id;
                $app_id  = $data->payload->subscription->entity->notes->app_id;
                $plan_id  = $data->payload->subscription->entity->notes->plan_id;
                $user_id  = $data->payload->subscription->entity->notes->customer_id;

                $subscribe = $subscribes
                    ->where("subscribe_external_id", $subscribe_external_id)
                    ->select("id")
                    ->first();

                if ($subscribe) {
                    // update expires date
                    $subscribes->update($subscribe["id"], [
                        "expires_at" => $expires_at,
                    ]);
                } else {
                    // create new subscribe
                    $subscribes->insert([
                        "subscribe_external_id" => $subscribe_external_id,
                        "customer_external_id"  => !$customer_external_id ? "" : $customer_external_id,
                        "plan_id"               => $plan_id,
                        "user_id"               => $user_id,
                        "expires_at"            => $expires_at,
                        "app_id"                => $app_id,
                        "price"                 => $amount_paid,
                        "uid"                   => $uid->create(),
                        "is_active"             => 1,
                        "method_id"             => 6
                    ]);
                    $projects = new AppsModel();

                    $projects->update($app_id, [
                        "status" => 1
                    ]);
                }

                $transactions = new TransactionsModel();

                $transactions->insert([
                    "uid"                   => $uid->create(),
                    "amount"                => $amount_paid,
                    "status"                => 1,
                    "method_id"             => 6,
                    "subscribe_external_id" => $subscribe_external_id,
                    "external_uid"          => $invoice_id
                ]);

                break;
            case 'subscription.cancelled':
                $subscribe_external_id = $data->payload->subscription->entity->id;

                $subscribe = $subscribes
                    ->where("subscribe_external_id", $subscribe_external_id)
                    ->select("id")
                    ->first();

                if ($subscribe) {
                    $subscribes->update($subscribe["id"], [
                        "is_active" => 0,
                    ]);
                }
                break;
            default:
                // Unhandled event type
        }

        return $this->respond(["status" => "ok"], 200);
    }

}