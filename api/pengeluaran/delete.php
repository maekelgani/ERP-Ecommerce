<?php 

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Database/DatabaseConnection.php';
require_once __DIR__ . '/../../app/Services/pengeluaranServices.php';

use App\Database\DatabaseConnection;
use App\Services\PengeluaranServices;
use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if (!$id) throw new Exception("ID tidak ditemukan");

    $db = DatabaseConnection::getInstance()->getConnection();
    $expenseModel = new PengeluaranServices($db);
    
    if ($expenseModel->delete($id)) {
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception("Gagal menghapus");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}