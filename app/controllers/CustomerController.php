<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/CrmRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Repository\CrmRepository;

AuthMiddleware::requireAdminLoginFromView();

$crmRepo = new CrmRepository();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$isSuperAdmin = PermissionHelper::isSuperAdmin();
$canViewCustomers = $isSuperAdmin || PermissionHelper::canViewCustomers();
$canManageCustomers = $isSuperAdmin || PermissionHelper::canManageCustomers();

switch ($action) {
    case 'list':
        if (!$canViewCustomers && !$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleList($crmRepo);
        break;
    
    case 'getCustomer':
        if (!$canViewCustomers && !$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleGetCustomer($crmRepo);
        break;
    
    case 'getCustomerDetail':
        if (!$canViewCustomers && !$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleGetCustomerDetail($crmRepo);
        break;
    
    case 'add':
        if (!$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendJsonResponse(['success' => false, 'message' => 'Metode tidak valid']);
            exit;
        }
        handleAdd($crmRepo);
        break;
    
    case 'edit':
        if (!$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendJsonResponse(['success' => false, 'message' => 'Metode tidak valid']);
            exit;
        }
        handleEdit($crmRepo);
        break;
    
    case 'toggleStatus':
        if (!$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleToggleStatus($crmRepo);
        break;
    
    case 'getAddresses':
        if (!$canViewCustomers && !$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleGetAddresses($crmRepo);
        break;
    
    case 'getOrders':
        if (!$canViewCustomers && !$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleGetOrders($crmRepo);
        break;
    
    case 'getReviews':
        if (!$canViewCustomers && !$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleGetReviews($crmRepo);
        break;
    
    case 'getVoucherUsage':
        if (!$canViewCustomers && !$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleGetVoucherUsage($crmRepo);
        break;
    
    case 'getOrderStats':
        if (!$canViewCustomers && !$canManageCustomers) {
            sendJsonResponse(['success' => false, 'message' => 'Akses ditolak']);
            exit;
        }
        handleGetOrderStats($crmRepo);
        break;
    
    default:
        sendJsonResponse(['success' => false, 'message' => 'Aksi tidak valid']);
}

function handleList(CrmRepository $crmRepo): void
{
    $filters = [
        'search' => $_GET['search'] ?? '',
        'is_active' => $_GET['is_active'] ?? '',
        'login_type' => $_GET['login_type'] ?? '',
        'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : 50,
        'offset' => isset($_GET['offset']) ? (int)$_GET['offset'] : 0
    ];
    
    $customers = $crmRepo->getAllCustomers($filters);
    $total = $crmRepo->countCustomers($filters);
    
    sendJsonResponse([
        'success' => true,
        'data' => $customers,
        'total' => $total,
        'filters' => $filters
    ]);
}

function handleGetCustomer(CrmRepository $crmRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $customer = $crmRepo->getCustomerById($id);
    
    if (!$customer) {
        sendJsonResponse(['success' => false, 'message' => 'Pelanggan tidak ditemukan']);
        return;
    }
    
    sendJsonResponse(['success' => true, 'data' => $customer]);
}

function handleGetCustomerDetail(CrmRepository $crmRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $customer = $crmRepo->getCustomerById($id);
    
    if (!$customer) {
        sendJsonResponse(['success' => false, 'message' => 'Pelanggan tidak ditemukan']);
        return;
    }
    
    $addresses = $crmRepo->getCustomerAddresses($id);
    $orders = $crmRepo->getCustomerOrders($id, 10);
    $reviews = $crmRepo->getCustomerReviews($id);
    $voucherUsage = $crmRepo->getCustomerVoucherUsage($id);
    $orderStats = $crmRepo->getOrderStatsByCustomer($id);
    
    sendJsonResponse([
        'success' => true,
        'data' => [
            'customer' => $customer,
            'addresses' => $addresses,
            'orders' => $orders,
            'reviews' => $reviews,
            'voucher_usage' => $voucherUsage,
            'order_stats' => $orderStats
        ]
    ]);
}

function handleAdd(CrmRepository $crmRepo): void
{
    $data = [
        'nama_lengkap' => $_POST['nama_lengkap'] ?? '',
        'email' => $_POST['email'] ?? '',
        'no_telp' => $_POST['no_telp'] ?? '',
        'password' => $_POST['password'] ?? '',
        'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1
    ];
    
    $result = $crmRepo->createCustomer($data);
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        sendJsonResponse($result);
    } else {
        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        header('Location: ../../view/admin/CustomerList.php');
        exit;
    }
}

function handleEdit(CrmRepository $crmRepo): void
{
    $id = isset($_POST['id_customer']) ? (int)$_POST['id_customer'] : 0;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $data = [
        'nama_lengkap' => $_POST['nama_lengkap'] ?? null,
        'email' => $_POST['email'] ?? null,
        'no_telp' => $_POST['no_telp'] ?? null,
        'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : null
    ];
    
    if (!empty($_POST['password'])) {
        $data['password'] = $_POST['password'];
    }
    
    $data = array_filter($data, function($v) { return $v !== null; });
    
    $result = $crmRepo->updateCustomer($id, $data);
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        sendJsonResponse($result);
    } else {
        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
        } else {
            $_SESSION['flash_error'] = $result['message'];
        }
        header('Location: ../../view/admin/CustomerList.php');
        exit;
    }
}

function handleToggleStatus(CrmRepository $crmRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $status = isset($_GET['status']) ? (int)$_GET['status'] : 0;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $success = $crmRepo->updateCustomerStatus($id, (bool)$status);
    
    if ($success) {
        $statusText = $status ? 'diaktifkan' : 'dinonaktifkan';
        sendJsonResponse(['success' => true, 'message' => "Pelanggan berhasil $statusText"]);
    } else {
        sendJsonResponse(['success' => false, 'message' => 'Gagal mengubah status pelanggan']);
    }
}

function handleGetAddresses(CrmRepository $crmRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $addresses = $crmRepo->getCustomerAddresses($id);
    sendJsonResponse(['success' => true, 'data' => $addresses]);
}

function handleGetOrders(CrmRepository $crmRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $orders = $crmRepo->getCustomerOrders($id, $limit);
    sendJsonResponse(['success' => true, 'data' => $orders]);
}

function handleGetReviews(CrmRepository $crmRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $reviews = $crmRepo->getCustomerReviews($id);
    sendJsonResponse(['success' => true, 'data' => $reviews]);
}

function handleGetVoucherUsage(CrmRepository $crmRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $usage = $crmRepo->getCustomerVoucherUsage($id);
    sendJsonResponse(['success' => true, 'data' => $usage]);
}

function handleGetOrderStats(CrmRepository $crmRepo): void
{
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'ID pelanggan tidak valid']);
        return;
    }
    
    $stats = $crmRepo->getOrderStatsByCustomer($id);
    sendJsonResponse(['success' => true, 'data' => $stats]);
}

function sendJsonResponse(array $data): void
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
