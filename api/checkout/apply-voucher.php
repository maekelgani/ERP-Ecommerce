<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin();

$customerId = \App\Auth\CustomerAuthMiddleware::getCustomerId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['code'])) {
    echo json_encode(['success' => false, 'message' => 'Kode voucher tidak boleh kosong']);
    exit;
}

$code = strtoupper(trim($input['code']));
$subtotal = floatval($input['subtotal'] ?? 0);

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    $stmt = $db->prepare("
        SELECT * FROM voucher 
        WHERE kode = :code 
        AND status = 'aktif'
        AND (mulai_pada IS NULL OR mulai_pada <= NOW())
        AND (selesai_pada IS NULL OR selesai_pada >= NOW())
        AND (kuota_total IS NULL OR kuota_terpakai < kuota_total)
    ");
    $stmt->execute([':code' => $code]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voucher) {
        echo json_encode(['success' => false, 'message' => 'Voucher tidak valid atau sudah kadaluarsa']);
        exit;
    }

    if ($voucher['minimal_belanja'] && $subtotal < floatval($voucher['minimal_belanja'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Minimal belanja Rp ' . number_format($voucher['minimal_belanja'], 0, ',', '.') . ' untuk menggunakan voucher ini'
        ]);
        exit;
    }

    if ($voucher['kuota_per_pengguna']) {
        $usageStmt = $db->prepare("
            SELECT COUNT(*) as usage_count FROM penggunaan_voucher 
            WHERE id_voucher = :voucher_id AND id_pengguna = :customer_id
        ");
        $usageStmt->execute([
            ':voucher_id' => $voucher['id_voucher'],
            ':customer_id' => $customerId
        ]);
        $usage = $usageStmt->fetch(PDO::FETCH_ASSOC);

        if ($usage['usage_count'] >= $voucher['kuota_per_pengguna']) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah mencapai batas penggunaan voucher ini']);
            exit;
        }
    }

    $discount = 0;
    switch ($voucher['jenis']) {
        case 'diskon_persen':
            $discount = $subtotal * (floatval($voucher['nilai']) / 100);
            if ($voucher['maksimal_diskon'] && $discount > floatval($voucher['maksimal_diskon'])) {
                $discount = floatval($voucher['maksimal_diskon']);
            }
            break;
        case 'diskon_nominal':
            $discount = floatval($voucher['nilai']);
            if ($voucher['maksimal_diskon'] && $discount > floatval($voucher['maksimal_diskon'])) {
                $discount = floatval($voucher['maksimal_diskon']);
            }
            break;
        case 'gratis_ongkir':
            $discount = 0;
            break;
        case 'cashback':
            $discount = 0;
            break;
    }

    if ($discount > $subtotal) {
        $discount = $subtotal;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Voucher berhasil diterapkan',
        'voucher' => [
            'id' => $voucher['id_voucher'],
            'kode' => $voucher['kode'],
            'judul' => $voucher['judul'],
            'jenis' => $voucher['jenis'],
            'nilai' => $voucher['nilai']
        ],
        'discount' => $discount
    ]);
} catch (Exception $e) {
    error_log('Voucher error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memvalidasi voucher']);
}
