<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

use App\Auth\CustomerAuthMiddleware;

if (!CustomerAuthMiddleware::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$customerId = CustomerAuthMiddleware::getCustomerId();
$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    
    switch ($method) {
        case 'GET':
            $stmt = $db->prepare("SELECT * FROM customer_preferences WHERE id_customer = :id");
            $stmt->execute([':id' => $customerId]);
            $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$preferences) {
                $stmt = $db->prepare("
                    INSERT INTO customer_preferences 
                    (id_customer, notif_email, notif_order_update, notif_promo, notif_wishlist, language, currency, dark_mode)
                    VALUES (:id, true, true, false, true, 'id', 'IDR', false)
                    ON CONFLICT (id_customer) DO NOTHING
                    RETURNING *
                ");
                $stmt->execute([':id' => $customerId]);
                $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$preferences) {
                    $stmt = $db->prepare("SELECT * FROM customer_preferences WHERE id_customer = :id");
                    $stmt->execute([':id' => $customerId]);
                    $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            
            echo json_encode([
                'success' => true, 
                'data' => [
                    'notif_email' => (bool)$preferences['notif_email'],
                    'notif_order_update' => (bool)$preferences['notif_order_update'],
                    'notif_promo' => (bool)$preferences['notif_promo'],
                    'notif_wishlist' => (bool)$preferences['notif_wishlist'],
                    'language' => $preferences['language'] ?? 'id',
                    'currency' => $preferences['currency'] ?? 'IDR',
                    'dark_mode' => (bool)$preferences['dark_mode']
                ]
            ]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $action = $_GET['action'] ?? 'update';
            
            if ($action === 'update') {
                $stmtCheck = $db->prepare("SELECT id_preference FROM customer_preferences WHERE id_customer = :id");
                $stmtCheck->execute([':id' => $customerId]);
                
                if (!$stmtCheck->fetch()) {
                    $stmt = $db->prepare("
                        INSERT INTO customer_preferences 
                        (id_customer, notif_email, notif_order_update, notif_promo, notif_wishlist, language, currency, dark_mode)
                        VALUES (:id, true, true, false, true, 'id', 'IDR', false)
                        ON CONFLICT (id_customer) DO NOTHING
                    ");
                    $stmt->execute([':id' => $customerId]);
                }
                
                $updateFields = [];
                $params = [':id' => $customerId];
                
                $allowedFields = ['notif_email', 'notif_order_update', 'notif_promo', 'notif_wishlist', 'language', 'currency', 'dark_mode'];
                
                foreach ($allowedFields as $field) {
                    if (isset($data[$field])) {
                        if (in_array($field, ['notif_email', 'notif_order_update', 'notif_promo', 'notif_wishlist', 'dark_mode'])) {
                            $updateFields[] = "$field = :$field";
                            $params[":$field"] = $data[$field] ? true : false;
                        } else {
                            $updateFields[] = "$field = :$field";
                            $params[":$field"] = $data[$field];
                        }
                    }
                }
                
                if (empty($updateFields)) {
                    echo json_encode(['success' => true, 'message' => 'Tidak ada perubahan']);
                    break;
                }
                
                $sql = "UPDATE customer_preferences SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id_customer = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                
                echo json_encode(['success' => true, 'message' => 'Preferensi berhasil diperbarui']);
                
            } else {
                throw new Exception('Action tidak valid');
            }
            break;
            
        default:
            throw new Exception('Method tidak didukung');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
