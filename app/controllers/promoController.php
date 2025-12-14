<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/PromoCampaignRepository.php';
require_once __DIR__ . '/../Repository/CampaignProductRepository.php';
require_once __DIR__ . '/../Repository/ProductRepository.php';

use App\Repository\PromoCampaignRepository;
use App\Repository\CampaignProductRepository;
use App\Repository\ProductRepository;
use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;

header('Content-Type: application/json');

AuthMiddleware::requireAdminLogin();

$campaignRepo = new PromoCampaignRepository();
$cpRepo = new CampaignProductRepository();
$productRepo = new ProductRepository();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            if (!PermissionHelper::hasPermission('manage_campaigns') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola kampanye');
            }

            $data = [
                'judul' => $_POST['judul'] ?? '',
                'deskripsi' => $_POST['deskripsi'] ?? '',
                'tipe' => $_POST['tipe'] ?? '',
                'mulai_pada' => $_POST['mulai_pada'] ?? '',
                'selesai_pada' => $_POST['selesai_pada'] ?? '',
                'kuota_total' => $_POST['kuota_total'] ?? null,
                'status' => $_POST['status'] ?? 'draf'
            ];

            $bannerFile = isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK ? $_FILES['banner'] : null;
            $result = $campaignRepo->create($data, $bannerFile);

            if ($result['success'] && !empty($_POST['products'])) {
                $productIds = is_array($_POST['products']) ? $_POST['products'] : json_decode($_POST['products'], true);
                if ($productIds) {
                    $cpRepo->attachMultipleProducts($result['id'], $productIds);
                }
            }

            echo json_encode($result);
            break;

        case 'update':
            if (!PermissionHelper::hasPermission('manage_campaigns') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola kampanye');
            }

            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                throw new Exception('ID kampanye tidak valid');
            }

            $data = [
                'judul' => $_POST['judul'] ?? '',
                'deskripsi' => $_POST['deskripsi'] ?? '',
                'tipe' => $_POST['tipe'] ?? '',
                'mulai_pada' => $_POST['mulai_pada'] ?? '',
                'selesai_pada' => $_POST['selesai_pada'] ?? '',
                'kuota_total' => $_POST['kuota_total'] ?? null,
                'status' => $_POST['status'] ?? 'draf'
            ];

            $bannerFile = isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK ? $_FILES['banner'] : null;
            $result = $campaignRepo->update($id, $data, $bannerFile);

            echo json_encode($result);
            break;

        case 'delete':
            if (!PermissionHelper::hasPermission('manage_campaigns') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola kampanye');
            }

            $id = $_POST['id'] ?? $_GET['id'] ?? '';
            if (empty($id)) {
                throw new Exception('ID kampanye tidak valid');
            }

            $result = $campaignRepo->delete($id);
            echo json_encode($result);
            break;

        case 'update_status':
            if (!PermissionHelper::hasPermission('manage_campaigns') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola kampanye');
            }

            $id = $_POST['id'] ?? '';
            $status = $_POST['status'] ?? '';
            
            if (empty($id) || empty($status)) {
                throw new Exception('Data tidak lengkap');
            }

            $result = $campaignRepo->updateStatus($id, $status);
            echo json_encode($result);
            break;

        case 'attach_products':
            if (!PermissionHelper::hasPermission('manage_campaigns') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola kampanye');
            }

            $campaignId = $_POST['campaign_id'] ?? '';
            $productIds = $_POST['product_ids'] ?? [];

            if (empty($campaignId)) {
                throw new Exception('ID kampanye tidak valid');
            }

            if (is_string($productIds)) {
                $productIds = json_decode($productIds, true) ?? [];
            }

            $result = $cpRepo->attachMultipleProducts($campaignId, $productIds);
            echo json_encode($result);
            break;

        case 'detach_product':
            if (!PermissionHelper::hasPermission('manage_campaigns') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola kampanye');
            }

            $campaignId = $_POST['campaign_id'] ?? '';
            $productId = $_POST['product_id'] ?? '';

            if (empty($campaignId) || empty($productId)) {
                throw new Exception('Data tidak lengkap');
            }

            $result = $cpRepo->detachProduct($campaignId, $productId);
            echo json_encode($result);
            break;

        case 'get':
            $id = $_GET['id'] ?? '';
            if (empty($id)) {
                throw new Exception('ID kampanye tidak valid');
            }

            $campaign = $campaignRepo->getById($id);
            if (!$campaign) {
                throw new Exception('Kampanye tidak ditemukan');
            }

            $products = $cpRepo->getProductsByCampaign($id);
            $campaign['products'] = $products;

            echo json_encode(['success' => true, 'data' => $campaign]);
            break;

        case 'list':
            $filters = [
                'tipe' => $_GET['tipe'] ?? '',
                'status' => $_GET['status'] ?? '',
                'search' => $_GET['search'] ?? '',
                'sort' => $_GET['sort'] ?? 'newest'
            ];

            $campaigns = $campaignRepo->getAll($filters);
            echo json_encode(['success' => true, 'data' => $campaigns]);
            break;

        case 'get_products':
            $campaignId = $_GET['campaign_id'] ?? '';
            if (empty($campaignId)) {
                throw new Exception('ID kampanye tidak valid');
            }

            $products = $cpRepo->getProductsByCampaign($campaignId);
            echo json_encode(['success' => true, 'data' => $products]);
            break;

        case 'get_available_products':
            $campaignId = $_GET['campaign_id'] ?? '';
            $search = $_GET['search'] ?? '';

            $allProducts = $productRepo->getAll(['search' => $search, 'status' => 'tersedia']);
            
            if ($campaignId) {
                $attachedProducts = $cpRepo->getProductsByCampaign($campaignId);
                $attachedIds = array_column($attachedProducts, 'id_produk');
                $allProducts = array_filter($allProducts, fn($p) => !in_array($p['id_product'], $attachedIds));
            }

            echo json_encode(['success' => true, 'data' => array_values($allProducts)]);
            break;

        default:
            throw new Exception('Aksi tidak valid');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
