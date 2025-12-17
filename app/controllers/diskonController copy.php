<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/DiskonRepository.php';
require_once __DIR__ . '/../Repository/PromoCampaignRepository.php';
require_once __DIR__ . '/../Repository/ProductRepository.php';

use App\Repository\DiskonRepository;
use App\Repository\PromoCampaignRepository;
use App\Repository\ProductRepository;
use App\Auth\SessionManager;
use App\Auth\PermissionHelper;

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
error_log('DiskonController accessed - Action: ' . $action);

if (!SessionManager::isAdminLoggedIn()) {
    error_log('Admin not logged in for diskonController');
    echo json_encode(['success' => false, 'message' => 'Sesi login tidak valid. Silakan login kembali.']);
    exit;
}

$sessionStatus = SessionManager::checkAdminSessionStatus();
if ($sessionStatus === 'expired') {
    error_log('Admin session expired');
    echo json_encode(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
    exit;
}

$diskonRepo = new DiskonRepository();
$campaignRepo = new PromoCampaignRepository();
$productRepo = new ProductRepository();

try {
    switch ($action) {
        case 'create':
            if (!PermissionHelper::hasPermission('manage_product_discounts') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola diskon');
            }

            error_log('Create diskon - POST data: ' . json_encode($_POST));

            $data = [
                'id_produk' => $_POST['id_produk'] ?? null,
                'id_kampanye' => !empty($_POST['id_kampanye']) ? $_POST['id_kampanye'] : null,
                'label' => $_POST['label'] ?? '',
                'jenis' => $_POST['jenis'] ?? 'persen',
                'nilai' => $_POST['nilai'] ?? 0,
                'stok_promo' => !empty($_POST['stok_promo']) ? $_POST['stok_promo'] : null,
                'maks_qty_per_pengguna' => !empty($_POST['maks_qty_per_pengguna']) ? $_POST['maks_qty_per_pengguna'] : null,
                'mulai_pada' => !empty($_POST['mulai_pada']) ? $_POST['mulai_pada'] : null,
                'selesai_pada' => !empty($_POST['selesai_pada']) ? $_POST['selesai_pada'] : null,
                'status' => $_POST['status'] ?? 'terjadwal'
            ];

            error_log('Create diskon - Processed data: ' . json_encode($data));

            $result = $diskonRepo->create($data);
            error_log('Create diskon - Result: ' . json_encode($result));
            echo json_encode($result);
            break;

        case 'update':
            if (!PermissionHelper::hasPermission('manage_product_discounts') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola diskon');
            }

            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                throw new Exception('ID diskon tidak valid');
            }

            $data = [
                'id_produk' => $_POST['id_produk'] ?? null,
                'id_kampanye' => $_POST['id_kampanye'] ?? null,
                'label' => $_POST['label'] ?? '',
                'jenis' => $_POST['jenis'] ?? 'persen',
                'nilai' => $_POST['nilai'] ?? 0,
                'stok_promo' => $_POST['stok_promo'] ?? null,
                'maks_qty_per_pengguna' => $_POST['maks_qty_per_pengguna'] ?? null,
                'mulai_pada' => $_POST['mulai_pada'] ?? null,
                'selesai_pada' => $_POST['selesai_pada'] ?? null,
                'status' => $_POST['status'] ?? 'terjadwal'
            ];

            $result = $diskonRepo->update($id, $data);
            echo json_encode($result);
            break;

        case 'delete':
            if (!PermissionHelper::hasPermission('manage_product_discounts') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola diskon');
            }

            $id = $_POST['id'] ?? $_GET['id'] ?? '';
            if (empty($id)) {
                throw new Exception('ID diskon tidak valid');
            }

            $result = $diskonRepo->delete($id);
            echo json_encode($result);
            break;

        case 'mass_create':
            if (!PermissionHelper::hasPermission('manage_product_discounts') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola diskon');
            }

            error_log('Mass create diskon - POST data: ' . json_encode($_POST));

            $productIds = $_POST['product_ids'] ?? [];
            if (is_string($productIds)) {
                $productIds = json_decode($productIds, true) ?? [];
            }

            error_log('Mass create diskon - Product IDs: ' . json_encode($productIds));

            if (empty($productIds)) {
                throw new Exception('Pilih minimal satu produk');
            }

            $discountData = [
                'id_kampanye' => !empty($_POST['id_kampanye']) ? $_POST['id_kampanye'] : null,
                'label' => $_POST['label'] ?? '',
                'jenis' => $_POST['jenis'] ?? 'persen',
                'nilai' => $_POST['nilai'] ?? 0,
                'stok_promo' => !empty($_POST['stok_promo']) ? $_POST['stok_promo'] : null,
                'maks_qty_per_pengguna' => !empty($_POST['maks_qty_per_pengguna']) ? $_POST['maks_qty_per_pengguna'] : null,
                'mulai_pada' => !empty($_POST['mulai_pada']) ? $_POST['mulai_pada'] : null,
                'selesai_pada' => !empty($_POST['selesai_pada']) ? $_POST['selesai_pada'] : null,
                'status' => $_POST['status'] ?? 'terjadwal'
            ];

            error_log('Mass create diskon - Discount data: ' . json_encode($discountData));

            $result = $diskonRepo->massCreate($productIds, $discountData);
            error_log('Mass create diskon - Result: ' . json_encode($result));
            echo json_encode($result);
            break;

        case 'get':
            $id = $_GET['id'] ?? '';
            if (empty($id)) {
                throw new Exception('ID diskon tidak valid');
            }

            $diskon = $diskonRepo->getById($id);
            if (!$diskon) {
                throw new Exception('Diskon tidak ditemukan');
            }

            echo json_encode(['success' => true, 'data' => $diskon]);
            break;

        case 'list':
            $filters = [
                'status' => $_GET['status'] ?? '',
                'jenis' => $_GET['jenis'] ?? '',
                'id_kampanye' => $_GET['id_kampanye'] ?? '',
                'id_produk' => $_GET['id_produk'] ?? '',
                'search' => $_GET['search'] ?? '',
                'sort' => $_GET['sort'] ?? 'newest'
            ];

            $diskons = $diskonRepo->getAll($filters);
            echo json_encode(['success' => true, 'data' => $diskons]);
            break;

        case 'get_by_product':
            $productId = $_GET['product_id'] ?? '';
            if (empty($productId)) {
                throw new Exception('ID produk tidak valid');
            }

            $diskons = $diskonRepo->getActiveDiscountsByProduct($productId);
            echo json_encode(['success' => true, 'data' => $diskons]);
            break;

        case 'get_campaigns':
            $campaigns = $campaignRepo->getAll(['status' => 'aktif']);
            echo json_encode(['success' => true, 'data' => $campaigns]);
            break;

        case 'get_products':
            $search = $_GET['search'] ?? '';
            $products = $productRepo->getAll(['search' => $search, 'status' => 'tersedia']);
            echo json_encode(['success' => true, 'data' => $products]);
            break;

        default:
            throw new Exception('Aksi tidak valid');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
