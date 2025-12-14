<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;
use PDO;

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!CustomerAuthMiddleware::isLoggedIn()) {
        $response['require_login'] = true;
        throw new Exception('Silakan login terlebih dahulu');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    if (!$customerId) {
        throw new Exception('Session tidak valid');
    }

    if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi batas server)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi batas form)',
            UPLOAD_ERR_PARTIAL => 'File hanya terunggah sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diunggah',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak tersedia',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menyimpan file',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP',
        ];
        $error = $_FILES['profile_photo']['error'] ?? UPLOAD_ERR_NO_FILE;
        throw new Exception($errorMessages[$error] ?? 'Terjadi kesalahan saat mengunggah file');
    }

    $file = $_FILES['profile_photo'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Format file tidak valid. Gunakan JPG, PNG, atau GIF.');
    }

    if ($file['size'] > $maxSize) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 2MB.');
    }

    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new Exception('File bukan gambar yang valid');
    }

    if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
        throw new Exception('Resolusi gambar terlalu kecil. Minimal 100x100 piksel.');
    }

    $uploadDir = __DIR__ . '/../../uploads/customers/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT profile_image FROM customers WHERE id_customer = :id");
    $stmt->execute([':id' => $customerId]);
    $currentData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($currentData && !empty($currentData['profile_image'])) {
        $oldFile = $uploadDir . $currentData['profile_image'];
        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
    }

    $extension = match($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        default => 'jpg'
    };
    
    $filename = 'customer_' . $customerId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destination = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Gagal menyimpan file');
    }

    $stmt = $db->prepare("UPDATE customers SET profile_image = :photo, updated_at = NOW() WHERE id_customer = :id");
    $result = $stmt->execute([
        ':photo' => $filename,
        ':id' => $customerId
    ]);

    if (!$result) {
        unlink($destination);
        throw new Exception('Gagal menyimpan ke database');
    }

    $response['success'] = true;
    $response['message'] = 'Foto profil berhasil diperbarui';
    $response['photo_url'] = '../../uploads/customers/' . $filename;
    $response['filename'] = $filename;

} catch (Exception $e) {
    error_log('Upload photo error: ' . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
