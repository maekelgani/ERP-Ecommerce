<?php
$pageTitle = "Kebijakan Privasi";
require_once __DIR__ . '/../../config/config.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

include '../../components/users/head.php';

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Kebijakan Privasi', 'url' => null]
];

$lastUpdated = "1 Desember 2025";
?>
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

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    .animate-slide-up {
        animation: slideUp 0.5s ease-out forwards;
    }

    /* Hero Section */
    .hero {
        background-color: var(--primary);
        color: var(--primary-foreground);
        padding: 4rem 1rem 6rem;
        text-align: center;
    }

    @media (min-width: 768px) {
        .hero {
            padding: 6rem 1rem 8rem;
        }
    }

    .hero-container {
        max-width: 56rem;
        margin: 0 auto;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 9999px;
        padding: 0.5rem 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .hero-icon-wrapper {
        width: 5rem;
        height: 5rem;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .hero h1 {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    @media (min-width: 768px) {
        .hero h1 {
            font-size: 3rem;
        }
    }

    .hero p {
        font-size: 1.125rem;
        opacity: 0.8;
        max-width: 42rem;
        margin: 0 auto 2rem;
    }

    @media (min-width: 768px) {
        .hero p {
            font-size: 1.25rem;
        }
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background-color: var(--primary-foreground);
        color: var(--primary);
    }

    .btn-primary:hover {
        background-color: rgba(255, 255, 255, 0.9);
    }

    .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-outline {
        background-color: transparent;
        color: var(--foreground);
        border: 1px solid var(--border);
    }

    .btn-outline:hover {
        background-color: var(--muted);
    }

    .hero-date {
        margin-top: 2rem;
        font-size: 0.875rem;
        opacity: 0.6;
    }

    /* Main Content */
    .main {
        max-width: 56rem;
        margin: 0 auto;
        padding: 1rem 1rem;
    }

    /* Notice Box */
    .notice {
        background-color: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 1rem;
        padding: 1.25rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .notice-icon {
        color: var(--success);
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .notice h3 {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .notice p {
        font-size: 0.875rem;
        color: var(--muted-foreground);
    }

    /* Table of Contents */
    .toc {
        background-color: var(--card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .toc-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .toc-icon {
        width: 2.5rem;
        height: 2.5rem;
        background-color: rgba(136, 36, 38, 0.1);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }

    .toc h2 {
        font-weight: 600;
        font-size: 1.125rem;
    }

    .toc-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }

    @media (min-width: 640px) {
        .toc-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .toc-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .toc-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .toc-item:hover {
        background-color: var(--muted);
    }

    .toc-item:hover .toc-number {
        background-color: var(--primary);
        color: var(--primary-foreground);
    }

    .toc-number {
        width: 1.5rem;
        height: 1.5rem;
        background-color: var(--muted);
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .toc-text {
        font-size: 0.875rem;
        color: var(--muted-foreground);
        transition: color 0.2s;
    }

    .toc-item:hover .toc-text {
        color: var(--foreground);
    }

    /* Policy Sections */
    .sections {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .section {
        background-color: var(--card);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        overflow: hidden;
        transition: box-shadow 0.3s;
    }

    .section:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: none;
        border: none;
        cursor: pointer;
        text-align: left;
        transition: background-color 0.2s;
    }

    .section-header:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .section-number {
        width: 2.5rem;
        height: 2.5rem;
        background-color: var(--primary);
        color: var(--primary-foreground);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .section-title {
        flex: 1;
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--foreground);
    }

    .section-chevron {
        color: var(--muted-foreground);
        transition: transform 0.3s;
    }

    .section.open .section-chevron {
        transform: rotate(180deg);
    }

    .section-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out, opacity 0.3s;
        opacity: 0;
    }

    .section.open .section-content {
        max-height: 2000px;
        opacity: 1;
    }

    .section-body {
        padding: 0 1.25rem 1.25rem;
        margin-left: 3.5rem;
    }

    .section-body p {
        color: var(--muted-foreground);
        margin-bottom: 1rem;
    }

    /* Info Cards Grid */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    @media (min-width: 768px) {
        .info-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .info-card {
        background-color: var(--muted);
        border-radius: 0.75rem;
        padding: 1rem;
        transition: background-color 0.3s;
    }

    .info-card:hover {
        background-color: var(--accent);
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .info-card-header i {
        color: var(--primary);
    }

    .info-card-header h3 {
        font-weight: 600;
        font-size: 0.9375rem;
    }

    .info-card ul {
        list-style: none;
    }

    .info-card li {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--muted-foreground);
        margin-bottom: 0.5rem;
    }

    .info-card li::before {
        content: '';
        width: 0.375rem;
        height: 0.375rem;
        background-color: var(--primary);
        border-radius: 50%;
        margin-top: 0.5rem;
        flex-shrink: 0;
    }

    /* Security Cards */
    .security-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    @media (min-width: 768px) {
        .security-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .security-card {
        background-color: var(--muted);
        border-radius: 0.75rem;
        padding: 1.25rem;
        text-align: center;
        transition: background-color 0.3s;
    }

    .security-card:hover {
        background-color: var(--accent);
    }

    .security-icon {
        width: 3.5rem;
        height: 3.5rem;
        background-color: rgba(136, 36, 38, 0.1);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        color: var(--primary);
        transition: background-color 0.3s;
    }

    .security-card:hover .security-icon {
        background-color: rgba(136, 36, 38, 0.2);
    }

    .security-card h4 {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .security-card p {
        font-size: 0.875rem;
        color: var(--muted-foreground);
        margin: 0;
    }

    /* Check Items Grid */
    .check-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    @media (min-width: 640px) {
        .check-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .check-item {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.75rem;
        background-color: var(--muted);
        border-radius: 0.5rem;
    }

    .check-item i {
        color: var(--success);
        margin-top: 0.125rem;
        flex-shrink: 0;
    }

    .check-item span {
        font-size: 0.875rem;
        color: var(--muted-foreground);
    }

    /* Rights Grid */
    .rights-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    @media (min-width: 640px) {
        .rights-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .right-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background-color: var(--muted);
        border-radius: 0.75rem;
        transition: background-color 0.3s;
    }

    .right-item:hover {
        background-color: var(--accent);
    }

    .right-check {
        width: 1.5rem;
        height: 1.5rem;
        background-color: var(--success);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .right-check i {
        color: white;
    }

    .right-item span {
        font-size: 0.875rem;
    }

    /* Warning Box */
    .warning {
        background-color: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .warning i {
        color: var(--warning);
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .warning p {
        font-size: 0.875rem;
        color: var(--muted-foreground);
        margin: 0;
    }

    .warning strong {
        color: var(--foreground);
    }

    /* Share Info List */
    .share-list {
        list-style: none;
    }

    .share-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem;
        background-color: var(--muted);
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .share-icon {
        width: 2rem;
        height: 2rem;
        background-color: rgba(136, 36, 38, 0.1);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--primary);
    }

    .share-item strong {
        display: block;
        margin-bottom: 0.125rem;
    }

    .share-item p {
        font-size: 0.875rem;
        color: var(--muted-foreground);
        margin: 0;
    }

    /* Note Box */
    .note {
        font-size: 0.875rem;
        color: var(--muted-foreground);
        background-color: var(--muted);
        padding: 1rem;
        border-radius: 0.5rem;
    }

    /* Cookie List */
    .cookie-list {
        list-style: none;
        margin-bottom: 1rem;
    }

    .cookie-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        color: var(--muted-foreground);
        margin-bottom: 0.5rem;
    }

    .cookie-list li::before {
        content: '';
        width: 0.375rem;
        height: 0.375rem;
        background-color: var(--primary);
        border-radius: 50%;
        margin-top: 0.625rem;
        flex-shrink: 0;
    }

    /* Contact Box */
    .contact-box {
        background-color: var(--muted);
        border-radius: 0.75rem;
        padding: 1.25rem;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .contact-item:last-child {
        margin-bottom: 0;
    }

    .contact-item.address {
        align-items: flex-start;
    }

    .contact-icon {
        width: 2.5rem;
        height: 2.5rem;
        background-color: rgba(136, 36, 38, 0.1);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--primary);
    }

    .contact-label {
        font-size: 0.875rem;
        color: var(--muted-foreground);
    }

    .contact-value {
        font-weight: 500;
    }

    /* Icon with text */
    .icon-text {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .icon-text i {
        color: var(--primary);
        flex-shrink: 0;
        margin-top: 0.25rem;
    }

    /* Back Button */
    .back-section {
        margin-top: 3rem;
        text-align: center;
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
</style>

<body class="w-full min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <div id="navbarSpacer" class="transition-all duration-300 pt-16 md:pt-40 lg:pt-[160px]"></div>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-badge animate-fade-in">
                <i data-lucide="file-text" style="width: 1rem; height: 1rem;"></i>
                <span>Dokumen Legal</span>
            </div>

            <div class="hero-icon-wrapper animate-slide-up" style="animation-delay: 0.1s;">
                <i data-lucide="shield" style="width: 2.5rem; height: 2.5rem;"></i>
            </div>

            <h1 class="animate-slide-up" style="animation-delay: 0.2s;">Kebijakan Privasi</h1>

            <p class="animate-slide-up" style="animation-delay: 0.3s;">
                Komitmen kami dalam melindungi privasi dan data pribadi Anda dengan standar keamanan tertinggi
            </p>

            <div class="animate-slide-up" style="animation-delay: 0.4s;">
                <button class="btn btn-primary" id="downloadPdfBtn" onclick="generatePDF()">
                    <i data-lucide="download" style="width: 1rem; height: 1rem;"></i>
                    <span>Unduh PDF</span>
                </button>
            </div>

            <div class="hero-date animate-fade-in" style="animation-delay: 0.5s;">
                Terakhir diperbarui: 1 Desember 2025
            </div>
        </div>
    </section>

    <div class="w-full px-4 md:px-8 lg:px-20 py-6">
        <?php include '../../components/users/breadcrumb.php'; ?>
    </div>
    <!-- Main Content -->
    <main class="main">
        <!-- Notice -->
        <div class="notice animate-slide-up">
            <i data-lucide="check-circle" class="notice-icon" style="width: 1.5rem; height: 1.5rem;"></i>
            <div>
                <h3>Komitmen Kami</h3>
                <p>Nano Komputer berkomitmen untuk melindungi privasi Anda. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda sesuai dengan peraturan yang berlaku di Indonesia.</p>
            </div>
        </div>

        <!-- Table of Contents -->
        <div class="toc animate-slide-up">
            <div class="toc-header">
                <div class="toc-icon">
                    <i data-lucide="list" style="width: 1.25rem; height: 1.25rem;"></i>
                </div>
                <h2>Daftar Isi</h2>
            </div>
            <div class="toc-grid">
                <div class="toc-item" onclick="toggleSection(1)">
                    <span class="toc-number">1</span>
                    <span class="toc-text">Informasi yang Kami Kumpulkan</span>
                </div>
                <div class="toc-item" onclick="toggleSection(2)">
                    <span class="toc-number">2</span>
                    <span class="toc-text">Penggunaan Informasi</span>
                </div>
                <div class="toc-item" onclick="toggleSection(3)">
                    <span class="toc-number">3</span>
                    <span class="toc-text">Berbagi Informasi</span>
                </div>
                <div class="toc-item" onclick="toggleSection(4)">
                    <span class="toc-number">4</span>
                    <span class="toc-text">Keamanan Data</span>
                </div>
                <div class="toc-item" onclick="toggleSection(5)">
                    <span class="toc-number">5</span>
                    <span class="toc-text">Cookies</span>
                </div>
                <div class="toc-item" onclick="toggleSection(6)">
                    <span class="toc-number">6</span>
                    <span class="toc-text">Hak Anda</span>
                </div>
                <div class="toc-item" onclick="toggleSection(7)">
                    <span class="toc-number">7</span>
                    <span class="toc-text">Penyimpanan Data</span>
                </div>
                <div class="toc-item" onclick="toggleSection(8)">
                    <span class="toc-number">8</span>
                    <span class="toc-text">Perubahan Kebijakan</span>
                </div>
                <div class="toc-item" onclick="toggleSection(9)">
                    <span class="toc-number">9</span>
                    <span class="toc-text">Hubungi Kami</span>
                </div>
            </div>
        </div>

        <!-- Policy Sections -->
        <div class="sections">
            <!-- Section 1 -->
            <div class="section open" id="section-1">
                <button class="section-header" onclick="toggleSection(1)">
                    <span class="section-number">1</span>
                    <span class="section-title">Informasi yang Kami Kumpulkan</span>
                    <i data-lucide="chevron-down" class="section-chevron" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div class="section-content">
                    <div class="section-body">
                        <p>Kami mengumpulkan beberapa jenis informasi untuk memberikan layanan terbaik kepada Anda:</p>
                        <div class="info-grid">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i data-lucide="user-check" style="width: 1.25rem; height: 1.25rem;"></i>
                                    <h3>Informasi yang Anda Berikan</h3>
                                </div>
                                <ul>
                                    <li>Nama lengkap dan alamat email</li>
                                    <li>Nomor telepon dan alamat pengiriman</li>
                                    <li>Informasi pembayaran (dienkripsi)</li>
                                    <li>Riwayat pesanan dan preferensi belanja</li>
                                </ul>
                            </div>
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i data-lucide="database" style="width: 1.25rem; height: 1.25rem;"></i>
                                    <h3>Informasi Otomatis</h3>
                                </div>
                                <ul>
                                    <li>Alamat IP dan jenis browser</li>
                                    <li>Perangkat yang digunakan</li>
                                    <li>Halaman yang dikunjungi dan waktu kunjungan</li>
                                    <li>Cookies dan teknologi pelacakan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2 -->
            <div class="section" id="section-2">
                <button class="section-header" onclick="toggleSection(2)">
                    <span class="section-number">2</span>
                    <span class="section-title">Penggunaan Informasi</span>
                    <i data-lucide="chevron-down" class="section-chevron" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div class="section-content">
                    <div class="section-body">
                        <p>Informasi yang kami kumpulkan digunakan untuk tujuan berikut:</p>
                        <div class="check-grid">
                            <div class="check-item">
                                <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                <span>Memproses dan mengirimkan pesanan Anda</span>
                            </div>
                            <div class="check-item">
                                <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                <span>Berkomunikasi tentang pesanan dan layanan</span>
                            </div>
                            <div class="check-item">
                                <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                <span>Menyediakan dukungan pelanggan</span>
                            </div>
                            <div class="check-item">
                                <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                <span>Mengirim promosi (dengan persetujuan)</span>
                            </div>
                            <div class="check-item">
                                <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                <span>Meningkatkan website dan pengalaman</span>
                            </div>
                            <div class="check-item">
                                <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                <span>Mencegah penipuan dan menjaga keamanan</span>
                            </div>
                            <div class="check-item">
                                <i data-lucide="check-circle" style="width: 1rem; height: 1rem;"></i>
                                <span>Mematuhi kewajiban hukum</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3 -->
            <div class="section" id="section-3">
                <button class="section-header" onclick="toggleSection(3)">
                    <span class="section-number">3</span>
                    <span class="section-title">Berbagi Informasi</span>
                    <i data-lucide="chevron-down" class="section-chevron" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div class="section-content">
                    <div class="section-body">
                        <div class="warning">
                            <i data-lucide="alert-circle" style="width: 1.25rem; height: 1.25rem;"></i>
                            <p><strong>Penting:</strong> Kami tidak menjual informasi pribadi Anda kepada pihak ketiga.</p>
                        </div>
                        <p>Kami hanya berbagi informasi dengan pihak berikut:</p>
                        <ul class="share-list">
                            <li class="share-item">
                                <div class="share-icon">
                                    <i data-lucide="server" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                <div>
                                    <strong>Penyedia Layanan</strong>
                                    <p>Kurir pengiriman, payment gateway, penyedia hosting</p>
                                </div>
                            </li>
                            <li class="share-item">
                                <div class="share-icon">
                                    <i data-lucide="shield" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                <div>
                                    <strong>Mitra Bisnis</strong>
                                    <p>Brand dan distributor untuk keperluan garansi produk</p>
                                </div>
                            </li>
                            <li class="share-item">
                                <div class="share-icon">
                                    <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                <div>
                                    <strong>Otoritas Hukum</strong>
                                    <p>Jika diwajibkan oleh hukum yang berlaku di Indonesia</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 4 -->
            <div class="section" id="section-4">
                <button class="section-header" onclick="toggleSection(4)">
                    <span class="section-number">4</span>
                    <span class="section-title">Keamanan Data</span>
                    <i data-lucide="chevron-down" class="section-chevron" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div class="section-content">
                    <div class="section-body">
                        <p>Kami mengimplementasikan berbagai langkah keamanan untuk melindungi data pribadi Anda:</p>
                        <div class="security-grid">
                            <div class="security-card">
                                <div class="security-icon">
                                    <i data-lucide="lock" style="width: 1.75rem; height: 1.75rem;"></i>
                                </div>
                                <h4>Enkripsi SSL</h4>
                                <p>Semua data dienkripsi saat ditransmisikan melalui internet</p>
                            </div>
                            <div class="security-card">
                                <div class="security-icon">
                                    <i data-lucide="server" style="width: 1.75rem; height: 1.75rem;"></i>
                                </div>
                                <h4>Server Aman</h4>
                                <p>Data disimpan di server dengan proteksi keamanan tinggi</p>
                            </div>
                        </div>
                        <div class="note">
                            Meskipun kami berusaha keras melindungi data Anda, tidak ada metode transmisi internet yang 100% aman. Kami terus meningkatkan langkah-langkah keamanan sesuai perkembangan teknologi.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5 -->
            <div class="section" id="section-5">
                <button class="section-header" onclick="toggleSection(5)">
                    <span class="section-number">5</span>
                    <span class="section-title">Cookies</span>
                    <i data-lucide="chevron-down" class="section-chevron" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div class="section-content">
                    <div class="section-body">
                        <div class="icon-text" style="margin-bottom: 1rem;">
                            <i data-lucide="cookie" style="width: 1.25rem; height: 1.25rem;"></i>
                            <p style="margin: 0;">Cookies adalah file teks kecil yang disimpan di perangkat Anda saat mengunjungi website kami.</p>
                        </div>
                        <p>Kami menggunakan cookies untuk:</p>
                        <ul class="cookie-list">
                            <li>Mengingat preferensi dan pengaturan Anda</li>
                            <li>Menjaga sesi login tetap aktif</li>
                            <li>Menganalisis lalu lintas website</li>
                            <li>Menampilkan konten yang relevan</li>
                        </ul>
                        <div class="note">
                            Anda dapat mengatur browser untuk menolak cookies, namun beberapa fitur website mungkin tidak berfungsi optimal tanpa cookies.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 6 -->
            <div class="section" id="section-6">
                <button class="section-header" onclick="toggleSection(6)">
                    <span class="section-number">6</span>
                    <span class="section-title">Hak Anda</span>
                    <i data-lucide="chevron-down" class="section-chevron" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div class="section-content">
                    <div class="section-body">
                        <p>Sesuai dengan peraturan perlindungan data yang berlaku, Anda memiliki hak untuk:</p>
                        <div class="rights-grid">
                            <div class="right-item">
                                <div class="right-check">
                                    <i data-lucide="check" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                <span>Mengakses data pribadi Anda</span>
                            </div>
                            <div class="right-item">
                                <div class="right-check">
                                    <i data-lucide="check" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                <span>Memperbaiki data yang tidak akurat</span>
                            </div>
                            <div class="right-item">
                                <div class="right-check">
                                    <i data-lucide="check" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                <span>Meminta penghapusan data</span>
                            </div>
                            <div class="right-item">
                                <div class="right-check">
                                    <i data-lucide="check" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                <span>Berhenti berlangganan newsletter</span>
                            </div>
                            <div class="right-item">
                                <div class="right-check">
                                    <i data-lucide="check" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                <span>Membatasi pemrosesan data</span>
                            </div>
                            <div class="right-item">
                                <div class="right-check">
                                    <i data-lucide="check" style="width: 1rem; height: 1rem;"></i>
                                </div>
                                <span>Portabilitas data Anda</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 7 -->
            <div class="section" id="section-7">
                <button class="section-header" onclick="toggleSection(7)">
                    <span class="section-number">7</span>
                    <span class="section-title">Penyimpanan Data</span>
                    <i data-lucide="chevron-down" class="section-chevron" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div class="section-content">
                    <div class="section-body">
                        <div class="icon-text">
                            <i data-lucide="clock" style="width: 1.25rem; height: 1.25rem;"></i>
                            <p style="margin: 0;">Kami menyimpan data pribadi Anda selama diperlukan untuk tujuan yang dijelaskan dalam kebijakan ini, atau sesuai yang diwajibkan oleh hukum yang berlaku di Indonesia. Setelah tidak diperlukan lagi, data akan dihapus atau dianonimkan dengan aman menggunakan metode yang sesuai standar industri.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 8 -->
            <div class="section" id="section-8">
                <button class="section-header" onclick="toggleSection(8)">
                    <span class="section-number">8</span>
                    <span class="section-title">Perubahan Kebijakan</span>
                    <i data-lucide="chevron-down" class="section-chevron" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div class="section-content">
                    <div class="section-body">
                        <div class="icon-text">
                            <i data-lucide="refresh-cw" style="width: 1.25rem; height: 1.25rem;"></i>
                            <div>
                                <p>Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu untuk mencerminkan perubahan dalam praktik kami atau untuk alasan operasional, hukum, atau peraturan lainnya.</p>
                                <p style="margin-top: 0.75rem; margin-bottom: 0;">Perubahan signifikan akan diberitahukan melalui email atau pemberitahuan di website. Kami menyarankan Anda untuk meninjau kebijakan ini secara berkala.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 9 -->
            <div class="section" id="section-9">
                <button class="section-header" onclick="toggleSection(9)">
                    <span class="section-number">9</span>
                    <span class="section-title">Hubungi Kami</span>
                    <i data-lucide="chevron-down" class="section-chevron" style="width: 1.25rem; height: 1.25rem;"></i>
                </button>
                <div class="section-content">
                    <div class="section-body">
                        <p>Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini atau ingin menggunakan hak privasi Anda, silakan hubungi kami:</p>
                        <div class="contact-box">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i data-lucide="mail" style="width: 1.25rem; height: 1.25rem;"></i>
                                </div>
                                <div>
                                    <div class="contact-label">Email</div>
                                    <div class="contact-value">cs@nanokomputer.com</div>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i data-lucide="phone" style="width: 1.25rem; height: 1.25rem;"></i>
                                </div>
                                <div>
                                    <div class="contact-label">Telepon</div>
                                    <div class="contact-value">(021) 623-09578</div>
                                </div>
                            </div>
                            <div class="contact-item address">
                                <div class="contact-icon">
                                    <i data-lucide="map-pin" style="width: 1.25rem; height: 1.25rem;"></i>
                                </div>
                                <div>
                                    <div class="contact-label">Alamat</div>
                                    <div class="contact-value">Mangga Dua Mall, Jl. Mangga Dua Raya No.47A-B Lantai 2, Mangga Dua Sel., Kec. Sawah Besar, Jakarta Pusat, DKI Jakarta 10730</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="back-section">
            <a href="landingPage.php" class="btn btn-outline">
                <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </main>


    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div>
            <div class="toast-title" id="toast-title">Berhasil</div>
            <div class="toast-message" id="toast-message">Pesan sukses</div>
        </div>
    </div>
    <footer class="footer">
        <?php include '../../components/users/footer.php'; ?>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Toggle section
        function toggleSection(num) {
            const section = document.getElementById('section-' + num);
            const allSections = document.querySelectorAll('.section');

            // Close other sections (optional - remove this block if you want multiple sections open)
            // allSections.forEach(s => {
            //     if (s !== section) s.classList.remove('open');
            // });

            section.classList.toggle('open');

            // Scroll to section
            setTimeout(() => {
                section.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        }

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

        // Generate PDF
        function generatePDF() {
            const btn = document.getElementById('downloadPdfBtn');
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

                // Header
                doc.setFillColor(136, 36, 38);
                doc.rect(0, 0, pageWidth, 40, 'F');

                doc.setTextColor(255, 255, 255);
                doc.setFontSize(24);
                doc.setFont('helvetica', 'bold');
                doc.text('Kebijakan Privasi', pageWidth / 2, 22, {
                    align: 'center'
                });

                doc.setFontSize(12);
                doc.setFont('helvetica', 'normal');
                doc.text('Nano Komputer', pageWidth / 2, 32, {
                    align: 'center'
                });

                yPosition = 55;
                doc.setTextColor(100, 100, 100);
                doc.setFontSize(10);
                doc.text('Terakhir diperbarui: 1 Desember 2025', margin, yPosition);
                yPosition += 15;

                // Content sections
                const sections = [{
                        title: '1. Informasi yang Kami Kumpulkan',
                        content: [
                            'Kami mengumpulkan beberapa jenis informasi untuk memberikan layanan terbaik:',
                            '',
                            'Informasi yang Anda Berikan:',
                            '• Nama lengkap dan alamat email',
                            '• Nomor telepon dan alamat pengiriman',
                            '• Informasi pembayaran (dienkripsi)',
                            '• Riwayat pesanan dan preferensi belanja',
                            '',
                            'Informasi yang Dikumpulkan Otomatis:',
                            '• Alamat IP dan jenis browser',
                            '• Perangkat yang digunakan',
                            '• Halaman yang dikunjungi dan waktu kunjungan',
                            '• Cookies dan teknologi pelacakan serupa'
                        ]
                    },
                    {
                        title: '2. Penggunaan Informasi',
                        content: [
                            'Informasi yang kami kumpulkan digunakan untuk:',
                            '• Memproses dan mengirimkan pesanan Anda',
                            '• Berkomunikasi tentang pesanan, produk, dan layanan',
                            '• Menyediakan dukungan pelanggan',
                            '• Mengirim promosi dan penawaran khusus (dengan persetujuan)',
                            '• Meningkatkan website dan pengalaman berbelanja',
                            '• Mencegah penipuan dan menjaga keamanan',
                            '• Mematuhi kewajiban hukum'
                        ]
                    },
                    {
                        title: '3. Berbagi Informasi',
                        content: [
                            'Kami tidak menjual informasi pribadi Anda. Kami hanya berbagi informasi dengan:',
                            '• Penyedia layanan: Kurir pengiriman, payment gateway, penyedia hosting',
                            '• Mitra bisnis: Brand dan distributor untuk keperluan garansi',
                            '• Otoritas hukum: Jika diwajibkan oleh hukum yang berlaku'
                        ]
                    },
                    {
                        title: '4. Keamanan Data',
                        content: [
                            'Kami mengimplementasikan langkah-langkah keamanan untuk melindungi data Anda:',
                            '• Enkripsi SSL: Semua data dienkripsi saat transit',
                            '• Server Aman: Data disimpan di server terproteksi',
                            '• Akses Terbatas: Hanya personel berwenang yang dapat mengakses data',
                            '',
                            'Meskipun kami berusaha keras melindungi data Anda, tidak ada metode transmisi',
                            'internet yang 100% aman.'
                        ]
                    },
                    {
                        title: '5. Cookies',
                        content: [
                            'Kami menggunakan cookies untuk:',
                            '• Mengingat preferensi dan pengaturan Anda',
                            '• Menjaga sesi login tetap aktif',
                            '• Menganalisis lalu lintas website',
                            '• Menampilkan iklan yang relevan',
                            '',
                            'Anda dapat mengatur browser untuk menolak cookies, namun beberapa fitur',
                            'website mungkin tidak berfungsi optimal.'
                        ]
                    },
                    {
                        title: '6. Hak Anda',
                        content: [
                            'Anda memiliki hak untuk:',
                            '• Mengakses data pribadi Anda',
                            '• Memperbaiki data yang tidak akurat',
                            '• Meminta penghapusan data',
                            '• Berhenti berlangganan newsletter',
                            '• Membatasi pemrosesan data',
                            '• Portabilitas data'
                        ]
                    },
                    {
                        title: '7. Penyimpanan Data',
                        content: [
                            'Kami menyimpan data pribadi Anda selama diperlukan untuk tujuan yang dijelaskan',
                            'dalam kebijakan ini, atau sesuai yang diwajibkan oleh hukum. Setelah tidak',
                            'diperlukan, data akan dihapus atau dianonimkan dengan aman.'
                        ]
                    },
                    {
                        title: '8. Perubahan Kebijakan',
                        content: [
                            'Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan',
                            'signifikan akan diberitahukan melalui email atau pemberitahuan di website.',
                            'Kami menyarankan Anda untuk meninjau kebijakan ini secara berkala.'
                        ]
                    },
                    {
                        title: '9. Hubungi Kami',
                        content: [
                            'Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini:',
                            '',
                            'Email: cs@nanokomputer.com',
                            'Telepon: (021) 623-09578',
                            'Alamat: Mangga Dua Mall, Jl. Mangga Dua Raya No.47A-B',
                            'Lantai 2, Jakarta Pusat 10730'
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
                    doc.setFontSize(8);
                    doc.setTextColor(150, 150, 150);
                    doc.text(
                        'Halaman ' + i + ' dari ' + pageCount + ' | © 2025 Nano Komputer',
                        pageWidth / 2,
                        290, {
                            align: 'center'
                        }
                    );
                }

                doc.save('Kebijakan-Privasi-Nano-Komputer.pdf');

                showToast('PDF Berhasil Dibuat', 'Dokumen Kebijakan Privasi telah diunduh.', 'success');
            } catch (error) {
                console.error('Error generating PDF:', error);
                showToast('Gagal Membuat PDF', 'Terjadi kesalahan saat membuat dokumen PDF.', 'error');
            } finally {
                btn.disabled = false;
                btnText.textContent = originalText;
            }
        }
    </script>
</body>

</html>