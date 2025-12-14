<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/StoreLocationRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\StoreLocationRepository;

header('Content-Type: application/json');

AuthMiddleware::requireAdminLoginFromView();

$storeRepo = new StoreLocationRepository();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        handleList($storeRepo);
        break;
    case 'get':
        handleGet($storeRepo);
        break;
    case 'add':
        handleAdd($storeRepo);
        break;
    case 'update':
        handleUpdate($storeRepo);
        break;
    case 'delete':
        handleDelete($storeRepo);
        break;
    case 'getNextId':
        handleGetNextId($storeRepo);
        break;
    default:
        sendJsonResponse(['success' => false, 'message' => 'Aksi tidak valid']);
}

function handleList(StoreLocationRepository $repo): void
{
    $filters = [];
    if (!empty($_GET['search'])) {
        $filters['search'] = $_GET['search'];
    }
    if (isset($_GET['is_active'])) {
        $filters['is_active'] = $_GET['is_active'];
    }

    $stores = $repo->getAll($filters);
    sendJsonResponse(['success' => true, 'data' => $stores]);
}

function handleGet(StoreLocationRepository $repo): void
{
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID toko tidak valid']);
        return;
    }

    $store = $repo->getById($id);
    if (!$store) {
        sendJsonResponse(['success' => false, 'message' => 'Toko tidak ditemukan']);
        return;
    }

    sendJsonResponse(['success' => true, 'data' => $store]);
}

function handleAdd(StoreLocationRepository $repo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(['success' => false, 'message' => 'Metode request tidak valid']);
        return;
    }

    $data = [
        'nama_toko' => $_POST['nama_toko'] ?? '',
        'no_telepon' => $_POST['no_telepon'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
        'provinsi' => $_POST['provinsi'] ?? '',
        'kota_kabupaten' => $_POST['kota_kabupaten'] ?? '',
        'kecamatan' => $_POST['kecamatan'] ?? '',
        'kelurahan' => $_POST['kelurahan'] ?? '',
        'kode_pos' => $_POST['kode_pos'] ?? '',
        'jam_buka' => $_POST['jam_buka'] ?? '',
        'jam_tutup' => $_POST['jam_tutup'] ?? '',
        'is_active' => $_POST['is_active'] ?? 1
    ];

    $result = $repo->create($data);
    sendJsonResponse($result);
}

function handleUpdate(StoreLocationRepository $repo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(['success' => false, 'message' => 'Metode request tidak valid']);
        return;
    }

    $id = $_POST['id_toko'] ?? '';
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID toko tidak valid']);
        return;
    }

    $data = [
        'nama_toko' => $_POST['nama_toko'] ?? '',
        'no_telepon' => $_POST['no_telepon'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
        'provinsi' => $_POST['provinsi'] ?? '',
        'kota_kabupaten' => $_POST['kota_kabupaten'] ?? '',
        'kecamatan' => $_POST['kecamatan'] ?? '',
        'kelurahan' => $_POST['kelurahan'] ?? '',
        'kode_pos' => $_POST['kode_pos'] ?? '',
        'jam_buka' => $_POST['jam_buka'] ?? '',
        'jam_tutup' => $_POST['jam_tutup'] ?? '',
        'is_active' => $_POST['is_active'] ?? 1
    ];

    $result = $repo->update($id, $data);
    sendJsonResponse($result);
}

function handleDelete(StoreLocationRepository $repo): void
{
    $id = $_GET['id'] ?? $_POST['id'] ?? '';
    if (empty($id)) {
        sendJsonResponse(['success' => false, 'message' => 'ID toko tidak valid']);
        return;
    }

    $result = $repo->delete($id);
    sendJsonResponse($result);
}

function handleGetNextId(StoreLocationRepository $repo): void
{
    sendJsonResponse(['success' => true, 'next_id' => $repo->getNextId()]);
}

function sendJsonResponse(array $data): void
{
    echo json_encode($data);
    exit;
}
