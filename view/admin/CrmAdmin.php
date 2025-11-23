<?php
// Definisikan title untuk halaman ini
$pageTitle = "Customer Relationship Management";
// Include file head.php dari components/admin
include '../../components/admin/head.php';
?>

<!-- NOTE!!! perlu diingat bahwa semuanya belum ada javascriptnya jadi belum interaktif dan responsive
-->

<body class="bg-no-repeat h-screen flex">


    <!-- Leftside: Sidebar -->
    <aside class="w-[250px] flex items-center sticky top-0 h-screen">
        <?php include '../../components/admin/sidebarAdmin.php'; ?>
    </aside>

    <!-- Rightside:-->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- navbar kawan -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <!-- Main Contet -->
        <main class="flex-1 overflow-y-auto p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold"> Customer Relationship </h1>
                    <p class="text-gray-400">Laporan komprehensif tentang performa website dan penjualan</p>
                </div>
            </div>

            <!-- Stsats Card -->
            <div class="grid grid-row-1 grid-cols-2 gap-4 mb-4">
                <!-- Card-1 -->
                <div class="col-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class=" text-base text-gray-600">Total Pelanggan Terdaftar</h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">1092</p>
                    </div>
                </div>

                <!-- Card-2 -->
                <div class="col-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div id="card-header" class="mb-3"">
                        <h2 class=" text-base text-gray-600">Jumlah pelanggan Aktif</h2>
                    </div>
                    <div id="card-content">
                        <p class="font-bold text-2xl">20</p>
                    </div>
                </div>
            </div>

            <!--  -->
            <div class="grid grid-cols-4 grid-rows-1 mb-4 gap-4">
                <!-- Tabel Daftar pelanggan Aktif -->
                <div class="col-span-3 rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div>
                        <div class="mb-4">
                            <h2 class="text-2xl font-bold">Pelanggan Paling Aktif</h2>
                            <p class="text-base text-gray-400">Menampilkan Users yang paling sering melakukan pembelian barang</p>
                        </div>
                        <div>
                            <table class="w-full caption-bottom text-sm">
                                <thead class="border-b uppercase">
                                    <tr class="border-b bg-gray-50 rounded-lg ">
                                        <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Nama Pelanggan</th>
                                        <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Email</th>
                                        <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Total Transaksi</th>
                                        <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Transaksi Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr class="border-t">
                                        <!-- Nama -->
                                        <td class="p-3">Jojon Saturo</td>
                                        <!-- Email -->
                                        <td class="p-3">jo.saturn@gmail.com</td>
                                        <!-- total Transaksi -->
                                        <td class="p-3 text-gray-500">18x transaksi</td>
                                        <!-- transaksi terakhir -->
                                        <td class="p-3 pl-10 text-gray-500">3 Hari yang lalu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-span-1 rounded-lg border border-gray-200 bg-white shadow-md p-4">
                    <div>
                        <div class="mb-4">
                            <h2 class="text-2xl font-bold">Rating Produk</h2>
                            <p class="text-base text-gray-400">Review produk terbaru</p>
                        </div>
                        <div>
                            <table class="w-full caption-bottom text-sm">
                                <thead class="border-b uppercase">
                                    <tr class="border-b rounded-lg ">
                                        <th class="h-12 px-3 text-left align-middle font-bold ">Produk</th>
                                        <th class="h-12 px-3 text-middle align-middle font-bold ">Rating</th>
                                        <th class="h-12 px-3 text-left align-middle font-bold "></th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr class="border-t">
                                        <!-- Nama -->
                                        <td class="p-3">Keyboard Razer V3</td>
                                        <!-- Rating -->
                                        <td class="p-3 text-center font-semibold">4 / 5</td>
                                        <td class="p-3">
                                            <button class="openReview text-blue-600 cursor-pointer hover:text-blue-400">Lihat Detail</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE Saran dan Masukkan -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6">
                    <h2 class="text-2xl font-bold">Saran dan Masukkan</h2>
                    <p class="text-gray-400">Menampilkan saran dan masukkan (feeback) dari users</p>
                </div>
                <div class="grid grid-cols-3 gap-4 px-4">

                    <!-- Dummy 2 -->
                    <div class="relative flex flex-col bg-white shadow-sm border border-slate-200 rounded-lg w-full">
                        <div class="px-4 mb-0 border-b bg-gray-800 border-slate-200 pt-3 pb-2 rounded-t-lg">
                            <span class="text-xl text-white font-semibold">
                                Saran dan Masukkan
                            </span>
                        </div>

                        <div class="p-4">
                            <p class="font-semibold mb-1">User : <span id=""></span></p>
                            <p class="text-gray-400 mb-1">Tanggal : <span id=""></span></p>
                            <p class="text-gray-400 mb-1">Saran & Masukkan : <span id=""></span></p>
                            <textarea class="w-full border border-gray-300 rounded-lg p-2 text-sm" disabled name="" id=""> WEB MANTAP </textarea>
                        </div>
                        <div class="p-4 pt-0 justify-end w-full">
                            <button class="p-2 px-4 rounded-lg bg-gray-800 text-white hover:bg-gray-600 duration-150 cursor-pointer">Kirim email</button>
                            <button class="p-2 px-4 rounded-lg font-base transform transition-transform duration-300 hover:scale-110 cursor-pointer">Hapus</button>
                        </div>
                    </div>

                    <!-- DUMMY 2 -->
                    <div class="relative flex flex-col bg-white shadow-sm border border-slate-200 rounded-lg w-full">
                        <div class="px-4 mb-0 border-b bg-gray-800 border-slate-200 pt-3 pb-2 rounded-t-lg">
                            <span class="text-xl text-white font-semibold">
                                Saran dan Masukkan
                            </span>
                        </div>

                        <div class="p-4">
                            <p class="font-semibold mb-1">User : <span id=""></span></p>
                            <p class="text-gray-400 mb-1">Tanggal : <span id=""></span></p>
                            <p class="text-gray-400 mb-1">Saran & Masukkan : <span id=""></span></p>
                            <textarea class="w-full border border-gray-300 rounded-lg p-2 text-sm" disabled name="" id=""> WEB MANTAP </textarea>
                        </div>
                        <div class="p-4 pt-0 justify-end w-full">
                            <button class="p-2 px-4 rounded-lg bg-gray-800 text-white hover:bg-gray-600 duration-150 cursor-pointer">Kirim email</button>
                            <button class="p-2 px-4 rounded-lg font-base transform transition-transform duration-300 hover:scale-110 cursor-pointer">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Detail Review Produk -->
    <div id="modal-review" class="fixed inset-0 z-50 flex items-center justify-center p-6 hidden">
        <div id="lb-backdrop" class="absolute inset-0 bg-black opacity-75 transition-opacity duration-300"></div>
        <div id="modal-card" class="relative z-10 max-w-lg w-full rounded-lg border border-gray-200 bg-white shadow-md p-4 px-8 justify-center
                opacity-0 scale-95 translate-y-4 transition-all duration-300 ease-out">

            <!-- Header -->
            <div class="mb-4 flex justify-between items-center">
                <h2 class="text-2xl border-b border-gray-400 font-semibold">Detail Review Produk</h2>
                <button class="cancel cursor-pointer text-gray-600 hover:text-black">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Isi -->
            <div class="space-y-3 text-sm">
                <p><strong>Order ID:</strong> <span id="review-orderid"></span></p>
                <p><strong>Produk:</strong> <span id="review-produk"></span></p>
                <p><strong>Pembeli:</strong> <span id="review-user"></span></p>
                <p><strong>Rating:</strong> <span id="review-rating"></span> / 5</p>
                <p><strong>Komentar:</strong> <span id="review-comment"></span></p>
            </div>

            <!-- Footer -->
            <div class="mt-6 flex justify-end gap-2">
                <button id="reply-btn" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 cursor-pointer">
                    Balas Komentar
                </button>
                <button class="cancel text-gray-800 px-4 py-2 transform transition-transform duration-300 hover:scale-110 cursor-pointer">
                    Keluar
                </button>
            </div>

            <!-- Textarea Balasan (hidden by default) -->
            <div id="reply-box" class="mt-4 hidden">
                <textarea id="reply-text" class="w-full border border-gray-300 rounded-lg p-2 text-sm"
                    placeholder="Tulis balasan untuk komentar ini..."></textarea>
                <div class="flex justify-end mt-2">
                    <button id="send-reply" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Kirim Balasan
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>