<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/BrandRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\BrandRepository;

AuthMiddleware::requireAdminLoginFromView();

$brandRepo = new BrandRepository();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithError('Metode request tidak valid - hanya POST yang diizinkan');
            exit;
        }
        handleAdd($brandRepo);
        break;
    
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithError('Metode request tidak valid - hanya POST yang diizinkan');
            exit;
        }
        handleEdit($brandRepo);
        break;
    
    case 'delete':
        handleDelete($brandRepo);
        break;
    
    case 'getBrand':
        handleGetBrand($brandRepo);
        break;

    case 'checkDuplicate':
        handleCheckDuplicate($brandRepo);
        break;

    case 'getNextId':
        handleGetNextId($brandRepo);
        break;
    
    default:
        redirectWithError('Aksi tidak valid');
}

function handleAdd(BrandRepository $brandRepo): void
{
    $data = [
        'nama_brand' => $_POST['nama_brand'] ?? '',
        'desc_brand' => $_POST['desc_brand'] ?? '',
        'website' => $_POST['website'] ?? ''
    ];

    $logoFile = null;
    if (isset($_FILES['logo_brand']) && $_FILES['logo_brand']['error'] !== UPLOAD_ERR_NO_FILE) {
        $logoFile = $_FILES['logo_brand'];
    }

    $result = $brandRepo->create($data, $logoFile);
    
    if ($result['success']) {
        redirectWithSuccess($result['message']);
    } else {
        $_SESSION['form_data'] = $data;
        redirectWithError($result['message'], 'add-brand.php');
    }
}

function handleEdit(BrandRepository $brandRepo): void
{
    $id = $_POST['id_brand'] ?? '';
    
    if (empty($id)) {
        redirectWithError('ID brand tidak valid');
        return;
    }

    $data = [
        'nama_brand' => $_POST['nama_brand'] ?? '',
        'desc_brand' => $_POST['desc_brand'] ?? '',
        'website' => $_POST['website'] ?? ''
    ];

    $logoFile = null;
    if (isset($_FILES['logo_brand']) && $_FILES['logo_brand']['error'] !== UPLOAD_ERR_NO_FILE) {
        $logoFile = $_FILES['logo_brand'];
    }

    $result = $brandRepo->update($id, $data, $logoFile);
    
    if ($result['success']) {
        redirectWithSuccess($result['message']);
    } else {
        $_SESSION['form_data'] = $data;
        redirectWithError($result['message'], 'edit-brand.php?id=' . $id);
    }
}

function handleDelete(BrandRepository $brandRepo): void
{
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID brand tidak valid']);
        return;
    }

    $result = $brandRepo->delete($id);
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        sendJsonResponse($result);
    } else {
        if ($result['success']) {
            redirectWithSuccess($result['message']);
        } else {
            redirectWithError($result['message']);
        }
    }
}

function handleGetBrand(BrandRepository $brandRepo): void
{
    header('Content-Type: application/json');
    
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID brand tidak valid']);
        exit;
    }
    
    $brand = $brandRepo->getById($id);
    
    if (!$brand) {
        echo json_encode(['error' => 'Brand tidak ditemukan']);
        exit;
    }
    
    echo json_encode($brand);
    exit;
}

function handleCheckDuplicate(BrandRepository $brandRepo): void
{
    header('Content-Type: application/json');
    
    $name = $_GET['name'] ?? '';
    $excludeId = $_GET['exclude_id'] ?? null;
    
    if (empty($name)) {
        echo json_encode(['duplicate' => false]);
        exit;
    }
    
    $isDuplicate = $brandRepo->isDuplicateName($name, $excludeId);
    
    echo json_encode(['duplicate' => $isDuplicate]);
    exit;
}

function handleGetNextId(BrandRepository $brandRepo): void
{
    header('Content-Type: application/json');
    echo json_encode(['next_id' => $brandRepo->getNextId()]);
    exit;
}

function sendJsonResponse(array $data): void
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function redirectWithSuccess(string $message, string $page = 'BrandAdmin.php'): void
{
    $_SESSION['flash_success'] = $message;
    header('Location: ../../view/admin/' . $page);
    exit;
}

function redirectWithError(string $message, string $page = 'BrandAdmin.php'): void
{
    $_SESSION['flash_error'] = $message;
    header('Location: ../../view/admin/' . $page);
    exit;
}
