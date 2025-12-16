<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Auth\SessionManager;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

AuthMiddleware::checkAdminLoginJson();
$admin = SessionManager::getCurrentAdmin();

try {
    if (!isset($_FILES['thumbnail']) || $_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File thumbnail tidak ditemukan']);
        exit;
    }

    $file = $_FILES['thumbnail'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024;

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Tipe file tidak diizinkan. Gunakan JPG, PNG, GIF, atau WebP']);
        exit;
    }

    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5MB']);
        exit;
    }

    $uploadDir = __DIR__ . '/../../uploads/blog/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'blog_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload file']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Thumbnail berhasil diupload',
        'filename' => $filename,
        'url' => '../../uploads/blog/' . $filename
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
