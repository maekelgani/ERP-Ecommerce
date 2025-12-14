<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin();

$customerId = \App\Auth\CustomerAuthMiddleware::getCustomerId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['address']) || empty($input['payment'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$allowedCouriers = [
    'jne-reg' => ['cost' => 25000, 'days' => '2-3'],
    'jne-yes' => ['cost' => 40000, 'days' => '1'],
    'jnt-reg' => ['cost' => 22000, 'days' => '2-4'],
    'sicepat-reg' => ['cost' => 20000, 'days' => '2-3'],
    'gosend-instant' => ['cost' => 35000, 'days' => 'Hari Ini']
];

$allowedStores = ['store-jakarta', 'store-bekasi'];
$packingCostFixed = 15000;
$taxRate = 0.11;

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    $db->beginTransaction();

    $checkoutData = $_SESSION['checkout_data'] ?? null;
    if (!$checkoutData || empty($checkoutData['items'])) {
        throw new Exception('Data checkout tidak valid. Silakan kembali ke keranjang.');
    }

    $serverSubtotal = 0;
    $verifiedItems = [];

    $productIds = array_column($checkoutData['items'], 'id_product');
    if (empty($productIds)) {
        throw new Exception('Tidak ada produk dalam checkout');
    }

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $verifyStmt = $db->prepare("
        SELECT id_product, nama_product, harga, stok, status_produk 
        FROM products 
        WHERE id_product IN ($placeholders)
    ");
    $verifyStmt->execute($productIds);
    $dbProducts = $verifyStmt->fetchAll(PDO::FETCH_ASSOC);
    $dbProductMap = array_column($dbProducts, null, 'id_product');

    foreach ($checkoutData['items'] as $item) {
        $productId = $item['id_product'];
        $qty = intval($item['quantity']);

        if (!isset($dbProductMap[$productId])) {
            throw new Exception("Produk {$item['nama_product']} tidak ditemukan");
        }

        $dbProduct = $dbProductMap[$productId];

        if ($dbProduct['stok'] < $qty) {
            throw new Exception("Stok {$dbProduct['nama_product']} tidak mencukupi (tersedia: {$dbProduct['stok']})");
        }

        if ($dbProduct['status_produk'] === 'habis' || $dbProduct['status_produk'] === 'nonaktif') {
            throw new Exception("Produk {$dbProduct['nama_product']} tidak tersedia");
        }

        $serverPrice = floatval($dbProduct['harga']);
        $itemSubtotal = $serverPrice * $qty;
        $serverSubtotal += $itemSubtotal;

        $verifiedItems[] = [
            'id_product' => $productId,
            'nama_product' => $dbProduct['nama_product'],
            'quantity' => $qty,
            'harga' => $serverPrice,
            'subtotal' => $itemSubtotal
        ];
    }

    $serverTax = $serverSubtotal * $taxRate;

    $shippingMethod = $input['shipping_method'] ?? 'delivery';
    $courier = $input['courier'] ?? null;
    $store = $input['store'] ?? null;
    $serverShippingCost = 0;
    $validatedCourier = null;
    $validatedStore = null;

    if ($shippingMethod === 'delivery') {
        if (!$courier || !isset($courier['value']) || !isset($allowedCouriers[$courier['value']])) {
            throw new Exception('Jasa pengiriman tidak valid');
        }
        $courierKey = $courier['value'];
        $serverShippingCost = $allowedCouriers[$courierKey]['cost'];
        $validatedCourier = [
            'value' => $courierKey,
            'cost' => $serverShippingCost,
            'days' => $allowedCouriers[$courierKey]['days']
        ];
    } else if ($shippingMethod === 'pickup') {
        if (!$store || !isset($store['value']) || !in_array($store['value'], $allowedStores)) {
            throw new Exception('Lokasi toko tidak valid');
        }
        $serverShippingCost = 0;
        $validatedStore = $store;
    } else {
        throw new Exception('Metode pengiriman tidak valid');
    }

    $serverPackingCost = 0;
    if (!empty($input['extra_packing']) && $input['extra_packing'] === true) {
        $serverPackingCost = $packingCostFixed;
    }

    $serverVoucherDiscount = 0;
    $validatedVoucher = null;

    if (!empty($input['voucher']) && isset($input['voucher']['id'])) {
        $voucherId = $input['voucher']['id'];

        $voucherStmt = $db->prepare("
            SELECT * FROM voucher 
            WHERE id_voucher = :id 
            AND status = 'aktif'
            AND (mulai_pada IS NULL OR mulai_pada <= NOW())
            AND (selesai_pada IS NULL OR selesai_pada >= NOW())
            AND (kuota_total IS NULL OR kuota_terpakai < kuota_total)
        ");
        $voucherStmt->execute([':id' => $voucherId]);
        $voucher = $voucherStmt->fetch(PDO::FETCH_ASSOC);

        if ($voucher) {
            if (!$voucher['minimal_belanja'] || $serverSubtotal >= floatval($voucher['minimal_belanja'])) {
                $usageStmt = $db->prepare("
                    SELECT COUNT(*) as usage_count FROM penggunaan_voucher 
                    WHERE id_voucher = :voucher_id AND id_pengguna = :customer_id
                ");
                $usageStmt->execute([':voucher_id' => $voucherId, ':customer_id' => $customerId]);
                $usage = $usageStmt->fetch(PDO::FETCH_ASSOC);

                $canUse = true;
                if ($voucher['kuota_per_pengguna'] && $usage['usage_count'] >= $voucher['kuota_per_pengguna']) {
                    $canUse = false;
                }

                if ($canUse) {
                    switch ($voucher['jenis']) {
                        case 'diskon_persen':
                            $serverVoucherDiscount = $serverSubtotal * (floatval($voucher['nilai']) / 100);
                            if ($voucher['maksimal_diskon'] && $serverVoucherDiscount > floatval($voucher['maksimal_diskon'])) {
                                $serverVoucherDiscount = floatval($voucher['maksimal_diskon']);
                            }
                            break;
                        case 'diskon_nominal':
                            $serverVoucherDiscount = floatval($voucher['nilai']);
                            if ($voucher['maksimal_diskon'] && $serverVoucherDiscount > floatval($voucher['maksimal_diskon'])) {
                                $serverVoucherDiscount = floatval($voucher['maksimal_diskon']);
                            }
                            break;
                    }

                    if ($serverVoucherDiscount > $serverSubtotal) {
                        $serverVoucherDiscount = $serverSubtotal;
                    }

                    $validatedVoucher = [
                        'id' => $voucher['id_voucher'],
                        'kode' => $voucher['kode'],
                        'discount' => $serverVoucherDiscount
                    ];
                }
            }
        }
    }

    $serverGrandTotal = $serverSubtotal + $serverTax + $serverShippingCost + $serverPackingCost - $serverVoucherDiscount;

    $clientGrandTotal = floatval($input['grand_total'] ?? 0);
    $tolerance = 100;
    if (abs($serverGrandTotal - $clientGrandTotal) > $tolerance) {
        throw new Exception('Total pembayaran tidak sesuai. Silakan refresh halaman dan coba lagi.');
    }

    $orderId = 'ORD' . date('Ymd') . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

    $insertOrder = $db->prepare("
        INSERT INTO orders (
            id_order, id_customer, total_harga, total_ongkir, total_diskon, total_bayar,
            status_order, tanggal_order, catatan_order
        ) VALUES (
            :id_order, :id_customer, :total_harga, :total_ongkir, :total_diskon, :total_bayar,
            'pending', NOW(), :catatan_order
        )
    ");

    $catatan_order = '';
    if ($serverPackingCost > 0) {
        $catatan_order .= 'Packing kayu (+Rp ' . number_format($serverPackingCost, 0, ',', '.') . ')';
    }

    $insertOrder->execute([
        ':id_order' => $orderId,
        ':id_customer' => $customerId,
        ':total_harga' => $serverSubtotal + $serverTax,
        ':total_ongkir' => $serverShippingCost + $serverPackingCost,
        ':total_diskon' => $serverVoucherDiscount,
        ':total_bayar' => $serverGrandTotal,
        ':catatan_order' => $catatan_order
    ]);

    $insertDetail = $db->prepare("
        INSERT INTO order_detail (
            id_detail, id_order, id_product, nama_product, jumlah, harga_satuan, diskon_satuan, subtotal
        ) VALUES (
            :id_detail, :id_order, :id_product, :nama_product, :jumlah, :harga_satuan, :diskon_satuan, :subtotal
        )
    ");

    $updateStock = $db->prepare("
        UPDATE products SET stok = stok - :qty WHERE id_product = :product_id AND stok >= :qty
    ");

    foreach ($verifiedItems as $index => $item) {
        $detailId = 'DTL' . date('Ymd') . str_pad($index + 1, 4, '0', STR_PAD_LEFT) . strtoupper(substr(md5(uniqid()), 0, 4));

        $insertDetail->execute([
            ':id_detail' => $detailId,
            ':id_order' => $orderId,
            ':id_product' => $item['id_product'],
            ':nama_product' => $item['nama_product'],
            ':jumlah' => $item['quantity'],
            ':harga_satuan' => $item['harga'],
            ':diskon_satuan' => 0,
            ':subtotal' => $item['subtotal']
        ]);

        $updateStock->execute([
            ':qty' => $item['quantity'],
            ':product_id' => $item['id_product']
        ]);
    }

    $shipmentId = 'SHP' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 8));
    $address = $input['address'];

    $jasaPengiriman = '';
    $estimasiHari = null;

    if ($shippingMethod === 'delivery' && $validatedCourier) {
        $jasaPengiriman = strtoupper(str_replace('-', ' ', $validatedCourier['value']));
        $estimasiDays = $validatedCourier['days'] ?? '';
        if (preg_match('/(\d+)/', $estimasiDays, $matches)) {
            $estimasiHari = intval($matches[1]);
        }
    } else if ($shippingMethod === 'pickup' && $validatedStore) {
        $storeName = '';
        if ($validatedStore['value'] === 'store-jakarta') {
            $storeName = 'Nano Komputer Jakarta Pusat';
        } else if ($validatedStore['value'] === 'store-bekasi') {
            $storeName = 'Nano Komputer Bekasi';
        }
        $jasaPengiriman = 'Ambil di Toko - ' . $storeName;
    }

    $alamatLengkap = ($address['alamat_lengkap'] ?? '') . ', ' .
        ($address['kelurahan'] ?? '') . ', ' .
        ($address['kecamatan'] ?? '') . ', ' .
        ($address['kota'] ?? '') . ', ' .
        ($address['provinsi'] ?? '') . ' ' .
        ($address['kode_pos'] ?? '');

    $insertShipment = $db->prepare("
        INSERT INTO shipment (
            id_shipment, id_order, id_alamat, jasa_pengiriman, nama_penerima, 
            nomor_hp_penerima, alamat_pengiriman, kota, kode_pos, ongkir, 
            estimasi_hari, status_pengiriman
        ) VALUES (
            :id_shipment, :id_order, :id_alamat, :jasa_pengiriman, :nama_penerima,
            :nomor_hp, :alamat_pengiriman, :kota, :kode_pos, :ongkir,
            :estimasi_hari, 'pending'
        )
    ");

    $insertShipment->execute([
        ':id_shipment' => $shipmentId,
        ':id_order' => $orderId,
        ':id_alamat' => $address['id_alamat'] ?? null,
        ':jasa_pengiriman' => $jasaPengiriman,
        ':nama_penerima' => $address['nama_penerima'] ?? '',
        ':nomor_hp' => $address['nomor_hp'] ?? '',
        ':alamat_pengiriman' => $alamatLengkap,
        ':kota' => $address['kota'] ?? '',
        ':kode_pos' => $address['kode_pos'] ?? '',
        ':ongkir' => $serverShippingCost,
        ':estimasi_hari' => $estimasiHari
    ]);

    $paymentId = 'PAY' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 8));
    $payment = $input['payment'];

    $allowedPaymentTypes = ['bank', 'ewallet', 'qris', 'cod'];
    if (!isset($payment['type']) || !in_array($payment['type'], $allowedPaymentTypes)) {
        throw new Exception('Metode pembayaran tidak valid');
    }

    $metodePembayaran = 'transfer_bank';
    switch ($payment['type']) {
        case 'bank':
            $metodePembayaran = 'transfer_bank';
            break;
        case 'ewallet':
        case 'qris':
            $metodePembayaran = 'ewallet';
            break;
        case 'cod':
            $metodePembayaran = 'cod';
            break;
    }

    $insertPayment = $db->prepare("
        INSERT INTO payment (
            id_payment, id_order, metode_pembayaran, nama_bank, status_pembayaran, total_bayar
        ) VALUES (
            :id_payment, :id_order, :metode_pembayaran, :nama_bank, 'pending', :total_bayar
        )
    ");

    $insertPayment->execute([
        ':id_payment' => $paymentId,
        ':id_order' => $orderId,
        ':metode_pembayaran' => $metodePembayaran,
        ':nama_bank' => $payment['name'] ?? null,
        ':total_bayar' => $serverGrandTotal
    ]);

    if ($validatedVoucher && $serverVoucherDiscount > 0) {
        $insertVoucherUsage = $db->prepare("
            INSERT INTO penggunaan_voucher (id_voucher, id_pengguna, id_pesanan, jumlah_diskon)
            VALUES (:voucher_id, :customer_id, :order_id, :discount)
        ");
        $insertVoucherUsage->execute([
            ':voucher_id' => $validatedVoucher['id'],
            ':customer_id' => $customerId,
            ':order_id' => $orderId,
            ':discount' => $serverVoucherDiscount
        ]);
    }

    if ($checkoutData['source'] === 'cart') {
        $deleteCart = $db->prepare("DELETE FROM cart WHERE id_customer = :customer_id");
        $deleteCart->execute([':customer_id' => $customerId]);
    }

    unset($_SESSION['checkout_data']);

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pesanan berhasil dibuat',
        'order_id' => $orderId,
        'payment_id' => $paymentId
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('Place order error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
