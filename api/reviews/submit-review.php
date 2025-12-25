<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

\App\Auth\CustomerAuthMiddleware::requireLogin();

$customerId = (int) \App\Auth\CustomerAuthMiddleware::getCustomerId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$orderId = $_POST['order_id'] ?? '';
$productId = $_POST['product_id'] ?? '';
$rating = intval($_POST['rating'] ?? 0);
$komentar = trim($_POST['komentar'] ?? '');
$isEdit = ($_POST['is_edit'] ?? '0') === '1';
$reviewId = $_POST['review_id'] ?? '';

if (empty($orderId) || empty($productId) || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap. Rating harus antara 1-5']);
    exit;
}

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    $orderQuery = $db->prepare("
        SELECT o.id_order, o.status_order, o.id_customer
        FROM orders o
        JOIN order_detail od ON o.id_order = od.id_order
        WHERE o.id_order = :order_id 
          AND o.id_customer = :customer_id 
          AND od.id_product = :product_id
          AND o.status_order = 'selesai'
    ");
    $orderQuery->execute([
        ':order_id' => $orderId,
        ':customer_id' => $customerId,
        ':product_id' => $productId
    ]);
    $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Anda hanya bisa memberikan review untuk produk yang sudah dibeli dan pesanan selesai']);
        exit;
    }

    $uploadDir = __DIR__ . '/../../uploads/reviews/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fotoReview = null;
    if (isset($_FILES['foto_review']) && $_FILES['foto_review']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileType = $_FILES['foto_review']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Format file tidak valid. Gunakan JPG, PNG, atau WebP']);
            exit;
        }

        $maxSize = 5 * 1024 * 1024;
        if ($_FILES['foto_review']['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5MB']);
            exit;
        }

        $extension = pathinfo($_FILES['foto_review']['name'], PATHINFO_EXTENSION);
        $fotoReview = 'review_' . uniqid() . '_' . time() . '.' . $extension;

        if (!move_uploaded_file($_FILES['foto_review']['tmp_name'], $uploadDir . $fotoReview)) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupload foto review']);
            exit;
        }
    }

    if ($isEdit && !empty($reviewId)) {
        $existingQuery = $db->prepare("
            SELECT id_review, foto_review FROM review 
            WHERE id_review = :review_id AND id_customer = :customer_id
        ");
        $existingQuery->execute([
            ':review_id' => $reviewId,
            ':customer_id' => $customerId
        ]);
        $existing = $existingQuery->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            echo json_encode(['success' => false, 'message' => 'Review tidak ditemukan']);
            exit;
        }

        if ($fotoReview && !empty($existing['foto_review'])) {
            $oldPhotoPath = $uploadDir . $existing['foto_review'];
            if (file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath);
            }
        }

        if ($fotoReview) {
            $updateQuery = $db->prepare("
                UPDATE review 
                SET rating = :rating, komentar = :komentar, foto_review = :foto_review, status_review = 'pending'
                WHERE id_review = :review_id AND id_customer = :customer_id
            ");
            $updateQuery->execute([
                ':rating' => $rating,
                ':komentar' => $komentar,
                ':foto_review' => $fotoReview,
                ':review_id' => $reviewId,
                ':customer_id' => $customerId
            ]);
        } else {
            $updateQuery = $db->prepare("
                UPDATE review 
                SET rating = :rating, komentar = :komentar, status_review = 'pending'
                WHERE id_review = :review_id AND id_customer = :customer_id
            ");
            $updateQuery->execute([
                ':rating' => $rating,
                ':komentar' => $komentar,
                ':review_id' => $reviewId,
                ':customer_id' => $customerId
            ]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Review berhasil diupdate dan akan ditampilkan setelah disetujui',
            'review_id' => $reviewId
        ]);
    } else {
        $existingQuery = $db->prepare("
            SELECT id_review FROM review 
            WHERE id_order = :order_id AND id_product = :product_id AND id_customer = :customer_id
        ");
        $existingQuery->execute([
            ':order_id' => $orderId,
            ':product_id' => $productId,
            ':customer_id' => $customerId
        ]);
        $existing = $existingQuery->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah memberikan review untuk produk ini pada pesanan ini']);
            exit;
        }

        $idReview = 'REV' . date('YmdHis') . strtoupper(substr(uniqid(), -6));

        $insertQuery = $db->prepare("
            INSERT INTO review (id_review, id_product, id_customer, id_order, rating, komentar, foto_review, tanggal_review, status_review)
            VALUES (:id_review, :id_product, :id_customer, :id_order, :rating, :komentar, :foto_review, NOW(), 'pending')
        ");

        $insertQuery->execute([
            ':id_review' => $idReview,
            ':id_product' => $productId,
            ':id_customer' => $customerId,
            ':id_order' => $orderId,
            ':rating' => $rating,
            ':komentar' => $komentar,
            ':foto_review' => $fotoReview
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Review berhasil dikirim dan akan ditampilkan setelah disetujui',
            'review_id' => $idReview
        ]);
    }
} catch (PDOException $e) {
    error_log('Submit Review Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
}
