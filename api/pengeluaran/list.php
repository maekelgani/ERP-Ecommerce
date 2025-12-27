<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Database/DatabaseConnection.php';
require_once __DIR__ . '/../../app/Services/pengeluaranServices.php';

use App\Database\DatabaseConnection;
use App\Services\PengeluaranServices;
use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

try {
    $db = DatabaseConnection::getInstance()->getConnection();
    $expenseModel = new PengeluaranServices($db);

    // Ambil parameter dari URL
    $filter = $_GET['filter'] ?? 'this_month';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) { $page = 1; }
    $limit  = 10; // Set statis 15 sesuai request

    // Panggil method baru
    $result = $expenseModel->getPaginated($filter, $page, $limit);

    // Output JSON struktur baru
    echo json_encode([
        'status' => 'success',
        'data' => $result['data'],
        'pagination' => $result['pagination']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
