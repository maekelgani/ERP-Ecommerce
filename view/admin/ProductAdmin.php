<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin</title>
    <link rel="stylesheet" href="/src/output.css">
</head>
<body class="bg-no-repeat h-screen flex">

    <!-- Leftside: Sidebar -->
    <aside class="w-[250px] flex items-center sticky top-0 h-screen">
        <?php include '../template/sidebarAdmin.php' ;?>
    </aside>

    <!-- Rightside:-->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <!-- navbar kawan -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../template/NavbarAdmin.php' ;?>
        </header>
        <!-- main kontent -->
        <main class="p-6">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold"> Product </h1>
                    <p class="text-gray-400">Manage your product inventory</p>
                </div>
                <div>
                    <a class="openForm px-5 py-2 bg-gray-800 text-white rounded-lg"> Add Product</a>
                </div>
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
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600 w-3">Gambar</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Product Name</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Brand</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Category</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Price</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Stock</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-bold text-muted-foreground text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <!-- Dummy 1 -->
                                <tr class="border-t">
                                    <td class="p-3"> <img class="lightbox-trigger w-10 object-cover rounded-sm cursor-pointer transition-transform transform hover:scale-105"
                                    src="https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/catalog-image/111/MTA-165688338/asus_pc_build_asus_rog_strix_i9-14900kf_32gb_ram_1tb_ssd_rtx_4080_super_16gb_gddr6x_full01_dazv7u7t.jpg" alt=""></td> <!--product-->
                                    <td class="p-3">Gaming PC RTX 4080</td> <!--product-->
                                    <td class="p-3">ROG</td> <!--product-->
                                    <td class="p-3">Pc Bundling</td> <!--Kategori-->
                                    <td class="p-3">Rp 25.000.000</td> <!--Price-->
                                    <td class="flex pt-3 gap-2">
                                        <input type="number" disabled value="2" class="w-7 ">
                                        <button class="w-5 pt-0 border rounded-md hover:bg-gray-100 cursor-pointer"> + </button>
                                        <button class="w-5 pt-0 border rounded-md hover:bg-gray-100 cursor-pointer"> - </button>
                                    </td> <!--Stock-->
                                    
                                    <td class="p-3">
                                        <div class=" text-center px-2 font-semibold bg-green-400 w-[50%] rounded-lg hidden">Active</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-orange-300 w-[50%] rounded-lg ">Low Stock</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-red-500 w-[50%] rounded-lg hidden">Out of Stock</div> <!--Buat Status nya-->
                                    </td> <!--Status-->

                                    <td class="p-3">Edit | Delete</td> <!--actions-->
                                </tr>
                                <!-- Dummy 2 -->
                                <tr class="border-t">
                                    <td class="p-3">Gaming PC RTX 4080</td> <!--product-->
                                    <td class="p-3">Pc Bundling</td> <!--Kategori-->
                                    <td class="p-3">Rp 25.000.000</td> <!--Price-->
                                    <td class="flex pt-3 gap-2">
                                        <input type="number" disabled value="2" class="w-7 ">
                                        <button class="w-5 pt-0 border rounded-md hover:bg-gray-100 cursor-pointer"> + </button>
                                        <button class="w-5 pt-0 border rounded-md hover:bg-gray-100 cursor-pointer"> - </button>
                                    </td> <!--Stock-->
                                    
                                    <td class="p-3">
                                        <div class=" text-center px-2 font-semibold bg-green-400 w-[50%] rounded-lg hidden">Active</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-orange-300 w-[50%] rounded-lg ">Low Stock</div> <!--Buat Status nya-->
                                        <div class=" text-center px-2 font-semibold bg-red-500 w-[50%] rounded-lg hidden">Out of Stock</div> <!--Buat Status nya-->
                                    </td> <!--Status-->

                                    <td class="p-3">Edit | Delete</td> <!--actions-->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal Lightbox -->
    <div id="lightbox" class="fixed inset-0 hidden z-50 items-center justify-center p-6">
        <div id="lb-backdrop" class="absolute inset-0 bg-black bg-opacity-60"></div>

        <div class="relative max-w-[95vw] max-h-[95vh] flex items-center justify-center">
            <button id="lb-close" class="absolute top-2 right-2 bg-white/90 rounded-full p-1 shadow hover:bg-white z-20">
            ✕
            </button>

        <img id="lb-image"
            src="https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/catalog-image/111/MTA-165688338/asus_pc_build_asus_rog_strix_i9-14900kf_32gb_ram_1tb_ssd_rtx_4080_super_16gb_gddr6x_full01_dazv7u7t.jpg"
            alt=""
            class="max-w-full max-h-[88vh] rounded-md shadow-lg opacity-0 transition-opacity duration-200" />
        </div>
    </div>
    <script src="/public/assets/js/main.js" defer></script>
</body>
</html>