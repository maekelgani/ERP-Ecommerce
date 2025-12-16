<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Auth\SessionManager;
use App\Repository\BlogPostRepository;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

    $id = $input['id_post'] ?? $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID artikel diperlukan']);
        exit;
    }

    $removeThumbnail = isset($input['remove_thumbnail']) && ($input['remove_thumbnail'] === true || $input['remove_thumbnail'] === 'true' || $input['remove_thumbnail'] === 1);

    $data = [
        'id_category' => $input['id_category'] ?? null,
        'judul' => $input['judul'] ?? '',
        'excerpt' => $input['excerpt'] ?? '',
        'konten' => $input['konten'] ?? '',
        'thumbnail' => $removeThumbnail ? null : ($input['thumbnail'] ?? null),
        'status' => $input['status'] ?? 'draft'
    ];

    $result = $postRepo->update((int)$id, $data);

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
