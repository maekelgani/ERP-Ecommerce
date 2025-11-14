<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin</title>
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
                    <h1 class="text-3xl font-bold"> Product </h1>
                    <p class="text-gray-400">Kelola produk-produk yang tersediadi penyimpanan</p>
                </div>
                <button  class="openForm px-5 py-2 bg-gray-800 text-white rounded-lg cursor-pointer transition duration-200 hover:bg-gray-600">
                    Tambah produk
                </button>
            </div>

            <!-- container TABEL PRODUCT -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6">
                    <h2 class="text-2xl font-bold">Product List</h2>
                </div>

                <!-- buat filter -->
                <div id="filter-field" class="mb-4 flex flex-wrap items-center gap-4 pb-3 pt-0"">
                    <!-- Filter cari produk -->
                    <div class="flex items-center gap-2">
                        <input type="text" placeholder="Cari Product" class="border border-gray-300 rounded-lg px-3 w-md py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                    <!-- filter by category -->
                    <div>
                        <select
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Semua Kategori</option>
                            <option value="laptop">Laptop</option>
                            <option value="aksesoris">Aksesoris</option>
                            <option value="komponen">Komponen</option>
                            <option value="peripheral">Peripheral</option>
                        </select>
                    </div>
                    <!-- filter by BRAND -->
                    <div>
                        <select
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Semua Brang</option>
                            <option value="Msi">Msi</option>
                            <option value="NVIDIA">NVIDIA</option>
                            <option value="Intel">Intel</option>
                            <option value="AMD">AMD</option>
                        </select>
                    </div>
                    <!-- filter urutan -->
                    <div>
                    <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500  focus:outline-none">
                        <option value="asc">Naik (ASC)</option>
                        <option value="desc">Turun (DESC)</option>
                    </select>
                    </div>

                    <button class=" px-10 bg-gray-800 text-white py-2 rounded-md font-semibold text-sm hover:bg-gray-500 cursor-pointer transition"> 
                        Terapkan Filter 
                    </button>
                </div>

                <!-- BAGIAN TABEL PRODUCT GEYS -->
                <div id="container-content" class="pt-0">
                    <div class="relative w-full overflow-auto">
                        <!-- TABEL NYA DI SINI -->
                        <table class="w-full caption-bottom text-sm">
                            <thead class="border-b uppercase">
                                <tr class="border-b bg-gray-50 rounded-lg">
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600 w-3">Rating</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600 w-3">Gambar</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Nama Produk</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Brand</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Kategori</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">harga</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Stock</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <!-- Dummy 1 -->
                                <tr class="border-t">
                                    <td class="p-4">Rating</td> <!--Rating-->
                                    <td class="p-4"> <img class="lightbox-trigger w-10 object-cover rounded-sm cursor-pointer transition-transform transform hover:scale-105"
                                    src="https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/catalog-image/111/MTA-165688338/asus_pc_build_asus_rog_strix_i9-14900kf_32gb_ram_1tb_ssd_rtx_4080_super_16gb_gddr6x_full01_dazv7u7t.jpg" alt=""></td> <!--product-->
                                    <td class="p-4">Gaming PC RTX 4080</td> <!--product-->
                                    <td class="p-4">ROG</td> <!--product-->
                                    <td class="p-4">Pc Bundling</td> <!--Kategori-->
                                    <td class="p-4">Rp 25.000.000</td> <!--Price-->
                                    <td class="flex pt-6 gap-2 items-center">
                                        <input type="number" readonly value="3" min="0" class="produkstok w-12 text-center rounded">
                                        <button type="button" class="btn-plus w-6 h-6 border rounded-md hover:bg-gray-100 cursor-pointer flex justify-center items-center">+</button>
                                        <button type="button" class="btn-minus w-6 h-6 border rounded-md hover:bg-gray-100 cursor-pointer flex justify-center items-center">−</button>
                                    </td> <!--Buat Stok -->
                                    
                                    <td class="p-4"> <!--Status-->
                                        <div class=" text-center px-2 font-semibold bg-green-400 w-[50%] rounded-lg hidden">Active</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-orange-300 w-[50%] rounded-lg ">Low Stock</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-red-500 w-[50%] rounded-lg hidden">Out of Stock</div> <!--Buat Status nya-->
                                    </td> 

                                    <td class="p-4 gap-3"> <!--actions-->
                                        <div>
                                            <button class="editForm cursor-pointer text-blue-500 font-semibold">Edit</button> |
                                            <button class="cursor-pointer text-red-500 font-semibold">Hapus</button>
                                        </div>
                                    </td> 
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal Lightbox -->
    <div id="lightbox" class="fixed inset-0 z-50 items-center justify-center p-6 hidden">
        <div id="lb-backdrop" class="absolute inset-0 bg-black opacity-75"></div>

        <div class="relative max-w-[95vw] max-h-[95vh] flex items-center justify-center">
            <button id="lb-close" class="absolute top-2 right-2 bg-white/90 rounded-lg p-2 shadow hover:bg-white z-20">
            ✕
            </button>

        <img id="lb-image"
            src="https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/catalog-image/111/MTA-165688338/asus_pc_build_asus_rog_strix_i9-14900kf_32gb_ram_1tb_ssd_rtx_4080_super_16gb_gddr6x_full01_dazv7u7t.jpg" 
            alt=""
            class="max-w-full max-h-[88vh] rounded-md shadow-lg opacity-0 transition-opacity duration-200" /> <!--bagian gambar ketika di klik di sini-->
        </div>
    </div>

    <!-- Modal Form tambah/edit barang -->
    <div id="form-product" class="fixed inset-0 z-50 flex items-center justify-center p-6 hidden">
        <div id="lb-backdrop" class="absolute inset-0 bg-black opacity-75 transition-opacity duration-300"></div>
        
        <div id="modal-card" class="relative z-10 max-w-lg w-full rounded-lg border border-gray-200 bg-white shadow-md p-4 px-8 justify-center
                opacity-0 scale-95 translate-y-4
                transition-all duration-300 ease-out">
            <div class="mb-4 flex justify-between"> <!--ini header-->
                <div>
                    <h2 class="text-lg font-semibold">Add Product</h2>
                    <p class="text-sm text-gray-400">Masukkan detail produk kedalam persediaan</p>
                </div>
                <button class="cancel cursor-pointer">
                    <span class="material-symbols-outlined">close</span> <!--Close/Cancel Button-->
                </button>
            </div>
            <form action="">
                <!-- Imput ID Produk/ dibuat Otomatis paling -->
                <div class="mb-4">
                    <label for="" class="font-semibold text-sm">ID Produk</label>
                    <input type="text" disabled
                    class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-200 text-base focus:ring-blue-500 focus:border-blue-500 ">
                </div>

                <!-- Input nama produk -->
                <div class="mb-4">
                    <label for="" class="font-semibold text-sm">Nama Produk</label>
                    <input type="text" required 
                    class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- input kategori dan brand -->
                <div class="flex gap-2 mb-4">
                    <div class="w-[50%]">
                        <label for="" class="font-semibold text-sm">Kategori</label>
                        <select name="" id="" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 ">
                            <option value="" class="value:text-gray">Pilih Kategori</option>
                            <option value="">CPU</option>
                            <option value="">Monitor</option>
                        </select>
                    </div>
                    <!-- brand -->
                    <div class="w-[50%]">
                        <label for="" class="font-semibold text-sm">Brand</label>
                        <select name="" id="" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 ">
                            <option value="" class="value:text-gray">Pilih Kategori</option>
                            <option value="">MSI</option>
                            <option value="">AMD</option>
                        </select>
                    </div>
                </div>

                <!-- input harga dan stok -->
                <div class="flex gap-2 mb-4">
                    <div class="w-[80%]">
                        <label for="" class="font-semibold text-sm">Harga</label>
                        <input type="text" placeholder="masukkan harga dalam rupiah" required
                        class="w-full italic p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <!-- untuk stok -->
                    <div class="w-[20%]">
                        <label for="" class="font-semibold text-sm">Stok</label>
                        <input type="number" required 
                        class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <label for="deskripsiProduk" class="font-semibold text-sm">Deskripsi Produk</label>
                <textarea name="deskripsiProduk" id="" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500"></textarea>

                <div class="mb-4"">
                    <label for="" class="text-sm font-semibold">Gambar Produk</label>
                    <input type="file" id="file-upload" name="file-upload" class="block w-[10   0%] text-sm file:mr-4 file:rounded-sm file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700 file:cursor-pointer border border-gray-300 rounded-lg"/>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="p-2 px-4 bg-gray-800 text-white rounded-lg hover:bg-gray-700 cursor-pointer">Tambah</button>
                </div>
            </form>
        </div>
    </div>
    <script src="/assets/js/admin/product.js" defer></script>
</body>
</html>