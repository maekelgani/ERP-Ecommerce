<?php
require_once __DIR__ . '/../../config/config.php';

use App\Database\DatabaseConnection;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo 'Silakan login terlebih dahulu';
    exit;
}

$customerId = $_SESSION['customer_id'];
$orderId = $_GET['order_id'] ?? '';

if (empty($orderId)) {
    http_response_code(400);
    echo 'Order ID diperlukan';
    exit;
}

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    $orderQuery = $db->prepare("
        SELECT o.*, c.nama_lengkap as customer_name, c.email as customer_email, c.no_telp as customer_phone
        FROM orders o
        LEFT JOIN customers c ON o.id_customer = c.id_customer
        WHERE o.id_order = :order_id AND o.id_customer = :customer_id
    ");
    $orderQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
    $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);
        echo 'Pesanan tidak ditemukan';
        exit;
    }

    if (!in_array($order['status_order'], ['selesai', 'dikirim', 'dikonfirmasi', 'diproses'])) {
        http_response_code(400);
        echo 'Invoice hanya tersedia untuk pesanan yang sudah dibayar';
        exit;
    }

    $detailQuery = $db->prepare("
        SELECT od.*
        FROM order_detail od
        WHERE od.id_order = :order_id
    ");
    $detailQuery->execute([':order_id' => $orderId]);
    $orderDetails = $detailQuery->fetchAll(PDO::FETCH_ASSOC);

    $paymentQuery = $db->prepare("SELECT * FROM payment WHERE id_order = :order_id LIMIT 1");
    $paymentQuery->execute([':order_id' => $orderId]);
    $payment = $paymentQuery->fetch(PDO::FETCH_ASSOC);

    $shipmentQuery = $db->prepare("SELECT * FROM shipment WHERE id_order = :order_id LIMIT 1");
    $shipmentQuery->execute([':order_id' => $orderId]);
    $shipment = $shipmentQuery->fetch(PDO::FETCH_ASSOC);

    $address = null;
    if ($shipment && !empty($shipment['id_alamat'])) {
        $addressQuery = $db->prepare("SELECT * FROM address_book WHERE id_alamat = :id_alamat");
        $addressQuery->execute([':id_alamat' => $shipment['id_alamat']]);
        $address = $addressQuery->fetch(PDO::FETCH_ASSOC);
    }

    if (!$address) {
        $defaultAddressQuery = $db->prepare("SELECT * FROM address_book WHERE id_customer = :customer_id AND default_alamat = TRUE LIMIT 1");
        $defaultAddressQuery->execute([':customer_id' => $customerId]);
        $address = $defaultAddressQuery->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log('Generate Invoice Error: ' . $e->getMessage());
    http_response_code(500);
    echo 'Terjadi kesalahan database: ' . $e->getMessage();
    exit;
}

function formatRupiah($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatTanggal($date)
{
    $bulan = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
    $timestamp = strtotime($date);
    return date('d', $timestamp) . ' ' . $bulan[(int)date('m', $timestamp)] . ' ' . date('Y', $timestamp);
}

$invoiceNumber = 'INV-' . $orderId;
$invoiceDate = formatTanggal($order['tanggal_order']);
$paymentDate = $payment && !empty($payment['tanggal_pembayaran']) ? formatTanggal($payment['tanggal_pembayaran']) : $invoiceDate;

$originalSubtotal = 0;
$subtotalAfterDiscount = 0;
$productDiscount = 0;

foreach ($orderDetails as $item) {
    $hargaAsli = floatval($item['harga_satuan'] ?? 0);
    $diskonSatuan = floatval($item['diskon_satuan'] ?? 0);
    $hargaFinal = floatval($item['harga_setelah_diskon'] ?? $hargaAsli);
    $jumlah = intval($item['jumlah'] ?? 1);

    $originalSubtotal += $hargaAsli * $jumlah;
    $subtotalAfterDiscount += floatval($item['subtotal'] ?? ($hargaFinal * $jumlah));
    $productDiscount += $diskonSatuan * $jumlah;
}

$taxRate = 0.11;
$taxAmount = $subtotalAfterDiscount * $taxRate;
$shippingCost = floatval($order['total_ongkir'] ?? 0);
$packingCost = floatval($order['biaya_packing'] ?? 0);
$totalDiskon = floatval($order['total_diskon'] ?? 0);
$voucherDiscount = max(0, $totalDiskon - $productDiscount);
$grandTotal = floatval($payment['total_bayar'] ?? $order['total_bayar'] ?? 0);
$isPickup = ($order['shipping_method'] ?? '') === 'pickup';

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= htmlspecialchars($invoiceNumber) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #f5f5f5;
        }

        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            background: linear-gradient(135deg, #882426 0%, #5c1819 100%);
            color: #fff;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-info h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-info p {
            font-size: 11px;
            opacity: 0.9;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 28px;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .invoice-title p {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 5px;
        }

        .invoice-body {
            padding: 30px;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-box {
            flex: 1;
        }

        .info-box h3 {
            font-size: 10px;
            text-transform: uppercase;
            color: #888;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .info-box p {
            font-size: 12px;
            color: #333;
            margin-bottom: 3px;
        }

        .info-box .highlight {
            font-weight: 600;
            color: #882426;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background: #f8f8f8;
            padding: 12px 15px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #eee;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .items-table .item-name {
            font-weight: 600;
            color: #333;
        }

        .items-table .item-qty {
            text-align: center;
        }

        .items-table .item-price,
        .items-table .item-total {
            text-align: right;
        }

        .items-table .discount {
            color: #22c55e;
            font-size: 11px;
        }

        .summary-section {
            display: flex;
            justify-content: flex-end;
        }

        .summary-box {
            width: 300px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 12px;
        }

        .summary-row.discount {
            color: #22c55e;
        }

        .summary-row.total {
            border-top: 2px solid #333;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 16px;
            font-weight: 700;
            color: #882426;
        }

        .invoice-footer {
            background: #f8f8f8;
            padding: 20px 30px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #eee;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #882426;
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(136, 36, 38, 0.3);
        }

        .print-btn:hover {
            background: #6e1d1f;
        }

        @media print {
            body {
                background: #fff;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
            }

            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <button class="print-btn" onclick="window.print()">Cetak Invoice</button>

    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-info">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                    <img src="../../assets/img/logo-nano.png" alt="Nano Komputer" style="height: 50px; width: auto;">
                    <div>
                        <h1>Nano Komputer</h1>
                        <p>Toko Komputer & Elektronik Terpercaya</p>
                    </div>
                </div>
                <p style="margin-top: 10px;">Mangga Dua Mall, Jl. Mangga Dua Raya No.47A-B</p>
                <p>Jakarta Pusat, DKI Jakarta 10730</p>
                <p>Telp: (021) 623-09578 | Email: cs@nanokomputer.com</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p><?= htmlspecialchars($invoiceNumber) ?></p>
                <div style="margin-top: 15px;">
                    <span class="status-badge status-paid">LUNAS</span>
                </div>
            </div>
        </div>

        <div class="invoice-body">
            <div class="invoice-info">
                <div class="info-box">
                    <h3>Ditagihkan Kepada</h3>
                    <p class="highlight"><?= htmlspecialchars($order['customer_name'] ?? ($address['nama_penerima'] ?? 'Customer')) ?></p>
                    <p><?= htmlspecialchars($order['customer_email'] ?? '-') ?></p>
                    <p><?= htmlspecialchars($order['customer_phone'] ?? ($address['nomor_hp'] ?? '-')) ?></p>
                    <?php if ($address): ?>
                        <p style="margin-top: 8px;"><?= htmlspecialchars($address['alamat_lengkap']) ?></p>
                        <p><?= htmlspecialchars($address['kecamatan']) ?>, <?= htmlspecialchars($address['kota']) ?></p>
                        <p><?= htmlspecialchars($address['provinsi']) ?> <?= htmlspecialchars($address['kode_pos']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="info-box" style="text-align: right;">
                    <h3>Detail Invoice</h3>
                    <p><strong>No. Invoice:</strong> <?= htmlspecialchars($invoiceNumber) ?></p>
                    <p><strong>No. Pesanan:</strong> <?= htmlspecialchars($orderId) ?></p>
                    <p><strong>Tanggal Pesanan:</strong> <?= $invoiceDate ?></p>
                    <p><strong>Tanggal Pembayaran:</strong> <?= $paymentDate ?></p>
                    <?php if ($payment): ?>
                        <p><strong>Metode:</strong> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $payment['metode_pembayaran'] ?? '-'))) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Produk</th>
                        <th style="width: 10%;" class="item-qty">Qty</th>
                        <th style="width: 20%;" class="item-price">Harga Satuan</th>
                        <th style="width: 20%;" class="item-total">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderDetails as $item): ?>
                        <tr>
                            <td>
                                <div class="item-name"><?= htmlspecialchars($item['nama_product']) ?></div>
                                <?php if ($item['diskon_satuan'] > 0): ?>
                                    <div class="discount">Diskon: -<?= formatRupiah($item['diskon_satuan']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="item-qty"><?= $item['jumlah'] ?></td>
                            <td class="item-price">
                                <?php if ($item['diskon_satuan'] > 0): ?>
                                    <div style="text-decoration: line-through; color: #999; font-size: 11px;"><?= formatRupiah($item['harga_satuan']) ?></div>
                                    <div><?= formatRupiah($item['harga_setelah_diskon']) ?></div>
                                <?php else: ?>
                                    <?= formatRupiah($item['harga_satuan']) ?>
                                <?php endif; ?>
                            </td>
                            <td class="item-total"><?= formatRupiah($item['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary-section">
                <div class="summary-box">
                    <?php if ($productDiscount > 0): ?>
                        <div class="summary-row">
                            <span>Subtotal Produk (<?= count($orderDetails) ?> item)</span>
                            <span style="text-decoration: line-through; color: #999;"><?= formatRupiah($originalSubtotal) ?></span>
                        </div>
                        <div class="summary-row discount">
                            <span>Diskon Produk</span>
                            <span>-<?= formatRupiah($productDiscount) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?= formatRupiah($subtotalAfterDiscount) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="summary-row">
                            <span>Subtotal (<?= count($orderDetails) ?> item)</span>
                            <span><?= formatRupiah($subtotalAfterDiscount) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="summary-row">
                        <span>Pajak (11%)</span>
                        <span><?= formatRupiah($taxAmount) ?></span>
                    </div>
                    <?php if ($isPickup): ?>
                        <div class="summary-row">
                            <span>Ongkos Kirim</span>
                            <span style="color: #22c55e;">Gratis (Ambil di Toko)</span>
                        </div>
                    <?php elseif ($shippingCost > 0): ?>
                        <div class="summary-row">
                            <span>Ongkos Kirim</span>
                            <span><?= formatRupiah($shippingCost) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($packingCost > 0): ?>
                        <div class="summary-row">
                            <span>Biaya Packing</span>
                            <span><?= formatRupiah($packingCost) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($voucherDiscount > 0): ?>
                        <div class="summary-row discount">
                            <span>Diskon Voucher</span>
                            <span>-<?= formatRupiah($voucherDiscount) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="summary-row total">
                        <span>TOTAL</span>
                        <span><?= formatRupiah($grandTotal) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-footer">
            <p style="margin-bottom: 10px;">Terima kasih telah berbelanja di <strong>Nano Komputer</strong></p>
            <p>Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan.</p>
            <p style="margin-top: 10px; color: #999;">Jika ada pertanyaan, hubungi kami di cs@nanokomputer.com atau (021) 623-09578</p>
        </div>
    </div>
</body>

</html>