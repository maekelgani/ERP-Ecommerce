<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/VoucherRepository.php';
require_once __DIR__ . '/../Repository/VoucherUsageRepository.php';

use App\Repository\VoucherRepository;
use App\Repository\VoucherUsageRepository;
use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;

header('Content-Type: application/json');

$voucherRepo = new VoucherRepository();
$usageRepo = new VoucherUsageRepository();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if (in_array($action, ['create', 'update', 'delete', 'list', 'get', 'export', 'generate_code', 'get_usage'])) {
        AuthMiddleware::requireAdminLogin();
    }

    switch ($action) {
        case 'create':
            if (!PermissionHelper::hasPermission('manage_vouchers') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola voucher');
            }

            $data = [
                'kode' => $_POST['kode'] ?? '',
                'judul' => $_POST['judul'] ?? '',
                'deskripsi' => $_POST['deskripsi'] ?? '',
                'jenis' => $_POST['jenis'] ?? 'diskon_persen',
                'nilai' => $_POST['nilai'] ?? 0,
                'minimal_belanja' => $_POST['minimal_belanja'] ?? 0,
                'maksimal_diskon' => $_POST['maksimal_diskon'] ?? null,
                'mulai_pada' => $_POST['mulai_pada'] ?? null,
                'selesai_pada' => $_POST['selesai_pada'] ?? null,
                'kuota_total' => $_POST['kuota_total'] ?? null,
                'kuota_per_pengguna' => $_POST['kuota_per_pengguna'] ?? null,
                'terbatas_produk' => $_POST['terbatas_produk'] ?? null,
                'terbatas_kategori' => $_POST['terbatas_kategori'] ?? null,
                'status' => $_POST['status'] ?? 'terjadwal'
            ];

            if (!empty($data['terbatas_produk']) && is_string($data['terbatas_produk'])) {
                $data['terbatas_produk'] = json_decode($data['terbatas_produk'], true);
            }
            if (!empty($data['terbatas_kategori']) && is_string($data['terbatas_kategori'])) {
                $data['terbatas_kategori'] = json_decode($data['terbatas_kategori'], true);
            }

            $result = $voucherRepo->create($data);
            echo json_encode($result);
            break;

        case 'update':
            if (!PermissionHelper::hasPermission('manage_vouchers') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola voucher');
            }

            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                throw new Exception('ID voucher tidak valid');
            }

            $data = [
                'kode' => $_POST['kode'] ?? '',
                'judul' => $_POST['judul'] ?? '',
                'deskripsi' => $_POST['deskripsi'] ?? '',
                'jenis' => $_POST['jenis'] ?? 'diskon_persen',
                'nilai' => $_POST['nilai'] ?? 0,
                'minimal_belanja' => $_POST['minimal_belanja'] ?? 0,
                'maksimal_diskon' => $_POST['maksimal_diskon'] ?? null,
                'mulai_pada' => $_POST['mulai_pada'] ?? null,
                'selesai_pada' => $_POST['selesai_pada'] ?? null,
                'kuota_total' => $_POST['kuota_total'] ?? null,
                'kuota_per_pengguna' => $_POST['kuota_per_pengguna'] ?? null,
                'terbatas_produk' => $_POST['terbatas_produk'] ?? null,
                'terbatas_kategori' => $_POST['terbatas_kategori'] ?? null,
                'status' => $_POST['status'] ?? 'terjadwal'
            ];

            if (!empty($data['terbatas_produk']) && is_string($data['terbatas_produk'])) {
                $data['terbatas_produk'] = json_decode($data['terbatas_produk'], true);
            }
            if (!empty($data['terbatas_kategori']) && is_string($data['terbatas_kategori'])) {
                $data['terbatas_kategori'] = json_decode($data['terbatas_kategori'], true);
            }

            $result = $voucherRepo->update($id, $data);
            echo json_encode($result);
            break;

        case 'delete':
            if (!PermissionHelper::hasPermission('manage_vouchers') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengelola voucher');
            }

            $id = $_POST['id'] ?? $_GET['id'] ?? '';
            if (empty($id)) {
                throw new Exception('ID voucher tidak valid');
            }

            $result = $voucherRepo->delete($id);
            echo json_encode($result);
            break;

        case 'get':
            $id = $_GET['id'] ?? '';
            if (empty($id)) {
                throw new Exception('ID voucher tidak valid');
            }

            $voucher = $voucherRepo->getById($id);
            if (!$voucher) {
                throw new Exception('Voucher tidak ditemukan');
            }

            echo json_encode(['success' => true, 'data' => $voucher]);
            break;

        case 'list':
            $filters = [
                'status' => $_GET['status'] ?? '',
                'jenis' => $_GET['jenis'] ?? '',
                'search' => $_GET['search'] ?? '',
                'sort' => $_GET['sort'] ?? 'newest'
            ];

            $vouchers = $voucherRepo->getAll($filters);
            echo json_encode(['success' => true, 'data' => $vouchers]);
            break;

        case 'validate':
            $code = $_POST['code'] ?? $_GET['code'] ?? '';
            $cartTotal = (float)($_POST['cart_total'] ?? $_GET['cart_total'] ?? 0);
            $userId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
            $productIds = $_POST['product_ids'] ?? $_GET['product_ids'] ?? [];
            $categoryIds = $_POST['category_ids'] ?? $_GET['category_ids'] ?? [];

            if (is_string($productIds)) {
                $productIds = json_decode($productIds, true) ?? [];
            }
            if (is_string($categoryIds)) {
                $categoryIds = json_decode($categoryIds, true) ?? [];
            }

            $result = $voucherRepo->validateVoucher($code, $userId, $cartTotal, $productIds, $categoryIds);
            echo json_encode($result);
            break;

        case 'generate_code':
            $length = (int)($_GET['length'] ?? 8);
            $code = $voucherRepo->generateCode($length);
            echo json_encode(['success' => true, 'code' => $code]);
            break;

        case 'get_usage':
            $voucherId = $_GET['voucher_id'] ?? '';
            if (empty($voucherId)) {
                throw new Exception('ID voucher tidak valid');
            }

            $usage = $usageRepo->getUsageByVoucher($voucherId);
            $totalDiscount = $usageRepo->getTotalDiscountByVoucher($voucherId);

            echo json_encode([
                'success' => true,
                'data' => [
                    'usage' => $usage,
                    'total_count' => count($usage),
                    'total_discount' => $totalDiscount
                ]
            ]);
            break;

        case 'export':
            if (!PermissionHelper::hasPermission('view_promo_reports') && !PermissionHelper::isSuperAdmin()) {
                throw new Exception('Anda tidak memiliki akses untuk mengekspor data');
            }

            $vouchers = $voucherRepo->getAll($_GET);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=vouchers_' . date('Y-m-d') . '.csv');

            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, ['Kode', 'Judul', 'Jenis', 'Nilai', 'Min. Belanja', 'Maks. Diskon', 'Kuota', 'Terpakai', 'Status', 'Mulai', 'Selesai']);

            foreach ($vouchers as $v) {
                fputcsv($output, [
                    $v['kode'],
                    $v['judul'],
                    $v['jenis'],
                    $v['nilai'],
                    $v['minimal_belanja'],
                    $v['maksimal_diskon'],
                    $v['kuota_total'],
                    $v['kuota_terpakai'],
                    $v['status'],
                    $v['mulai_pada'],
                    $v['selesai_pada']
                ]);
            }

            fclose($output);
            exit;

        default:
            throw new Exception('Aksi tidak valid');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
