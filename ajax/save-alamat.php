<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$addressId = $input['address_id'] ?? null;

if (empty($addressId)) {
    echo json_encode(['success' => false, 'message' => 'Alamat tidak dipilih']);
    exit;
}

$customerId = \App\Auth\CustomerAuthMiddleware::getCustomerId();

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    
    $stmt = $db->prepare("
        SELECT * FROM address_book 
        WHERE id_alamat = :id_alamat AND id_customer = :id_customer
    ");
    $stmt->execute([
        ':id_alamat' => $addressId,
        ':id_customer' => $customerId
    ]);
    
    $address = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if (!$address) {
        echo json_encode(['success' => false, 'message' => 'Alamat tidak ditemukan']);
        exit;
    }
    
    $_SESSION['checkout']['alamat'] = [
        'id_alamat' => $address['id_alamat'],
        'label_alamat' => $address['label_alamat'],
        'nama_penerima' => $address['nama_penerima'],
        'nomor_hp' => $address['nomor_hp'],
        'alamat_lengkap' => $address['alamat_lengkap'],
        'kelurahan' => $address['kelurahan'],
        'kecamatan' => $address['kecamatan'],
        'kota' => $address['kota'],
        'provinsi' => $address['provinsi'],
        'kode_pos' => $address['kode_pos']
    ];
    
    echo json_encode([
        'success' => true,
        'message' => 'Alamat berhasil disimpan',
        'address' => $_SESSION['checkout']['alamat']
    ]);
    
} catch (Exception $e) {
    error_log('save-alamat error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan alamat']);
}
