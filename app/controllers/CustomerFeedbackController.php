<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/CustomerFeedbackRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Auth\SessionManager;
use App\Repository\CustomerFeedbackRepository;

AuthMiddleware::requireAdminLoginFromView();

$feedbackRepo = new CustomerFeedbackRepository();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$isSuperAdmin = PermissionHelper::isSuperAdmin();
$canViewFeedback = $isSuperAdmin || PermissionHelper::canViewCustomerFeedback();
$canManageCrm = $isSuperAdmin || PermissionHelper::canManageCrm();

if (!$canViewFeedback && !$canManageCrm) {
    sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

switch ($action) {
    case 'getReviews':
        handleGetReviews($feedbackRepo);
        break;
    
    case 'getReview':
        handleGetReview($feedbackRepo);
        break;
    
    case 'updateReviewStatus':
        if (!$canManageCrm) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleUpdateReviewStatus($feedbackRepo);
        break;
    
    case 'deleteReview':
        if (!$canManageCrm) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleDeleteReview($feedbackRepo);
        break;
    
    case 'getReviewStats':
        handleGetReviewStats($feedbackRepo);
        break;
    
    case 'getFeedback':
        handleGetFeedback($feedbackRepo);
        break;
    
    case 'getFeedbackDetail':
        handleGetFeedbackDetail($feedbackRepo);
        break;
    
    case 'respondFeedback':
        if (!$canManageCrm) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendJsonResponse(['success' => false, 'message' => 'Metode tidak valid']);
            exit;
        }
        handleRespondFeedback($feedbackRepo);
        break;
    
    case 'updateFeedbackStatus':
        if (!$canManageCrm) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleUpdateFeedbackStatus($feedbackRepo);
        break;
    
    case 'getFeedbackStats':
        handleGetFeedbackStats($feedbackRepo);
        break;
    
    default:
        sendJsonResponse(['success' => false, 'message' => 'Aksi tidak valid']);
}

function handleGetReviews(CustomerFeedbackRepository $feedbackRepo): void
{
    $filters = [
        'search' => $_GET['search'] ?? '',
        'status' => $_GET['status'] ?? '',
        'rating' => $_GET['rating'] ?? '',
        'customer_id' => $_GET['customer_id'] ?? '',
        'product_id' => $_GET['product_id'] ?? '',
        'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : 50,
        'offset' => isset($_GET['offset']) ? (int)$_GET['offset'] : 0
    ];
    
    $reviews = $feedbackRepo->getAllReviews($filters);
    $total = $feedbackRepo->countReviews($filters);
    
    sendJsonResponse([
        'success' => true,
        'data' => $reviews,
        'total' => $total
    ]);
}

function handleGetReview(CustomerFeedbackRepository $feedbackRepo): void
{
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID review tidak valid']);
        return;
    }
    
    $review = $feedbackRepo->getReviewById($id);
    
    if (!$review) {
        sendJsonResponse(['success' => false, 'message' => 'Review tidak ditemukan']);
        return;
    }
    
    sendJsonResponse(['success' => true, 'data' => $review]);
}

function handleUpdateReviewStatus(CustomerFeedbackRepository $feedbackRepo): void
{
    $id = $_GET['id'] ?? $_POST['id'] ?? '';
    $status = $_GET['status'] ?? $_POST['status'] ?? '';
    
    if (empty($id) || empty($status)) {
        sendJsonResponse(['success' => false, 'message' => 'Parameter tidak lengkap']);
        return;
    }
    
    $result = $feedbackRepo->updateReviewStatus($id, $status);
    sendJsonResponse($result);
}

function handleDeleteReview(CustomerFeedbackRepository $feedbackRepo): void
{
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID review tidak valid']);
        return;
    }
    
    $result = $feedbackRepo->deleteReview($id);
    sendJsonResponse($result);
}

function handleGetReviewStats(CustomerFeedbackRepository $feedbackRepo): void
{
    $stats = $feedbackRepo->getReviewStats();
    sendJsonResponse(['success' => true, 'data' => $stats]);
}

function handleGetFeedback(CustomerFeedbackRepository $feedbackRepo): void
{
    $filters = [
        'search' => $_GET['search'] ?? '',
        'type' => $_GET['type'] ?? '',
        'status' => $_GET['status'] ?? '',
        'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : 50,
        'offset' => isset($_GET['offset']) ? (int)$_GET['offset'] : 0
    ];
    
    $feedback = $feedbackRepo->getAllFeedback($filters);
    
    sendJsonResponse([
        'success' => true,
        'data' => $feedback
    ]);
}

function handleGetFeedbackDetail(CustomerFeedbackRepository $feedbackRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID feedback tidak valid']);
        return;
    }
    
    $feedback = $feedbackRepo->getFeedbackById($id);
    
    if (!$feedback) {
        sendJsonResponse(['success' => false, 'message' => 'Feedback tidak ditemukan']);
        return;
    }
    
    sendJsonResponse(['success' => true, 'data' => $feedback]);
}

function handleRespondFeedback(CustomerFeedbackRepository $feedbackRepo): void
{
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $response = $_POST['response'] ?? '';
    
    if ($id <= 0 || empty($response)) {
        sendJsonResponse(['success' => false, 'message' => 'Parameter tidak lengkap']);
        return;
    }
    
    $admin = SessionManager::getCurrentAdmin();
    $adminId = $admin['id_admin'] ?? 0;
    
    if ($adminId <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'Admin tidak valid']);
        return;
    }
    
    $result = $feedbackRepo->respondToFeedback($id, $response, $adminId);
    sendJsonResponse($result);
}

function handleUpdateFeedbackStatus(CustomerFeedbackRepository $feedbackRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $status = $_GET['status'] ?? '';
    
    if ($id <= 0 || empty($status)) {
        sendJsonResponse(['success' => false, 'message' => 'Parameter tidak lengkap']);
        return;
    }
    
    $result = $feedbackRepo->updateFeedbackStatus($id, $status);
    sendJsonResponse($result);
}

function handleGetFeedbackStats(CustomerFeedbackRepository $feedbackRepo): void
{
    $stats = $feedbackRepo->getFeedbackStats();
    sendJsonResponse(['success' => true, 'data' => $stats]);
}

function sendJsonResponse(array $data): void
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
