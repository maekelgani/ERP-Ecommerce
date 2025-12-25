<?php
namespace App\Services;

require_once __DIR__ . '/../../config/api.php';

class MidtransService
{
    private $baseUrl;
    private $serverKey;
    private $clientKey;
    private $isProduction;
    
    public function __construct()
    {
        $this->serverKey = MIDTRANS_SERVER_KEY;
        $this->clientKey = MIDTRANS_CLIENT_KEY;
        $this->isProduction = MIDTRANS_IS_PRODUCTION;
        $this->baseUrl = $this->isProduction 
            ? 'https://api.midtrans.com' 
            : 'https://api.sandbox.midtrans.com';
    }
    
    private function makeRequest($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Authorization: Basic ' . base64_encode($this->serverKey . ':'),
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception('Midtrans API Error: ' . $error);
        }
        
        $result = json_decode($response, true);
        
        return [
            'http_code' => $httpCode,
            'data' => $result
        ];
    }
    
    public function createBankTransfer($orderId, $grossAmount, $paymentType, $customerDetails)
    {
        $bankCode = $this->getBankCode($paymentType);
        
        $params = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int)$grossAmount
            ],
            'bank_transfer' => [
                'bank' => $bankCode
            ],
            'customer_details' => $customerDetails
        ];
        
        $response = $this->makeRequest('/v2/charge', 'POST', $params);
        return $this->formatChargeResponse($response);
    }
    
    public function createEWallet($orderId, $grossAmount, $paymentType, $customerDetails)
    {
        $params = [
            'payment_type' => $paymentType,
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int)$grossAmount
            ],
            'customer_details' => $customerDetails
        ];
        
        if ($paymentType === 'gopay') {
            $params['gopay'] = [
                'enable_callback' => true,
                'callback_url' => $this->getCallbackUrl()
            ];
        } elseif ($paymentType === 'shopeepay') {
            $params['shopeepay'] = [
                'callback_url' => $this->getCallbackUrl()
            ];
        }
        
        $response = $this->makeRequest('/v2/charge', 'POST', $params);
        return $this->formatChargeResponse($response);
    }
    
    public function createQRIS($orderId, $grossAmount, $customerDetails)
    {
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int)$grossAmount
            ],
            'qris' => [
                'acquirer' => 'gopay'
            ],
            'customer_details' => $customerDetails
        ];
        
        $response = $this->makeRequest('/v2/charge', 'POST', $params);
        return $this->formatChargeResponse($response);
    }
    
    public function createCreditCard($orderId, $grossAmount, $tokenId, $customerDetails)
    {
        $params = [
            'payment_type' => 'credit_card',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int)$grossAmount
            ],
            'credit_card' => [
                'token_id' => $tokenId,
                'authentication' => true
            ],
            'customer_details' => $customerDetails
        ];
        
        $response = $this->makeRequest('/v2/charge', 'POST', $params);
        return $this->formatChargeResponse($response);
    }
    
    public function charge($paymentType, $orderId, $grossAmount, $customerDetails, $additionalParams = [])
    {
        switch ($paymentType) {
            case 'bca':
            case 'bni':
            case 'bri':
            case 'mandiri':
            case 'permata':
                return $this->createBankTransfer($orderId, $grossAmount, $paymentType, $customerDetails);
                
            case 'gopay':
            case 'shopeepay':
                return $this->createEWallet($orderId, $grossAmount, $paymentType, $customerDetails);
                
            case 'qris':
                return $this->createQRIS($orderId, $grossAmount, $customerDetails);
                
            case 'credit_card':
                if (!isset($additionalParams['token_id'])) {
                    throw new \Exception('Token ID required for credit card payment');
                }
                return $this->createCreditCard($orderId, $grossAmount, $additionalParams['token_id'], $customerDetails);
                
            default:
                throw new \Exception('Unsupported payment type: ' . $paymentType);
        }
    }
    
    private function formatChargeResponse($response)
    {
        $httpCode = $response['http_code'];
        $data = $response['data'];
        
        if ($httpCode >= 400 || (isset($data['status_code']) && $data['status_code'] >= 400)) {
            return [
                'success' => false,
                'message' => $data['status_message'] ?? 'Payment failed',
                'data' => $data
            ];
        }
        
        $result = [
            'success' => true,
            'transaction_id' => $data['transaction_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'gross_amount' => $data['gross_amount'] ?? null,
            'payment_type' => $data['payment_type'] ?? null,
            'transaction_status' => $data['transaction_status'] ?? null,
            'fraud_status' => $data['fraud_status'] ?? null,
            'transaction_time' => $data['transaction_time'] ?? null,
            'expiry_time' => $data['expiry_time'] ?? null
        ];
        
        if (isset($data['va_numbers']) && !empty($data['va_numbers'])) {
            $result['va_number'] = $data['va_numbers'][0]['va_number'] ?? null;
            $result['bank'] = $data['va_numbers'][0]['bank'] ?? null;
        }
        
        if (isset($data['actions'])) {
            $result['actions'] = $data['actions'];
            foreach ($data['actions'] as $action) {
                if ($action['name'] === 'generate-qr-code') {
                    $result['qr_code_url'] = $action['url'];
                }
                if ($action['name'] === 'deeplink-redirect') {
                    $result['deeplink_url'] = $action['url'];
                }
            }
        }
        
        if (isset($data['qr_string'])) {
            $result['qr_string'] = $data['qr_string'];
        }
        
        $result['raw_response'] = $data;
        
        return $result;
    }
    
    private function getBankCode($paymentType)
    {
        $bankCodes = [
            'bca' => 'bca',
            'bni' => 'bni',
            'bri' => 'bri',
            'mandiri' => 'mandiri',
            'permata' => 'permata'
        ];
        
        return $bankCodes[$paymentType] ?? 'bca';
    }
    
    private function getCallbackUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host . '/api/payment/callback.php';
    }
    
    public function getTransactionStatus($orderId)
    {
        try {
            $response = $this->makeRequest('/v2/' . $orderId . '/status');
            return $response['data'];
        } catch (\Exception $e) {
            error_log('Midtrans getTransactionStatus error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function cancelTransaction($orderId)
    {
        try {
            $response = $this->makeRequest('/v2/' . $orderId . '/cancel', 'POST');
            return $response['data'];
        } catch (\Exception $e) {
            error_log('Midtrans cancelTransaction error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function getClientKey()
    {
        return $this->clientKey;
    }
    
    public function isProduction()
    {
        return $this->isProduction;
    }
    
    public function verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)
    {
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        return $signatureKey === $expectedSignature;
    }
}
