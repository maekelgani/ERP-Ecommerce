<?php
$pageTitle = "Syarat & Ketentuan";
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Database/DatabaseConnection.php';
require_once __DIR__ . '/../../app/Services/SiteSetting.php';

use App\Database\DatabaseConnection;
use App\Services\SiteSetting;

if (!isset($settingService)) {
    $db = DatabaseConnection::getInstance()->getConnection();
    $settingService = new SiteSetting($db);
}

if (!isset($globalSettings)) {
    $globalSettings = $settingService->getAllSettings();
}

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

include '../../components/users/head.php';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Syarat & Ketentuan', 'url' => null]
];
function formatKapitalDepan(string $title): string
{
    $title = mb_convert_case($title, MB_CASE_TITLE, 'UTF-8');
    return str_replace(['Pc', 'It'], ['PC', 'IT'], $title);
}
$lastUpdated = "1 Desember 2025";
?>

<!-- jsPDF Library for PDF Generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
    :root {
        --background: #fafafa;
        --foreground: #0f172a;
        --card: #ffffff;
        --card-foreground: #0f172a;
        --primary: #882426;
        --primary-foreground: #ffffff;
        --secondary: #f1f5f9;
        --secondary-foreground: #0f172a;
        --muted: #f1f5f9;
        --muted-foreground: #64748b;
        --accent: #fce8e8;
        --accent-foreground: #882426;
        --border: #e2e8f0;
        --success: #22c55e;
        --success-foreground: #ffffff;
        --warning: #f59e0b;
        --radius: 0.75rem;
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        background-color: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 1.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transform: translateX(120%);
        transition: transform 0.3s ease;
        z-index: 1000;
    }

    .toast.show {
        transform: translateX(0);
    }

    .toast.success {
        border-left: 4px solid var(--success);
    }

    .toast.error {
        border-left: 4px solid #ef4444;
    }

    .toast-title {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .toast-message {
        font-size: 0.8125rem;
        color: var(--muted-foreground);
    }

    /* Download Button Styles */
    .btn-download {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
    }

    .btn-download-primary {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
    }

    .btn-download-primary:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .btn-download-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .btn-download-secondary {
        background: linear-gradient(135deg, #882426 0%, #a52d2f 100%);
        color: white;
    }

    .btn-download-secondary:hover {
        background: linear-gradient(135deg, #a52d2f 0%, #b73436 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(136, 36, 38, 0.3);
    }

    .btn-download-secondary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    /* Spinner animation */
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>

<body class="w-full min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>
    <main class="max-w-full mb-10 pt-16 md:pt-40 lg:pt-[165px]">
        <section class="bg-[#882426] py-12 md:py-16 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 right-20 w-40 h-40 border-4 border-white rounded-full"></div>
                <div class="absolute bottom-10 left-20 w-32 h-32 border-4 border-white rounded-full"></div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl mb-6 backdrop-blur-sm">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-4">Syarat & Ketentuan</h1>
                <p class="text-white/80 text-lg max-w-2xl mx-auto">
                    Harap membaca syarat dan ketentuan ini dengan saksama sebelum melakukan transaksi di
                    <span class="text-white/70 font-medium">
                        <?= !empty($globalSettings['site_title'])
                            ? htmlspecialchars(mb_convert_case(formatKapitalDepan($globalSettings['site_title']), MB_CASE_TITLE, 'UTF-8'))
                            : 'Nano Komputer'
                        ?>
                    </span>
                </p>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-sm text-white/70">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Terakhir diperbarui: <?= $lastUpdated ?>
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Waktu baca: 8 menit
                    </span>
                </div>
            </div>
        </section>

        <div class="mt-10 w-full px-5 md:px-8 lg:px-20">
            <?php include '../../components/users/breadcrumb.php'; ?>
            <div class="lg:hidden mb-8">
                <label for="mobile-nav" class="block text-sm font-semibold text-gray-700 mb-2">Navigasi Cepat</label>
                <select id="mobile-nav" onchange="scrollToSection(this.value)" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl text-gray-700 focus:border-[#882426] focus:outline-none transition-colors">
                    <option value="pendahuluan">1. Pendahuluan</option>
                    <option value="pemesanan">2. Pemesanan & Pembayaran</option>
                    <option value="pengiriman">3. Pengiriman & Rakit PC</option>
                    <option value="garansi">4. Garansi & Pengembalian</option>
                    <option value="privasi">5. Kebijakan Privasi</option>
                </select>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <aside class="hidden lg:block w-72 flex-shrink-0">
                    <div class="sticky top-24 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4 text-lg">Daftar Isi</h3>
                        <nav class="space-y-1" id="terms-nav">
                            <a href="#pendahuluan" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all bg-[#882426] text-white">
                                <span class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center text-xs font-bold">1</span>
                                Pendahuluan
                            </a>
                            <a href="#pemesanan" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">
                                <span class="w-6 h-6 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-bold">2</span>
                                Pemesanan & Pembayaran
                            </a>
                            <a href="#pengiriman" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">
                                <span class="w-6 h-6 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-bold">3</span>
                                Pengiriman & Rakit PC
                            </a>
                            <a href="#garansi" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">
                                <span class="w-6 h-6 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-bold">4</span>
                                Garansi & Pengembalian
                            </a>
                            <a href="#privasi" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">
                                <span class="w-6 h-6 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-bold">5</span>
                                Kebijakan Privasi
                            </a>
                        </nav>

                        <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                            <!-- Download PDF Button -->
                            <button onclick="generatePDF('sidebar')" id="downloadPdfBtnSidebar" class="w-full btn-download btn-download-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Unduh PDF</span>
                            </button>
                        </div>
                    </div>
                </aside>

                <div class="flex-1 space-y-6">
                    <section id="pendahuluan" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-blue-500 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">1. Pendahuluan</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="prose text-gray-600 leading-relaxed space-y-4">
                                <p>Selamat datang di <strong class="text-[#882426]">
                                        <?= !empty($globalSettings['site_title'])
                                            ? htmlspecialchars(mb_convert_case(formatKapitalDepan($globalSettings['site_title']), MB_CASE_TITLE, 'UTF-8'))
                                            : 'Nano Komputer'
                                        ?>
                                    </strong>.
                                    Syarat & ketentuan berikut menjelaskan peraturan dan ketentuan penggunaan Website <?= !empty($globalSettings['site_title']) ? htmlspecialchars(strtolower($globalSettings['site_title'])) : 'Nano Komputer' ?>.</p>
                                <p>Dengan menggunakan layanan kami, Anda dianggap telah <strong>menyetujui seluruh ketentuan</strong> yang berlaku di halaman ini.</p>
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mt-4">
                                    <p class="text-sm text-blue-800 flex items-start gap-2">
                                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Kami berhak untuk mengubah, memodifikasi, menambah, atau menghapus bagian dari syarat dan ketentuan ini kapan saja tanpa pemberitahuan sebelumnya.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="pemesanan" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-green-500 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">2. Pemesanan & Pembayaran</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="space-y-4">
                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Ketersediaan Stok</h4>
                                        <p class="text-sm text-gray-600">Stok produk di website bersifat dinamis. Meskipun kami berusaha memperbarui stok secara <em>real-time</em>, konfirmasi ketersediaan barang sangat disarankan sebelum melakukan pembayaran.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Perubahan Harga</h4>
                                        <p class="text-sm text-gray-600">Harga produk dapat berubah sewaktu-waktu mengikuti nilai tukar mata uang asing dan kebijakan distributor resmi tanpa pemberitahuan sebelumnya.</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-10 h-10 bg-[#882426]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Pembayaran</h4>
                                        <p class="text-sm text-gray-600">Kami menerima pembayaran melalui Transfer Bank, E-Wallet, dan Kartu Kredit. Pesanan akan diproses setelah pembayaran terverifikasi oleh sistem kami (maksimal 1x24 jam).</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 bg-amber-50 rounded-xl border border-amber-100">
                                    <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-amber-800 mb-1">Pembatalan Otomatis</h4>
                                        <p class="text-sm text-amber-700">Pesanan yang belum dibayar dalam waktu <strong>24 jam</strong> akan otomatis dibatalkan oleh sistem.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="pengiriman" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-amber-500 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">3. Pengiriman & Jasa Rakit</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-5 mb-6">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="font-bold text-amber-800 mb-1">Penting: Asuransi Pengiriman</p>
                                        <p class="text-sm text-amber-700">Untuk pembelian produk bernilai tinggi (VGA, Monitor, CPU, Laptop), pembeli <strong>WAJIB</strong> menggunakan asuransi pengiriman. Segala kehilangan atau kerusakan saat pengiriman tanpa asuransi adalah tanggung jawab ekspedisi & pembeli.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4">
                                <div class="flex items-start gap-4">
                                    <span class="flex-shrink-0 w-8 h-8 bg-[#882426] text-white rounded-lg flex items-center justify-center text-sm font-bold">1</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Waktu Proses</h4>
                                        <p class="text-sm text-gray-600">Pesanan komponen lepas (loose parts) akan dikirim <strong>H+1</strong> setelah pembayaran terverifikasi.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <span class="flex-shrink-0 w-8 h-8 bg-[#882426] text-white rounded-lg flex items-center justify-center text-sm font-bold">2</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Pemesanan Rakit PC</h4>
                                        <p class="text-sm text-gray-600">Untuk pemesanan Full PC Build (Rakit), proses perakitan, instalasi, dan stress test membutuhkan waktu <strong>2-3 hari kerja</strong> untuk memastikan PC berjalan stabil.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <span class="flex-shrink-0 w-8 h-8 bg-[#882426] text-white rounded-lg flex items-center justify-center text-sm font-bold">3</span>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Packing Kayu</h4>
                                        <p class="text-sm text-gray-600">Pengiriman PC Rakitan ke luar kota Jakarta <strong>WAJIB</strong> menggunakan Packing Kayu untuk keamanan ekstra. Biaya packing kayu akan ditambahkan pada total ongkos kirim.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="garansi" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">4. Garansi & Pengembalian (RMA)</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-5 mb-6">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-bold text-red-700 mb-2 uppercase tracking-wide">Wajib Video Unboxing</p>
                                        <p class="text-sm text-red-700">Komplain kekurangan barang, cacat fisik, atau barang tidak sesuai <strong>TIDAK AKAN DITERIMA</strong> tanpa menyertakan video unboxing utuh (tanpa cut/edit) yang memperlihatkan label pengiriman hingga barang dibuka & dites fisik.</p>
                                    </div>
                                </div>
                            </div>

                            <h4 class="font-bold text-gray-900 mb-4">Ketentuan Garansi Komponen:</h4>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-600">Barang yang kami jual bergaransi resmi distributor Indonesia (kecuali tertulis "Garansi Toko").</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-600">Untuk klaim garansi (RMA), pembeli dapat menyerahkan barang ke toko kami atau langsung ke Service Center distributor terkait.</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-[#882426] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-gray-600"><strong>Biaya ongkir PP ditanggung pembeli.</strong></span>
                                </li>
                            </ul>

                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Garansi Batal (Void) Jika:
                                </h4>
                                <div class="grid sm:grid-cols-2 gap-3">
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cacat fisik (patah, bengkok, korosi, terbakar)
                                    </div>
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Segel garansi rusak/hilang
                                    </div>
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Kesalahan penggunaan (Human Error)
                                    </div>
                                    <div class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Modifikasi BIOS yang gagal
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="privasi" class="scroll-mt-28 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-purple-500 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">5. Kebijakan Privasi</h2>
                            </div>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="prose text-gray-600 leading-relaxed space-y-4">
                                <p>
                                    <strong class="text-[#882426] capitalize">
                                        <?= !empty($globalSettings['site_title'])
                                            ? htmlspecialchars(mb_convert_case(formatKapitalDepan($globalSettings['site_title']), MB_CASE_TITLE, 'UTF-8'))
                                            : 'Nano Komputer'
                                        ?>
                                    </strong>
                                    menghargai privasi Anda. Informasi pribadi yang Anda berikan (Nama, Alamat, No. Telepon) hanya digunakan untuk keperluan pemrosesan pesanan dan pengiriman.
                                </p>
                                <p>Kami <strong>tidak akan</strong> menjual, menyewakan, atau membagikan informasi pribadi Anda kepada pihak ketiga manapun tanpa persetujuan Anda, kecuali jika diwajibkan oleh hukum atau untuk keperluan logistik (Ekspedisi).</p>

                                <div class="bg-purple-50 border border-purple-100 rounded-xl p-5 mt-6">
                                    <h4 class="font-bold text-purple-800 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Data Anda Aman
                                    </h4>
                                    <ul class="space-y-2 text-sm text-purple-700">
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Enkripsi SSL pada seluruh transaksi
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Data tidak dibagikan ke pihak ketiga
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Penyimpanan data sesuai standar keamanan
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="bg-[#882426] rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-center md:text-left">
                            <h3 class="font-bold text-white text-xl mb-2">Masih ada pertanyaan?</h3>
                            <p class="text-white/80">Tim Customer Service kami siap membantu Anda 24/7.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="faq.php" class="px-6 py-3 bg-white/10 text-white font-medium rounded-xl hover:bg-white/20 transition-colors border border-white/20 text-center">
                                Lihat FAQ
                            </a>
                            <a href="aboutContact.php#contact" class="px-6 py-3 bg-white text-[#882426] font-semibold rounded-xl hover:bg-gray-100 transition-colors shadow-lg text-center">
                                Hubungi Kami
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div>
            <div class="toast-title" id="toast-title">Berhasil</div>
            <div class="toast-message" id="toast-message">Pesan sukses</div>
        </div>
    </div>

    <script>
        function scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('#terms-nav a');
            const mobileNav = document.getElementById('mobile-nav');

            function updateActiveNav() {
                let current = '';

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (scrollY >= (sectionTop - 150)) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    const href = link.getAttribute('href').substring(1);
                    const numSpan = link.querySelector('span');

                    if (href === current) {
                        link.classList.add('bg-[#882426]', 'text-white');
                        link.classList.remove('text-gray-600', 'hover:bg-gray-100');
                        if (numSpan) {
                            numSpan.classList.add('bg-white/20');
                            numSpan.classList.remove('bg-gray-200');
                        }
                    } else {
                        link.classList.remove('bg-[#882426]', 'text-white');
                        link.classList.add('text-gray-600', 'hover:bg-gray-100');
                        if (numSpan) {
                            numSpan.classList.remove('bg-white/20');
                            numSpan.classList.add('bg-gray-200');
                        }
                    }
                });

                if (mobileNav && current) {
                    mobileNav.value = current;
                }
            }

            window.addEventListener('scroll', updateActiveNav);
            updateActiveNav();
        });

        // Show toast notification
        function showToast(title, message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastTitle = document.getElementById('toast-title');
            const toastMessage = document.getElementById('toast-message');

            toastTitle.textContent = title;
            toastMessage.textContent = message;

            toast.className = 'toast ' + type;
            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Generate PDF for Terms & Conditions
        function generatePDF(source = 'sidebar') {
            const btnId = source === 'hero' ? 'downloadPdfBtnHero' : 'downloadPdfBtnSidebar';
            const btn = document.getElementById(btnId);
            const btnText = btn.querySelector('span');
            const originalText = btnText.textContent;

            btn.disabled = true;
            btnText.textContent = 'Membuat PDF...';

            try {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const pageWidth = doc.internal.pageSize.getWidth();
                const margin = 20;
                const contentWidth = pageWidth - margin * 2;
                let yPosition = 20;

                // Header with Nano Komputer branding
                doc.setFillColor(136, 36, 38);
                doc.rect(0, 0, pageWidth, 45, 'F');

                // Company name
                doc.setTextColor(255, 255, 255);
                doc.setFontSize(12);
                doc.setFont('helvetica', 'normal');
                doc.text('NANO KOMPUTER', pageWidth / 2, 15, {
                    align: 'center'
                });

                // Document title
                doc.setFontSize(24);
                doc.setFont('helvetica', 'bold');
                doc.text('Syarat & Ketentuan', pageWidth / 2, 28, {
                    align: 'center'
                });

                // Subtitle
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                doc.text('Dokumen Resmi - www.nanokomputer.com', pageWidth / 2, 38, {
                    align: 'center'
                });

                yPosition = 55;

                // Last updated info
                doc.setTextColor(100, 100, 100);
                doc.setFontSize(10);
                doc.text('Terakhir diperbarui: <?= $lastUpdated ?>', margin, yPosition);
                yPosition += 15;

                // Content sections for Terms & Conditions
                const sections = [{
                        title: '1. Pendahuluan',
                        content: [
                            'Selamat datang di Nano Komputer. Syarat & ketentuan berikut menjelaskan',
                            'peraturan dan ketentuan penggunaan Website Nano Komputer.',
                            '',
                            'Dengan menggunakan layanan kami, Anda dianggap telah menyetujui seluruh',
                            'ketentuan yang berlaku di halaman ini.',
                            '',
                            'Kami berhak untuk mengubah, memodifikasi, menambah, atau menghapus bagian',
                            'dari syarat dan ketentuan ini kapan saja tanpa pemberitahuan sebelumnya.'
                        ]
                    },
                    {
                        title: '2. Pemesanan & Pembayaran',
                        content: [
                            'Ketersediaan Stok:',
                            '• Stok produk di website bersifat dinamis',
                            '• Konfirmasi ketersediaan barang sangat disarankan sebelum pembayaran',
                            '',
                            'Perubahan Harga:',
                            '• Harga produk dapat berubah sewaktu-waktu mengikuti nilai tukar mata uang',
                            '• Mengikuti kebijakan distributor resmi tanpa pemberitahuan sebelumnya',
                            '',
                            'Pembayaran:',
                            '• Kami menerima Transfer Bank, E-Wallet, dan Kartu Kredit',
                            '• Pesanan akan diproses setelah pembayaran terverifikasi (maksimal 1x24 jam)',
                            '',
                            'Pembatalan Otomatis:',
                            '• Pesanan yang belum dibayar dalam waktu 24 jam akan otomatis dibatalkan'
                        ]
                    },
                    {
                        title: '3. Pengiriman & Jasa Rakit',
                        content: [
                            'PENTING - Asuransi Pengiriman:',
                            '• Untuk produk bernilai tinggi (VGA, Monitor, CPU, Laptop), pembeli WAJIB',
                            '  menggunakan asuransi pengiriman',
                            '• Kehilangan/kerusakan tanpa asuransi adalah tanggung jawab ekspedisi & pembeli',
                            '',
                            'Waktu Proses:',
                            '• Pesanan komponen lepas dikirim H+1 setelah pembayaran terverifikasi',
                            '• Pemesanan Full PC Build membutuhkan waktu 2-3 hari kerja',
                            '',
                            'Packing Kayu:',
                            '• Pengiriman PC Rakitan ke luar Jakarta WAJIB menggunakan Packing Kayu',
                            '• Biaya packing kayu ditambahkan pada total ongkos kirim'
                        ]
                    },
                    {
                        title: '4. Garansi & Pengembalian (RMA)',
                        content: [
                            'WAJIB VIDEO UNBOXING:',
                            '• Komplain kekurangan barang, cacat fisik, atau barang tidak sesuai TIDAK',
                            '  AKAN DITERIMA tanpa video unboxing utuh (tanpa cut/edit)',
                            '• Video harus memperlihatkan label pengiriman hingga barang dibuka & dites',
                            '',
                            'Ketentuan Garansi:',
                            '• Barang bergaransi resmi distributor Indonesia (kecuali tertulis "Garansi Toko")',
                            '• Klaim garansi dapat diserahkan ke toko kami atau Service Center distributor',
                            '• Biaya ongkir PP ditanggung pembeli',
                            '',
                            'Garansi Batal (Void) Jika:',
                            '• Cacat fisik (patah, bengkok, korosi, terbakar)',
                            '• Segel garansi rusak/hilang',
                            '• Kesalahan penggunaan (Human Error)',
                            '• Modifikasi BIOS yang gagal'
                        ]
                    },
                    {
                        title: '5. Kebijakan Privasi',
                        content: [
                            'Nano Komputer menghargai privasi Anda. Informasi pribadi yang Anda berikan',
                            '(Nama, Alamat, No. Telepon) hanya digunakan untuk keperluan pemrosesan',
                            'pesanan dan pengiriman.',
                            '',
                            'Kami tidak akan menjual, menyewakan, atau membagikan informasi pribadi Anda',
                            'kepada pihak ketiga manapun tanpa persetujuan Anda, kecuali jika diwajibkan',
                            'oleh hukum atau untuk keperluan logistik (Ekspedisi).',
                            '',
                            'Keamanan Data:',
                            '• Enkripsi SSL pada seluruh transaksi',
                            '• Data tidak dibagikan ke pihak ketiga',
                            '• Penyimpanan data sesuai standar keamanan'
                        ]
                    },
                    {
                        title: '6. Hubungi Kami',
                        content: [
                            'Jika Anda memiliki pertanyaan tentang Syarat & Ketentuan ini:',
                            '',
                            'Email: cs@nanokomputer.com',
                            'Telepon: (021) 623-09578',
                            'WhatsApp: +62 812-8888-9578',
                            'Alamat: Mangga Dua Mall, Jl. Mangga Dua Raya No.47A-B',
                            'Lantai 2, Jakarta Pusat 10730',
                            '',
                            'Jam Operasional:',
                            'Senin - Sabtu: 09.00 - 18.00 WIB',
                            'Minggu & Hari Libur: Tutup'
                        ]
                    }
                ];

                doc.setTextColor(50, 50, 50);

                sections.forEach((section) => {
                    // Check if we need a new page
                    if (yPosition > 250) {
                        doc.addPage();
                        yPosition = 20;
                    }

                    // Section title
                    doc.setFontSize(14);
                    doc.setFont('helvetica', 'bold');
                    doc.setTextColor(136, 36, 38);
                    doc.text(section.title, margin, yPosition);
                    yPosition += 8;

                    // Section content
                    doc.setFontSize(10);
                    doc.setFont('helvetica', 'normal');
                    doc.setTextColor(60, 60, 60);

                    section.content.forEach((line) => {
                        if (yPosition > 270) {
                            doc.addPage();
                            yPosition = 20;
                        }

                        const splitText = doc.splitTextToSize(line, contentWidth);
                        splitText.forEach((textLine) => {
                            doc.text(textLine, margin, yPosition);
                            yPosition += 5;
                        });
                    });

                    yPosition += 8;
                });

                // Footer on all pages
                const pageCount = doc.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);

                    // Footer line
                    doc.setDrawColor(136, 36, 38);
                    doc.setLineWidth(0.5);
                    doc.line(margin, 282, pageWidth - margin, 282);

                    // Footer text
                    doc.setFontSize(8);
                    doc.setTextColor(150, 150, 150);
                    doc.text(
                        'Halaman ' + i + ' dari ' + pageCount + ' | © 2025 Nano Komputer - Syarat & Ketentuan',
                        pageWidth / 2,
                        288, {
                            align: 'center'
                        }
                    );
                }

                doc.save('Syarat-Ketentuan-Nano-Komputer.pdf');

                showToast('PDF Berhasil Dibuat', 'Dokumen Syarat & Ketentuan telah diunduh.', 'success');
            } catch (error) {
                console.error('Error generating PDF:', error);
                showToast('Gagal Membuat PDF', 'Terjadi kesalahan saat membuat dokumen PDF.', 'error');
            } finally {
                btn.disabled = false;
                btnText.textContent = originalText;
            }
        }
    </script>

    <style>
        @media print {

            header,
            footer,
            aside,
            .no-print {
                display: none !important;
            }

            main {
                padding: 0 !important;
            }

            section {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</body>

</html>