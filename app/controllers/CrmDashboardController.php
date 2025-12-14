<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/CrmRepository.php';
require_once __DIR__ . '/../Repository/CustomerFeedbackRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\CrmRepository;
use App\Repository\CustomerFeedbackRepository;

AuthMiddleware::requireAdminLoginFromView();

$crmRepo = new CrmRepository();
$feedbackRepo = new CustomerFeedbackRepository();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getStats':
        handleGetStats($crmRepo, $feedbackRepo);
        break;
    
    case 'getMostActiveCustomers':
        handleGetMostActiveCustomers($crmRepo);
        break;
    
    case 'getRecentReviews':
        handleGetRecentReviews($crmRepo);
        break;
    
    case 'getProductRatings':
        handleGetProductRatings($crmRepo);
        break;
    
    case 'getFeedback':
        handleGetFeedback($crmRepo);
        break;
    
    default:
        sendJsonResponse(['success' => false, 'message' => 'Aksi tidak valid']);
}

function handleGetStats(CrmRepository $crmRepo, CustomerFeedbackRepository $feedbackRepo): void
{
    $customerStats = $crmRepo->getCustomerStats();
    $reviewStats = $feedbackRepo->getReviewStats();
    $feedbackStats = $feedbackRepo->getFeedbackStats();
    
    sendJsonResponse([
        'success' => true,
        'data' => [
            'customers' => $customerStats,
            'reviews' => $reviewStats,
            'feedback' => $feedbackStats
        ]
    ]);
}

function handleGetMostActiveCustomers(CrmRepository $crmRepo): void
{
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $customers = $crmRepo->getMostActiveCustomers($limit);
    
    sendJsonResponse([
        'success' => true,
        'data' => $customers
    ]);
}

function handleGetRecentReviews(CrmRepository $crmRepo): void
{
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $reviews = $crmRepo->getRecentReviews($limit);
    
    sendJsonResponse([
        'success' => true,
        'data' => $reviews
    ]);
}

function handleGetProductRatings(CrmRepository $crmRepo): void
{
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $ratings = $crmRepo->getProductRatings($limit);
    
    sendJsonResponse([
        'success' => true,
        'data' => $ratings
    ]);
}

function handleGetFeedback(CrmRepository $crmRepo): void
{
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $feedback = $crmRepo->getCustomerFeedback($limit);
    
    sendJsonResponse([
        'success' => true,
        'data' => $feedback
    ]);
}

function sendJsonResponse(array $data): void
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
