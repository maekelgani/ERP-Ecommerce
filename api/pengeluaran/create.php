<?php 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Database/DatabaseConnection.php';
require_once __DIR__ . '/../../app/Services/pengeluaranServices.php';

use App\Database\DatabaseConnection;
use App\Services\PengeluaranServices;
use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
}

try {
    // 1. Validasi Method harus POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed. Use POST.", 405);
    }

    // 2. Ambil Raw JSON Data (PENTING: Karena JS mengirim JSON, bukan Form Data biasa)
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);

    if (!$input) {
        throw new Exception("Data JSON tidak valid atau kosong.");
    }

    // 3. Validasi Field Wajib (Sesuai name di HTML Form Anda)
    if (empty($input['date']) || empty($input['amount']) || empty($input['category'])) {
        throw new Exception("Mohon lengkapi Tanggal, Kategori, dan Nominal.");
    }

    // 4. Inisialisasi Database
    $db = DatabaseConnection::getInstance()->getConnection();
    $expenseModel = new PengeluaranServices($db);

    // 5. MAPPING DATA
    // Kiri: Kolom Database Baru | Kanan: Name di Input HTML/JS
    $dataToSave = [
        'tgl_pengeluaran' => $input['date'],        // Mapping: date -> tgl_pengeluaran
        'kategori'        => $input['category'],    // Mapping: category -> kategori
        'jumlah'          => $input['amount'],      // Mapping: amount -> jumlah
        'description'     => $input['description'] ?? '' // Opsional
    ];

    // 6. Simpan
    if ($expenseModel->create($dataToSave)) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    } else {
        throw new Exception("Gagal menyimpan ke database.");
    }

} catch (Exception $e) {
    $code = $e->getCode() ?: 500;
    // Pastikan code valid HTTP status (400-599)
    if ($code < 100 || $code > 599) $code = 500;
    
    http_response_code($code);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}
?>