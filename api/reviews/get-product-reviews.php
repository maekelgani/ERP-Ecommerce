<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

$orderId = $_GET['order_id'] ?? '';
$customerId = null;

if (isset($_SESSION['customer_id'])) {
    $customerId = (int) $_SESSION['customer_id'];
}

try {
    $db = DatabaseConnection::getInstance()->getConnection();
    
    if (!empty($orderId) && $customerId) {
        $query = $db->prepare("
            SELECT r.*, od.id_product, p.nama_product
            FROM review r
            JOIN order_detail od ON r.id_product = od.id_product AND r.id_order = od.id_order
            JOIN products p ON r.id_product = p.id_product
            WHERE r.id_order = :order_id AND r.id_customer = :customer_id
        ");
        $query->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
        $reviews = $query->fetchAll(PDO::FETCH_ASSOC);
        
        $reviewedProducts = [];
        foreach ($reviews as $review) {
            $reviewedProducts[$review['id_product']] = $review;
        }
        
        echo json_encode([
            'success' => true,
            'reviews' => $reviews,
            'reviewed_products' => $reviewedProducts
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'reviews' => [],
            'reviewed_products' => []
        ]);
    }
    
} catch (PDOException $e) {
    error_log('Get Product Reviews Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}
