<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;

$response = ['success' => false, 'message' => '', 'action' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!CustomerAuthMiddleware::isLoggedIn()) {
        $response['require_login'] = true;
        throw new Exception('Silakan login terlebih dahulu');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['product_id'] ?? null;
    $noToggle = $input['no_toggle'] ?? false;

    if (empty($productId)) {
        throw new Exception('Product ID diperlukan');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    $stmtCheck = $db->prepare("
        SELECT id_wishlist 
        FROM wishlist 
        WHERE id_customer = :customer_id AND id_product = :product_id
    ");
    $stmtCheck->execute([
        ':customer_id' => $customerId,
        ':product_id' => $productId
    ]);

    $existingWishlist = $stmtCheck->fetch();

    if ($existingWishlist) {
        if ($noToggle) {
            $response['success'] = true;
            $response['message'] = 'Produk sudah ada di wishlist';
            $response['action'] = 'exists';
        } else {
            $stmtDelete = $db->prepare("
                DELETE FROM wishlist 
                WHERE id_customer = :customer_id AND id_product = :product_id
            ");
            $stmtDelete->execute([
                ':customer_id' => $customerId,
                ':product_id' => $productId
            ]);

            $response['success'] = true;
            $response['message'] = 'Produk dihapus dari wishlist';
            $response['action'] = 'removed';
        }
    } else {
        $stmtProduct = $db->prepare("
            SELECT id_product, status_produk, nama_product 
            FROM products 
            WHERE id_product = :id
        ");
        $stmtProduct->execute([':id' => $productId]);
        $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception('Produk tidak ditemukan');
        }

        $stmtMaxId = $db->query("SELECT id_wishlist FROM wishlist ORDER BY id_wishlist DESC LIMIT 1");
        $lastId = $stmtMaxId->fetch(PDO::FETCH_ASSOC);

        if ($lastId) {
            $num = (int)substr($lastId['id_wishlist'], 3) + 1;
            $newId = 'WSH' . str_pad($num, 7, '0', STR_PAD_LEFT);
        } else {
            $newId = 'WSH' . str_pad(1, 7, '0', STR_PAD_LEFT);
        }

        $stmtInsert = $db->prepare("
            INSERT INTO wishlist (id_wishlist, id_customer, id_product, tanggal_ditambahkan) 
            VALUES (:id_wishlist, :customer_id, :product_id, NOW())
        ");
        $stmtInsert->execute([
            ':id_wishlist' => $newId,
            ':customer_id' => $customerId,
            ':product_id' => $productId
        ]);

        $response['success'] = true;
        $response['message'] = 'Produk ditambahkan ke wishlist';
        $response['action'] = 'added';
        $response['product_name'] = $product['nama_product'];
    }

    $stmtCount = $db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE id_customer = :customer_id");
    $stmtCount->execute([':customer_id' => $customerId]);
    $response['wishlist_count'] = (int)$stmtCount->fetch(PDO::FETCH_ASSOC)['count'];
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
