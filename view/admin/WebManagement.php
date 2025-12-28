<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Web Management";
include '../../components/admin/head.php';
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<style>
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 12px !important;
        padding: 8px 12px !important;
        background: white !important;
        transition: all 0.2s ease !important;
    }

    .select2-container--default .select2-selection--single:hover {
        border-color: #882426 !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #882426 !important;
        box-shadow: 0 0 0 3px rgba(136, 36, 38, 0.1) !important;
        outline: none !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px !important;
        color: #374151 !important;
        padding-left: 0 !important;
        font-size: 14px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 8px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {

        /* Toast Container - High z-index */
        #toastContainer {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 200;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        /* Modal Animation */
        @keyframes modal-in {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .animate-modal-in {
            animation: modal-in 0.3s ease-out forwards;
        }

        .animate-pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }

        /* Toast Animations */
        @keyframes toast-in {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toast-out {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        .toast-enter {
            animation: toast-in 0.4s ease-out forwards;
        }

        .toast-exit {
            animation: toast-out 0.3s ease-in forwards;
        }

        /* Circular Progress */
        @keyframes circular-progress {
            0% {
                stroke-dashoffset: 0;
            }

            100% {
                stroke-dashoffset: 100;
            }
        }

        .circular-progress {
            animation: circular-progress linear forwards;
        }

        /* Custom Scrollbar for Modal */
        .modal-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .modal-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-scrollbar::-webkit-scrollbar-thumb {
            background: #882426;
            border-radius: 10px;
        }

        .modal-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6d1a1c;
        }

        /* Input Focus Animation */
        input:focus,
        select:focus,
        textarea:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(136, 36, 38, 0.15);
        }

        /* Button Hover Effects */
        .btn-primary:hover {
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0) scale(0.98);
        }

        /* Toggle Switch */
        .toggle-switch {
            position: relative;
            width: 56px;
            height: 32px;
            flex-shrink: 0;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e5e7eb;
            border-radius: 9999px;
            transition: all 0.3s ease;
        }

        .toggle-slider::before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 4px;
            top: 4px;
            background-color: white;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .toggle-switch input:checked+.toggle-slider {
            background-color: #882426;
        }

        .toggle-switch input:checked+.toggle-slider::before {
            transform: translateX(24px);
        }

        border-color: #9ca3af transparent transparent transparent !important;
    }

    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #882426 transparent !important;
    }

    .select2-dropdown {
        border: 1px solid #e5e7eb !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
        margin-top: 4px !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
        font-size: 14px !important;
        margin: 8px !important;
        width: calc(100% - 16px) !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #882426 !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(136, 36, 38, 0.1) !important;
    }

    .select2-container--default .select2-results__option {
        padding: 10px 16px !important;
        font-size: 14px !important;
        transition: all 0.15s ease !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: #882426 !important;
        color: white !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #fef2f2 !important;
        color: #882426 !important;
        font-weight: 500 !important;
    }

    .select2-results__options {
        max-height: 250px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #9ca3af !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #882426 !important;
        font-size: 16px !important;
        margin-right: 16px !important;
    }
</style>

<body class="bg-gray-50 h-screen flex">
    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[200] flex flex-col gap-3"></div>

    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Manajemen Website</h1>
                    <p class="text-gray-500 mt-1">Kelola pengaturan dan informasi mengenai website anda</p>
                </div>
            </div>

            <div class="flex flex-wrap mb-6 overflow-x-auto">
                <nav class="bg-white rounded-xl p-1.5 font-semibold text-sm gap-1 flex flex-nowrap shadow-sm border border-gray-100">
                    <button class="tab-btn-managementweb px-5 py-2.5 rounded-lg text-gray-500 cursor-pointer whitespace-nowrap hover:bg-[#882426]/10 hover:text-[#882426] transition-all duration-300 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">store</span>
                        Lokasi Toko
                    </button>
                    <button class="tab-btn-managementweb px-5 py-2.5 rounded-lg text-gray-500 cursor-pointer whitespace-nowrap hover:bg-[#882426]/10 hover:text-[#882426] transition-all duration-300 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">confirmation_number</span>
                        Support Tickets
                    </button>
                    <button class="tab-btn-managementweb px-5 py-2.5 rounded-lg text-gray-500 cursor-pointer whitespace-nowrap hover:bg-[#882426]/10 hover:text-[#882426] transition-all duration-300 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">article</span>
                        Blog & Artikel
                    </button>
                    <button class="tab-btn-managementweb px-5 py-2.5 rounded-lg text-gray-500 cursor-pointer whitespace-nowrap hover:bg-[#882426]/10 hover:text-[#882426] transition-all duration-300 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">article</span>
                        Site Settings
                    </button>
                </nav>
            </div>

            <!-- Tab 1: Lokasi Toko -->
            <div class="tab-content-managementweb">
                <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Lokasi Toko</h2>
                            <p class="text-sm text-gray-500 mt-1">Kelola semua lokasi toko fisik Anda</p>
                        </div>
                        <button id="btnAddStore" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                            <span class="material-symbols-outlined text-xl">add_circle</span>
                            Tambah Toko
                        </button>
                    </div>

                    <div id="storeList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="flex items-center justify-center py-12 col-span-full">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-800"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Support Tickets -->
            <div class="tab-content-managementweb hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-[#882426] flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-2xl">confirmation_number</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800" id="statTotal">0</p>
                                <p class="text-xs text-gray-500">Total Tiket</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-yellow-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-2xl">pending</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800" id="statOpen">0</p>
                                <p class="text-xs text-gray-500">Open</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-2xl">sync</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800" id="statInProgress">0</p>
                                <p class="text-xs text-gray-500">In Progress</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-2xl">check_circle</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800" id="statResolved">0</p>
                                <p class="text-xs text-gray-500">Resolved</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Support Tickets</h2>
                                <p class="text-sm text-gray-500 mt-1">Kelola tiket bantuan dari pelanggan</p>
                            </div>
                            <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                <span class="material-symbols-outlined text-gray-400 text-sm">
                                    view_list
                                </span>
                                <select id="ticketPerPageSelect" onchange="changeTicketPerPage(this.value)" class="bg-transparent text-sm font-medium text-gray-700 focus:outline-none cursor-pointer">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="text-sm text-gray-600">entries per page</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50/50 border-b border-gray-100">
                        <div class="flex flex-wrap gap-3">
                            <div class="flex-1 min-w-[200px] relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                                <input type="text" id="ticketSearch" placeholder="Cari tiket..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                            </div>
                            <select id="filterStatus" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                <option value="">Semua Status</option>
                                <option value="Open">Open</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Closed">Closed</option>
                            </select>
                            <select id="filterKategori" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                <option value="">Semua Kategori</option>
                                <option value="General">General</option>
                                <option value="Garansi & Servis">Garansi & Servis</option>
                                <option value="Aktivasi Akun">Aktivasi Akun</option>
                                <option value="Komplain">Komplain</option>
                                <option value="Pertanyaan Produk">Pertanyaan Produk</option>
                                <option value="Status Pesanan">Status Pesanan</option>
                            </select>
                            <input type="date" id="filterDateFrom" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all" title="Dari Tanggal">
                            <input type="date" id="filterDateTo" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all" title="Sampai Tanggal">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID Tiket</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pengaju</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Subjek</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="ticketTableBody" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#882426] mx-auto"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Info & Controls -->
                    <div class="px-4 md:px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div class="text-sm text-gray-600" id="ticketPaginationInfo">
                            Showing <span class="font-semibold text-gray-800">0</span> to <span class="font-semibold text-gray-800">0</span> of <span class="font-semibold text-gray-800">0</span> entries
                        </div>

                        <div class="flex items-center gap-1 flex-shrink-0" id="ticketPaginationNav">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Blog & Artikel -->
            <div class="tab-content-managementweb hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-[#882426] flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-2xl">article</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800" id="statTotalArticles">0</p>
                                <p class="text-xs text-gray-500">Total Artikel</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-2xl">check_circle</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800" id="statPublished">0</p>
                                <p class="text-xs text-gray-500">Published</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-2xl">edit_note</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800" id="statDraft">0</p>
                                <p class="text-xs text-gray-500">Draft</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-2xl">visibility</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-800" id="statTotalViews">0</p>
                                <p class="text-xs text-gray-500">Total Views</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 md:p-6 border-b border-gray-100">
                        <div class="flex flex-col gap-4">
                            <div class="min-w-0">
                                <h2 class="text-xl font-bold text-gray-800">Blog & Artikel</h2>
                                <p class="text-sm text-gray-500 mt-1">Kelola konten blog dan artikel website</p>
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                    <span class="material-symbols-outlined text-gray-400 text-sm">view_list</span>
                                    <select id="articlePerPageSelect" onchange="changeArticlePerPage(this.value)" class="bg-transparent text-sm font-medium text-gray-700 focus:outline-none cursor-pointer">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="text-sm text-gray-600">entries per page</span>
                                </div>
                                <button id="btnAddArticle" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                                    <span class="material-symbols-outlined text-xl">add_circle</span>
                                    Tambah Artikel
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50/50 border-b border-gray-100">
                        <div class="flex flex-wrap gap-3">
                            <div class="flex-1 min-w-[200px] relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                                <input type="text" id="articleSearch" placeholder="Cari artikel..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                            </div>
                            <select id="filterArticleCategory" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                <option value="">Semua Kategori</option>
                            </select>
                            <select id="filterArticleStatus" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                <option value="">Semua Status</option>
                                <option value="publish">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Gambar</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Judul</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Kategori</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Penulis</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Tanggal</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Views</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="articleTableBody" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#882426] mx-auto"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Info & Controls -->
                    <div class="px-4 md:px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div class="text-sm text-gray-600" id="articlePaginationInfo">
                            Showing <span class="font-semibold text-gray-800">0</span> to <span class="font-semibold text-gray-800">0</span> of <span class="font-semibold text-gray-800">0</span> entries
                        </div>

                        <div class="flex items-center gap-1 flex-shrink-0" id="articlePaginationNav">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Site Settings -->
            <div class="tab-content-managementweb hidden">
                <form id="siteSettingsForm" enctype="multipart/form-data" class="h-full flex flex-col">
                    <div class="rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden flex-1">

                        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Pengaturan Situs</h2>
                                <p class="text-sm text-gray-500">Konfigurasi informasi global, logo, dan kontak website.</p>
                            </div>
                            <button id="btnSaveSettings" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                                <span class="material-symbols-outlined text-xl">save</span>
                                Simpan Perubahan
                            </button>
                        </div>

                        <div class="p-6 overflow-y-auto space-y-8">

                            <div class="bg-gray-50/50 rounded-xl p-6 border border-gray-200">
                                <div class="flex items-center gap-2 mb-5">
                                    <div class="w-8 h-8 rounded-lg bg-[#882426] flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white text-sm">language</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800">Identitas & SEO</h4>
                                </div>

                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Website</label>
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">badge</span>
                                            <input type="text" name="site_title" placeholder="Contoh: Nano Komputer"
                                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                            <input type="hidden" name="existing_site_logo" id="existing_site_logo">
                                            <input type="hidden" name="existing_site_favicon" id="existing_site_favicon">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Website (SEO)</label>
                                        <textarea name="site_description" rows="3" placeholder="Deskripsi singkat untuk mesin pencari..."
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50/50 rounded-xl p-6 border border-gray-200">
                                <div class="flex items-center gap-2 mb-5">
                                    <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white text-sm">image</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800">Logo & Aset Visual</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Logo Utama</label>
                                        <div class="flex items-start gap-4">
                                            <div class="w-24 h-24 bg-gray-100 rounded-lg border border-dashed border-gray-300 flex items-center justify-center overflow-hidden shrink-0 relative group">
                                                <img id="preview_site_logo" src="" alt="" class="w-full h-full object-contain p-1 hidden">
                                                <span class="material-symbols-outlined text-gray-400 group-hover:hidden" id="icon_site_logo"></span>
                                            </div>
                                            <div class="flex-1">
                                                <input type="file" name="site_logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#882426]/10 file:text-[#882426] hover:file:bg-[#882426]/20 transition cursor-pointer mb-2"
                                                    onchange="document.getElementById('preview_site_logo').src = window.URL.createObjectURL(this.files[0]); document.getElementById('preview_site_logo').classList.remove('hidden'); document.getElementById('icon_site_logo').classList.add('hidden');">
                                                <p class="text-xs text-gray-400">Format: PNG/SVG (Transparan). Max 2MB.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Favicon (Icon Tab)</label>
                                        <div class="flex items-start gap-4">
                                            <div class="w-16 h-16 bg-gray-100 rounded-lg border border-dashed border-gray-300 flex items-center justify-center overflow-hidden shrink-0 relative">
                                                <img id="preview_site_favicon" src="" alt="" class="w-full h-full object-contain p-1 hidden">
                                                <!-- <span class="material-symbols-outlined text-gray-400" id="icon_site_favicon">stars</span> -->
                                            </div>
                                            <div class="flex-1">
                                                <input type="file" name="site_favicon" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#882426]/10 file:text-[#882426] hover:file:bg-[#882426]/20 transition cursor-pointer mb-2"
                                                    onchange="document.getElementById('preview_site_favicon').src = window.URL.createObjectURL(this.files[0]); document.getElementById('preview_site_favicon').classList.remove('hidden'); document.getElementById('icon_site_favicon').classList.add('hidden');">
                                                <p class="text-xs text-gray-400">Format: ICO/PNG (Kotak). Max 1MB.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50/50 rounded-xl p-6 border border-gray-200">
                                <div class="flex items-center gap-2 mb-5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white text-sm">share</span>
                                    </div>
                                    <h4 class="font-bold text-gray-800">Kontak & Sosial Media</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Kontak</label>
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">mail</span>
                                            <input type="email" name="contact_email" placeholder="admin@domain.com"
                                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor WhatsApp</label>
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">chat</span>
                                            <input type="number" name="contact_phone" placeholder="628123xxxx"
                                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook URL</label>
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">public</span>
                                            <input type="url" name="facebook_url" placeholder="https://facebook.com/page"
                                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Instagram URL</label>
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">photo_camera</span>
                                            <input type="url" name="instagram_url" placeholder="https://instagram.com/username"
                                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">TikTok URL</label>
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">music_note</span>
                                            <input type="url" name="tiktok_url" placeholder="https://tiktok.com/@username"
                                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">YouTube URL</label>
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">alternate_email</span>
                                            <input type="url" name="youtube_url" placeholder="https://youtube.com/channel/yourchannel"
                                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
                <div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hidden z-50">
                    <span id="toastMessage"></span>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal: Add/Edit Store - Enhanced with Background Click Close -->
    <div id="storeModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeStoreModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden transform transition-all animate-modal-in">
                <!-- Modern Header with Solid Primary Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl" id="storeModalIcon">store</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="storeModalTitle">Tambah Lokasi Toko</h3>
                            <p class="text-white/70 text-sm mt-0.5" id="storeModalSubtitle">Isi data lokasi toko dengan lengkap</p>
                        </div>
                    </div>
                    <button type="button" id="closeStoreModal" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="storeForm" class="overflow-y-auto max-h-[calc(90vh-90px)] modal-scrollbar">
                    <input type="hidden" id="storeId" name="id_toko">

                    <div class="p-6 bg-gray-50 space-y-6">
                        <!-- Info Alert -->
                        <div class="p-4 rounded-xl border-l-4 border-[#882426] bg-[#882426]/5">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[#882426] text-xl flex-shrink-0">info</span>
                                <div>
                                    <p class="text-sm text-gray-700 font-medium" id="storeFormInfoText">Lengkapi data lokasi toko untuk ditampilkan kepada pelanggan.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Informasi Dasar -->
                        <div class="bg-white rounded-xl p-5 border-2 border-gray-200 shadow-sm">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-[#882426] flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-sm">info</span>
                                </div>
                                <h4 class="font-semibold text-gray-800">Informasi Dasar</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">storefront</span>
                                        Nama Toko <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="namaToko" name="nama_toko" required placeholder="Contoh: Nano Komputer Pusat"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400">
                                </div>
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">phone</span>
                                        No. Telepon
                                    </label>
                                    <input type="tel" id="noTelepon" name="no_telepon" placeholder="08xx-xxxx-xxxx"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400">
                                </div>
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">markunread_mailbox</span>
                                        Kode Pos
                                    </label>
                                    <input type="text" id="kodePos" name="kode_pos" placeholder="12345"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400">
                                </div>
                            </div>
                        </div>

                        <!-- Section: Alamat -->
                        <div class="bg-white rounded-xl p-5 border-2 border-gray-200 shadow-sm">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-sm">location_on</span>
                                </div>
                                <h4 class="font-semibold text-gray-800">Alamat Toko</h4>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">home</span>
                                        Alamat Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="alamatToko" name="alamat" rows="2" required placeholder="Jl. Contoh No. 123, RT/RW, Gedung ABC Lantai 2"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all resize-none placeholder:text-gray-400"></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">Provinsi <span class="text-red-500">*</span></label>
                                        <select id="provinsiToko" name="provinsi" required class="w-full select2-provinsi">
                                            <option value="">Pilih Provinsi</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                        <select id="kotaToko" name="kota_kabupaten" required class="w-full select2-kota">
                                            <option value="">Pilih Kota/Kabupaten</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">Kecamatan</label>
                                        <select id="kecamatanToko" name="kecamatan" class="w-full select2-kecamatan">
                                            <option value="">Pilih Kecamatan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">Kelurahan</label>
                                        <select id="kelurahanToko" name="kelurahan" class="w-full select2-kelurahan">
                                            <option value="">Pilih Kelurahan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Jam Operasional -->
                        <div class="bg-white rounded-xl p-5 border-2 border-gray-200 shadow-sm">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-sm">schedule</span>
                                </div>
                                <h4 class="font-semibold text-gray-800">Jam Operasional</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">schedule</span>
                                        Jam Buka
                                    </label>
                                    <input type="time" id="jamBuka" name="jam_buka"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200">
                                </div>
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">schedule</span>
                                        Jam Tutup
                                    </label>
                                    <input type="time" id="jamTutup" name="jam_tutup"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200">
                                </div>
                            </div>
                        </div>

                        <!-- Status Aktif -->
                        <div class="p-4 bg-white rounded-xl border-2 border-gray-200">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <div class="toggle-switch">
                                    <input type="checkbox" name="is_active" id="isActive" value="1" checked>
                                    <span class="toggle-slider"></span>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-700">Status Aktif</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Toko akan ditampilkan kepada pelanggan</p>
                                </div>
                            </label>
                        </div>

                        <style>
                            .toggle-switch {
                                position: relative;
                                width: 56px;
                                height: 32px;
                                flex-shrink: 0;
                            }

                            .toggle-switch input {
                                opacity: 0;
                                width: 0;
                                height: 0;
                            }

                            .toggle-slider {
                                position: absolute;
                                cursor: pointer;
                                top: 0;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                background-color: #e5e7eb;
                                border-radius: 9999px;
                                transition: all 0.3s ease;
                            }

                            .toggle-slider::before {
                                position: absolute;
                                content: "";
                                height: 24px;
                                width: 24px;
                                left: 4px;
                                top: 4px;
                                background-color: white;
                                border-radius: 50%;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                                transition: transform 0.3s ease;
                            }

                            .toggle-switch input:checked+.toggle-slider {
                                background-color: #882426;
                            }

                            .toggle-switch input:checked+.toggle-slider::before {
                                transform: translateX(24px);
                            }
                        </style>
                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 z-20 bg-white px-6 py-4 border-t border-gray-200 flex justify-between items-center shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
                        <p class="text-xs text-gray-400 hidden sm:block">
                            <span class="material-symbols-outlined text-sm align-middle">keyboard</span>
                            Tekan ESC untuk menutup
                        </p>
                        <div class="flex gap-3 ml-auto">

                            <button type="button" id="cancelStoreBtn"
                                class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">close</span>
                                Batal
                            </button>
                            <button type="submit" id="storeSubmitBtn"
                                class="px-6 py-2.5 bg-[#882426] text-white rounded-xl transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg font-medium flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span id="storeSubmitBtnText">Simpan</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Delete Confirmation - Enhanced Design -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all animate-modal-in">
                <!-- Modern Header with Primary Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg animate-pulse-slow">
                            <span class="material-symbols-outlined text-white text-2xl">warning</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Konfirmasi Hapus</h3>
                            <p class="text-white/70 text-sm mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDeleteModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 bg-gray-50 space-y-5">
                    <!-- Icon -->
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center ring-4 ring-red-50">
                            <span class="material-symbols-outlined text-red-500 text-4xl">delete_forever</span>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-800" id="deleteItemName"></p>
                            <p class="text-sm text-gray-500" id="deleteItemId"></p>
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="p-4 rounded-xl border-l-4 border-red-500 bg-red-50">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-500 text-xl flex-shrink-0">warning</span>
                            <div>
                                <p class="text-sm font-semibold text-red-700 mb-1">Peringatan!</p>
                                <p class="text-sm text-red-600" id="deleteMessage">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan dan data akan dihapus permanen.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Checkbox Confirmation -->
                    <label class="flex items-center gap-3 p-4 bg-white rounded-xl border-2 border-gray-200 cursor-pointer hover:border-red-300 transition-colors delete-confirm-label">
                        <input type="checkbox" id="deleteConfirmCheck" class="w-5 h-5 text-red-600 border-2 border-gray-300 rounded focus:ring-red-500 focus:ring-offset-0 delete-confirm-checkbox">
                        <span class="text-sm text-gray-700">Saya mengerti dan ingin melanjutkan penghapusan</span>
                    </label>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                    <button type="button" id="cancelDeleteBtn"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                        <span class="material-symbols-outlined text-lg">close</span>
                        Batal
                    </button>
                    <button type="button" id="confirmDeleteBtn" disabled
                        class="inline-flex items-center gap-2 px-5 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-red-700 transition-all duration-200 shadow-lg shadow-red-600/30 btn-primary disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                        <span class="material-symbols-outlined text-lg">delete_forever</span>
                        Ya, Hapus!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Ticket Detail -->
    <div id="ticketModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col shadow-2xl transform transition-all duration-300">
            <div class="sticky top-0 z-10 px-6 py-5 flex items-center justify-between border-b border-gray-100 bg-[#882426]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">confirmation_number</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Detail Tiket</h3>
                        <p class="text-white/70 text-sm" id="ticketIdDisplay"></p>
                    </div>
                </div>
                <button id="closeTicketModal" class="p-2 hover:bg-white/20 rounded-lg transition-all text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50" id="ticketDetailContent">
            </div>
        </div>
    </div>

    <!-- Modal: Add/Edit Article - Enhanced Design -->
    <div id="articleModal" class="fixed inset-0 z-50 hidden">
        <!-- Background Overlay with click to close -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeArticleModalHandler()"></div>

        <!-- Modal Content -->
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl transform transition-all animate-modal-in">
                <!-- Header -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl">article</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="articleModalTitle">Tambah Artikel</h3>
                            <p class="text-white/70 text-sm mt-0.5">Kelola konten artikel website</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeArticleModalHandler()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Form Content -->
                <form id="articleForm" class="overflow-y-auto max-h-[calc(90vh-88px)]">
                    <div class="p-6 space-y-5 bg-gray-50">
                        <input type="hidden" id="articleId" name="id_post">

                        <!-- Info Section -->
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
                            <span class="material-symbols-outlined text-blue-500 flex-shrink-0">info</span>
                            <div>
                                <p class="text-sm text-blue-700 font-medium">Informasi Artikel</p>
                                <p class="text-xs text-blue-600 mt-1">Isi semua field yang bertanda (*) untuk menyimpan artikel.</p>
                            </div>
                        </div>

                        <!-- Title & Slug -->
                        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-[#882426]">title</span>
                                <h4 class="font-semibold text-gray-800">Judul & Identitas</h4>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Artikel <span class="text-red-500">*</span></label>
                                <input type="text" id="articleTitle" name="judul" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all"
                                    placeholder="Masukkan judul artikel...">
                                <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">link</span>
                                    Slug akan digenerate otomatis dari judul
                                </p>
                            </div>
                        </div>

                        <!-- Category & Status -->
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-[#882426]">category</span>
                                <h4 class="font-semibold text-gray-800">Kategori & Status</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                                    <select id="articleCategory" name="id_category" required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                        <option value="">Pilih Kategori</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Publikasi</label>
                                    <select id="articleStatus" name="status"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                        <option value="draft">Draft</option>
                                        <option value="publish">Publish</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Thumbnail Upload Section -->
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-symbols-outlined text-[#882426]">image</span>
                                <h4 class="font-semibold text-gray-800">Thumbnail Artikel</h4>
                            </div>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center bg-gray-50 hover:border-[#882426]/50 hover:bg-gray-100/50 transition-all duration-300 cursor-pointer" id="thumbnailDropZone">
                                <input type="file" id="thumbnailInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                                <input type="hidden" id="thumbnailFilename" name="thumbnail">

                                <!-- Upload State -->
                                <div id="uploadThumbnailBtn">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-white border-2 border-gray-200 flex items-center justify-center shadow-sm">
                                        <span class="material-symbols-outlined text-3xl text-[#882426]">cloud_upload</span>
                                    </div>
                                    <p class="text-gray-700 font-medium mb-1">Drag & drop atau klik untuk upload</p>
                                    <p class="text-gray-400 text-sm">Format: JPG, PNG, WebP (Maks. 5MB)</p>
                                    <button type="button" id="btnUploadThumbnail"
                                        class="mt-4 px-6 py-2.5 bg-[#882426] text-white rounded-xl font-medium hover:bg-[#6d1a1c] transition-all duration-200 inline-flex items-center gap-2 shadow-md hover:shadow-lg">
                                        <span class="material-symbols-outlined text-lg">upload</span>
                                        Pilih Gambar
                                    </button>
                                </div>

                                <!-- Preview State -->
                                <div id="thumbnailPreviewContainer" class="hidden">
                                    <div class="relative group cursor-pointer inline-block" onclick="openLightbox(document.getElementById('thumbnailImage').src)">
                                        <img id="thumbnailImage" src="" alt="Preview" class="max-h-48 mx-auto rounded-xl shadow-lg transition-transform group-hover:scale-[1.02]">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-outlined text-white text-3xl">zoom_in</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-3" id="thumbnailFileName"></p>
                                    <button type="button" id="btnRemoveThumbnail"
                                        class="mt-4 px-5 py-2.5 bg-red-50 text-red-600 rounded-xl font-medium hover:bg-red-100 transition-all duration-200 flex items-center gap-2 mx-auto border border-red-200">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                        Hapus Gambar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Excerpt Section -->
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-[#882426]">short_text</span>
                                <h4 class="font-semibold text-gray-800">Excerpt (Ringkasan)</h4>
                            </div>
                            <textarea id="articleExcerpt" name="excerpt" rows="2"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all resize-none"
                                placeholder="Ringkasan singkat artikel untuk preview..."></textarea>
                            <p class="text-xs text-gray-400 mt-1.5">Tampil di halaman daftar artikel sebagai preview singkat</p>
                        </div>

                        <!-- Content Section -->
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-[#882426]">description</span>
                                <h4 class="font-semibold text-gray-800">Konten Artikel <span class="text-red-500">*</span></h4>
                            </div>
                            <textarea id="articleContent" name="konten" rows="10"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all resize-none"
                                placeholder="Tulis konten artikel lengkap di sini..."></textarea>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="sticky bottom-0 z-20 bg-white px-6 py-4 border-t border-gray-200 flex justify-between items-center shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
                        <p class="text-xs text-gray-400 hidden sm:block">
                            <span class="material-symbols-outlined text-sm align-middle">keyboard</span>
                            Tekan ESC untuk menutup
                        </p>
                        <div class="flex gap-3 ml-auto">
                            <button type="button" onclick="closeArticleModalHandler()"
                                class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">close</span>
                                Batal
                            </button>
                            <button type="submit" id="submitArticleBtn"
                                class="px-6 py-2.5 bg-[#882426] text-white rounded-xl transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg font-medium flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">save</span>
                                Simpan Artikel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Delete Article Confirmation - Enhanced Design -->
    <div id="deleteArticleModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeDeleteArticleModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all animate-modal-in">
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg animate-pulse-slow">
                            <span class="material-symbols-outlined text-white text-2xl">delete_forever</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Hapus Artikel</h3>
                            <p class="text-white/70 text-sm mt-0.5">Konfirmasi penghapusan artikel</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDeleteArticleModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="p-6 bg-gray-50 space-y-5">
                    <!-- Article Preview -->
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center ring-4 ring-red-50">
                            <span class="material-symbols-outlined text-red-500 text-4xl">article</span>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-800" id="deleteArticleName"></p>
                            <p class="text-sm text-gray-500" id="deleteArticleId"></p>
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="p-4 rounded-xl border-l-4 border-red-500 bg-red-50">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-500 text-xl flex-shrink-0">warning</span>
                            <div>
                                <p class="text-sm font-semibold text-red-700 mb-1">Peringatan!</p>
                                <p class="text-sm text-red-600" id="deleteArticleMessage">Apakah Anda yakin ingin menghapus artikel ini? Artikel akan dihapus permanen dan tidak dapat dikembalikan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Checkbox Confirmation -->
                    <label class="flex items-center gap-3 p-4 bg-white rounded-xl border-2 border-gray-200 cursor-pointer hover:border-red-300 transition-colors delete-confirm-label">
                        <input type="checkbox" id="deleteArticleConfirmCheck" class="w-5 h-5 text-red-600 border-2 border-gray-300 rounded focus:ring-red-500 focus:ring-offset-0 delete-confirm-checkbox">
                        <span class="text-sm text-gray-700">Saya mengerti dan ingin melanjutkan penghapusan</span>
                    </label>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                    <button type="button" id="cancelDeleteArticleBtn"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                        <span class="material-symbols-outlined text-lg">close</span>
                        Batal
                    </button>
                    <button type="button" id="confirmDeleteArticleBtn" disabled
                        class="inline-flex items-center gap-2 px-5 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-red-700 transition-all duration-200 shadow-lg shadow-red-600/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                        <span class="material-symbols-outlined text-lg">delete_forever</span>
                        Ya, Hapus Artikel!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 z-[100] hidden bg-black/90 flex items-center justify-center p-4">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>
        <img id="lightbox-image" src="" alt="Preview" class="max-w-[90%] max-h-[85vh] object-contain rounded-lg shadow-2xl">
    </div>

    <script>
        // Toast Notification System with Circular Progress
        function showToast(type, title, message, duration = 4000) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const id = 'toast-' + Date.now();
            toast.id = id;
            const colors = {
                success: {
                    bg: 'bg-white',
                    border: 'border-emerald-200',
                    icon: 'check_circle',
                    iconBg: 'bg-emerald-500',
                    iconColor: 'text-white',
                    title: 'text-emerald-800',
                    progressCircle: '#10b981'
                },
                error: {
                    bg: 'bg-white',
                    border: 'border-red-200',
                    icon: 'error',
                    iconBg: 'bg-red-500',
                    iconColor: 'text-white',
                    title: 'text-red-800',
                    progressCircle: '#ef4444'
                },
                warning: {
                    bg: 'bg-white',
                    border: 'border-amber-200',
                    icon: 'warning',
                    iconBg: 'bg-amber-500',
                    iconColor: 'text-white',
                    title: 'text-amber-800',
                    progressCircle: '#f59e0b'
                },
                info: {
                    bg: 'bg-white',
                    border: 'border-blue-200',
                    icon: 'info',
                    iconBg: 'bg-blue-500',
                    iconColor: 'text-white',
                    title: 'text-blue-800',
                    progressCircle: '#3b82f6'
                }
            };
            const c = colors[type] || colors.info;
            toast.className = `${c.bg} border ${c.border} rounded-xl shadow-2xl overflow-hidden min-w-[320px] max-w-[400px] toast-enter`;
            toast.innerHTML = `
                <div class="p-4 flex items-start gap-3">
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 ${c.iconBg} rounded-full flex items-center justify-center ${c.iconColor} shadow-lg">
                            <span class="material-symbols-outlined">${c.icon}</span>
                        </div>
                        <svg class="absolute -top-1 -left-1 w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" stroke="#e5e7eb" stroke-width="2.5"></circle>
                            <circle cx="18" cy="18" r="16" fill="none" stroke="${c.progressCircle}" stroke-width="2.5" stroke-dasharray="100" stroke-dashoffset="0" stroke-linecap="round" class="circular-progress" style="animation-duration: ${duration}ms;"></circle>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold ${c.title}">${title}</p>
                        <p class="text-sm text-gray-600 mt-0.5">${message}</p>
                    </div>
                    <button onclick="removeToast('${id}')" class="flex-shrink-0 w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>`;
            container.appendChild(toast);
            setTimeout(() => removeToast(id), duration);
        }

        // Make showToast available globally
        window.showToast = showToast;

        function removeToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }
        }

        function openLightbox(src) {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-image');
            img.src = src;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        // Close Article Modal Handler
        function closeArticleModalHandler() {
            const modal = document.getElementById('articleModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Open Article Modal Handler
        function openArticleModal() {
            const modal = document.getElementById('articleModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        // Escape key handler for all modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
                closeArticleModalHandler();
                // Close other modals if they exist
                const storeModal = document.getElementById('storeModal');
                const deleteModal = document.getElementById('deleteModal');
                const deleteArticleModal = document.getElementById('deleteArticleModal');

                if (storeModal && !storeModal.classList.contains('hidden')) {
                    storeModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
                if (deleteModal && !deleteModal.classList.contains('hidden')) {
                    deleteModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
                if (deleteArticleModal && !deleteArticleModal.classList.contains('hidden')) {
                    deleteArticleModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }
        });

        // Delete Modal Checkbox Confirmation Handler
        const deleteConfirmCheck = document.getElementById('deleteConfirmCheck');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        if (deleteConfirmCheck && confirmDeleteBtn) {
            deleteConfirmCheck.addEventListener('change', function() {
                confirmDeleteBtn.disabled = !this.checked;
            });
        }

        // Delete Article Modal Checkbox Confirmation Handler
        const deleteArticleConfirmCheck = document.getElementById('deleteArticleConfirmCheck');
        const confirmDeleteArticleBtn = document.getElementById('confirmDeleteArticleBtn');

        if (deleteArticleConfirmCheck && confirmDeleteArticleBtn) {
            deleteArticleConfirmCheck.addEventListener('change', function() {
                confirmDeleteArticleBtn.disabled = !this.checked;
            });
        }

        // Reset checkbox and button when modal is closed
        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                // Reset checkbox and button
                if (deleteConfirmCheck) deleteConfirmCheck.checked = false;
                if (confirmDeleteBtn) confirmDeleteBtn.disabled = true;
            }
        }

        function closeDeleteArticleModal() {
            const modal = document.getElementById('deleteArticleModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                // Reset checkbox and button
                if (deleteArticleConfirmCheck) deleteArticleConfirmCheck.checked = false;
                if (confirmDeleteArticleBtn) confirmDeleteArticleBtn.disabled = true;
            }
        }

        // Confirm Delete Functions (can be overridden by external JS)
        function confirmDelete(itemName = '', itemId = '', message = '') {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                // Reset state
                if (deleteConfirmCheck) deleteConfirmCheck.checked = false;
                if (confirmDeleteBtn) confirmDeleteBtn.disabled = true;

                // Set item info if provided
                const nameEl = document.getElementById('deleteItemName');
                const idEl = document.getElementById('deleteItemId');
                const msgEl = document.getElementById('deleteMessage');

                if (nameEl) nameEl.textContent = itemName || '';
                if (idEl) idEl.textContent = itemId ? 'ID: ' + itemId : '';
                if (msgEl && message) msgEl.textContent = message;

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function confirmDeleteArticle(articleName = '', articleId = '', message = '') {
            const modal = document.getElementById('deleteArticleModal');
            if (modal) {
                // Reset state
                if (deleteArticleConfirmCheck) deleteArticleConfirmCheck.checked = false;
                if (confirmDeleteArticleBtn) confirmDeleteArticleBtn.disabled = true;

                // Set article info if provided
                const nameEl = document.getElementById('deleteArticleName');
                const idEl = document.getElementById('deleteArticleId');
                const msgEl = document.getElementById('deleteArticleMessage');

                if (nameEl) nameEl.textContent = articleName || '';
                if (idEl) idEl.textContent = articleId ? 'ID: ' + articleId : '';
                if (msgEl && message) msgEl.textContent = message;

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('siteSettingsForm');
            const btnSave = document.getElementById('btnSaveSettings');

            fetch('../../api/settings/get_settings.php')
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success' && res.data) {
                        const d = res.data;

                        // Isi Input Text
                        form.querySelector('[name="site_title"]').value = d.site_title || '';
                        form.querySelector('[name="site_description"]').value = d.site_description || '';
                        form.querySelector('[name="contact_email"]').value = d.contact_email || '';
                        form.querySelector('[name="contact_phone"]').value = d.contact_phone || '';
                        form.querySelector('[name="facebook_url"]').value = d.facebook_url || '';
                        form.querySelector('[name="instagram_url"]').value = d.instagram_url || '';
                        form.querySelector('[name="tiktok_url"]').value = d.tiktok_url || '';
                        form.querySelector('[name="youtube_url"]').value = d.youtube_url || '';

                        // Handle Preview Logo
                        if (d.site_logo) {
                            const img = document.getElementById('preview_site_logo');
                            img.src = '../../' + d.site_logo + '?v=' + new Date().getTime();
                            img.classList.remove('hidden');
                            document.getElementById('icon_site_logo').classList.add('hidden');
                        }

                        // Handle Preview Favicon
                        if (d.site_favicon) {
                            const img = document.getElementById('preview_site_favicon');
                            img.src = '../../' + d.site_favicon + '?v=' + new Date().getTime();
                            img.classList.remove('hidden');
                            // document.getElementById('icon_site_favicon').classList.add('hidden');
                        }
                    }
                })
                .catch(err => console.error('Gagal memuat pengaturan:', err));


            // --- 2. Simpan Data (Event Listener Tombol) ---
            btnSave.addEventListener('click', function(e) {
                e.preventDefault();

                // UI Loading State
                const originalBtnText = btnSave.innerHTML;
                btnSave.innerHTML = '<span class="material-symbols-outlined animate-spin text-xl">progress_activity</span> Menyimpan...';
                btnSave.disabled = true;

                const formData = new FormData(form);

                fetch('../../api/settings/save_settings.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.status === 'success') {
                            window.showToast('success', 'Berhasil!', res.message || 'Pengaturan berhasil disimpan');
                        } else {
                            window.showToast('error', 'Gagal!', res.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(err => {
                        window.showToast('error', 'Error!', 'Error koneksi server');
                        console.error(err);
                    })
                    .finally(() => {
                        // Restore UI
                        btnSave.innerHTML = originalBtnText;
                        btnSave.disabled = false;
                    });
            });
        });
    </script>

    <script src="../../assets/js/main.js" defer></script>
    <script src="../../assets/js/admin/webManagement.js" defer></script>
</body>

</html>