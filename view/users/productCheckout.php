<?php
$pageTitle = "Checkout";
require_once __DIR__ . '/../../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin('productCheckout.php');

$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();
$customerId = \App\Auth\CustomerAuthMiddleware::getCustomerId();
$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();

use App\Helper\ProductLandingHelper;
use App\Helper\DiscountHelper;
use App\Repository\StoreLocationRepository;

$productHelper = new ProductLandingHelper();
$discountHelper = new DiscountHelper();
$storeRepository = new StoreLocationRepository();
$storeLocations = [];

try {
    $storeLocations = $storeRepository->getActiveStores();
} catch (Exception $e) {
    error_log('Store locations error: ' . $e->getMessage());
    $storeLocations = [];
}

$checkoutItems = [];
$subtotal = 0;
$originalSubtotal = 0;
$totalDiscount = 0;
$totalItems = 0;
$totalWeight = 0;
$errorMessage = '';
$checkoutSource = $_GET['from'] ?? '';

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    $currentTime = date('Y-m-d H:i:s');

    if ($checkoutSource === 'cart') {
        $selectedItems = isset($_GET['items']) ? array_filter(explode(',', $_GET['items'])) : [];

        $query = "
            SELECT c.id_cart, c.jumlah as quantity, c.id_product,
                    p.id_product, p.nama_product, p.harga, p.stok, p.status_produk, 
                    p.gambar, p.deskripsi_speksifikasi, p.berat_gram,
                    k.nama_kategori, b.nama_brand,
                    pd.id_diskon, pd.label as discount_label, pd.jenis as discount_type,
                    pd.nilai as discount_value, pd.stok_promo, pd.stok_terpakai,
                    CASE 
                        WHEN pd.id_diskon IS NOT NULL THEN
                            CASE 
                                WHEN pd.jenis = 'persen' THEN p.harga - (p.harga * pd.nilai / 100)
                                ELSE GREATEST(0, p.harga - pd.nilai)
                            END
                        ELSE p.harga
                    END as harga_final
            FROM cart c
            JOIN products p ON c.id_product = p.id_product
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            LEFT JOIN promo_diskon pd ON p.id_product = pd.id_produk 
                AND pd.status = 'aktif' 
                AND (pd.mulai_pada IS NULL OR :now1 >= pd.mulai_pada)
                AND (pd.selesai_pada IS NULL OR :now2 <= pd.selesai_pada)
                AND (pd.stok_promo IS NULL OR pd.stok_promo > COALESCE(pd.stok_terpakai, 0))
            WHERE c.id_customer = :customer_id
                AND p.stok > 0 
                AND p.status_produk NOT IN ('habis', 'nonaktif')
        ";

        $params = [':customer_id' => $customerId, ':now1' => $currentTime, ':now2' => $currentTime];

        if (!empty($selectedItems)) {
            $placeholders = [];
            foreach ($selectedItems as $index => $cartId) {
                $key = ':cart_id_' . $index;
                $placeholders[] = $key;
                $params[$key] = $cartId;
            }
            $query .= " AND c.id_cart IN (" . implode(',', $placeholders) . ")";
        }

        $query .= " ORDER BY c.tanggal_ditambahkan DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $checkoutItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($checkoutItems as &$item) {
            $itemPrice = $item['harga_final'] ?? $item['harga'];
            $item['has_discount'] = $item['id_diskon'] !== null;
            $originalSubtotal += $item['harga'] * $item['quantity'];
            $subtotal += $itemPrice * $item['quantity'];
            $totalItems += $item['quantity'];
            $totalWeight += ($item['berat_gram'] ?? 0) * $item['quantity'];

            if ($item['has_discount']) {
                $totalDiscount += ($item['harga'] - $itemPrice) * $item['quantity'];
            }
        }
        unset($item);
    } elseif ($checkoutSource === 'buynow' && !empty($_GET['product'])) {
        $productId = $_GET['product'];
        $quantity = max(1, (int)($_GET['qty'] ?? 1));

        $stmt = $db->prepare("
            SELECT p.id_product, p.nama_product, p.harga, p.stok, p.status_produk, 
                   p.gambar, p.deskripsi_speksifikasi, p.berat_gram,
                   k.nama_kategori, b.nama_brand,
                   pd.id_diskon, pd.label as discount_label, pd.jenis as discount_type,
                   pd.nilai as discount_value, pd.stok_promo, pd.stok_terpakai,
                   CASE 
                       WHEN pd.id_diskon IS NOT NULL THEN
                           CASE 
                               WHEN pd.jenis = 'persen' THEN p.harga - (p.harga * pd.nilai / 100)
                               ELSE GREATEST(0, p.harga - pd.nilai)
                           END
                       ELSE p.harga
                   END as harga_final
            FROM products p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            LEFT JOIN promo_diskon pd ON p.id_product = pd.id_produk 
                AND pd.status = 'aktif' 
                AND (pd.mulai_pada IS NULL OR :now1 >= pd.mulai_pada)
                AND (pd.selesai_pada IS NULL OR :now2 <= pd.selesai_pada)
                AND (pd.stok_promo IS NULL OR pd.stok_promo > COALESCE(pd.stok_terpakai, 0))
            WHERE p.id_product = :product_id
              AND p.stok > 0 
              AND p.status_produk NOT IN ('habis', 'nonaktif')
        ");
        $stmt->execute([':product_id' => $productId, ':now1' => $currentTime, ':now2' => $currentTime]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $quantity = min($quantity, $product['stok']);
            $product['quantity'] = $quantity;
            $product['has_discount'] = $product['id_diskon'] !== null;
            $itemPrice = $product['harga_final'] ?? $product['harga'];
            $checkoutItems[] = $product;
            $originalSubtotal = $product['harga'] * $quantity;
            $subtotal = $itemPrice * $quantity;
            $totalItems = $quantity;
            $totalWeight = ($product['berat_gram'] ?? 0) * $quantity;

            if ($product['has_discount']) {
                $totalDiscount = ($product['harga'] - $itemPrice) * $quantity;
            }
        } else {
            $errorMessage = 'Produk tidak tersedia atau stok habis.';
        }
    } else {
        $errorMessage = 'Silakan pilih produk untuk checkout.';
    }

    $addressStmt = $db->prepare("
        SELECT * FROM address_book 
        WHERE id_customer = :customer_id 
        ORDER BY default_alamat DESC, id_alamat DESC
    ");
    $addressStmt->execute([':customer_id' => $customerId]);
    $addresses = $addressStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Checkout error: ' . $e->getMessage());
    $errorMessage = 'Terjadi kesalahan saat memuat data checkout.';
    $addresses = [];
}

$taxRate = 0.11;
$taxAmount = $subtotal * $taxRate;
$grandTotal = $subtotal + $taxAmount;

$_SESSION['checkout_data'] = [
    'items' => $checkoutItems,
    'subtotal' => $subtotal,
    'original_subtotal' => $originalSubtotal,
    'total_discount' => $totalDiscount,
    'total_items' => $totalItems,
    'total_weight' => $totalWeight,
    'tax_rate' => $taxRate,
    'tax_amount' => $taxAmount,
    'source' => $checkoutSource
];

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Checkout', 'url' => null]
];

include '../../components/users/head.php';
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<body class="w-full bg-gray-50 min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <div id="navbarSpacer" class="transition-all duration-300 h-32 md:h-44"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const promoBanner = document.getElementById('promoBanner');
            const navbarSpacer = document.getElementById('navbarSpacer');

            function updateSpacerHeight() {
                if (window.innerWidth >= 768 && promoBanner) {
                    navbarSpacer.style.height = window.scrollY > 50 ? '112px' : '156px';
                } else {
                    navbarSpacer.style.height = '112px';
                }
            }

            updateSpacerHeight();
            window.addEventListener('scroll', updateSpacerHeight);
            window.addEventListener('resize', updateSpacerHeight);
        });
    </script>

    <main class="max-w-full mb-10">

        <div class="w-full px-4 md:px-8 lg:px-20 py-6">
            <?php include '../../components/users/breadcrumb.php'; ?>

            <?php if (!empty($errorMessage) || empty($checkoutItems)): ?>
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-28 h-28 bg-red-100 rounded-full mb-6">
                        <svg class="w-14 h-14 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3"><?= !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'Tidak Ada Produk untuk Checkout' ?></h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">Silakan tambahkan produk ke keranjang atau pilih produk untuk dibeli langsung.</p>
                    <div class="flex items-center justify-center gap-4">
                        <a href="cart.php" class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-[#882426] text-[#882426] font-semibold rounded-xl hover:bg-[#882426]/5 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Lihat Keranjang
                        </a>
                        <a href="productCollection.php" class="inline-flex items-center gap-2 px-6 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Jelajahi Produk
                        </a>
                    </div>
                </div>
            <?php else: ?>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-8 md:gap-16 w-full justify-center" id="checkoutStepper">
                            <div class="step-item active" data-step="1">
                                <div class="step-circle">
                                    <span class="step-number">1</span>
                                    <svg class="step-check hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="step-label">Alamat</span>
                            </div>

                            <div class="step-line" data-after="1"></div>

                            <div class="step-item" data-step="2">
                                <div class="step-circle">
                                    <span class="step-number">2</span>
                                    <svg class="step-check hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="step-label">Pengiriman</span>
                            </div>

                            <div class="step-line" data-after="2"></div>

                            <div class="step-item" data-step="3">
                                <div class="step-circle">
                                    <span class="step-number">3</span>
                                    <svg class="step-check hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="step-label">Pembayaran</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div id="stepContent">
                            <div id="step1Content" class="step-content active">
                                <?php include __DIR__ . '/checkout/step-alamat.php'; ?>
                            </div>

                            <div id="step2Content" class="step-content hidden">
                                <?php include __DIR__ . '/checkout/step-pengiriman.php'; ?>
                            </div>

                            <div id="step3Content" class="step-content hidden">
                                <?php include __DIR__ . '/checkout/step-pembayaran.php'; ?>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1 mt-6 lg:mt-0">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-28" id="orderSummary">
                            <div class="bg-[#882426] px-6 py-4">
                                <h2 class="text-lg font-bold text-white">Ringkasan Pesanan</h2>
                                <p class="text-white/70 text-sm"><?= $totalItems ?> produk</p>
                            </div>

                            <div class="p-6">
                                <div class="max-h-64 overflow-y-auto space-y-4 mb-6 custom-scrollbar">
                                    <?php foreach ($checkoutItems as $item):
                                        $imagePath = !empty($item['gambar']) ? '../../uploads/products/' . htmlspecialchars($item['gambar']) : '../../assets/img/placeholder-product.png';
                                    ?>
                                        <div class="flex gap-3">
                                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($item['nama_product']) ?>"
                                                class="w-16 h-16 object-cover rounded-lg border border-gray-100"
                                                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%22 y=%2255%22 font-size=%2212%22 fill=%22%239ca3af%22 text-anchor=%22middle%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 line-clamp-2"><?= htmlspecialchars($item['nama_product']) ?></p>
                                                <p class="text-xs text-gray-500 mt-1">Qty: <?= $item['quantity'] ?></p>
                                                <p class="text-sm font-bold text-[#882426] mt-1">Rp <?= number_format($item['harga'] * $item['quantity'], 0, ',', '.') ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="border-t border-gray-100 pt-4 space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal Produk</span>
                                        <span class="font-medium">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">PPN (11%)</span>
                                        <span class="font-medium">Rp <?= number_format($taxAmount, 0, ',', '.') ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm" id="shippingCostRow">
                                        <span class="text-gray-600">Ongkos Kirim</span>
                                        <span class="font-medium text-gray-400" id="shippingCostDisplay">Belum dipilih</span>
                                    </div>
                                    <div class="flex justify-between text-sm hidden" id="packingCostRow">
                                        <span class="text-gray-600">Biaya Packing</span>
                                        <span class="font-medium" id="packingCostDisplay">Rp 0</span>
                                    </div>
                                    <div class="border-t border-gray-100 pt-3 mt-3">
                                        <div class="flex justify-between">
                                            <span class="text-base font-bold text-gray-900">Total Pembayaran</span>
                                            <span class="text-lg font-bold text-[#882426]" id="grandTotalDisplay">Rp <?= number_format($grandTotal, 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>

    <style>
        .address-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 1rem;
        }

        .address-modal-overlay.hidden {
            display: none;
        }

        .address-modal-overlay.show {
            display: flex;
            animation: addressModalFadeIn 0.3s ease-out;
        }

        @keyframes addressModalFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes addressModalSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .address-modal-content {
            background: white;
            border-radius: 1.5rem;
            max-width: 640px;
            width: 100%;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            animation: addressModalSlideUp 0.4s ease-out;
            overflow: hidden;
        }

        .address-modal-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            color: white;
            flex-shrink: 0;
        }

        .address-modal-header-icon {
            width: 48px;
            height: 48px;
            background: #882426;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .address-modal-header-icon .material-symbols-outlined {
            font-size: 1.75rem;
            color: white;
        }

        .address-modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
        }

        .address-modal-subtitle {
            font-size: 0.875rem;
            opacity: 0.8;
            margin: 0;
        }

        .address-modal-close {
            margin-left: auto;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .address-modal-close:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .address-modal-close .material-symbols-outlined {
            font-size: 1.5rem;
            color: #6b7280;
        }

        .address-modal-form {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }

        .address-modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .address-modal-footer {
            display: flex;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            flex-shrink: 0;
        }

        .address-modal-btn {
            flex: 1;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
        }

        .address-modal-btn .material-symbols-outlined {
            font-size: 1.25rem;
        }

        .address-modal-btn-cancel {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .address-modal-btn-cancel:hover {
            background: #f3f4f6;
        }

        .address-modal-btn-save {
            background: #882426;
            color: white;
        }

        .address-modal-btn-save:hover {
            background: #6a1c1e;
            transform: translateY(-1px);
        }

        .address-modal-btn:active {
            transform: scale(0.98);
        }

        .address-modal-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        @media (max-width: 640px) {
            .address-modal-content {
                max-height: 95vh;
                margin: 0.5rem;
            }

            .address-modal-header {
                padding: 1rem;
            }

            .address-modal-body {
                padding: 1rem;
            }

            .address-modal-footer {
                flex-direction: column;
                padding: 1rem;
            }
        }

        .select2-container--default .select2-selection--single {
            height: 48px !important;
            padding: 8px 12px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            background-color: #fff !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease !important;
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: #d1d5db !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #374151 !important;
            padding-left: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #9ca3af transparent transparent transparent !important;
            border-width: 6px 5px 0 5px !important;
        }

        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #882426 transparent !important;
            border-width: 0 5px 6px 5px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #882426 !important;
            box-shadow: 0 0 0 3px rgba(136, 36, 38, 0.1) !important;
            outline: none !important;
        }

        .select2-container--default.select2-container--disabled .select2-selection--single {
            background-color: #f3f4f6 !important;
            cursor: not-allowed !important;
        }

        .select2-dropdown {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            margin-top: 4px !important;
            z-index: 99999 !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 8px 12px !important;
            font-size: 0.9rem !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #882426 !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(136, 36, 38, 0.1) !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #882426 !important;
            color: white !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: rgba(136, 36, 38, 0.1) !important;
            color: #882426 !important;
        }

        .select2-results__option {
            padding: 10px 12px !important;
            font-size: 0.9rem !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-results__option--disabled {
            color: #9ca3af !important;
        }

        .wilayah-loading {
            position: relative;
        }

        .wilayah-loading::after {
            content: '';
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #882426;
            border-top-color: transparent;
            border-radius: 50%;
            animation: wilayah-spin 0.8s linear infinite;
        }

        @keyframes wilayah-spin {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .step-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #e5e7eb;
            color: #9ca3af;
            border: 3px solid transparent;
        }

        .step-item.active .step-circle {
            background: linear-gradient(135deg, #882426 0%, #6a1c1e 100%);
            color: white;
            border-color: #882426;
            box-shadow: 0 4px 15px rgba(136, 36, 38, 0.4);
        }

        .step-item.completed .step-circle {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }

        .step-item.completed .step-number {
            display: none;
        }

        .step-item.completed .step-check {
            display: block;
        }

        .step-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #9ca3af;
            transition: color 0.3s ease;
        }

        .step-item.active .step-label,
        .step-item.completed .step-label {
            color: #1f2937;
        }

        .step-line {
            flex: 1;
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            max-width: 100px;
            transition: background 0.3s ease;
        }

        .step-line.completed {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        }

        .step-content {
            animation: fadeIn 0.3s ease;
        }

        .step-content.hidden {
            display: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        @media (max-width: 640px) {
            .step-circle {
                width: 40px;
                height: 40px;
                font-size: 0.875rem;
            }

            .step-label {
                font-size: 0.75rem;
            }

            .step-line {
                max-width: 60px;
            }
        }
    </style>

    <input type="hidden" id="checkoutData" value='<?= json_encode([
                                                        'subtotal' => $subtotal,
                                                        'tax_amount' => $taxAmount,
                                                        'total_weight' => $totalWeight,
                                                        'total_items' => $totalItems,
                                                        'customer_id' => $customerId
                                                    ]) ?>'>

    <script src="../../assets/js/users/productCheckout.js"></script>
</body>

</html>