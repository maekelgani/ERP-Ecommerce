<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Auth\SessionManager;
use App\Repository\BlogPostRepository;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

AuthMiddleware::checkAdminLoginJson();
$admin = SessionManager::getCurrentAdmin();

try {
    $postRepo = new BlogPostRepository();

    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id_post'] ?? $_GET['id'] ?? $_POST['id_post'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID artikel diperlukan']);
        exit;
    }

    $result = $postRepo->delete((int)$id);

    if (!$result['success']) {
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
