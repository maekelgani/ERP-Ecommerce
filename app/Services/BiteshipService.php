<?php

namespace App\Services;

require_once __DIR__ . '/../../config/api.php';

class BiteshipService
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = BITESHIP_BASE_URL;
        $this->apiKey = BITESHIP_API_KEY;
    }

    private function makeRequest($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
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
            throw new \Exception('Biteship API Error: ' . $error);
        }

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMessage = $result['error'] ?? 'Unknown error';
            throw new \Exception('Biteship API Error: ' . $errorMessage);
        }

        return $result;
    }

    public function getShippingRates($originPostalCode, $destinationPostalCode, $items)
    {
        $data = [
            'origin_postal_code' => (int)$originPostalCode,
            'destination_postal_code' => (int)$destinationPostalCode,
            'couriers' => 'jne,jnt,sicepat,anteraja,ninja,pos,tiki,lion,sap',
            'items' => $items
        ];

        try {
            $response = $this->makeRequest('/rates/couriers', 'POST', $data);
            return $this->formatRatesResponse($response);
        } catch (\Exception $e) {
            error_log('Biteship getShippingRates error: ' . $e->getMessage());
            return $this->getFallbackRates();
        }
    }

    public function getShippingRatesByAreaId($originAreaId, $destinationAreaId, $items)
    {
        $data = [
            'origin_area_id' => $originAreaId,
            'destination_area_id' => $destinationAreaId,
            'couriers' => 'jne,jnt,sicepat,anteraja,ninja,pos,tiki,lion,sap',
            'items' => $items
        ];

        try {
            $response = $this->makeRequest('/rates/couriers', 'POST', $data);
            return $this->formatRatesResponse($response);
        } catch (\Exception $e) {
            error_log('Biteship getShippingRatesByAreaId error: ' . $e->getMessage());
            return $this->getFallbackRates();
        }
    }

    private function formatRatesResponse($response)
    {
        $rates = [];

        if (!isset($response['pricing']) || !is_array($response['pricing'])) {
            return $this->getFallbackRates();
        }

        foreach ($response['pricing'] as $courier) {
            $courierCode = $courier['courier_code'] ?? '';
            $courierName = $courier['courier_name'] ?? '';

            foreach (($courier['rates'] ?? []) as $rate) {
                $rates[] = [
                    'rate_id' => $rate['rate_id'] ?? '',
                    'courier_code' => $courierCode,
                    'courier_name' => $courierName,
                    'courier_service' => $rate['courier_service_name'] ?? '',
                    'courier_service_code' => $rate['courier_service_code'] ?? '',
                    'description' => $rate['description'] ?? '',
                    'duration' => $rate['duration'] ?? '',
                    'duration_days' => $this->parseDuration($rate['duration'] ?? ''),
                    'price' => (int)($rate['price'] ?? 0),
                    'logo' => $this->getCourierLogo($courierCode)
                ];
            }
        }

        usort($rates, function ($a, $b) {
            return $a['price'] - $b['price'];
        });

        return [
            'success' => true,
            'rates' => $rates,
            'origin' => $response['origin'] ?? null,
            'destination' => $response['destination'] ?? null
        ];
    }

    private function parseDuration($duration)
    {
        if (preg_match('/(\d+)\s*-\s*(\d+)/', $duration, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }
        if (preg_match('/(\d+)/', $duration, $matches)) {
            return $matches[1];
        }
        return '2-3';
    }

    private function getCourierLogo($courierCode)
    {
        $logos = [
            'jne' => 'https://upload.wikimedia.org/wikipedia/commons/9/9e/JNE_logo.svg',
            'jnt' => 'https://upload.wikimedia.org/wikipedia/commons/3/3e/J%26T_Express_logo.svg',
            'sicepat' => 'https://upload.wikimedia.org/wikipedia/commons/4/47/SiCepat_logo.svg',
            'anteraja' => 'https://anteraja.id/assets/images/logo-anteraja.svg',
            'ninja' => 'https://upload.wikimedia.org/wikipedia/commons/8/8d/Ninja_Xpress_logo.svg',
            'pos' => 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Logo_Pos_Indonesia.svg',
            'tiki' => 'https://upload.wikimedia.org/wikipedia/commons/2/2c/TIKI_Logo.svg',
            'lion' => 'https://lionparcel.com/assets/images/logo-lion-parcel.svg',
            'sap' => 'https://sapexpress.co.id/wp-content/uploads/2020/03/logo-sap.png',
            'gosend' => 'https://upload.wikimedia.org/wikipedia/commons/0/0c/GoSend.svg'
        ];

        return $logos[$courierCode] ?? '';
    }

    public function getFallbackRates($totalWeight = 0)
    {
        return [
            'success' => true,
            'is_fallback' => true,
            'total_weight' => $totalWeight,
            'rates' => [
                [
                    'rate_id' => 'fallback-jne-reg',
                    'courier_code' => 'jne',
                    'courier_name' => 'JNE',
                    'courier_service' => 'REG',
                    'courier_service_code' => 'REG',
                    'description' => 'Regular Service',
                    'duration' => '2-3 hari',
                    'duration_days' => '2-3',
                    'price' => 25000,
                    'logo' => 'https://upload.wikimedia.org/wikipedia/commons/9/92/New_Logo_JNE.png'
                ],
                [
                    'rate_id' => 'fallback-jne-yes',
                    'courier_code' => 'jne',
                    'courier_name' => 'JNE',
                    'courier_service' => 'YES',
                    'courier_service_code' => 'YES',
                    'description' => 'Yakin Esok Sampai',
                    'duration' => '1 hari',
                    'duration_days' => '1',
                    'price' => 40000,
                    'logo' => 'https://upload.wikimedia.org/wikipedia/commons/9/92/New_Logo_JNE.png '
                ],
                [
                    'rate_id' => 'fallback-jnt',
                    'courier_code' => 'jnt',
                    'courier_name' => 'J&T Express',
                    'courier_service' => 'Regular',
                    'courier_service_code' => 'EZ',
                    'description' => 'Regular Service',
                    'duration' => '2-4 hari',
                    'duration_days' => '2-4',
                    'price' => 22000,
                    'logo' => 'https://upload.wikimedia.org/wikipedia/commons/0/01/J%26T_Express_logo.svg'
                ],
                [
                    'rate_id' => 'fallback-sicepat',
                    'courier_code' => 'sicepat',
                    'courier_name' => 'SiCepat',
                    'courier_service' => 'REG',
                    'courier_service_code' => 'SIUNT',
                    'description' => 'Regular Service',
                    'duration' => '2-3 hari',
                    'duration_days' => '2-3',
                    'price' => 20000,
                    'logo' => '../../assets/img/payment/sicepat_merah.svg'
                ],
                [
                    'rate_id' => 'cargo-sap',
                    'courier_code' => 'sap_cargo',
                    'courier_name' => 'SAP Express',
                    'courier_service' => 'Cargo',
                    'courier_service_code' => 'SAP-CARGO',
                    'description' => 'Cargo Darat & Udara',
                    'duration' => '3-6 hari',
                    'duration_days' => '3-6',
                    'price' => 140000,
                    'is_cargo' => true,
                    'logo' => '../../assets/img/payment/sap-express-courier.png'
                ],
                [
                    'rate_id' => 'cargo-idexpress',
                    'courier_code' => 'idexpress_cargo',
                    'courier_name' => 'ID Express',
                    'courier_service' => 'Cargo',
                    'courier_service_code' => 'ID-CARGO',
                    'description' => 'Pengiriman Barang Besar',
                    'duration' => '3-5 hari',
                    'duration_days' => '3-5',
                    'price' => 135000,
                    'is_cargo' => true,
                    'logo' => '../../assets/img/payment/id-express.png'
                ],
                [
                    'rate_id' => 'cargo-lion',
                    'courier_code' => 'lion_bigpack',
                    'courier_name' => 'Lion Parcel',
                    'courier_service' => 'Big Pack',
                    'courier_service_code' => 'BIGPACK',
                    'description' => 'Cargo Berat & Volume Besar',
                    'duration' => '4-6 hari',
                    'duration_days' => '4-6',
                    'price' => 145000,
                    'is_cargo' => true,
                    'logo' => '../../assets/img/payment/LogoLionParcel.svg.png'
                ],
                [
                    'rate_id' => 'cargo-paxel',
                    'courier_code' => 'paxel_cargo',
                    'courier_name' => 'Paxel',
                    'courier_service' => 'Large',
                    'courier_service_code' => 'PXL-LARGE',
                    'description' => 'Pengiriman Barang Besar',
                    'duration' => '2-4 hari',
                    'duration_days' => '2-4',
                    'price' => 160000,
                    'is_cargo' => true,
                    'logo' => '../../assets/img/payment/paxel.png'
                ],
                [
                    'rate_id' => 'gojek-instant',
                    'courier_code' => 'gojek',
                    'courier_name' => 'Gojek',
                    'courier_service' => 'Instant',
                    'courier_service_code' => 'GOSEND-INSTANT',
                    'description' => 'On Demand Instant (bike)',
                    'duration' => '1 - 3 jam',
                    'duration_days' => '0',
                    'price' => 25000,
                    'is_instant' => true,
                    'is_same_day' => false,
                    'vehicle' => 'bike',
                    'logo' => '../../assets/img/payment/gosend-gojek.png'
                ],
                [
                    'rate_id' => 'gojek-sameday',
                    'courier_code' => 'gojek',
                    'courier_name' => 'Gojek',
                    'courier_service' => 'Same Day',
                    'courier_service_code' => 'GOSEND-SAMEDAY',
                    'description' => 'On Demand within 8 hours (bike)',
                    'duration' => '6 - 8 jam',
                    'duration_days' => '0',
                    'price' => 20000,
                    'is_instant' => false,
                    'is_same_day' => true,
                    'vehicle' => 'bike',
                    'logo' => '../../assets/img/payment/gosend-gojek.png'
                ],

                // ================= GRAB =================
                [
                    'rate_id' => 'grab-instant',
                    'courier_code' => 'grab',
                    'courier_name' => 'Grab',
                    'courier_service' => 'Instant',
                    'courier_service_code' => 'GRAB-INSTANT',
                    'description' => 'On Demand Instant (bike)',
                    'duration' => '1 - 3 jam',
                    'duration_days' => '0',
                    'price' => 26000,
                    'is_instant' => true,
                    'is_same_day' => false,
                    'vehicle' => 'bike',
                    'logo' => 'https://upload.wikimedia.org/wikipedia/commons/f/f6/Grab_Logo.svg'
                ],
                [
                    'rate_id' => 'grab-sameday',
                    'courier_code' => 'grab',
                    'courier_name' => 'Grab',
                    'courier_service' => 'Same Day',
                    'courier_service_code' => 'GRAB-SAMEDAY',
                    'description' => 'On Demand within 8 hours (bike)',
                    'duration' => '6 - 8 jam',
                    'duration_days' => '0',
                    'price' => 21000,
                    'is_instant' => false,
                    'is_same_day' => true,
                    'vehicle' => 'bike',
                    'logo' => 'https://upload.wikimedia.org/wikipedia/commons/f/f6/Grab_Logo.svg'
                ]
            ]

        ];
    }

    public function searchArea($query)
    {
        try {
            $endpoint = '/maps/areas?countries=ID&input=' . urlencode($query) . '&type=single';
            $response = $this->makeRequest($endpoint);
            return $response['areas'] ?? [];
        } catch (\Exception $e) {
            error_log('Biteship searchArea error: ' . $e->getMessage());
            return [];
        }
    }

    public function createOrder($orderData)
    {
        try {
            $response = $this->makeRequest('/orders', 'POST', $orderData);
            return $response;
        } catch (\Exception $e) {
            error_log('Biteship createOrder error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function trackOrder($biteshipOrderId)
    {
        try {
            $response = $this->makeRequest('/orders/' . $biteshipOrderId);
            return $response;
        } catch (\Exception $e) {
            error_log('Biteship trackOrder error: ' . $e->getMessage());
            throw $e;
        }
    }
}
