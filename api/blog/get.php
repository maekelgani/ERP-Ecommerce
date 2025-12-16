<?php
require_once __DIR__ . '/../../config/config.php';

use App\Repository\BlogPostRepository;

header('Content-Type: application/json');

try {
    $postRepo = new BlogPostRepository();

    $id = $_GET['id'] ?? null;
    $slug = $_GET['slug'] ?? null;

    if (!$id && !$slug) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID atau slug diperlukan']);
        exit;
    }

    if ($id) {
        $post = $postRepo->getById((int)$id);
    } else {
        $post = $postRepo->getBySlug($slug);
    }

    if (!$post) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Artikel tidak ditemukan']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $post
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
