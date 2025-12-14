<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/CustomerActivityRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Repository\CustomerActivityRepository;

AuthMiddleware::requireAdminLoginFromView();

$activityRepo = new CustomerActivityRepository();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$isSuperAdmin = PermissionHelper::isSuperAdmin();
$canViewActivity = $isSuperAdmin || PermissionHelper::canViewCustomerActivity();
$canManageCrm = $isSuperAdmin || PermissionHelper::canManageCrm();

if (!$canViewActivity && !$canManageCrm) {
    sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

switch ($action) {
    case 'getCustomerActivities':
        handleGetCustomerActivities($activityRepo);
        break;
    
    case 'getAllActivities':
        handleGetAllActivities($activityRepo);
        break;
    
    case 'getStats':
        handleGetStats($activityRepo);
        break;
    
    case 'getRecentLogins':
        handleGetRecentLogins($activityRepo);
        break;
    
    default:
        sendJsonResponse(['success' => false, 'message' => 'Aksi tidak valid']);
}

function handleGetCustomerActivities(CustomerActivityRepository $activityRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $activities = $activityRepo->getCustomerActivities($id, $limit);
    sendJsonResponse(['success' => true, 'data' => $activities]);
}

function handleGetAllActivities(CustomerActivityRepository $activityRepo): void
{
    $filters = [
        'customer_id' => isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : null,
        'date_from' => $_GET['date_from'] ?? null,
        'date_to' => $_GET['date_to'] ?? null,
        'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : 100
    ];
    
    $filters = array_filter($filters, function($v) { return $v !== null && $v !== ''; });
    
    $activities = $activityRepo->getAllActivities($filters);
    sendJsonResponse(['success' => true, 'data' => $activities]);
}

function handleGetStats(CustomerActivityRepository $activityRepo): void
{
    $stats = $activityRepo->getActivityStats();
    sendJsonResponse(['success' => true, 'data' => $stats]);
}

function handleGetRecentLogins(CustomerActivityRepository $activityRepo): void
{
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $logins = $activityRepo->getRecentLoginActivity($limit);
    sendJsonResponse(['success' => true, 'data' => $logins]);
}

function sendJsonResponse(array $data): void
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
