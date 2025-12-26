<?php

declare(strict_types=1);
error_reporting(0); // MATIKAN WARNING KE OUTPUT
ini_set('display_errors', '0');

header('Content-Type: application/json');

ob_start(); // TANGKAP SEMUA OUTPUT

require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

try {
    session_start();

    if (empty($_SESSION['admin_id'])) {
        throw new Exception('Unauthorized');
    }

    $adminId = (int) $_SESSION['admin_id'];

    $idReturn = $_POST['return_id'] ?? null;
    $status   = $_POST['status'] ?? null;
    $catatan  = $_POST['catatan_admin'] ?? null;

    if (!$idReturn || !$status) {
        throw new Exception('Data tidak lengkap');
    }

    $allowedStatus = ['pending', 'diproses', 'disetujui', 'ditolak', 'selesai'];
    if (!in_array($status, $allowedStatus, true)) {
        throw new Exception('Status tidak valid');
    }

    $db = DatabaseConnection::getInstance()->getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare("
        UPDATE return_request
        SET 
            status_return = :status,
            catatan_admin = :catatan,
            id_admin = :admin,
            tanggal_diproses = NOW()
        WHERE id_return = :id
    ");

    $stmt->execute([
        ':status'  => $status,
        ':catatan' => $catatan,
        ':admin'  => $adminId,
        ':id'     => $idReturn
    ]);

    ob_clean(); // BERSIHKAN OUTPUT LIAR

    echo json_encode([
        'success' => true,
        'message' => 'Status pengembalian berhasil diperbarui'
    ]);
} catch (Throwable $e) {
    ob_clean();

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
exit;
