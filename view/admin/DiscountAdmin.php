<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category - Admin</title>
    <link rel="stylesheet" href="/../../src/output.css">
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

        <!-- main content -->
        <main class="flex-1 overflow-y-auto p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold"> Discount </h1>
                    <p class="text-gray-400">Kelola Diskon</p>
                </div>
                <div>
                    <a class="px-5 py-2 bg-gray-800 text-white rounded-lg" href=""> Tambah Discount</a>
                </div>
            </div>

            <!-- container TABEL Diskon -->
            <div id="list-container" class="rounded-lg border border-gray-200 bg-white shadow-md p-4 justify-center">
                <div id="container-header" class="mb-6">
                    <h2 class="text-2xl font-bold">List Diskon</h2>
                    <p class="text-gray-400">Menampilkan list diskon yang tersedia</p>
                </div>
                <div class="grid grid-cols-3 gap-4 px-4">

                <!-- Dummy 2 -->
                    <div class="relative flex flex-col bg-white shadow-sm border border-slate-200 rounded-lg w-full">
                        <div class="px-4 mb-0 border-b bg-gray-800 border-slate-200 pt-3 pb-2 rounded-t-lg flex items-center justify-between ">
                            <div class="flex gap-4 items-center">
                                <span class="material-symbols-outlined text-white">percent_discount</span>
                                <div>
                                    <h2 class="text-xl text-white font-semibold">PayDay Voucer 50%</h2>
                                    <p class="text-sm text-yellow-200">NOVPAYDAY</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-medium ml-2 bg-green-500/10 text-green-500">Active</span>
                        </div>
                        
                        <div class="p-4">
                            <p class="text-2xl font-bold mb-3">Diskon <span>80%</span></p>
                            <p class="text-xs text-gray-400">Tersisa 123 dari 500</p>
                            <div class="w-full bg-slate-200/50 rounded-full h-2 mt-2">
                                <div class="w-[30%] bg-blue-400 h-2 rounded-full"></div>
                            </div>
                            <p class="text-xs text-gray-400">Berlaku sampai dengan: 25-11-2025</p>
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
</body>
</html>