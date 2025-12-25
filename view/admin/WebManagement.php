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
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold">Web Management</h1>
                    <p class="text-gray-400">Kelola pengaturan dan informasi mengenai website anda</p>
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
        </main>
    </div>

    <!-- Modal: Add/Edit Store -->
    <div id="storeModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl transform transition-all duration-300">
            <!-- Header with gradient -->
            <div class="sticky top-0 z-10 px-6 py-5 flex items-center justify-between border-b border-gray-100 bg-[#882426]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">store</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white" id="storeModalTitle">Tambah Lokasi Toko</h3>
                        <p class="text-white/70 text-sm">Isi data lokasi toko dengan lengkap</p>
                    </div>
                </div>
                <button id="closeStoreModal" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-white">close</span>
                </button>
            </div>

            <form id="storeForm" class="overflow-y-auto max-h-[calc(90vh-80px)]">
                <input type="hidden" id="storeId" name="id_toko">

                <div class="p-6 space-y-6">
                    <!-- Section: Informasi Dasar -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-[#882426] flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-sm">info</span>
                            </div>
                            <h4 class="font-semibold text-gray-800">Informasi Dasar</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Toko <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">storefront</span>
                                    <input type="text" id="namaToko" name="nama_toko" required placeholder="Contoh: Nano Komputer Pusat" class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">phone</span>
                                    <input type="tel" id="noTelepon" name="no_telepon" placeholder="08xx-xxxx-xxxx" class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">markunread_mailbox</span>
                                    <input type="text" id="kodePos" name="kode_pos" placeholder="12345" class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Alamat -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-sm">location_on</span>
                            </div>
                            <h4 class="font-semibold text-gray-800">Alamat Toko</h4>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea id="alamatToko" name="alamat" rows="2" required placeholder="Jl. Contoh No. 123, RT/RW, Gedung ABC Lantai 2" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all resize-none bg-white"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi <span class="text-red-500">*</span></label>
                                    <select id="provinsiToko" name="provinsi" required class="w-full select2-provinsi">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                    <select id="kotaToko" name="kota_kabupaten" required class="w-full select2-kota">
                                        <option value="">Pilih Kota/Kabupaten</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kecamatan</label>
                                    <select id="kecamatanToko" name="kecamatan" class="w-full select2-kecamatan">
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kelurahan</label>
                                    <select id="kelurahanToko" name="kelurahan" class="w-full select2-kelurahan">
                                        <option value="">Pilih Kelurahan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Jam Operasional -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-sm">schedule</span>
                            </div>
                            <h4 class="font-semibold text-gray-800">Jam Operasional</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Buka</label>
                                <div class="relative">
                                    <input type="time" id="jamBuka" name="jam_buka" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Tutup</label>
                                <div class="relative">
                                    <input type="time" id="jamTutup" name="jam_tutup" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Toggle -->
                    <div class="flex items-center justify-between bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white">check_circle</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Status Toko</p>
                                <p class="text-sm text-gray-500">Aktifkan untuk menampilkan toko kepada pelanggan</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="isActive" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300/50 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 mb-2">
                    <button type="button" id="cancelStoreBtn" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all font-medium flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">close</span>
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-[#882426] text-white rounded-xl transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg font-medium flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Simpan Toko
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Delete Confirmation -->
    <div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-red-600 text-3xl">warning</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Hapus</h3>
                <p class="text-gray-500 mb-6" id="deleteMessage">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex justify-center gap-3">
                    <button id="cancelDeleteBtn" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">Batal</button>
                    <button id="confirmDeleteBtn" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">Hapus</button>
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

    <!-- Modal: Add/Edit Article -->
    <div id="articleModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden shadow-2xl">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#882426] flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">article</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800" id="articleModalTitle">Tambah Artikel</h3>
                </div>
                <button id="closeArticleModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-gray-500">close</span>
                </button>
            </div>
            <form id="articleForm" class="p-6 space-y-5 overflow-y-auto max-h-[calc(90vh-80px)]">
                <input type="hidden" id="articleId" name="id_post">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Artikel <span class="text-red-500">*</span></label>
                        <input type="text" id="articleTitle" name="judul" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all" placeholder="Masukkan judul artikel...">
                        <p class="text-xs text-gray-400 mt-1">Slug akan digenerate otomatis dari judul</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select id="articleCategory" name="id_category" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                            <option value="">Pilih Kategori</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select id="articleStatus" name="status" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                            <option value="draft">Draft</option>
                            <option value="publish">Publish</option>
                        </select>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">image</span>
                        Thumbnail Artikel
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-[#882426]/50 transition-all duration-300 cursor-pointer" id="thumbnailDropZone">
                        <input type="file" id="thumbnailInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                        <input type="hidden" id="thumbnailFilename" name="thumbnail">
                        <div id="uploadThumbnailBtn">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl text-gray-400">cloud_upload</span>
                            </div>
                            <p class="text-gray-600 font-medium mb-1">Drag & drop atau klik untuk upload</p>
                            <p class="text-gray-400 text-sm">Format: JPG, PNG, WebP (Maks. 5MB)</p>
                            <button type="button" id="btnUploadThumbnail" class="mt-4 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors inline-flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">upload</span>
                                Pilih Gambar
                            </button>
                        </div>
                        <div id="thumbnailPreviewContainer" class="hidden">
                            <div class="relative group cursor-pointer inline-block" onclick="openLightbox(document.getElementById('thumbnailImage').src)">
                                <img id="thumbnailImage" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-md transition-transform group-hover:scale-[1.02]">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-2xl">zoom_in</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-2" id="thumbnailFileName"></p>
                            <button type="button" id="btnRemoveThumbnail" class="mt-4 px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors flex items-center gap-2 mx-auto">
                                <span class="material-symbols-outlined text-lg">delete</span>
                                Hapus Gambar
                            </button>
                        </div>
                    </div>
                </div>
                <!-- <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">image</span>
                        Thumbnail Artikel
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-[#882426]/50 transition-colors" id="thumbnailDropZone">
                        <input type="file" id="thumbnailInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                        <input type="hidden" id="thumbnailFilename" name="thumbnail">
                        <div id="uploadThumbnailBtn">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl text-gray-400">cloud_upload</span>
                            </div>
                            <p class="text-gray-600 font-medium mb-1">Klik untuk upload thumbnail</p>
                            <p class="text-gray-400 text-sm">Format: JPG, PNG, WebP (Maks. 5MB)</p>
                            <button type="button" id="btnUploadThumbnail" class="mt-4 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                Pilih Gambar
                            </button>
                        </div>
                        <div id="thumbnailPreviewContainer" class="hidden">
                            <div class="relative group cursor-pointer inline-block" onclick="openLightbox(document.getElementById('thumbnailImage').src)">
                                <img id="thumbnailImage" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-md transition-transform group-hover:scale-[1.02]">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-2xl">zoom_in</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-2" id="thumbnailFileName"></p>
                            <button type="button" id="btnRemoveThumbnail" class="mt-4 px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors flex items-center gap-2 mx-auto">
                                <span class="material-symbols-outlined text-lg">delete</span>
                                Hapus Gambar
                            </button>
                        </div>
                    </div>
                </div> -->

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Excerpt (Ringkasan)</label>
                    <textarea id="articleExcerpt" name="excerpt" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all resize-none" placeholder="Ringkasan singkat artikel untuk preview..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konten Artikel <span class="text-red-500">*</span></label>
                    <textarea id="articleContent" name="konten" rows="12" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all resize-none" placeholder="Tulis konten artikel..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" id="cancelArticleBtn" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg font-medium flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Simpan Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Delete Article Confirmation -->
    <div id="deleteArticleModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-red-600 text-3xl">warning</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Hapus Artikel</h3>
                <p class="text-gray-500 mb-6" id="deleteArticleMessage">Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex justify-center gap-3">
                    <button id="cancelDeleteArticleBtn" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">Batal</button>
                    <button id="confirmDeleteArticleBtn" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">Hapus</button>
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });
    </script>

    <script src="../../assets/js/main.js" defer></script>
    <script src="../../assets/js/admin/webManagement.js" defer></script>
</body>

</html>