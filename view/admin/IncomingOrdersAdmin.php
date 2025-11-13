<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="icon" href="../assets/img/Nanocomp.png">
</head>
<body class="bg-no-repeat h-screen flex">

    <!-- Leftside: Sidebar -->
    <aside class="w-[250px] flex items-center sticky top-0 h-screen">
        <?php include '../../components/admin/sidebarAdmin.php' ;?>
    </aside>

    <!-- Rightside:-->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- navbar kawan -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php' ;?>
        </header>

        <!-- main kontent -->
        <main class="flex-1 overflow-y-auto p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold"> Incoming Orders </h1>
                    <p class="text-gray-400">Kelola order yang masuk dan ubah status pengiriman</p>
                </div>
            </div>

            <!-- container TABEL orders -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6">
                    <h2 class="text-2xl font-bold">Order List</h2>
                    <p class="text-base text-gray-400">Daftar order yang sedang dalam proses pengemasan</p>
                </div>

                <!-- Filter -->
                <div id="filter-field" class="mb-4 flex flex-wrap items-center gap-4 pb-3 pt-0"">
                    <!-- Filter cari produk -->
                    <div class="flex items-center gap-2">
                        <input type="text" placeholder="Cari Product" class="border border-gray-300 rounded-lg px-3 w-md py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <!-- filter by category -->
                    <div>
                        <select
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Semua Status</option>
                            <option value="laptop">Laptop</option>
                            <option value="aksesoris">Aksesoris</option>
                            <option value="komponen">Komponen</option>
                            <option value="peripheral">Peripheral</option>
                        </select>
                    </div>
                    <!-- filter by Tanggal -->
                    <div>
                        <input type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <button class=" px-10 bg-gray-800 text-white py-2 rounded-md font-semibold text-sm hover:bg-gray-500 cursor-pointer transition"> 
                        Terapkan Filter 
                    </button>
                </div>

                <!-- BAGIAN TABEL orders GEYS -->
                <div id="container-content" class="pt-0">
                    <div class="relative w-full overflow-auto">
                        <!-- TABEL NYA DI SINI -->
                        <table class="w-full caption-bottom text-sm">
                            <thead class="border-b uppercase">
                                <tr class="border-b bg-gray-50 rounded-lg ">
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Order ID</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Customer</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Items</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Total</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Tanggal-Pesan</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Status Pembayaran</th>
                                    <th class="h-12 px-4 text-center align-middle font-bold text-muted-foreground text-gray-600">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]">Actions</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600 w-[10%]"></th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-t">
                                    <td class="p-3 text-xs text-gray-500"># ORD69</td>
                                    <td class="p-3">Juan Kamil</td>
                                    <td class="p-3">2</td>
                                    <td class="p-3">Rp. 24,425,000</td>
                                    <td class="p-3">23-10-2025</td>
                                    <td class="p-3">
                                        <div class="px-2 text-green-800 border border-green-500 rounded-lg bg-green-200 hidden">Sudah Membayar</div>
                                        <div class="px-2 text-gray-500 border border-gray-400 rounded-lg bg-gray-100">Belum Membayar</div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <select name="" id="" class="bg-gray-50 rounded-lg px-5 border border-gray-300">
                                            <option value="dikemas" class="">Dikemas</option>
                                            <option value="dikirim">Dikirim</option>
                                        </select>
                                    </td>
                                    <td class="p-3">Tugaskan Kurir</td>
                                    <td class="p-3">
                                        <button class="detailOrder px-2 font-semibold cursor-pointer text-blue-900 hover:brightness-100">Lihat Detail</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal lihat detail pesanan -->
    <div id="order-details" class="fixed inset-0 z-50 hidden">
        <div id="lb-backdrop" class="absolute inset-0 bg-black opacity-75 transition-opacity duration-300"></div>

        <!-- Gunakan flex-col dan overflow-y-auto di sini -->
        <div class="relative z-10 flex justify-center items-start p-4 overflow-y-auto h-full">
            <div
            id="modal-card"
            class="w-[50%] md:w-1/2 bg-white rounded-lg shadow-md p-6 mt-10 mb-10 max-h-[90vh] overflow-y-auto transition-all duration-300 ease-out opacity-0 scale-95 translate-y-4"
            >

                <div class="mb-4 flex justify-between">
                    <div>
                        <h2 class="tracking-wide text-2xl font-bold">Detail Pesanan</h2>
                        <p class="text-sm text-gray-400">Informasi lengkap pemesanan</p>
                    </div>
                    <button class="cancel cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- informasi pemesanan -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <!-- gambar disiini -->Informasi Pemesanan
                        </h2>
                        <!-- ISI INFORMASINYA -->
                        <div class="grid grid-cols-2 gap-4 p-4 rounded-lg">
                            <!-- order id -->
                            <div>
                                <p class="text-sm text-gray-400">Order ID</p>
                                <span id="d-order-id" class="font-semibold"></span>
                            </div>
                            <!-- Nama Pemesan -->
                            <div>
                                <p class="text-sm text-gray-400">Nama Pemesanan</p>
                                <span id="d-name" class="font-semibold"></span>
                            </div>
                            <!-- Tanggal Pemesanan -->
                            <div>
                                <p class="text-sm text-gray-400">Tanggal Pemesanan</p>
                                <span id="d-date" class="font-semibold">
                            </div>
                            <!-- tanggal Diterima -->
                            <div>
                                <p class="text-sm text-gray-400">Tanggal Diterima</p>
                                <span id="d-recieveDate" class="font-semibold"></span>
                            </div>
                            <!-- Alamat -->
                            <div class="col-span-2">
                                <p class="text-sm text-gray-400">Alamat</p>
                                <span id="d-address" class="font-semibold"></span>
                            </div>
                        </div>
                        <!-- Bagian pemesanan -->
                        <div class="gap-4 space-y-4 p-4">
                            <!-- Status Pemesanan -->
                            <div class="col-span-2">
                                <p class="text-sm text-gray-400">Status Pemesanan</p>
                                <span id="d-status"></span>
                            </div>
                            <!-- Bar status -->
                            <div class="col-span-2">
                                <div class="mb-4">
                                    <label class="font-semibold text-sm">Status Pengiriman</label>
                                    <div class="flex items-center mt-2">
                                        <!-- Step 1 -->
                                        <div class="flex flex-col items-center">
                                            <div id="step-packed" class="w-4 h-4 rounded-full bg-gray-300 border border-gray-500 transition-all"></div>
                                            <span class="text-xs mt-1">Dikemas</span>
                                        </div>
                                        <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
                                        <!-- Step 2 -->
                                        <div class="flex flex-col items-center">
                                            <div id="step-shipped" class="w-4 h-4 rounded-full bg-gray-300 border border-gray-500 transition-all"></div>
                                            <span class="text-xs mt-1">Dikirim</span>
                                        </div>
                                        <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
                                        <!-- Step 3 -->
                                        <div class="flex flex-col items-center">
                                            <div id="step-done" class="w-4 h-4 rounded-full bg-gray-300 border border-gray-500 transition-all"></div>
                                            <span class="text-xs mt-1">Selesai</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div data-orientation="horizontal" role="none" class="shrink-0 bg-gray-200 h-px w-full"></div>
                    <!-- informasi Detail Produk-->
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <!-- gambar disini --> Daftar produk (<span id=""></span>items) 
                        </h2>
                        <!-- List produknya di sini -->
                        <div class="space-y-3"> 
                            <div class="flex items-center justify-between p-4 rounded-lg">
                                <!-- Nama produk dan Quantity nya -->
                                <div class="flex-1">
                                    <p class="font-semibold"><span id=""></span></p>
                                    <p class="text-sm text-gray-400"><span id=""></span></p>
                                </div>
                                <!-- Subtotal -->
                                <div class="text-right"><span id=""> </span></div>
                            </div>
                        </div>
                        <!-- Total Harga -->
                        <div class="flex items-center justify-between p-4 rounded-lg">
                            <p class="text-lg font-bold">Total Harga:</p>
                            <p class="text-xl font-bold text-blue-500">Rp <span id="d-total"></span></p>
                        </div>
                    </div>
                    <div data-orientation="horizontal" role="none" class="shrink-0 bg-gray-200 h-px w-full"></div>
                    <!-- informasi Detail Pembayaran -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                        <!--gambar disini--> Detail Pembayaran</h2>
                        <div class="grid grid-cols-2 gap-4 bg-muted/30 p-4 rounded-lg">
                            <!-- ID Pembayaran -->
                            <div>
                                <p class="text-sm text-gray-400">ID Pembayaran</p>
                                <span id="" class="font-semibold"></span>
                            </div>
                            <!-- Metode Pembayaran -->
                            <div>
                                <p class="text-sm text-gray-400">Metode Pembayaran</p>
                                <span id="" class="font-semibold"></span>
                            </div>
                            <!-- Tanggal Pembayaran -->
                            <div>
                                <p class="text-sm text-gray-400">Tanggal Pembayaran</p>
                                <span id="" class="font-semibold">
                            </div>
                            <!-- Status Pembayaran -->
                            <div>
                                <p class="text-sm text-gray-400">Status Pembayaran</p>
                                <span id="" class="font-semibold"></span>
                            </div>
                            <!-- Bukti Pembayaran -->
                            <div class="col-span-2">
                                <p class="text-sm text-gray-400">Bukti Pembayaran</p>
                                <a class="inline-flex items-center gap-2 text-blue-500 hover:underline" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-4 w-4"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>Lihat bukti pembayaran</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/admin/order.js" defer></script>
</body>
</html>