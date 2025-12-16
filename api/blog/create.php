<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Auth\SessionManager;
use App\Repository\BlogPostRepository;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

AuthMiddleware::checkAdminLoginJson();
$admin = SessionManager::getCurrentAdmin();

try {
    $postRepo = new BlogPostRepository();

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $data = [
        'id_admin' => $admin['id_admin'],
        'id_category' => $input['id_category'] ?? null,
        'judul' => $input['judul'] ?? '',
        'excerpt' => $input['excerpt'] ?? '',
        'konten' => $input['konten'] ?? '',
        'thumbnail' => $input['thumbnail'] ?? null,
        'status' => $input['status'] ?? 'draft'
    ];

    $result = $postRepo->create($data);

    if ($result['success']) {
        http_response_code(201);
    } else {
        http_response_code(400);
    }

    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
