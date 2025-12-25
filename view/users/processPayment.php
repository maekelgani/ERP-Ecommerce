`<?php
    $pageTitle = "Pembayaran";
    require_once __DIR__ . '/../../config/config.php';

    use App\Database\DatabaseConnection;

    \App\Auth\CustomerAuthMiddleware::requireLogin('processPayment.php');

    $customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();
    $customerId = (int) \App\Auth\CustomerAuthMiddleware::getCustomerId();

    $orderId = $_GET['order_id'] ?? $_GET['order'] ?? null;

    if (!$orderId) {
        header('Location: myOrder.php');
        exit;
    }

    $db = DatabaseConnection::getInstance()->getConnection();

    $orderQuery = $db->prepare("
    SELECT 
        o.*,
        c.nama_lengkap,
        c.no_telp,
        c.email
    FROM orders o
    LEFT JOIN customers c ON o.id_customer = c.id_customer
    WHERE o.id_order = :order_id AND o.id_customer = :customer_id
");
    $orderQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
    $order = $orderQuery->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: myOrder.php');
        exit;
    }

    $paymentQuery = $db->prepare("
    SELECT * FROM payment WHERE id_order = :order_id
");
    $paymentQuery->execute([':order_id' => $orderId]);
    $payment = $paymentQuery->fetch(PDO::FETCH_ASSOC);

    $shipmentQuery = $db->prepare("
    SELECT s.*, sl.nama_toko, sl.alamat as alamat_toko, sl.no_telepon as telp_toko
    FROM shipment s
    LEFT JOIN store_locations sl ON s.store_id = sl.id_toko
    WHERE s.id_order = :order_id
");
    $shipmentQuery->execute([':order_id' => $orderId]);
    $shipment = $shipmentQuery->fetch(PDO::FETCH_ASSOC);

    $itemsQuery = $db->prepare("
    SELECT od.*, p.gambar 
    FROM order_detail od
    LEFT JOIN products p ON od.id_product = p.id_product
    WHERE od.id_order = :order_id
");
    $itemsQuery->execute([':order_id' => $orderId]);
    $orderItems = $itemsQuery->fetchAll(PDO::FETCH_ASSOC);

    // Query untuk mendapatkan data penggunaan voucher
    $voucherUsageQuery = $db->prepare("
        SELECT pv.*, v.kode, v.judul, v.jenis, v.nilai as voucher_nilai, v.maksimal_diskon
        FROM penggunaan_voucher pv
        LEFT JOIN voucher v ON pv.id_voucher = v.id_voucher
        WHERE pv.id_pesanan = :order_id
        LIMIT 1
    ");
    $voucherUsageQuery->execute([':order_id' => $orderId]);
    $voucherUsage = $voucherUsageQuery->fetch(PDO::FETCH_ASSOC);

    $subtotal = 0;
    $originalSubtotal = 0;
    $productDiscount = 0;

    foreach ($orderItems as $item) {
        $hargaAsli = floatval($item['harga_satuan'] ?? 0);
        $diskonSatuan = floatval($item['diskon_satuan'] ?? 0);
        $hargaFinal = floatval($item['harga_setelah_diskon'] ?? $hargaAsli);
        $jumlah = intval($item['jumlah'] ?? 1);

        $originalSubtotal += $hargaAsli * $jumlah;
        $subtotal += floatval($item['subtotal'] ?? ($hargaFinal * $jumlah));
        $productDiscount += $diskonSatuan * $jumlah;
    }

    $taxRate = 0.11;
    $taxAmount = $subtotal * $taxRate;
    $shippingCost = floatval($order['total_ongkir'] ?? 0);
    $packingCost = floatval($order['biaya_packing'] ?? 0);

    $totalDiskonOrder = floatval($order['total_diskon'] ?? 0);
    $voucherDiscount = max(0, $totalDiskonOrder - $productDiscount);

    $grandTotal = floatval($payment['total_bayar'] ?? $order['total_bayar'] ?? 0);

    $expiryTime = null;
    $isExpired = false;
    if ($payment && !empty($payment['expiry_time'])) {
        $expiryTime = strtotime($payment['expiry_time']);
        $isExpired = $expiryTime < time();
    } else {
        $expiryTime = strtotime($order['tanggal_order']) + (24 * 60 * 60);
        $isExpired = $expiryTime < time();
    }

    // Otomatis mengubah status pembayaran menjadi gagal jika waktu pembayaran telah habis
    if ($payment && ($isExpired || $payment['status_pembayaran'] === 'gagal') && $order['status_order'] === 'pending') {
        // Update order status to cancelled and add system cancellation info
        $updateOrderQuery = $db->prepare("
            UPDATE orders 
            SET status_order = 'dibatalkan',
                catatan_order = CONCAT(
                    IFNULL(catatan_order, ''),
                    CASE WHEN catatan_order IS NOT NULL AND catatan_order != '' THEN '\n\n' ELSE '' END,
                    '[SISTEM] Pesanan otomatis dibatalkan oleh Nano Komputer karena waktu pembayaran habis pada ', DATE_FORMAT(NOW(), '%d/%m/%Y %H:%i')
                )
            WHERE id_order = :order_id AND status_order = 'pending'
        ");
        $updateOrderQuery->execute([':order_id' => $orderId]);

        // Refresh order data
        $orderQuery = $db->prepare("
            SELECT 
                o.*,
                c.nama_lengkap,
                c.no_telp,
                c.email
            FROM orders o
            LEFT JOIN customers c ON o.id_customer = c.id_customer
            WHERE o.id_order = :order_id AND o.id_customer = :customer_id
        ");
        $orderQuery->execute([':order_id' => $orderId, ':customer_id' => $customerId]);
        $order = $orderQuery->fetch(PDO::FETCH_ASSOC);
    }

    if ($isExpired && $payment && $payment['status_pembayaran'] === 'pending') {
        // Update payment status to failed
        $updatePaymentQuery = $db->prepare("
            UPDATE payment 
            SET status_pembayaran = 'gagal'
            WHERE id_order = :order_id
        ");
        $updatePaymentQuery->execute([':order_id' => $orderId]);

        // Refresh payment data setelah update
        $paymentQuery = $db->prepare("
            SELECT * FROM payment WHERE id_order = :order_id
        ");
        $paymentQuery->execute([':order_id' => $orderId]);
        $payment = $paymentQuery->fetch(PDO::FETCH_ASSOC);
    }

    function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    function getPaymentMethodLabel($method, $bank = null)
    {
        $labels = [
            'transfer_bank' => 'Transfer Bank',
            'ewallet' => 'E-Wallet',
            'cod' => 'COD',
            'kartu_kredit' => 'Kartu Kredit'
        ];

        $label = $labels[$method] ?? $method;
        if ($bank) {
            $label .= ' - ' . $bank;
        }
        return $label;
    }

    function getStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu Pembayaran</span>',
            'verifikasi' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Verifikasi</span>',
            'berhasil' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Berhasil</span>',
            'gagal' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Gagal</span>',
        ];
        return $badges[$status] ?? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' . ucfirst($status) . '</span>';
    }

    $vaNumber = $payment['va_number'] ?? null;
    if (!$vaNumber && $payment) {
        $vaNumber = substr(str_replace(['ORD', 'PAY'], '', $payment['id_payment'] ?? $orderId), 0, 16);
    }

    $breadcrumbs = [
        ['label' => 'Home', 'url' => 'landingPage.php'],
        ['label' => 'Proses Pembayaran', 'url' => null]
    ];

    include '../../components/users/head.php';
    ?>

<body class="w-full min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>
    <!-- <div id="navbarSpacer" class="transition-all duration-300 pt-16 md:pt-40 lg:pt-[158px]"></div> -->
    <!-- <main class="max-w-full mb-10 pt-16 md:pt-40 lg:pt-[172px]"> -->

    <main class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-8 pt-16 md:pt-40 lg:pt-[172px]">
        <?php include '../../components/users/breadcrumb.php'; ?>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">Pembayaran</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 order-2 lg:order-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-28">
                    <h2 class="text-lg font-bold text-gray-900">Rincian Order</h2>
                    <p class="text-sm text-gray-400 mb-4">ID: #<?= htmlspecialchars($orderId) ?></p>

                    <div class="space-y-4 mb-6 text-sm">
                        <div>
                            <p class="text-gray-400">Nama Pelanggan</p>
                            <p class="font-medium text-gray-900"><?= htmlspecialchars($order['nama_lengkap'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-gray-400">No.telp</p>
                            <p class="font-medium text-gray-900"><?= htmlspecialchars($shipment['nomor_hp_penerima'] ?? $order['no_telp'] ?? '-') ?></p>
                        </div>

                        <?php if ($order['shipping_method'] === 'pickup'): ?>
                            <div>
                                <p class="text-gray-400">Metode Pengiriman</p>
                                <p class="font-medium text-green-600">Ambil di Toko</p>
                                <?php if (!empty($shipment['jasa_pengiriman'])): ?>
                                    <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars(str_replace('Ambil di Toko - ', '', $shipment['jasa_pengiriman'])) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div>
                                <p class="text-gray-400">Alamat Tujuan</p>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($shipment['alamat_pengiriman'] ?? '-') ?></p>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-gray-400">Kota</p>
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($shipment['kota'] ?? '-') ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Kurir</p>
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($shipment['jasa_pengiriman'] ?? '-') ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Kode pos</p>
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($shipment['kode_pos'] ?? '-') ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="h-px bg-gray-200 my-4"></div>

                    <div class="space-y-3 mb-6 max-h-64 overflow-y-auto custom-scrollbar">
                        <?php foreach ($orderItems as $item):
                            $imagePath = !empty($item['gambar'])
                                ? '../../uploads/products/' . $item['gambar']
                                : '../../assets/img/placeholder-product.png';
                            $hargaAsli = floatval($item['harga_satuan'] ?? 0);
                            $diskonSatuan = floatval($item['diskon_satuan'] ?? 0);
                            $hargaFinal = floatval($item['harga_setelah_diskon'] ?? $hargaAsli);
                            $jumlah = intval($item['jumlah'] ?? 1);
                            $hasDiscount = $diskonSatuan > 0;
                        ?>
                            <div class="flex gap-3">
                                <img src="<?= htmlspecialchars($imagePath) ?>"
                                    alt="<?= htmlspecialchars($item['nama_product']) ?>"
                                    class="w-16 h-16 object-cover rounded-lg border border-gray-100"
                                    onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2212%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 line-clamp-2"><?= htmlspecialchars($item['nama_product']) ?></p>
                                    <p class="text-xs text-gray-500 mt-1">Qty: <?= $jumlah ?></p>
                                    <?php if ($hasDiscount): ?>
                                        <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                            <span class="text-xs text-gray-400 line-through">
                                                <?= formatRupiah($hargaAsli * $jumlah) ?>
                                            </span>
                                            <span class="text-sm font-bold text-[#882426]">
                                                <?= formatRupiah($hargaFinal * $jumlah) ?>
                                            </span>
                                        </div>
                                        <p class="text-xs text-green-600 font-medium">
                                            Hemat <?= formatRupiah($diskonSatuan * $jumlah) ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-sm font-bold text-[#882426] mt-1">
                                            <?= formatRupiah($hargaFinal * $jumlah) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="h-px bg-gray-200 my-4"></div>

                    <div class="space-y-2 text-sm">
                        <?php if ($productDiscount > 0): ?>
                            <div class="flex justify-between text-gray-500">
                                <span>Subtotal Produk</span>
                                <span class="text-gray-400 line-through"><?= formatRupiah($originalSubtotal) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-green-600 font-medium">Diskon Produk</span>
                                <span class="text-green-600 font-medium">- <?= formatRupiah($productDiscount) ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium"><?= formatRupiah($subtotal) ?></span>
                            </div>
                        <?php else: ?>
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium"><?= formatRupiah($subtotal) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between text-gray-500">
                            <span>Pajak (11%)</span>
                            <span><?= formatRupiah($taxAmount) ?></span>
                        </div>
                        <?php if ($order['shipping_method'] === 'pickup'): ?>
                            <div class="flex justify-between text-gray-500">
                                <span>Ongkir</span>
                                <span class="text-green-600 font-medium">Gratis (Ambil di Toko)</span>
                            </div>
                        <?php elseif ($shippingCost > 0): ?>
                            <div class="flex justify-between text-gray-500">
                                <span>Ongkir</span>
                                <span><?= formatRupiah($shippingCost) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($packingCost > 0): ?>
                            <div class="flex justify-between text-gray-500">
                                <span>Pengemasan</span>
                                <span><?= formatRupiah($packingCost) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($voucherDiscount > 0 && $voucherUsage): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-green-600 font-medium">Diskon Voucher</span>
                                <span class="font-medium text-green-600">- <?= formatRupiah($voucherDiscount) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="h-px bg-gray-200 my-3"></div>

                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-[#882426]"><?= formatRupiah($grandTotal) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 order-1 lg:order-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900">Proses Pembayaran</h2>
                    <p class="text-sm text-gray-400 mb-2">ID pembayaran: <?= htmlspecialchars($payment['id_payment'] ?? '-') ?></p>
                    <div class="h-px bg-gray-200 mb-6"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="md:col-span-2">
                            <p class="text-sm font-semibold text-gray-700">Order ID</p>
                            <p class="text-gray-500"><?= htmlspecialchars($orderId) ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Metode Pembayaran</p>
                            <p class="text-gray-500"><?= getPaymentMethodLabel($payment['metode_pembayaran'] ?? '-', $payment['nama_bank'] ?? null) ?></p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Status Pembayaran</p>
                            <div class="mt-1"><?= getStatusBadge($payment['status_pembayaran'] ?? 'pending') ?></div>
                        </div>
                    </div>

                    <?php if ($payment && $payment['status_pembayaran'] === 'pending' && !$isExpired): ?>
                        <div class="bg-gray-50 rounded-xl p-6 text-center">
                            <?php if ($payment['metode_pembayaran'] === 'ewallet' && !empty($payment['qr_code_url'])): ?>
                                <div class="mb-6">
                                    <p class="text-sm font-semibold text-gray-700 mb-3">Scan QR Code untuk Pembayaran</p>
                                    <img src="<?= htmlspecialchars($payment['qr_code_url']) ?>"
                                        alt="QR Code"
                                        class="w-48 h-48 mx-auto border border-gray-200 rounded-lg">
                                    <?php if (!empty($payment['deeplink_url'])): ?>
                                        <a href="<?= htmlspecialchars($payment['deeplink_url']) ?>"
                                            class="inline-block mt-4 px-6 py-2 bg-[#882426] text-white text-sm font-medium rounded-lg hover:bg-[#6a1c1e] transition-colors">
                                            Buka Aplikasi
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="mb-8">
                                    <p class="text-sm font-semibold text-gray-700 mb-3">Kode Pembayaran / No. Virtual Account</p>
                                    <div class="flex items-center justify-center gap-2">
                                        <input type="text"
                                            value="<?= htmlspecialchars($vaNumber ?? '-') ?>"
                                            id="vaNumber"
                                            readonly
                                            class="px-4 py-2.5 border border-dashed border-gray-300 rounded-lg text-center text-lg font-mono bg-white">
                                        <button type="button"
                                            onclick="copyVA()"
                                            class="px-5 py-2.5 bg-[#882426] text-white font-medium rounded-lg hover:bg-[#6a1c1e] transition-colors">
                                            Salin
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-8">
                                <p class="text-sm text-gray-600 mb-2">Jumlah yang harus anda bayar</p>
                                <p class="text-3xl font-bold text-[#882426]"><?= formatRupiah($grandTotal) ?></p>
                            </div>

                            <div class="max-w-md mx-auto mb-8">
                                <div class="border border-dashed border-gray-300 rounded-xl p-4 bg-white">
                                    <p class="text-sm text-gray-600 mb-2">Batas waktu pembayaran</p>
                                    <div id="countdown" class="text-lg">
                                        <span class="text-2xl font-bold text-[#882426]" id="hours">00</span>
                                        <span class="text-gray-400">Jam</span>
                                        <span class="mx-1 text-gray-400">:</span>
                                        <span class="text-2xl font-bold text-[#882426]" id="minutes">00</span>
                                        <span class="text-gray-400">Menit</span>
                                        <span class="mx-1 text-gray-400">:</span>
                                        <span class="text-2xl font-bold text-[#882426]" id="seconds">00</span>
                                        <span class="text-gray-400">Detik</span>
                                    </div>
                                </div>
                                <p class="text-sm text-blue-600 mt-3 font-medium">
                                    Pembayaran akan dibatalkan secara otomatis apabila dalam batas waktu di atas tidak dilakukan pembayaran
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <button type="button"
                                    onclick="cancelPayment()"
                                    class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                    Batalkan Pembayaran
                                </button>
                                <button type="button"
                                    onclick="checkPayment()"
                                    class="px-6 py-2.5 border-2 border-[#882426] text-[#882426] font-medium rounded-lg hover:bg-red-50 transition-colors">
                                    Cek Status Pembayaran
                                </button>
                                <button type="button"
                                    id="btnBayarSekarang"
                                    onclick="simulatePayment()"
                                    class="px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Bayar Sekarang
                                </button>
                            </div>
                        </div>

                    <?php elseif ($payment && $payment['status_pembayaran'] === 'berhasil'): ?>
                        <div class="bg-green-50 rounded-xl p-8 text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-green-800 mb-2">Pembayaran Berhasil!</h3>
                            <p class="text-green-600 mb-6">Terima kasih, pembayaran Anda telah kami terima.</p>
                            <a href="detailOrder.php?id=<?= urlencode($orderId) ?>"
                                class="inline-block px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                Lihat Detail Pesanan
                            </a>
                        </div>

                    <?php elseif ($isExpired || ($payment && $payment['status_pembayaran'] === 'gagal')): ?>
                        <div class="bg-red-50 rounded-xl p-8 text-center">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-red-800 mb-2">
                                <?= $isExpired ? 'Waktu Pembayaran Habis' : 'Pembayaran Gagal' ?>
                            </h3>
                            <p class="text-red-600 mb-6">
                                <?= $isExpired
                                    ? 'Maaf, batas waktu pembayaran telah berakhir.'
                                    : 'Pembayaran tidak dapat diproses. Silakan coba lagi.'
                                ?>
                            </p>
                            <a href="landingPage.php"
                                class="inline-block px-6 py-2.5 bg-[#882426] text-white font-medium rounded-lg hover:bg-[#6a1c1e] transition-colors">
                                Kembali Berbelanja
                            </a>
                        </div>

                    <?php else: ?>
                        <div class="bg-yellow-50 rounded-xl p-8 text-center">
                            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-yellow-800 mb-2">Menunggu Verifikasi</h3>
                            <p class="text-yellow-600 mb-6">Pembayaran Anda sedang dalam proses verifikasi.</p>
                            <button type="button"
                                onclick="checkPayment()"
                                class="px-6 py-2.5 bg-[#882426] text-white font-medium rounded-lg hover:bg-[#6a1c1e] transition-colors">
                                Refresh Status
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Cara Pembayaran</h3>
                    <?php if ($payment && $payment['metode_pembayaran'] === 'transfer_bank'): ?>
                        <ol class="text-sm text-gray-600 space-y-2 list-decimal list-inside">
                            <li>Salin nomor Virtual Account di atas</li>
                            <li>Buka aplikasi mobile banking atau ATM <?= htmlspecialchars($payment['nama_bank'] ?? '') ?></li>
                            <li>Pilih menu Transfer atau Pembayaran</li>
                            <li>Masukkan nomor Virtual Account dan jumlah pembayaran</li>
                            <li>Konfirmasi dan selesaikan transaksi</li>
                            <li>Simpan bukti pembayaran</li>
                        </ol>
                    <?php elseif ($payment && $payment['metode_pembayaran'] === 'ewallet'): ?>
                        <ol class="text-sm text-gray-600 space-y-2 list-decimal list-inside">
                            <li>Buka aplikasi <?= htmlspecialchars($payment['nama_bank'] ?? 'E-Wallet') ?> Anda</li>
                            <li>Scan QR Code yang tersedia atau gunakan tombol "Buka Aplikasi"</li>
                            <li>Periksa detail pembayaran dan konfirmasi</li>
                            <li>Masukkan PIN untuk menyelesaikan transaksi</li>
                            <li>Pembayaran akan otomatis terkonfirmasi</li>
                        </ol>
                    <?php else: ?>
                        <ol class="text-sm text-gray-600 space-y-2 list-decimal list-inside">
                            <li>Lakukan pembayaran sesuai metode yang dipilih</li>
                            <li>Pastikan jumlah pembayaran sesuai dengan total tagihan</li>
                            <li>Selesaikan pembayaran sebelum batas waktu berakhir</li>
                            <li>Status pembayaran akan diperbarui secara otomatis</li>
                        </ol>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <div id="cancelModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCancelModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
                <button onclick="closeCancelModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Batalkan Pembayaran?</h3>
                    <p class="text-gray-500 text-sm">Pesanan <span class="font-semibold text-gray-700"><?= htmlspecialchars($orderId) ?></span> akan dibatalkan dan tidak dapat dikembalikan.</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan pembatalan (opsional)</label>
                    <select id="cancelReason" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#882426] focus:border-transparent">
                        <option value="Berubah pikiran">Berubah pikiran</option>
                        <option value="Ingin mengubah pesanan">Ingin mengubah pesanan</option>
                        <option value="Menemukan harga lebih murah">Menemukan harga lebih murah</option>
                        <option value="Waktu pengiriman terlalu lama">Waktu pengiriman terlalu lama</option>
                        <option value="Salah memilih metode pembayaran">Salah memilih metode pembayaran</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeCancelModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Kembali
                    </button>
                    <button type="button" onclick="confirmCancelPayment()" id="btnConfirmCancel"
                        class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                        <span>Ya, Batalkan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="successCancelModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Pembayaran Dibatalkan</h3>
                <p class="text-gray-500 mb-6">Pesanan Anda telah berhasil dibatalkan. Stok produk akan dikembalikan secara otomatis.</p>
                <a href="myOrder.php" class="inline-block w-full px-6 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-colors">
                    Lihat Pesanan Saya
                </a>
            </div>
        </div>
    </div>

    <footer>
        <?php include '../../components/users/footer.php'; ?>
    </footer>

    <script>
        const orderId = '<?= htmlspecialchars($orderId) ?>';
        const expiryTime = <?= $expiryTime * 1000 ?>;

        function updateCountdown() {
            const hoursEl = document.getElementById('hours');
            const minutesEl = document.getElementById('minutes');
            const secondsEl = document.getElementById('seconds');

            if (!hoursEl || !minutesEl || !secondsEl) return;

            const now = Date.now();
            const remaining = expiryTime - now;

            if (remaining <= 0) {
                hoursEl.textContent = '00';
                minutesEl.textContent = '00';
                secondsEl.textContent = '00';
                setTimeout(() => location.reload(), 2000);
                return;
            }

            const hours = Math.floor(remaining / (1000 * 60 * 60));
            const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

            hoursEl.textContent = hours.toString().padStart(2, '0');
            minutesEl.textContent = minutes.toString().padStart(2, '0');
            secondsEl.textContent = seconds.toString().padStart(2, '0');
        }

        if (document.getElementById('countdown')) {
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        function copyVA() {
            const vaInput = document.getElementById('vaNumber');
            if (vaInput) {
                navigator.clipboard.writeText(vaInput.value).then(() => {
                    showToast('Nomor VA berhasil disalin!', 'success');
                }).catch(() => {
                    vaInput.select();
                    document.execCommand('copy');
                    showToast('Nomor VA berhasil disalin!', 'success');
                });
            }
        }

        function showToast(message, type = 'info') {
            const existingToasts = document.querySelectorAll('.toast-notification');
            existingToasts.forEach(t => t.remove());

            const toast = document.createElement('div');
            toast.className = `toast-notification fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white font-medium z-[60] transition-all transform translate-y-0 opacity-100 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function checkPayment() {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memeriksa...
            `;

            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        function cancelPayment() {
            const modal = document.getElementById('cancelModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        async function confirmCancelPayment() {
            const btn = document.getElementById('btnConfirmCancel');
            const reason = document.getElementById('cancelReason').value;

            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memproses...</span>
            `;

            try {
                const response = await fetch('../../api/checkout/cancel-payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        reason: reason
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP Error: ${response.status} ${response.statusText}`);
                }

                const result = await response.json();

                if (result && result.success === true) {
                    closeCancelModal();

                    const successModal = document.getElementById('successCancelModal');
                    successModal.classList.remove('hidden');

                    setTimeout(() => {
                        window.location.href = 'myOrder.php';
                    }, 3000);
                } else {
                    const errorMsg = result?.message || 'Pembayaran tidak berhasil dibatalkan';
                    showToast(errorMsg, 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<span>Ya, Batalkan</span>';
                }
            } catch (error) {
                console.error('Cancel payment error:', error);
                let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';

                if (error instanceof SyntaxError) {
                    errorMsg = 'Format respons tidak valid. Silakan refresh dan coba lagi.';
                } else if (error instanceof TypeError) {
                    errorMsg = 'Gagal menghubungi server. Periksa koneksi internet Anda.';
                }

                showToast(errorMsg, 'error');
                btn.disabled = false;
                btn.innerHTML = '<span>Ya, Batalkan</span>';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCancelModal();
            }
        });

        async function simulatePayment() {
            const btn = document.getElementById('btnBayarSekarang');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memproses...</span>
            `;

            try {
                const response = await fetch('../../ajax/simulate-payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: orderId
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(result.message || 'Gagal memproses pembayaran', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }
    </script>
</body>

</html>