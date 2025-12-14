<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/CategoryRepository.php';
require_once __DIR__ . '/../Repository/BrandRepository.php';
require_once __DIR__ . '/../Repository/ProductRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\BrandRepository;

AuthMiddleware::requireAdminLoginFromView();

$productRepo = new ProductRepository();
$categoryRepo = new CategoryRepository();
$brandRepo = new BrandRepository();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithError('Metode request tidak valid - hanya POST yang diizinkan');
            exit;
        }
        handleAdd($productRepo);
        break;
    
    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithError('Metode request tidak valid - hanya POST yang diizinkan');
            exit;
        }
        handleEdit($productRepo);
        break;
    
    case 'delete':
        handleDelete($productRepo);
        break;
    
    case 'updateStock':
        handleUpdateStock($productRepo);
        break;
    
    case 'getProduct':
        handleGetProduct($productRepo);
        break;

    case 'checkUsedInOrders':
        handleCheckUsedInOrders($productRepo);
        break;
    
    default:
        redirectWithError('Aksi tidak valid');
}

function handleAdd(ProductRepository $productRepo): void
{
    $data = [
        'nama_product' => trim($_POST['nama_product'] ?? ''),
        'deskripsi_speksifikasi' => trim($_POST['deskripsi_speksifikasi'] ?? ''),
        'harga' => $_POST['harga'] ?? 0,
        'stok' => intval($_POST['stok'] ?? 0),
        'id_kategori' => $_POST['id_kategori'] ?? null,
        'id_brand' => $_POST['id_brand'] ?? null,
        'berat_gram' => intval($_POST['berat_gram'] ?? 0),
        'status_produk' => $_POST['status_produk'] ?? 'tersedia'
    ];

    $imageFile = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imageFile = $_FILES['gambar'];
    }

    $result = $productRepo->create($data, $imageFile);
    
    if ($result['success']) {
        redirectWithSuccess($result['message']);
    } else {
        $_SESSION['form_data'] = $data;
        redirectWithError($result['message'], 'add-product.php');
    }
}

function handleEdit(ProductRepository $productRepo): void
{
    $id = $_POST['id_product'] ?? '';
    
    if (empty($id)) {
        redirectWithError('ID produk tidak valid');
        return;
    }

    $data = [
        'nama_product' => trim($_POST['nama_product'] ?? ''),
        'deskripsi_speksifikasi' => trim($_POST['deskripsi_speksifikasi'] ?? ''),
        'harga' => $_POST['harga'] ?? 0,
        'stok' => intval($_POST['stok'] ?? 0),
        'id_kategori' => $_POST['id_kategori'] ?? null,
        'id_brand' => $_POST['id_brand'] ?? null,
        'berat_gram' => intval($_POST['berat_gram'] ?? 0),
        'status_produk' => $_POST['status_produk'] ?? 'tersedia'
    ];

    $imageFile = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $imageFile = $_FILES['gambar'];
    }

    $result = $productRepo->update($id, $data, $imageFile);
    
    if ($result['success']) {
        redirectWithSuccess($result['message']);
    } else {
        $_SESSION['form_data'] = $data;
        redirectWithError($result['message'], 'edit-product.php?id=' . $id);
    }
}

function handleDelete(ProductRepository $productRepo): void
{
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID produk tidak valid']);
        return;
    }

    $result = $productRepo->delete($id);
    
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

function handleUpdateStock(ProductRepository $productRepo): void
{
    header('Content-Type: application/json');
    
    $id = $_POST['id_product'] ?? '';
    $stok = intval($_POST['stok'] ?? 0);
    
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID produk tidak valid']);
        exit;
    }
    
    $result = $productRepo->updateStock($id, $stok);
    
    echo json_encode($result);
    exit;
}

function handleGetProduct(ProductRepository $productRepo): void
{
    header('Content-Type: application/json');
    
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID produk tidak valid']);
        exit;
    }
    
    $product = $productRepo->getById($id);
    
    if (!$product) {
        echo json_encode(['error' => 'Produk tidak ditemukan']);
        exit;
    }

    $product['stock_status'] = $productRepo->getStockStatus($product['stok']);
    $product['product_status_info'] = $productRepo->getProductStatus($product['status_produk']);
    
    echo json_encode($product);
    exit;
}

function handleCheckUsedInOrders(ProductRepository $productRepo): void
{
    header('Content-Type: application/json');
    
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['error' => 'ID produk tidak valid']);
        exit;
    }
    
    $isUsed = $productRepo->isUsedInOrders($id);
    
    echo json_encode(['used' => $isUsed]);
    exit;
}

function sendJsonResponse(array $data): void
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function redirectWithSuccess(string $message, string $page = 'ProductAdmin.php'): void
{
    $_SESSION['flash_success'] = $message;
    header('Location: ../../view/admin/' . $page);
    exit;
}

function redirectWithError(string $message, string $page = 'ProductAdmin.php'): void
{
    $_SESSION['flash_error'] = $message;
    header('Location: ../../view/admin/' . $page);
    exit;
}
