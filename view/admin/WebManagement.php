<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category - Admin</title>
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

        <!-- main content -->
        <main class="p-6">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold"> Web Management</h1>
                    <p class="text-gray-400">Kelola pengaturan dan informasi mengenai website anda</p>
                </div>  
            </div>
            <div class="flex flex-wrap mb-4">
                <nav class="bg-gray-100 rounded-lg p-1 font-semibold text-sm gap-4 flex">
                    <button 
                    class="text-gray-400 p-1 px-2 rounded-lg  focus:bg-white focus:text-black active:bg-white active:text-black">Umum</button>
                    <button class="text-gray-400 p-1 px-2 rounded-lg  focus:bg-white focus:text-black active:bg-white active:text-black">Kontak</button>
                    <button class="text-gray-400 p-1 px-2 rounded-lg  focus:bg-white focus:text-black active:bg-white active:text-black">Sosial Media</button>
                </nav>
            </div>

            <!-- container WEB MANAJEMEN UMUM Diskon -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4 px-8 justify-center">
                <div id="container-header" class="mb-2 justify-between">
                    <h2 class="text-2xl font-bold">General Settings</h2>
                    <p class="text-sm text-gray-400">Basic website information</p>
                </div>
                <div>
                    <form action="max-w-sm mx-auto">
                        <div class="mt-4">
                            <label for="" class="text-sm font-semibold">Nama Website</label>
                            <input type="text"
                            class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 ">
                        </div>
                        <div class="mt-4">
                            <label for="" class="text-sm font-semibold">Tag line Website</label>
                            <input type="text" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mt-4">
                            <label for="" class="text-sm font-semibold">Deskripsi Website</label>
                            <input type="text" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mt-4">
                                <label for="" class="text-sm font-semibold">Fav icon</label>
                                <input type="file" id="file-upload" name="file-upload" class="block w-[35%] text-sm file:mr-4 file:rounded-sm file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700 file:cursor-pointer border border-gray-300 rounded-lg"/>
                        </div>
                        <button class="mt-4 px-5 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 cursor-pointer">Save</button>

                    </form>
                </div>
            </div>

            <!-- container WEB MANAJEMEN KONTAK -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4 px-8 justify-center">
                <div id="container-header" class="mb-2 justify-between">
                    <h2 class="text-2xl font-bold">Contact Settings</h2>
                    <p class="text-sm text-gray-400">Your business contact details</p>
                </div>
                <div>
                    <form action="max-w-sm mx-auto">
                        <div class="mt-4">
                            <div class="flex item-center gap-2">
                                <span class="material-symbols-outlined">mail</span>
                                <label for="" class="text-sm font-semibold">Email</label>
                            </div>
                            <input type="text"
                            class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 ">
                        </div>
                        <div class="mt-4">
                            <div class="flex item-center gap-2">
                                <span class="material-symbols-outlined">Phone</span>
                                <label for="" class="text-sm font-semibold">Telepone</label>
                            </div>
                            <input type="text" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mt-4">
                            <div class="flex item-center gap-2">
                                <span class="material-symbols-outlined">Phone</span>
                                <label for="" class="text-sm font-semibold">WhatsApp</label>
                            </div>
                            <input type="text" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mt-4 mb-4">
                            <div class="flex item-center gap-2">
                                <span class="material-symbols-outlined">distance</span>
                                <label for="" class="text-sm font-semibold">Alamat</label>
                            </div>
                            <input type="text" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button class="p-2 px-4 bg-gray-800 text-white rounded-lg hover:bg-gray-700 cursor-pointer">Save</button>
                    </form>
                </div>
            </div>

            <!-- container WEB MANAJEMEN UMUM Diskon -->
            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4 px-8 justify-center">
                <div id="container-header" class="mb-2 justify-between">
                    <h2 class="text-2xl font-bold">Social Media Links</h2>
                    <p class="text-sm text-gray-400">Connect your social media accounts</p>
                </div>
                <div>
                    <form action="max-w-sm mx-auto">
                        <div class="mt-4">
                            <label for="" class="text-sm font-semibold">Facebook</label>
                            <input type="text"
                            class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 ">
                        </div>
                        <div class="mt-4">
                            <label for="" class="text-sm font-semibold">Instagram</label>
                            <input type="text" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mt-4">
                            <label for="" class="text-sm font-semibold">Tiktok</label>
                            <input type="text" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mt-4 mb-4">
                            <label for="" class="text-sm font-semibold">YouTube</label>
                            <input type="text" class="w-full p-1 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button class="p-2 px-4 bg-gray-800 text-white rounded-lg hover:bg-gray-700 cursor-pointer">Save</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>