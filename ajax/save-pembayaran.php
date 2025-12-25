<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$paymentMethod = $input['payment_method'] ?? '';
$paymentType = $input['payment_type'] ?? '';

if (empty($paymentMethod)) {
    echo json_encode(['success' => false, 'message' => 'Pilih metode pembayaran']);
    exit;
}

$customerId = \App\Auth\CustomerAuthMiddleware::getCustomerId();

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT nama_customer, email, nomor_hp FROM customers WHERE id_customer = :id");
    $stmt->execute([':id' => $customerId]);
    $customer = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if (!$customer) {
        echo json_encode(['success' => false, 'message' => 'Data customer tidak ditemukan']);
        exit;
    }
    
    $checkoutData = $_SESSION['checkout_data'] ?? null;
    $pengiriman = $_SESSION['checkout']['pengiriman'] ?? null;
    $alamat = $_SESSION['checkout']['alamat'] ?? null;
    
    if (!$checkoutData || !$pengiriman) {
        echo json_encode(['success' => false, 'message' => 'Data checkout tidak lengkap']);
        exit;
    }
    
    $subtotal = $checkoutData['subtotal'] ?? 0;
    $tax = $checkoutData['tax_amount'] ?? 0;
    $ongkir = $pengiriman['ongkir'] ?? 0;
    $packingCost = $pengiriman['biaya_packing'] ?? 0;
    
    $voucherDiscount = 0;
    if (isset($_SESSION['checkout']['voucher'])) {
        $voucherDiscount = $_SESSION['checkout']['voucher']['discount'] ?? 0;
    }
    
    $grossAmount = $subtotal + $tax + $ongkir + $packingCost - $voucherDiscount;
    
    if ($paymentMethod === 'cod') {
        $_SESSION['checkout']['pembayaran'] = [
            'method' => 'cod',
            'type' => 'cod',
            'name' => 'Bayar di Tempat (COD)',
            'gross_amount' => $grossAmount
        ];
        
        echo json_encode([
            'success' => true,
            'payment_type' => 'cod',
            'message' => 'Metode COD dipilih. Lanjutkan untuk membuat pesanan.'
        ]);
        exit;
    }
    
    $midtransService = new \App\Services\MidtransService();
    
    $orderId = 'ORD' . date('Ymd') . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
    
    $customerDetails = [
        'first_name' => $customer['nama_customer'],
        'email' => $customer['email'],
        'phone' => $customer['nomor_hp'] ?? ''
    ];
    
    if ($alamat) {
        $customerDetails['shipping_address'] = [
            'first_name' => $alamat['nama_penerima'],
            'phone' => $alamat['nomor_hp'],
            'address' => $alamat['alamat_lengkap'],
            'city' => $alamat['kota'],
            'postal_code' => $alamat['kode_pos'],
            'country_code' => 'IDN'
        ];
    }
    
    $midtransPaymentType = $paymentMethod;
    if (in_array($paymentMethod, ['bca', 'bni', 'bri', 'mandiri', 'permata'])) {
        $midtransPaymentType = $paymentMethod;
    }
    
    $additionalParams = [];
    if (isset($input['token_id'])) {
        $additionalParams['token_id'] = $input['token_id'];
    }
    
    $result = $midtransService->charge($midtransPaymentType, $orderId, $grossAmount, $customerDetails, $additionalParams);
    
    if (!$result['success']) {
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Pembayaran gagal'
        ]);
        exit;
    }
    
    $_SESSION['checkout']['pembayaran'] = [
        'method' => $paymentMethod,
        'type' => $paymentType,
        'midtrans_order_id' => $orderId,
        'transaction_id' => $result['transaction_id'],
        'gross_amount' => $grossAmount,
        'transaction_status' => $result['transaction_status'],
        'expiry_time' => $result['expiry_time'],
        'va_number' => $result['va_number'] ?? null,
        'bank' => $result['bank'] ?? null,
        'qr_code_url' => $result['qr_code_url'] ?? null,
        'deeplink_url' => $result['deeplink_url'] ?? null,
        'actions' => $result['actions'] ?? null
    ];
    
    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'transaction_id' => $result['transaction_id'],
        'payment_type' => $result['payment_type'],
        'transaction_status' => $result['transaction_status'],
        'gross_amount' => $grossAmount,
        'va_number' => $result['va_number'] ?? null,
        'bank' => $result['bank'] ?? null,
        'qr_code_url' => $result['qr_code_url'] ?? null,
        'deeplink_url' => $result['deeplink_url'] ?? null,
        'expiry_time' => $result['expiry_time'] ?? null,
        'actions' => $result['actions'] ?? null
    ]);
    
} catch (Exception $e) {
    error_log('save-pembayaran error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage()
    ]);
}
