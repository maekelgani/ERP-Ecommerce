<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Database/DatabaseConnection.php';
require_once __DIR__ . '/../Services/pengeluaranServices.php';

use App\Repository\ReportRepository;
use App\Database\DatabaseConnection;
use App\Services\PengeluaranServices;

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$period = $_GET['period'] ?? 'month';
$format = $_GET['format'] ?? 'json';
if ($format === 'excel') {
    $format = 'csv';
}

$reportRepo = new ReportRepository();

try {
    switch ($action) {
        case 'sales':
            $data = $reportRepo->getSalesReport($period);
            if ($format === 'csv') {
                exportSalesCSV($data);
            } else {
                echo json_encode(['success' => true, 'data' => $data]);
            }
            break;

        case 'orders':
            $data = $reportRepo->getOrdersReport($period);
            if ($format === 'csv') {
                exportOrdersCSV($data);
            } else {
                echo json_encode(['success' => true, 'data' => $data]);
            }
            break;

        case 'customers':
            $data = $reportRepo->getCustomersReport($period);
            if ($format === 'csv') {
                exportCustomersCSV($data);
            } else {
                echo json_encode(['success' => true, 'data' => $data]);
            }
            break;

        case 'inventory':
            $data = $reportRepo->getInventoryReport();
            if ($format === 'csv') {
                exportInventoryCSV($data);
            } else {
                echo json_encode(['success' => true, 'data' => $data]);
            }
            break;

        case 'daily_sales':
            $data = $reportRepo->getDailySales($period);
            echo json_encode(['success' => true, 'data' => $data]);
            break;

        case 'top_products':
            $limit = (int)($_GET['limit'] ?? 10);
            $data = $reportRepo->getTopProducts($period, $limit);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
        
        case 'financial_summary':

            $salesData = $reportRepo->getSalesReport($period);
            
            $totalRevenue = $salesData['summary']['total_revenue'] ?? 0;

            $db = DatabaseConnection::getInstance()->getConnection();
            $expenseModel = new PengeluaranServices($db);
            
            $totalExpense = $expenseModel->getTotalByPeriod($period);

            $netProfit = $totalRevenue - $totalExpense;

            echo json_encode([
                'success' => true,
                'data' => [
                    'revenue'    => (float)$totalRevenue,
                    'expense'    => (float)$totalExpense,
                    'net_profit' => (float)$netProfit
                ]
            ]);
            break;
            
        case 'export_pdf':
            $type = $_GET['type'] ?? 'sales';
            exportPDF($type, $period, $reportRepo);
            break;

        case 'export_excel':
            $type = $_GET['type'] ?? 'sales';
            exportExcel($type, $period, $reportRepo);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function exportSalesCSV($data)
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan_penjualan_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, ['LAPORAN PENJUALAN - NANO KOMPUTER']);
    fputcsv($output, ['Tanggal Export: ' . date('d/m/Y H:i')]);
    fputcsv($output, ['Periode: ' . getPeriodLabel($data['period'])]);
    fputcsv($output, []);

    fputcsv($output, ['RINGKASAN']);
    fputcsv($output, ['Total Order', $data['summary']['total_orders']]);
    fputcsv($output, ['Total Pendapatan', 'Rp ' . number_format($data['summary']['total_revenue'], 0, ',', '.')]);
    fputcsv($output, ['Total Diskon', 'Rp ' . number_format($data['summary']['total_discount'], 0, ',', '.')]);
    fputcsv($output, ['Rata-rata Nilai Order', 'Rp ' . number_format($data['summary']['avg_order_value'], 0, ',', '.')]);
    fputcsv($output, []);

    fputcsv($output, ['DETAIL PESANAN']);
    fputcsv($output, ['ID Order', 'Tanggal', 'Pelanggan', 'Subtotal', 'Diskon', 'Ongkir', 'Total', 'Status', 'Metode Bayar']);

    foreach ($data['orders'] as $order) {
        fputcsv($output, [
            $order['id_order'],
            date('d/m/Y H:i', strtotime($order['tanggal_order'])),
            $order['customer_name'],
            $order['total_harga'],
            $order['total_diskon'],
            $order['total_ongkir'],
            $order['total_bayar'],
            ucfirst($order['status_order']),
            $order['nama_bank'] ?? $order['metode_pembayaran']
        ]);
    }

    fputcsv($output, []);
    fputcsv($output, ['PENJUALAN PER KATEGORI']);
    fputcsv($output, ['Kategori', 'Qty Terjual', 'Pendapatan']);

    foreach ($data['categories'] as $cat) {
        fputcsv($output, [
            $cat['nama_kategori'],
            $cat['qty_sold'],
            'Rp ' . number_format($cat['revenue'], 0, ',', '.')
        ]);
    }

    fclose($output);
    exit;
}

function exportOrdersCSV($data)
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan_pembelian_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, ['LAPORAN PEMBELIAN - NANO KOMPUTER']);
    fputcsv($output, ['Tanggal Export: ' . date('d/m/Y H:i')]);
    fputcsv($output, ['Periode: ' . getPeriodLabel($data['period'])]);
    fputcsv($output, []);

    fputcsv($output, ['ID Order', 'Tanggal', 'Pelanggan', 'Email', 'Telepon', 'Subtotal', 'Diskon', 'Ongkir', 'Packing', 'Total', 'Status Order', 'Metode Kirim', 'Pembayaran', 'Status Bayar', 'No Resi']);

    foreach ($data['orders'] as $order) {
        fputcsv($output, [
            $order['id_order'],
            date('d/m/Y H:i', strtotime($order['tanggal_order'])),
            $order['customer_name'],
            $order['customer_email'],
            $order['customer_phone'] ?? '-',
            $order['total_harga'],
            $order['total_diskon'],
            $order['total_ongkir'],
            $order['biaya_packing'] ?? 0,
            $order['total_bayar'],
            ucfirst($order['status_order']),
            $order['shipping_method'],
            $order['nama_bank'] ?? $order['metode_pembayaran'] ?? '-',
            $order['status_pembayaran'] ?? '-',
            // $order['nama_kurir'] ?? '-',
            $order['no_resi'] ?? '-'
        ]);
    }

    fclose($output);
    exit;
}

function exportCustomersCSV($data)
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan_pelanggan_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, ['LAPORAN PELANGGAN - NANO KOMPUTER']);
    fputcsv($output, ['Tanggal Export: ' . date('d/m/Y H:i')]);
    fputcsv($output, ['Periode: ' . getPeriodLabel($data['period'])]);
    fputcsv($output, []);

    fputcsv($output, ['RINGKASAN']);
    fputcsv($output, ['Total Pelanggan', $data['summary']['total_customers']]);
    fputcsv($output, ['User Google', $data['summary']['google_users']]);
    fputcsv($output, ['User Regular', $data['summary']['regular_users']]);
    fputcsv($output, ['User Aktif', $data['summary']['active_users']]);
    fputcsv($output, []);

    fputcsv($output, ['DETAIL PELANGGAN']);
    fputcsv($output, ['ID', 'Nama', 'Email', 'Telepon', 'Tipe Login', 'Status', 'Tanggal Daftar', 'Total Order', 'Total Belanja']);

    foreach ($data['customers'] as $customer) {
        fputcsv($output, [
            $customer['id_customer'],
            $customer['nama_lengkap'],
            $customer['email'],
            $customer['no_telp'] ?? '-',
            ucfirst($customer['login_type']),
            $customer['is_active'] ? 'Aktif' : 'Nonaktif',
            date('d/m/Y', strtotime($customer['created_at'])),
            $customer['total_orders'],
            'Rp ' . number_format($customer['total_spent'], 0, ',', '.')
        ]);
    }

    fclose($output);
    exit;
}

function exportInventoryCSV($data)
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan_persediaan_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, ['LAPORAN PERSEDIAAN - NANO KOMPUTER']);
    fputcsv($output, ['Tanggal Export: ' . date('d/m/Y H:i')]);
    fputcsv($output, []);

    fputcsv($output, ['RINGKASAN']);
    fputcsv($output, ['Total Produk', $data['summary']['total_products']]);
    fputcsv($output, ['Total Stok', $data['summary']['total_stock']]);
    fputcsv($output, ['Nilai Stok', 'Rp ' . number_format($data['summary']['total_stock_value'], 0, ',', '.')]);
    fputcsv($output, ['Stok Habis', $data['summary']['out_of_stock']]);
    fputcsv($output, ['Stok Menipis', $data['summary']['low_stock']]);
    fputcsv($output, []);

    fputcsv($output, ['DETAIL PRODUK']);
    fputcsv($output, ['ID', 'Nama Produk', 'Kategori', 'Brand', 'Harga', 'Stok', 'Terjual', 'Nilai Stok', 'Status']);

    foreach ($data['products'] as $product) {
        fputcsv($output, [
            $product['id_product'],
            $product['nama_product'],
            $product['nama_kategori'] ?? '-',
            $product['nama_brand'] ?? '-',
            'Rp ' . number_format($product['harga'], 0, ',', '.'),
            $product['stok'],
            $product['total_sold'],
            'Rp ' . number_format($product['stock_value'], 0, ',', '.'),
            ucfirst($product['status_produk'])
        ]);
    }

    fclose($output);
    exit;
}

function exportPDF($type, $period, $reportRepo)
{
    switch ($type) {
        case 'sales':
            $data = $reportRepo->getSalesReport($period);
            $title = 'Laporan Penjualan';
            break;
        case 'orders':
            $data = $reportRepo->getOrdersReport($period);
            $title = 'Laporan Pembelian';
            break;
        case 'customers':
            $data = $reportRepo->getCustomersReport($period);
            $title = 'Laporan Pelanggan';
            break;
        case 'inventory':
            $data = $reportRepo->getInventoryReport();
            $title = 'Laporan Persediaan';
            break;
        default:
            $data = $reportRepo->getSalesReport($period);
            $title = 'Laporan Penjualan';
    }

    $periodLabel = getPeriodLabel($period);

    header('Content-Type: text/html; charset=utf-8');
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($title) ?> - Nano Komputer</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Arial, sans-serif;
                font-size: 11px;
                color: #333;
                padding: 20px;
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #882426;
                padding-bottom: 15px;
            }

            .header h1 {
                color: #882426;
                font-size: 24px;
                margin-bottom: 5px;
            }

            .header p {
                color: #666;
            }

            .summary-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
                margin-bottom: 20px;
            }

            .summary-card {
                background: #f8f8f8;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 15px;
                text-align: center;
            }

            .summary-card h3 {
                color: #882426;
                font-size: 20px;
                margin-bottom: 5px;
            }

            .summary-card p {
                color: #666;
                font-size: 10px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                font-size: 10px;
            }

            th {
                background: #882426;
                color: white;
                padding: 8px 5px;
                text-align: left;
            }

            td {
                padding: 6px 5px;
                border-bottom: 1px solid #eee;
            }

            tr:nth-child(even) {
                background: #f9f9f9;
            }

            .section-title {
                color: #882426;
                font-size: 14px;
                margin: 20px 0 10px;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5px;
            }

            .footer {
                text-align: center;
                margin-top: 20px;
                padding-top: 10px;
                border-top: 1px solid #ddd;
                color: #666;
                font-size: 10px;
            }

            .text-right {
                text-align: right;
            }

            .badge {
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 9px;
            }

            .badge-success {
                background: #d4edda;
                color: #155724;
            }

            .badge-warning {
                background: #fff3cd;
                color: #856404;
            }

            .badge-danger {
                background: #f8d7da;
                color: #721c24;
            }

            @media print {
                body {
                    padding: 0;
                }

                .no-print {
                    display: none;
                }
            }
        </style>
    </head>

    <body>
        <div class="no-print" style="margin-bottom: 20px; text-align: center;">
            <button onclick="window.print()" style="background: #882426; color: white; padding: 10px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                Cetak / Simpan PDF
            </button>
        </div>

        <div class="header">
            <h1>NANO KOMPUTER</h1>
            <p style="font-size: 16px; margin-top: 10px; font-weight: bold;"><?= htmlspecialchars($title) ?></p>
            <p>Periode: <?= htmlspecialchars($periodLabel) ?> | Dicetak: <?= date('d/m/Y H:i') ?></p>
        </div>

        <?php if ($type === 'sales'): ?>
            <div class="summary-grid">
                <div class="summary-card">
                    <h3><?= number_format($data['summary']['total_orders']) ?></h3>
                    <p>Total Order</p>
                </div>
                <div class="summary-card">
                    <h3>Rp <?= number_format($data['summary']['total_revenue'], 0, ',', '.') ?></h3>
                    <p>Total Pendapatan</p>
                </div>
                <div class="summary-card">
                    <h3>Rp <?= number_format($data['summary']['total_discount'], 0, ',', '.') ?></h3>
                    <p>Total Diskon</p>
                </div>
                <div class="summary-card">
                    <h3>Rp <?= number_format($data['summary']['avg_order_value'], 0, ',', '.') ?></h3>
                    <p>Rata-rata Order</p>
                </div>
            </div>

            <h2 class="section-title">Detail Pesanan</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Order</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Diskon</th>
                        <th class="text-right">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['orders'] as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id_order']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['tanggal_order'])) ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td class="text-right">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($order['total_diskon'], 0, ',', '.') ?></td>
                            <td class="text-right"><strong>Rp <?= number_format($order['total_bayar'], 0, ',', '.') ?></strong></td>
                            <td><span class="badge badge-success"><?= ucfirst($order['status_order']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2 class="section-title">Penjualan per Kategori</h2>
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th class="text-right">Qty Terjual</th>
                        <th class="text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['categories'] as $cat): ?>
                        <tr>
                            <td><?= htmlspecialchars($cat['nama_kategori']) ?></td>
                            <td class="text-right"><?= number_format($cat['qty_sold']) ?> unit</td>
                            <td class="text-right">Rp <?= number_format($cat['revenue'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($type === 'orders'): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Order</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th class="text-right">Total</th>
                        <th>Status Order</th>
                        <th>Pembayaran</th>
                        <th>No Resi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['orders'] as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id_order']) ?></td>
                            <td><?= date('d/m/Y', strtotime($order['tanggal_order'])) ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td class="text-right">Rp <?= number_format($order['total_bayar'], 0, ',', '.') ?></td>
                            <td><span class="badge badge-success"><?= ucfirst($order['status_order']) ?></span></td>
                            <td><?= htmlspecialchars($order['nama_bank'] ?? $order['metode_pembayaran'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($order['no_resi'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($type === 'customers'): ?>
            <div class="summary-grid">
                <div class="summary-card">
                    <h3><?= number_format($data['summary']['total_customers']) ?></h3>
                    <p>Total Pelanggan</p>
                </div>
                <div class="summary-card">
                    <h3><?= number_format($data['summary']['active_users']) ?></h3>
                    <p>Pelanggan Aktif</p>
                </div>
                <div class="summary-card">
                    <h3><?= number_format($data['summary']['google_users']) ?></h3>
                    <p>User Google</p>
                </div>
                <div class="summary-card">
                    <h3><?= number_format($data['summary']['regular_users']) ?></h3>
                    <p>User Regular</p>
                </div>
            </div>

            <h2 class="section-title">Detail Pelanggan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Tipe</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-right">Total Order</th>
                        <th class="text-right">Total Belanja</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['customers'] as $customer): ?>
                        <tr>
                            <td><?= htmlspecialchars($customer['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($customer['email']) ?></td>
                            <td><?= htmlspecialchars($customer['no_telp'] ?? '-') ?></td>
                            <td><?= ucfirst($customer['login_type']) ?></td>
                            <td><?= date('d/m/Y', strtotime($customer['created_at'])) ?></td>
                            <td class="text-right"><?= $customer['total_orders'] ?></td>
                            <td class="text-right">Rp <?= number_format($customer['total_spent'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($type === 'inventory'): ?>
            <div class="summary-grid">
                <div class="summary-card">
                    <h3><?= number_format($data['summary']['total_products']) ?></h3>
                    <p>Total Produk</p>
                </div>
                <div class="summary-card">
                    <h3><?= number_format($data['summary']['total_stock']) ?></h3>
                    <p>Total Stok</p>
                </div>
                <div class="summary-card">
                    <h3>Rp <?= number_format($data['summary']['total_stock_value'], 0, ',', '.') ?></h3>
                    <p>Nilai Stok</p>
                </div>
                <div class="summary-card">
                    <h3><?= number_format($data['summary']['out_of_stock']) ?></h3>
                    <p>Stok Habis</p>
                </div>
            </div>

            <h2 class="section-title">Detail Produk</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Stok</th>
                        <th class="text-right">Terjual</th>
                        <th class="text-right">Nilai Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['products'] as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars(substr($product['nama_product'], 0, 40)) ?><?= strlen($product['nama_product']) > 40 ? '...' : '' ?></td>
                            <td><?= htmlspecialchars($product['nama_kategori'] ?? '-') ?></td>
                            <td class="text-right">Rp <?= number_format($product['harga'], 0, ',', '.') ?></td>
                            <td class="text-right"><?= $product['stok'] ?></td>
                            <td class="text-right"><?= $product['total_sold'] ?></td>
                            <td class="text-right">Rp <?= number_format($product['stock_value'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($product['stok'] == 0): ?>
                                    <span class="badge badge-danger">Habis</span>
                                <?php elseif ($product['stok'] <= 10): ?>
                                    <span class="badge badge-warning">Menipis</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Tersedia</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="footer">
            <p>Nano Komputer &copy; <?= date('Y') ?> - Laporan ini digenerate secara otomatis</p>
        </div>

        <script>
            window.onafterprint = function() {
                window.close();
            };
        </script>
    </body>

    </html>
<?php
    exit;
}

function exportExcel($type, $period, $reportRepo)
{
    switch ($type) {
        case 'sales':
            $data = $reportRepo->getSalesReport($period);
            exportSalesCSV($data);
            break;
        case 'orders':
            $data = $reportRepo->getOrdersReport($period);
            exportOrdersCSV($data);
            break;
        case 'customers':
            $data = $reportRepo->getCustomersReport($period);
            exportCustomersCSV($data);
            break;
        case 'inventory':
            $data = $reportRepo->getInventoryReport();
            exportInventoryCSV($data);
            break;
        default:
            $data = $reportRepo->getSalesReport($period);
            exportSalesCSV($data);
    }
}

function getPeriodLabel($period)
{
    $labels = [
        'today' => 'Hari Ini',
        'week' => 'Minggu Ini',
        'month' => 'Bulan Ini',
        'last_month' => 'Bulan Lalu',
        '6months' => '6 Bulan Terakhir',
        'year' => 'Tahun Ini'
    ];
    return $labels[$period] ?? $period;
}
