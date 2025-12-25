<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$shippingMethod = $input['shipping_method'] ?? 'delivery';

$_SESSION['checkout']['pengiriman'] = [
    'method' => $shippingMethod
];

if ($shippingMethod === 'pickup') {
    $storeId = $input['store_id'] ?? '';
    
    if (empty($storeId)) {
        echo json_encode(['success' => false, 'message' => 'Pilih lokasi toko untuk pickup']);
        exit;
    }
    
    try {
        $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM store_locations WHERE id_toko = :id_toko AND is_active = 1");
        $stmt->execute([':id_toko' => $storeId]);
        $store = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$store) {
            echo json_encode(['success' => false, 'message' => 'Lokasi toko tidak ditemukan']);
            exit;
        }
        
        $_SESSION['checkout']['pengiriman']['store'] = [
            'id_toko' => $store['id_toko'],
            'nama_toko' => $store['nama_toko'],
            'alamat' => $store['alamat'],
            'kota' => $store['kota_kabupaten'],
            'jam_buka' => $store['jam_buka'],
            'jam_tutup' => $store['jam_tutup']
        ];
        $_SESSION['checkout']['pengiriman']['ongkir'] = 0;
        $_SESSION['checkout']['pengiriman']['estimasi'] = 'Ambil di toko';
        
    } catch (Exception $e) {
        error_log('save-pengiriman pickup error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
        exit;
    }
    
} else {
    $courierCode = $input['courier_code'] ?? '';
    $courierService = $input['courier_service'] ?? '';
    $courierName = $input['courier_name'] ?? '';
    $rateId = $input['rate_id'] ?? '';
    $ongkir = (int)($input['ongkir'] ?? 0);
    $estimasi = $input['estimasi'] ?? '';
    
    if (empty($courierCode) || $ongkir <= 0) {
        echo json_encode(['success' => false, 'message' => 'Pilih jasa pengiriman']);
        exit;
    }
    
    $_SESSION['checkout']['pengiriman']['courier'] = [
        'code' => $courierCode,
        'service' => $courierService,
        'name' => $courierName,
        'rate_id' => $rateId
    ];
    $_SESSION['checkout']['pengiriman']['ongkir'] = $ongkir;
    $_SESSION['checkout']['pengiriman']['estimasi'] = $estimasi;
}

$bubbleWrap = filter_var($input['bubble_wrap'] ?? false, FILTER_VALIDATE_BOOLEAN);
$packingKayu = filter_var($input['packing_kayu'] ?? false, FILTER_VALIDATE_BOOLEAN);

$packingCost = 0;
if ($bubbleWrap) $packingCost += 5000;
if ($packingKayu) $packingCost += 20000;

$_SESSION['checkout']['pengiriman']['bubble_wrap'] = $bubbleWrap;
$_SESSION['checkout']['pengiriman']['packing_kayu'] = $packingKayu;
$_SESSION['checkout']['pengiriman']['biaya_packing'] = $packingCost;

echo json_encode([
    'success' => true,
    'message' => 'Data pengiriman berhasil disimpan',
    'pengiriman' => $_SESSION['checkout']['pengiriman']
]);
