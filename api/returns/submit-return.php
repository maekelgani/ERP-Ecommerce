<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

try {
    \App\Auth\CustomerAuthMiddleware::requireLogin();

    $customerId = (int) \App\Auth\CustomerAuthMiddleware::getCustomerId();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $orderId = trim($_POST['order_id'] ?? '');
    $alasan = trim($_POST['alasan_return'] ?? '');
    $deskripsi = trim($_POST['deskripsi_return'] ?? '');

    if (empty($orderId) || empty($alasan)) {
        echo json_encode(['success' => false, 'message' => 'Order ID dan alasan pengembalian harus diisi']);
        exit;
    }

    if (!isset($_FILES['file_invoice']) || $_FILES['file_invoice']['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = 'File invoice wajib disertakan';
        if (isset($_FILES['file_invoice']['error'])) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File terlalu besar',
                UPLOAD_ERR_FORM_SIZE => 'File terlalu besar',
                UPLOAD_ERR_PARTIAL => 'File terupload sebagian',
                UPLOAD_ERR_NO_TMP_DIR => 'Direktori temporary tidak tersedia',
                UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file'
            ];
            $errorMsg = $uploadErrors[$_FILES['file_invoice']['error']] ?? $errorMsg;
        }
        echo json_encode(['success' => false, 'message' => $errorMsg]);
        exit;
    }

    $db = DatabaseConnection::getInstance()->getConnection();

    $orderQuery = $db->prepare("SELECT o.id_order, o.status_order, o.id_customer, o.tanggal_order FROM orders o WHERE o.id_order = :order_id AND o.id_customer = :customer_id");
    $orderQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
    $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }

    if ($order['status_order'] !== 'selesai') {
        echo json_encode(['success' => false, 'message' => 'Pengajuan pengembalian hanya bisa dilakukan untuk pesanan yang sudah selesai']);
        exit;
    }

    $completionDateQuery = $db->prepare("SELECT COALESCE(s.tanggal_diterima, o.tanggal_order) as completion_date FROM orders o LEFT JOIN shipment s ON o.id_order = s.id_order WHERE o.id_order = :order_id");
    $completionDateQuery->execute([':order_id' => $orderId]);
    $completionResult = $completionDateQuery->fetch(PDO::FETCH_ASSOC);

    $completionDate = new DateTime($completionResult['completion_date'] ?? $order['tanggal_order']);
    $now = new DateTime();
    $diff = $now->diff($completionDate);
    $daysDiff = $diff->days;

    if ($daysDiff > 7) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan pengembalian hanya bisa dilakukan dalam 7 hari setelah pesanan selesai']);
        exit;
    }

    $existingQuery = $db->prepare("SELECT id_return FROM return_request WHERE id_order = :order_id AND status_return NOT IN ('ditolak', 'selesai')");
    $existingQuery->execute([':order_id' => $orderId]);
    $existing = $existingQuery->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan pengembalian sudah ada untuk pesanan ini']);
        exit;
    }

    $uploadDir = __DIR__ . '/../../uploads/returns/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedInvoiceTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    $invoiceType = $_FILES['file_invoice']['type'];

    if (!in_array($invoiceType, $allowedInvoiceTypes)) {
        echo json_encode(['success' => false, 'message' => 'Format file invoice tidak valid. Gunakan PDF, JPG, atau PNG']);
        exit;
    }

    $maxSize = 10 * 1024 * 1024;
    if ($_FILES['file_invoice']['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'Ukuran file invoice maksimal 10MB']);
        exit;
    }

    $invoiceExtension = strtolower(pathinfo($_FILES['file_invoice']['name'], PATHINFO_EXTENSION));
    $fileInvoice = 'invoice_' . uniqid() . '_' . time() . '.' . $invoiceExtension;

    if (!move_uploaded_file($_FILES['file_invoice']['tmp_name'], $uploadDir . $fileInvoice)) {
        error_log('Failed to upload invoice for order ' . $orderId);
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload file invoice']);
        exit;
    }

    $fotoBukti = null;
    if (isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] === UPLOAD_ERR_OK) {
        $allowedPhotoTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $photoType = $_FILES['foto_bukti']['type'];

        if (in_array($photoType, $allowedPhotoTypes)) {
            $photoMaxSize = 5 * 1024 * 1024;
            if ($_FILES['foto_bukti']['size'] <= $photoMaxSize) {
                $photoExtension = strtolower(pathinfo($_FILES['foto_bukti']['name'], PATHINFO_EXTENSION));
                $fotoBukti = 'return_' . uniqid() . '_' . time() . '.' . $photoExtension;
                move_uploaded_file($_FILES['foto_bukti']['tmp_name'], $uploadDir . $fotoBukti);
            }
        }
    }

    $idReturn = 'RET' . date('YmdHis') . strtoupper(substr(uniqid(), -6));

    $insertQuery = $db->prepare("INSERT INTO return_request (id_return, id_order, id_customer, alasan_return, deskripsi_return, file_invoice, foto_bukti, status_return, tanggal_pengajuan) VALUES (:id_return, :id_order, :id_customer, :alasan_return, :deskripsi_return, :file_invoice, :foto_bukti, 'pending', NOW())");

    $insertQuery->execute([
        ':id_return' => $idReturn,
        ':id_order' => $orderId,
        ':id_customer' => $customerId,
        ':alasan_return' => $alasan,
        ':deskripsi_return' => $deskripsi,
        ':file_invoice' => $fileInvoice,
        ':foto_bukti' => $fotoBukti
    ]);

    echo json_encode(['success' => true, 'message' => 'Pengajuan pengembalian berhasil dikirim', 'return_id' => $idReturn]);
} catch (PDOException $e) {
    error_log('Submit Return Database Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan coba lagi']);
} catch (Exception $e) {
    error_log('Submit Return Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan yang tidak terduga']);
}
