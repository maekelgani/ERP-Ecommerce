<?php

/**
 * API Endpoint untuk Upload Foto Admin
 * 
 * Request:
 * - POST /view/admin/AdminPhotoUpload.php
 * - File: $_FILES['photo']
 * 
 * Response: JSON
 * {
 *   "success": true/false,
 *   "message": "string",
 *   "photo_path": "string (optional)"
 * }
 */

require_once '../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Auth\SessionManager;
use App\Services\AdminPhotoUploadService;

// Check if admin is logged in
AuthMiddleware::requireAdminLogin();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
    exit;
}

// Get current admin
$admin = SessionManager::getCurrentAdmin();
if (!$admin) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Admin tidak terautentikasi'
    ]);
    exit;
}

// Process upload
try {
    $uploadService = new AdminPhotoUploadService();
    $result = $uploadService->uploadPhoto($admin['id_admin'], $_FILES['photo'] ?? []);

    http_response_code($result['success'] ? 200 : 400);
    echo json_encode($result);
} catch (\Exception $e) {
    error_log('Photo upload error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi error saat memproses upload'
    ]);
}
