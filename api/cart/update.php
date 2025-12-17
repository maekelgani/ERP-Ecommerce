<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;
use App\Helper\DiscountHelper;

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!CustomerAuthMiddleware::isLoggedIn()) {
        $response['require_login'] = true;
        throw new Exception('Silakan login terlebih dahulu');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $cartId = $input['cart_id'] ?? null;
    $quantity = (int)($input['quantity'] ?? 0);

    if (empty($cartId)) {
        throw new Exception('Cart ID diperlukan');
    }

    if ($quantity < 1) {
        throw new Exception('Jumlah minimal adalah 1');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    $discountHelper = new DiscountHelper();

    $stmtCart = $db->prepare("
        SELECT c.id_cart, c.id_product, c.jumlah, p.stok, p.status_produk, p.harga
        FROM cart c 
        JOIN products p ON c.id_product = p.id_product 
        WHERE c.id_cart = :cart_id AND c.id_customer = :customer_id
    ");
    $stmtCart->execute([':cart_id' => $cartId, ':customer_id' => $customerId]);
    $cart = $stmtCart->fetch(PDO::FETCH_ASSOC);

    if (!$cart) {
        throw new Exception('Item keranjang tidak ditemukan');
    }

    if ($cart['stok'] <= 0 || $cart['status_produk'] === 'habis' || $cart['status_produk'] === 'nonaktif') {
        throw new Exception('Produk tidak tersedia');
    }

    $discount = $discountHelper->getActiveDiscountForProduct($cart['id_product']);
    $hargaFinal = $cart['harga'];
    $savingsPerItem = 0;

    if ($discount) {
        $hargaFinal = $discountHelper->calculateDiscountedPrice($cart['harga'], $discount);
        $savingsPerItem = $cart['harga'] - $hargaFinal;
    }

    $adjusted = false;
    if ($quantity > $cart['stok']) {
        $quantity = $cart['stok'];
        $adjusted = true;
    }

    $stmtUpdate = $db->prepare("UPDATE cart SET jumlah = :qty, harga_satuan = :harga, tgl_diubah = NOW() WHERE id_cart = :cart_id");
    $stmtUpdate->execute([':qty' => $quantity, ':harga' => $hargaFinal, ':cart_id' => $cartId]);

    $itemTotal = $hargaFinal * $quantity;
    $itemSavings = $savingsPerItem * $quantity;

    $stmtProducts = $db->prepare("
        SELECT c.id_product, c.jumlah, p.harga
        FROM cart c
        JOIN products p ON c.id_product = p.id_product
        WHERE c.id_customer = :customer_id
            AND p.stok > 0 
            AND p.status_produk NOT IN ('habis', 'nonaktif')
    ");
    $stmtProducts->execute([':customer_id' => $customerId]);
    $cartItems = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

    $productIds = array_column($cartItems, 'id_product');
    $activeDiscounts = $discountHelper->getActiveDiscountsForProducts($productIds);

    $subtotal = 0;
    $subtotalBeforeDiscount = 0;
    $totalSavings = 0;
    $totalItems = 0;

    foreach ($cartItems as $item) {
        $productId = $item['id_product'];
        $qty = (int)$item['jumlah'];
        $originalPrice = (float)$item['harga'];

        $itemDiscount = $activeDiscounts[$productId] ?? null;
        $finalPrice = $originalPrice;

        if ($itemDiscount) {
            $finalPrice = $discountHelper->calculateDiscountedPrice($originalPrice, $itemDiscount);
        }

        $subtotal += $finalPrice * $qty;
        $subtotalBeforeDiscount += $originalPrice * $qty;
        $totalSavings += ($originalPrice - $finalPrice) * $qty;
        $totalItems += $qty;
    }

    $tax = $subtotal * 0.11;
    $grandTotal = $subtotal + $tax;

    $response['success'] = true;
    $response['message'] = $adjusted ? 'Jumlah disesuaikan dengan stok tersedia' : 'Jumlah diperbarui';
    $response['adjusted'] = $adjusted;
    $response['quantity'] = $quantity;
    $response['item_total'] = $itemTotal;
    $response['item_savings'] = $itemSavings;
    $response['price'] = $hargaFinal;
    $response['original_price'] = $cart['harga'];
    $response['savings_per_item'] = $savingsPerItem;
    $response['max_stock'] = $cart['stok'];
    $response['subtotal'] = $subtotal;
    $response['subtotal_before_discount'] = $subtotalBeforeDiscount;
    $response['total_savings'] = $totalSavings;
    $response['tax'] = $tax;
    $response['grand_total'] = $grandTotal;
    $response['total_items'] = $totalItems;
    $response['cart_count'] = count($cartItems);
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
