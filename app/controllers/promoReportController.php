<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Repository/PromoCampaignRepository.php';
require_once __DIR__ . '/../Repository/DiskonRepository.php';
require_once __DIR__ . '/../Repository/VoucherRepository.php';
require_once __DIR__ . '/../Repository/VoucherUsageRepository.php';

use App\Repository\PromoCampaignRepository;
use App\Repository\DiskonRepository;
use App\Repository\VoucherRepository;
use App\Repository\VoucherUsageRepository;
use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Database\DatabaseConnection;

header('Content-Type: application/json');

AuthMiddleware::requireAdminLogin();

if (!PermissionHelper::hasPermission('view_promo_reports') && !PermissionHelper::isSuperAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses untuk melihat laporan']);
    exit;
}

$campaignRepo = new PromoCampaignRepository();
$diskonRepo = new DiskonRepository();
$voucherRepo = new VoucherRepository();
$usageRepo = new VoucherUsageRepository();
$db = DatabaseConnection::getInstance()->getConnection();

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'dashboard':
            $stats = [
                'campaigns' => [
                    'total' => $campaignRepo->count(),
                    'active' => $campaignRepo->countByStatus('aktif'),
                    'draft' => $campaignRepo->countByStatus('draf'),
                    'ended' => $campaignRepo->countByStatus('berakhir')
                ],
                'discounts' => [
                    'total' => $diskonRepo->count(),
                    'active' => $diskonRepo->countByStatus('aktif'),
                    'scheduled' => $diskonRepo->countByStatus('terjadwal')
                ],
                'vouchers' => [
                    'total' => $voucherRepo->count(),
                    'active' => $voucherRepo->countByStatus('aktif'),
                    'scheduled' => $voucherRepo->countByStatus('terjadwal')
                ],
                'recent_usage' => $usageRepo->getRecentUsage(10),
                'top_vouchers' => $usageRepo->getTopVouchers(5)
            ];

            echo json_encode(['success' => true, 'data' => $stats]);
            break;

        case 'usage_stats':
            $period = $_GET['period'] ?? 'daily';
            $stats = $usageRepo->getUsageStats($period);
            echo json_encode(['success' => true, 'data' => $stats]);
            break;

        case 'campaign_performance':
            $campaignId = $_GET['campaign_id'] ?? '';
            
            $stmt = $db->prepare("
                SELECT 
                    pk.id_kampanye,
                    pk.judul,
                    pk.tipe,
                    pk.status,
                    pk.kuota_total,
                    pk.kuota_terpakai,
                    COUNT(DISTINCT kp.id_produk) as total_produk,
                    COALESCE(SUM(d.stok_terpakai), 0) as total_diskon_digunakan
                FROM promo_kampanye pk
                LEFT JOIN kampanye_produk kp ON pk.id_kampanye = kp.id_kampanye
                LEFT JOIN diskon d ON pk.id_kampanye = d.id_kampanye
                WHERE (:campaign_id = '' OR pk.id_kampanye = :campaign_id2)
                GROUP BY pk.id_kampanye, pk.judul, pk.tipe, pk.status, pk.kuota_total, pk.kuota_terpakai
                ORDER BY pk.dibuat_pada DESC
            ");
            $stmt->execute(['campaign_id' => $campaignId, 'campaign_id2' => $campaignId]);
            $data = $stmt->fetchAll();

            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'voucher_performance':
            $voucherId = $_GET['voucher_id'] ?? '';

            $stmt = $db->prepare("
                SELECT 
                    v.id_voucher,
                    v.kode,
                    v.judul,
                    v.jenis,
                    v.nilai,
                    v.kuota_total,
                    v.kuota_terpakai,
                    COUNT(pv.id_penggunaan) as total_penggunaan,
                    COALESCE(SUM(pv.jumlah_diskon), 0) as total_diskon
                FROM voucher v
                LEFT JOIN penggunaan_voucher pv ON v.id_voucher = pv.id_voucher
                WHERE (:voucher_id = '' OR v.id_voucher = :voucher_id2)
                GROUP BY v.id_voucher, v.kode, v.judul, v.jenis, v.nilai, v.kuota_total, v.kuota_terpakai
                ORDER BY total_penggunaan DESC
            ");
            $stmt->execute(['voucher_id' => $voucherId, 'voucher_id2' => $voucherId]);
            $data = $stmt->fetchAll();

            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'redemption_log':
            $limit = (int)($_GET['limit'] ?? 50);
            $offset = (int)($_GET['offset'] ?? 0);
            $dateFrom = $_GET['date_from'] ?? '';
            $dateTo = $_GET['date_to'] ?? '';

            $sql = "
                SELECT 
                    pv.id_penggunaan,
                    pv.digunakan_pada,
                    pv.jumlah_diskon,
                    pv.id_pesanan,
                    v.kode,
                    v.judul as nama_voucher,
                    c.nama_lengkap as nama_pelanggan
                FROM penggunaan_voucher pv
                LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
                LEFT JOIN customers c ON pv.id_pengguna = c.id_customer
                WHERE 1=1
            ";
            $params = [];

            if ($dateFrom) {
                $sql .= " AND pv.digunakan_pada >= :date_from";
                $params['date_from'] = $dateFrom;
            }
            if ($dateTo) {
                $sql .= " AND pv.digunakan_pada <= :date_to";
                $params['date_to'] = $dateTo . ' 23:59:59';
            }

            $sql .= " ORDER BY pv.digunakan_pada DESC LIMIT :limit OFFSET :offset";

            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll();

            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'summary_report':
            $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
            $dateTo = $_GET['date_to'] ?? date('Y-m-d');

            $stmt = $db->prepare("
                SELECT 
                    COUNT(*) as total_redemptions,
                    COALESCE(SUM(jumlah_diskon), 0) as total_discount_given,
                    COUNT(DISTINCT id_voucher) as unique_vouchers_used,
                    COUNT(DISTINCT id_pengguna) as unique_users
                FROM penggunaan_voucher
                WHERE digunakan_pada BETWEEN :date_from AND :date_to
            ");
            $stmt->execute(['date_from' => $dateFrom, 'date_to' => $dateTo . ' 23:59:59']);
            $summary = $stmt->fetch();

            $stmt2 = $db->prepare("
                SELECT 
                    v.jenis,
                    COUNT(*) as count,
                    COALESCE(SUM(pv.jumlah_diskon), 0) as total_discount
                FROM penggunaan_voucher pv
                JOIN voucher v ON pv.id_voucher = v.id_voucher
                WHERE pv.digunakan_pada BETWEEN :date_from AND :date_to
                GROUP BY v.jenis
            ");
            $stmt2->execute(['date_from' => $dateFrom, 'date_to' => $dateTo . ' 23:59:59']);
            $byType = $stmt2->fetchAll();

            echo json_encode([
                'success' => true,
                'data' => [
                    'summary' => $summary,
                    'by_type' => $byType,
                    'period' => ['from' => $dateFrom, 'to' => $dateTo]
                ]
            ]);
            break;

        case 'export_report':
            $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
            $dateTo = $_GET['date_to'] ?? date('Y-m-d');

            $stmt = $db->prepare("
                SELECT 
                    pv.digunakan_pada,
                    v.kode,
                    v.judul,
                    v.jenis,
                    pv.jumlah_diskon,
                    c.nama_lengkap as pelanggan,
                    pv.id_pesanan
                FROM penggunaan_voucher pv
                LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
                LEFT JOIN customers c ON pv.id_pengguna = c.id_customer
                WHERE pv.digunakan_pada BETWEEN :date_from AND :date_to
                ORDER BY pv.digunakan_pada DESC
            ");
            $stmt->execute(['date_from' => $dateFrom, 'date_to' => $dateTo . ' 23:59:59']);
            $data = $stmt->fetchAll();

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=promo_report_' . $dateFrom . '_to_' . $dateTo . '.csv');

            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, ['Tanggal', 'Kode Voucher', 'Nama Voucher', 'Jenis', 'Jumlah Diskon', 'Pelanggan', 'ID Pesanan']);

            foreach ($data as $row) {
                fputcsv($output, [
                    $row['digunakan_pada'],
                    $row['kode'],
                    $row['judul'],
                    $row['jenis'],
                    $row['jumlah_diskon'],
                    $row['pelanggan'],
                    $row['id_pesanan']
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
