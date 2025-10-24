<?php
namespace App\Controllers\Api\Ipn;

use App\Controllers\BaseController;
use App\Libraries\Uid;
use App\Models\AppsModel;
use App\Models\DepositMethodsModel;
use App\Models\PlansModel;
use App\Models\SubscribesModel;
use App\Models\TransactionsModel;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Exception\UnexpectedValueException;
use Stripe\StripeClient;
use Stripe\Webhook;

require_once(APPPATH . 'ThirdParty/Stripe/init.php');

class Stripe extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Stripe webhook handler
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function index(): ResponseInterface
    {
        $methods = new DepositMethodsModel();

        $method = $methods
            ->where("id", 1)
            ->where("status", 1)
            ->first();

        if (!$method) {
            return $this->respond(["message" => lang("Message.message_84")], 400);
        }

        if (empty($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
            return $this->respond(["status" => "fail"], 400);
        }

        new StripeClient($method["api_value_1"]);

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $method["api_value_2"]);
        } catch(UnexpectedValueException $e) {
            // Invalid payload
            return $this->respond(["message" => $e->getMessage()], 400);
        } catch(SignatureVerificationException $e) {
            // Invalid signature
            return $this->respond(["message" => $e->getMessage()], 400);
        }

        $subscribes = new SubscribesModel();
        $uid = new Uid();

        switch ($event->type) {
            case 'checkout.session.completed':
                $plan_id = $event->data["object"]->metadata["plan_id"];
                $user_id = $event->data["object"]->metadata["customer_id"];
                $app_id  = $event->data["object"]->metadata["app_id"];
                $amount_total = $event->data["object"]->amount_total / 100;

                $plans = new PlansModel();

                $plan = $plans
                    ->where("id", $plan_id)
                    ->select("count")
                    ->first();

                $currentDate = date('Y-m-d');
                $newDate = date('Y-m-d', strtotime('+'.$plan['count'].' month', strtotime($currentDate)));

                $subscribes->insert([
                    "subscribe_external_id" => $event->data["object"]->subscription,
                    "customer_external_id"  => $event->data["object"]->customer,
                    "plan_id"               => $plan_id,
                    "user_id"               => $user_id,
                    "expires_at"            => strtotime($newDate),
                    "app_id"                => $app_id,
                    "price"                 => $amount_total,
                    "uid"                   => $uid->create(),
                    "is_active"             => 1,
                    "method_id"             => 1
                ]);

                $projects = new AppsModel();

                $projects->update($app_id, [
                    "status" => 1
                ]);

                break;
            case 'invoice.paid':
                $subscribe_external_id = $event->data["object"]->lines->data[0]->subscription;
                $expires_at = $event->data["object"]->lines->data[0]->period->end;
                $amount_paid  = $event->data["object"]->amount_paid / 100;
                $invoice_id  = $event->data["object"]->id;

                $subscribe = $subscribes
                    ->where("subscribe_external_id", $subscribe_external_id)
                    ->select("id")
                    ->first();

                if ($subscribe) {
                    $subscribes->update($subscribe["id"], [
                        "expires_at" => strtotime($expires_at),
                    ]);
                }

                $transactions = new TransactionsModel();

                $transactions->insert([
                    "uid"                   => $uid->create(),
                    "amount"                => $amount_paid,
                    "status"                => 1,
                    "method_id"             => 1,
                    "subscribe_external_id" => $subscribe_external_id,
                    "external_uid"          => $invoice_id
                ]);

                break;
            case 'invoice.payment_failed':

                break;
            default:
                // Unhandled event type
        }

        return $this->respond(["status" => "ok"], 200);
    }
}