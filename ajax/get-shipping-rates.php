<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$postalCode = $input['postal_code'] ?? '';
$areaId = $input['area_id'] ?? '';
$kecamatan = $input['kecamatan'] ?? '';
$kota = $input['kota'] ?? '';
$provinsi = $input['provinsi'] ?? '';

if (empty($postalCode) && empty($areaId)) {
    echo json_encode(['success' => false, 'message' => 'Kode pos atau area ID tujuan diperlukan']);
    exit;
}

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    $biteshipService = new \App\Services\BiteshipService();

    $checkoutData = $_SESSION['checkout_data'] ?? null;
    if (!$checkoutData || empty($checkoutData['items'])) {
        echo json_encode(['success' => false, 'message' => 'Data checkout tidak ditemukan']);
        exit;
    }

    $productIds = array_column($checkoutData['items'], 'id_product');
    if (empty($productIds)) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada produk dalam checkout']);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $db->prepare("
        SELECT id_product, nama_product, berat_gram 
        FROM products 
        WHERE id_product IN ($placeholders)
    ");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $productMap = array_column($products, null, 'id_product');

    $items = [];
    $totalWeight = 0;

    foreach ($checkoutData['items'] as $item) {
        $productId = $item['id_product'];
        $quantity = (int)($item['quantity'] ?? 1);

        $weight = 500;
        $productName = $item['nama_product'] ?? 'Product';

        if (isset($productMap[$productId])) {
            $dbProduct = $productMap[$productId];
            $weight = (int)($dbProduct['berat_gram'] ?? 500);
            if ($weight <= 0) {
                $weight = 500;
            }
            $productName = $dbProduct['nama_product'];
        }

        $items[] = [
            'name' => $productName,
            'quantity' => $quantity,
            'weight' => $weight
        ];

        $totalWeight += $weight * $quantity;
    }

    $_SESSION['checkout_data']['total_weight'] = $totalWeight;

    $destinationAreaId = $areaId;

    if (empty($destinationAreaId) && !empty($kecamatan)) {
        try {
            $searchQuery = trim($kecamatan);
            if (!empty($kota)) {
                $searchQuery .= ', ' . $kota;
            }

            $areas = $biteshipService->searchArea($searchQuery);
            if (!empty($areas) && isset($areas[0]['id'])) {
                $destinationAreaId = $areas[0]['id'];
            }
        } catch (Exception $e) {
            error_log('Area search failed: ' . $e->getMessage());
        }
    }

    $result = null;

    if (!empty($destinationAreaId)) {
        $result = $biteshipService->getShippingRatesByAreaId(
            STORE_ORIGIN_AREA_ID,
            $destinationAreaId,
            $items
        );

        if ($result['success'] && !empty($result['rates']) && !isset($result['is_fallback'])) {
            $result['area_id'] = $destinationAreaId;
            $result['total_weight'] = $totalWeight;
            echo json_encode($result);
            exit;
        }
    }

    if (!empty($postalCode)) {
        $result = $biteshipService->getShippingRates(
            STORE_ORIGIN_POSTAL_CODE,
            $postalCode,
            $items
        );

        if ($result['success'] && !empty($result['rates'])) {
            $result['total_weight'] = $totalWeight;
            if (isset($result['is_fallback']) && $result['is_fallback']) {
                $result['message'] = 'Menggunakan estimasi ongkir standar. Ongkir aktual akan dikonfirmasi saat pemrosesan pesanan.';
            }
            echo json_encode($result);
            exit;
        }
    }

    $fallback = $biteshipService->getFallbackRates($totalWeight);
    $fallback['message'] = 'Menggunakan estimasi ongkir standar.';
    echo json_encode($fallback);
} catch (Exception $e) {
    error_log('get-shipping-rates error: ' . $e->getMessage());

    $totalWeight = $_SESSION['checkout_data']['total_weight'] ?? 0;
    $biteshipService = new \App\Services\BiteshipService();
    $fallback = $biteshipService->getFallbackRates($totalWeight);
    $fallback['message'] = 'Menggunakan estimasi ongkir standar.';

    echo json_encode($fallback);
}
