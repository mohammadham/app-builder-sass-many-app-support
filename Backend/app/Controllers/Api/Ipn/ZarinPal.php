<?php

namespace App\Controllers\Api\Ipn;

use App\Controllers\BaseController;
use App\Libraries\ZarinPal as ZarinPalLib;
use App\Models\TransactionsModel;
use App\Models\SubscribesModel;
use App\Models\UsersModel;
use App\Models\PlansModel;

/**
 * ZarinPal IPN (Instant Payment Notification) Handler
 */
class ZarinPal extends BaseController
{
    /**
     * Handle ZarinPal callback
     * GET /public/ipn/zarinpal
     */
    public function index()
    {
        $authority = $this->request->getGet('Authority');
        $status = $this->request->getGet('Status');

        // Check if payment was successful from user perspective
        if ($status !== 'OK' || empty($authority)) {
            return $this->handleFailedPayment($authority, 'Payment cancelled or failed by user');
        }

        // Find transaction by authority
        $transactionModel = new TransactionsModel();
        $transaction = $transactionModel->where('order_number', $authority)->first();

        if (!$transaction) {
            return $this->handleFailedPayment($authority, 'Transaction not found');
        }

        // Check if already verified
        if ($transaction['status'] == 1) {
            return $this->showMessage('success', 'پرداخت قبلاً تایید شده است', 'Payment already verified');
        }

        // Get ZarinPal settings
        $settings = $this->getZarinPalSettings();
        
        if (!$settings['enabled']) {
            return $this->handleFailedPayment($authority, 'ZarinPal is disabled');
        }

        // Initialize ZarinPal
        $zarinpal = new ZarinPalLib(
            $settings['merchant_id'],
            $settings['sandbox'],
            base_url('public/ipn/zarinpal')
        );

        // Verify payment
        $verifyResult = $zarinpal->verify($authority, (int)$transaction['amount']);

        if (!$verifyResult['status']) {
            // Update transaction as failed
            $transactionModel->update($transaction['id'], [
                'status' => 2,
                'gateway_response' => json_encode($verifyResult)
            ]);

            return $this->handleFailedPayment(
                $authority, 
                $verifyResult['message'],
                $verifyResult['code']
            );
        }

        // Payment verified successfully
        $transactionModel->update($transaction['id'], [
            'status' => 1,
            'transaction_id' => $verifyResult['ref_id'],
            'gateway_response' => json_encode($verifyResult),
            'paid_at' => time()
        ]);

        // Activate subscription
        $this->activateSubscription($transaction);

        return $this->showMessage(
            'success',
            'پرداخت با موفقیت انجام شد',
            'Payment completed successfully',
            [
                'ref_id' => $verifyResult['ref_id'],
                'card_pan' => $verifyResult['card_pan']
            ]
        );
    }

    /**
     * Get ZarinPal settings from database
     */
    private function getZarinPalSettings(): array
    {
        $settingsModel = new \App\Models\SiteSettingsModel();
        
        $merchantId = $settingsModel->getSetting('zarinpal_merchant_id');
        $enabled = $settingsModel->getSetting('zarinpal_enabled');
        $sandbox = $settingsModel->getSetting('zarinpal_sandbox');

        return [
            'merchant_id' => $merchantId ?? '',
            'enabled' => $enabled == '1' || $enabled === true,
            'sandbox' => $sandbox == '1' || $sandbox === true
        ];
    }

    /**
     * Activate user subscription after successful payment
     */
    private function activateSubscription(array $transaction): void
    {
        $subscribeModel = new SubscribesModel();
        $planModel = new PlansModel();

        // Get plan details
        $plan = $planModel->find($transaction['plan']);
        
        if (!$plan) {
            return;
        }

        // Calculate expiry date
        $expiryDate = strtotime('+' . $plan['days'] . ' days');

        // Create or update subscription
        $existingSubscription = $subscribeModel->where('user', $transaction['user'])->first();

        if ($existingSubscription) {
            // Extend existing subscription
            $currentExpiry = $existingSubscription['until'];
            $newExpiry = $currentExpiry > time() ? strtotime('+' . $plan['days'] . ' days', $currentExpiry) : $expiryDate;

            $subscribeModel->update($existingSubscription['id'], [
                'plan' => $plan['id'],
                'until' => $newExpiry,
                'apps' => $plan['apps'],
                'builds' => $plan['builds'],
                'updated_at' => time()
            ]);
        } else {
            // Create new subscription
            $subscribeModel->insert([
                'user' => $transaction['user'],
                'plan' => $plan['id'],
                'until' => $expiryDate,
                'apps' => $plan['apps'],
                'builds' => $plan['builds'],
                'created_at' => time(),
                'updated_at' => time()
            ]);
        }
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(string $authority, string $message, int $code = 0)
    {
        $errorMessage = $code > 0 ? ZarinPalLib::getErrorMessage($code) : $message;
        
        return $this->showMessage(
            'error',
            $errorMessage,
            $message,
            ['authority' => $authority, 'code' => $code]
        );
    }

    /**
     * Show user-friendly message
     */
    private function showMessage(string $type, string $messageFa, string $messageEn, array $data = [])
    {
        // Get language from session or default to Persian
        $lang = session()->get('app_language') ?? 'fa';
        $message = $lang === 'fa' ? $messageFa : $messageEn;

        // Return JSON for AJAX requests
        if ($this->request->isAJAX()) {
            return $this->respond([
                'status' => $type === 'success' ? 200 : 400,
                'error' => $type !== 'success',
                'messages' => [
                    $type => $message
                ],
                'data' => $data
            ]);
        }

        // Redirect to a payment result page
        $redirect = base_url('payment-result?type=' . $type . '&message=' . urlencode($message));
        return redirect()->to($redirect);
    }
}
