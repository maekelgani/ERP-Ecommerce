<?php
require_once __DIR__ . '/../../config/config.php';

use App\Repository\BlogCategoryRepository;

header('Content-Type: application/json');

try {
    $categoryRepo = new BlogCategoryRepository();
    
    $activeOnly = isset($_GET['active']) && $_GET['active'] === '1';
    $categories = $categoryRepo->getAll($activeOnly);

    echo json_encode([
        'success' => true,
        'data' => $categories
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
