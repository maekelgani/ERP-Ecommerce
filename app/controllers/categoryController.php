<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/CategoryRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\CategoryRepository;

AuthMiddleware::requireAdminLoginFromView();

$categoryRepo = new CategoryRepository();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithError('Metode request tidak valid - hanya POST yang diizinkan');
            exit;
        }
        handleAdd($categoryRepo);
        break;

    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirectWithError('Metode request tidak valid - hanya POST yang diizinkan');
            exit;
        }
        handleEdit($categoryRepo);
        break;

    case 'delete':
        handleDelete($categoryRepo);
        break;

    case 'getCategory':
        handleGetCategory($categoryRepo);
        break;

    case 'checkDuplicate':
        handleCheckDuplicate($categoryRepo);
        break;

    case 'getNextId':
        handleGetNextId($categoryRepo);
        break;

    default:
        redirectWithError('Aksi tidak valid');
}

function handleAdd(CategoryRepository $categoryRepo): void
{
    $data = [
        'nama_kategori' => $_POST['nama_kategori'] ?? '',
        'deskripsi_kategori' => $_POST['deskripsi_kategori'] ?? ''
    ];

    $iconFile = null;
    if (!empty($_FILES['icon_kategori']['name'])) {
        $iconFile = handleIconUpload($_FILES['icon_kategori']);
        if ($iconFile['success']) {
            $data['icon_kategori'] = $iconFile['filename'];
        } else {
            $_SESSION['form_data'] = $data;
            redirectWithError($iconFile['message'], 'add-category.php');
            return;
        }
    }

    $result = $categoryRepo->create($data);

    if ($result['success']) {
        redirectWithSuccess($result['message']);
    } else {
        if ($iconFile) {
            @unlink(__DIR__ . '/../../uploads/products/' . $iconFile['filename']);
        }
        $_SESSION['form_data'] = $data;
        redirectWithError($result['message'], 'add-category.php');
    }
}

function handleEdit(CategoryRepository $categoryRepo): void
{
    $id = $_POST['id_kategori'] ?? '';

    if (empty($id)) {
        redirectWithError('ID kategori tidak valid');
        return;
    }

    $data = [
        'nama_kategori' => $_POST['nama_kategori'] ?? '',
        'deskripsi_kategori' => $_POST['deskripsi_kategori'] ?? ''
    ];

    $iconFile = null;
    if (!empty($_FILES['icon_kategori']['name'])) {
        $category = $categoryRepo->getById($id);

        $iconFile = handleIconUpload($_FILES['icon_kategori']);
        if ($iconFile['success']) {
            $data['icon_kategori'] = $iconFile['filename'];

            if (!empty($category['icon_kategori'])) {
                @unlink(__DIR__ . '/../../uploads/products/' . $category['icon_kategori']);
            }
        } else {
            $_SESSION['form_data'] = $data;
            redirectWithError($iconFile['message'], 'edit-category.php?id=' . $id);
            return;
        }
    }

    $result = $categoryRepo->update($id, $data);

    if ($result['success']) {
        redirectWithSuccess($result['message']);
    } else {
        if ($iconFile) {
            @unlink(__DIR__ . '/../../uploads/products/' . $iconFile['filename']);
        }
        $_SESSION['form_data'] = $data;
        redirectWithError($result['message'], 'edit-category.php?id=' . $id);
    }
}

function handleDelete(CategoryRepository $categoryRepo): void
{
    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID kategori tidak valid']);
        return;
    }

    $result = $categoryRepo->delete($id);

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

function handleGetCategory(CategoryRepository $categoryRepo): void
{
    header('Content-Type: application/json');

    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        echo json_encode(['error' => 'ID kategori tidak valid']);
        exit;
    }

    $category = $categoryRepo->getById($id);

    if (!$category) {
        echo json_encode(['error' => 'Kategori tidak ditemukan']);
        exit;
    }

    echo json_encode($category);
    exit;
}

function handleCheckDuplicate(CategoryRepository $categoryRepo): void
{
    header('Content-Type: application/json');

    $name = $_GET['name'] ?? '';
    $excludeId = $_GET['exclude_id'] ?? null;

    if (empty($name)) {
        echo json_encode(['duplicate' => false]);
        exit;
    }

    $isDuplicate = $categoryRepo->isDuplicateName($name, $excludeId);

    echo json_encode(['duplicate' => $isDuplicate]);
    exit;
}

function handleGetNextId(CategoryRepository $categoryRepo): void
{
    header('Content-Type: application/json');
    echo json_encode(['next_id' => $categoryRepo->getNextId()]);
    exit;
}

function sendJsonResponse(array $data): void
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function redirectWithSuccess(string $message, string $page = 'CategoryAdmin.php'): void
{
    $_SESSION['flash_success'] = $message;
    header('Location: ../../view/admin/' . $page);
    exit;
}

function redirectWithError(string $message, string $page = 'CategoryAdmin.php'): void
{
    $_SESSION['flash_error'] = $message;
    header('Location: ../../view/admin/' . $page);
    exit;
}

function handleIconUpload(array $file): array
{
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $max_size = 5 * 1024 * 1024;

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file tidak boleh melebihi 5MB'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_mimes)) {
        return ['success' => false, 'message' => 'Format file harus gambar (JPG, PNG, WebP, GIF)'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        return ['success' => false, 'message' => 'Ekstensi file tidak diizinkan'];
    }

    $upload_dir = __DIR__ . '/../../uploads/category/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filename = uniqid('cat_icon_') . '.' . $ext;
    $upload_path = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['success' => false, 'message' => 'Gagal mengupload file'];
}
