<?php namespace App\Controllers\Api\Customer\Payment\ZarinPal;

use App\Controllers\PrivateController;
use App\Libraries\ZarinPal;
use App\Models\PlansModel;
use App\Models\SiteSettingsModel;
use App\Models\TransactionsModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class CreatePaymentRequest extends PrivateController
{
    /**
     * Create ZarinPal payment request
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        // Get settings
        $settingsModel = new SiteSettingsModel();
        $zarinpalEnabled = $settingsModel->getSetting('zarinpal_enabled');
        $zarinpalSandbox = $settingsModel->getSetting('zarinpal_sandbox');
        $zarinpalMerchantId = $settingsModel->getSetting('zarinpal_merchant_id');

        // Check if ZarinPal is enabled
        if ($zarinpalEnabled !== '1') {
            return $this->respond(['message' => 'ZarinPal is not enabled'], 400);
        }

        if (empty($zarinpalMerchantId)) {
            return $this->respond(['message' => 'ZarinPal Merchant ID is not configured'], 400);
        }

        // Get plan ID from request
        $planId = $this->request->getVar('plan_id');
        if (!$planId) {
            return $this->respond(['message' => 'Plan ID is required'], 400);
        }

        // Get plan details
        $plansModel = new PlansModel();
        $plan = $plansModel->find($planId);

        if (!$plan) {
            return $this->respond(['message' => 'Plan not found'], 404);
        }

        // Get user
        $usersModel = new UsersModel();
        $user = $usersModel->find($this->user['id']);

        if (!$user) {
            return $this->respond(['message' => 'User not found'], 404);
        }

        // Create transaction record
        $transactionsModel = new TransactionsModel();
        $transactionId = $transactionsModel->insert([
            'user_id' => $this->user['id'],
            'plan_id' => $planId,
            'amount' => $plan['price'],
            'currency' => 'IRR',
            'status' => 'pending',
            'provider' => 'zarinpal',
            'created_at' => time()
        ]);

        // Initialize ZarinPal
        $callbackUrl = base_url('public/ipn/zarinpal');
        $zarinpal = new ZarinPal(
            $zarinpalMerchantId,
            $zarinpalSandbox === '1',
            $callbackUrl
        );

        // Request payment
        $amount = $plan['price']; // Amount in Rials
        $description = 'Subscription: ' . $plan['name'];
        $email = $user['email'];
        $mobile = $user['phone'] ?? '';

        $metadata = [
            'transaction_id' => $transactionId,
            'user_id' => $this->user['id'],
            'plan_id' => $planId
        ];

        $result = $zarinpal->request($amount, $description, $email, $mobile, $metadata);

        if ($result['status'] === 'success') {
            // Update transaction with authority
            $transactionsModel->update($transactionId, [
                'transaction_id' => $result['authority'],
                'updated_at' => time()
            ]);

            return $this->respond([
                'url' => $result['url']
            ], 200);
        } else {
            // Update transaction status
            $transactionsModel->update($transactionId, [
                'status' => 'failed',
                'updated_at' => time()
            ]);

            return $this->respond([
                'message' => $result['message']
            ], 400);
        }
    }
}
