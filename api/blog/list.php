<?php
require_once __DIR__ . '/../../config/config.php';

use App\Repository\BlogPostRepository;
use App\Repository\BlogCategoryRepository;

header('Content-Type: application/json');

try {
    $postRepo = new BlogPostRepository();
    $categoryRepo = new BlogCategoryRepository();

    $filters = [
        'status' => $_GET['status'] ?? null,
        'category' => $_GET['category'] ?? null,
        'search' => $_GET['search'] ?? null,
        'limit' => $_GET['limit'] ?? null,
        'offset' => $_GET['offset'] ?? null
    ];

    $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');

    $posts = $postRepo->getAll($filters);
    $total = $postRepo->count($filters);
    $categories = $categoryRepo->getAll(true);

    echo json_encode([
        'success' => true,
        'data' => $posts,
        'total' => $total,
        'categories' => $categories
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
