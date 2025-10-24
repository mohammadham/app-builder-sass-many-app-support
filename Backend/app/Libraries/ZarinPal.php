<?php

namespace App\Libraries;

/**
 * ZarinPal Payment Gateway Library
 * Supports both IRR (Rial) and IRT (Toman)
 */
class ZarinPal
{
    private $merchantId;
    private $sandbox;
    private $callbackUrl;
    private $apiUrl;
    private $paymentUrl;

    /**
     * Constructor
     * 
     * @param string $merchantId Merchant ID from ZarinPal
     * @param bool $sandbox Use sandbox mode
     * @param string $callbackUrl Callback URL for payment verification
     */
    public function __construct(string $merchantId, bool $sandbox = false, string $callbackUrl = '')
    {
        $this->merchantId = $merchantId;
        $this->sandbox = $sandbox;
        $this->callbackUrl = $callbackUrl;

        if ($sandbox) {
            $this->apiUrl = 'https://sandbox.zarinpal.com/pg/v4/payment/';
            $this->paymentUrl = 'https://sandbox.zarinpal.com/pg/StartPay/';
        } else {
            $this->apiUrl = 'https://payment.zarinpal.com/pg/v4/payment/';
            $this->paymentUrl = 'https://www.zarinpal.com/pg/StartPay/';
        }
    }

    /**
     * Create payment request
     * 
     * @param int $amount Amount in Rials (IRR) - will be converted to Tomans automatically
     * @param string $description Payment description
     * @param string $email User email (optional)
     * @param string $mobile User mobile (optional)
     * @param array $metadata Additional metadata (optional)
     * @return array ['status' => bool, 'authority' => string|null, 'message' => string, 'payment_url' => string|null]
     */
    public function request(int $amount, string $description, string $email = '', string $mobile = '', array $metadata = []): array
    {
        // Convert Rials to Tomans (ZarinPal uses Tomans)
        $amountInTomans = $amount / 10;

        $data = [
            'merchant_id' => $this->merchantId,
            'amount' => $amountInTomans,
            'description' => $description,
            'callback_url' => $this->callbackUrl,
            'metadata' => $metadata
        ];

        if (!empty($email)) {
            $data['metadata']['email'] = $email;
        }

        if (!empty($mobile)) {
            $data['metadata']['mobile'] = $mobile;
        }

        $response = $this->callApi('request.json', $data);

        if ($response['status']) {
            $authority = $response['data']['authority'] ?? null;
            
            return [
                'status' => true,
                'authority' => $authority,
                'message' => 'Payment request created successfully',
                'payment_url' => $authority ? $this->paymentUrl . $authority : null,
                'code' => $response['data']['code'] ?? 100
            ];
        }

        return [
            'status' => false,
            'authority' => null,
            'message' => $response['message'] ?? 'Payment request failed',
            'payment_url' => null,
            'code' => $response['code'] ?? 0
        ];
    }

    /**
     * Verify payment
     * 
     * @param string $authority Authority code from callback
     * @param int $amount Amount in Rials (must match request amount)
     * @return array ['status' => bool, 'ref_id' => string|null, 'card_pan' => string|null, 'message' => string]
     */
    public function verify(string $authority, int $amount): array
    {
        // Convert Rials to Tomans
        $amountInTomans = $amount / 10;

        $data = [
            'merchant_id' => $this->merchantId,
            'authority' => $authority,
            'amount' => $amountInTomans
        ];

        $response = $this->callApi('verify.json', $data);

        if ($response['status']) {
            return [
                'status' => true,
                'ref_id' => $response['data']['ref_id'] ?? null,
                'card_pan' => $response['data']['card_pan'] ?? null,
                'card_hash' => $response['data']['card_hash'] ?? null,
                'fee_type' => $response['data']['fee_type'] ?? null,
                'fee' => $response['data']['fee'] ?? null,
                'message' => 'Payment verified successfully',
                'code' => $response['data']['code'] ?? 100
            ];
        }

        return [
            'status' => false,
            'ref_id' => null,
            'card_pan' => null,
            'message' => $response['message'] ?? 'Payment verification failed',
            'code' => $response['code'] ?? 0
        ];
    }

    /**
     * Unverified transactions (for report)
     * 
     * @return array
     */
    public function unverified(): array
    {
        $data = [
            'merchant_id' => $this->merchantId
        ];

        return $this->callApi('unVerified.json', $data);
    }

    /**
     * Call ZarinPal API
     * 
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array
     */
    private function callApi(string $endpoint, array $data): array
    {
        $url = $this->apiUrl . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'status' => false,
                'message' => 'cURL Error: ' . $curlError,
                'code' => 0
            ];
        }

        $result = json_decode($response, true);

        if (!$result) {
            return [
                'status' => false,
                'message' => 'Invalid JSON response',
                'code' => 0
            ];
        }

        // ZarinPal returns code 100 for success
        if (isset($result['data']['code']) && $result['data']['code'] == 100) {
            return [
                'status' => true,
                'data' => $result['data'],
                'message' => $result['data']['message'] ?? 'Success',
                'code' => 100
            ];
        }

        return [
            'status' => false,
            'message' => $result['errors']['message'] ?? ($result['data']['message'] ?? 'Unknown error'),
            'code' => $result['errors']['code'] ?? ($result['data']['code'] ?? 0)
        ];
    }

    /**
     * Get error message by code
     * 
     * @param int $code Error code
     * @return string
     */
    public static function getErrorMessage(int $code): string
    {
        $errors = [
            -1 => 'اطلاعات ارسال شده ناقص است',
            -2 => 'IP و یا مرچنت کد پذیرنده صحیح نیست',
            -3 => 'با توجه به محدودیت‌های شاپرک امکان پرداخت با رقم درخواست شده میسر نمی‌باشد',
            -4 => 'سطح تایید پذیرنده پایین‌تر از سطح نقره‌ای است',
            -11 => 'درخواست مورد نظر یافت نشد',
            -12 => 'امکان ویرایش درخواست میسر نمی‌باشد',
            -21 => 'هیچ نوع عملیات مالی برای این تراکنش یافت نشد',
            -22 => 'تراکنش ناموفق می‌باشد',
            -33 => 'رقم تراکنش با رقم پرداخت شده مطابقت ندارد',
            -34 => 'سقف تقسیم تراکنش از لحاظ تعداد یا رقم عبور کرده است',
            -40 => 'اجازه دسترسی به متد مربوطه وجود ندارد',
            -41 => 'اطلاعات ارسال شده مربوط به AdditionalData غیرمعتبر است',
            -42 => 'مدت زمان معتبر طول عمر شناسه پرداخت باید بین 30 دقیقه تا 45 روز باشد',
            -54 => 'درخواست مورد نظر آرشیو شده است',
            100 => 'عملیات موفق',
            101 => 'عملیات پرداخت موفق بوده و قبلا وریفای شده است'
        ];

        return $errors[$code] ?? 'خطای نامشخص';
    }
}
