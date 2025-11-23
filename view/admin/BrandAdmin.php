<?php
// Definisikan title untuk halaman ini
$pageTitle = "Brand";
// Include file head.php dari components/admin
include '../../components/admin/head.php';
?>

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

        <!-- main content -->
        <main class="flex-1 overflow-y-auto p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold"> Brand </h1>
                    <p class="text-gray-400">Kelola Semua Brand yang ada di ecommerce</p>
                </div>
                <button class="openBrand px-5 py-2 bg-gray-800 text-white rounded-lg cursor-pointer transition duration-200 hover:bg-gray-600">
                    Tambah Brand
                </button>
            </div>

            <!-- container TABEL CATEGORY -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-2 flex justify-between">
                    <h2 class="text-2xl font-bold">Brand List</h2>
                    <!-- buat filter -->
                    <div id="filter-field" class="mb-2 flex flex-wrap items-center gap-4 pb-3 pt-0"">
                        <!-- Filter cari produk -->
                        <input type=" text" placeholder="Cari Kategori..." class="border border-gray-300 rounded-lg px-3 w-xl py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    <button class="bg-gray-800 text-white px-6 py-2 rounded-lg">Cari</button>
                </div>
            </div>

            <!-- BAGIAN TABEL CATEGORY GEYS -->
            <div id="container-content" class="pt-0">
                <div class="relative w-full overflow-auto">
                    <!-- TABEL NYA DI SINI -->
                    <table class="w-full caption-bottom text-sm">
                        <thead class="border-b uppercase">
                            <tr class="border-b bg-gray-50 rounded-lg ">
                                <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Logo</th>
                                <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">ID Brand</th>
                                <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Nama Brand</th>
                                <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Deskripsi</th>
                                <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Website</th>
                                <th class="h-12 px-3 text-left align-middle font-bold text-muted-foreground text-gray-600">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr class="border-t">
                                <td class="p-3 text-xs text-gray-500"># CAT01</td> <!-- ID kategori -->
                                <td class="p-3">PC Bundling</td> <!-- NAMA kategori -->
                                <td class="p-3 text-gray-500">Kategori untuk pc jadi</td> <!-- Deskripsi kategori -->
                                <td class="p-3 pl-10">
                                    <div class="text-center border w-15 rounded-md bg-gray-50 border-gray-200 text-shadow-gray-950">14</div>
                                </td> <!-- jumlah produk di kategori -->
                                <td class="p-3 gap-3">
                                    <div>
                                        <button class="editBrand cursor-pointer text-blue-500 font-semibold">Edit</button> |
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


    <!-- Modal Form Brand -->
    <div id="form-brand" class="fixed inset-0 z-50 hidden flex items-center justify-center p-6">
        <div id="lb-backdrop" class="absolute inset-0 bg-black opacity-75 transition-opacity duration-300"></div>

        <div id="modal-card"
            class="relative z-10 max-w-lg w-full rounded-lg border border-gray-200 bg-white shadow-md p-4 px-8 justify-center
            opacity-0 scale-95 translate-y-4 transition-all duration-300 ease-out">

            <div class="mb-4 flex justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Tambah Brand</h2>
                    <p class="text-sm text-gray-400">Masukkan detail mengenai brand ini</p>
                </div>
                <button class="cancel cursor-pointer">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form>
                <div class="mb-4">
                    <label class="font-semibold text-sm">ID Brand</label>
                    <input type="text" disabled
                        class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-200 text-base">
                </div>

                <div class="mb-4">
                    <label class="font-semibold text-sm">Nama Brand</label>
                    <input type="text" required
                        class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base">
                </div>

                <div class="mb-4">
                    <label class="font-semibold text-sm">Web Resmi Brand</label>
                    <input type="text" required
                        class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base">
                </div>

                <div class="mb-4">
                    <label class="font-semibold text-sm">Deskripsi</label>
                    <textarea placeholder="Jelaskan mengenai brand ini..."
                        class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base"></textarea>
                </div>

                <div class="mt-4 mb-4"">
                    <label for="" class=" text-sm font-semibold">Brand Icon</label>
                    <input type="file" id="file-upload" name="file-upload" class="block w-full text-sm file:mr-4 file:rounded-sm file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700 file:cursor-pointer border border-gray-300 rounded-lg" />
                </div>

                <button type="submit"
                    class="p-2 px-4 bg-gray-800 text-white rounded-lg hover:bg-gray-700 cursor-pointer">
                    Tambah
                </button>
            </form>
        </div>
    </div>
    <script src="/assets/js/admin/brand.js"></script>
</body>

</html>