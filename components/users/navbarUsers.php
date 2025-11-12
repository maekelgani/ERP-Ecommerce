<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar - Users</title>
    <link rel="stylesheet" href="/../../src/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body>
    <header class="bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-1">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo -->
            <div class="md:flex md:items-center md:gap-12">
                <a class="block rounded-lg" href="#">
                <span class="sr-only">Home</span>
                    <img src="/assets/img/favicon.png" alt="" width="40px" class="p-1">
                </a>
            </div>

            <!-- navbar (desktop) di sini -->
            <div class="hidden md:block">
                <nav aria-label="Global">
                <ul class="flex items-center gap-6 text-sm">
                    <li>
                    <a class="transition hover:text-red-800/75" href="#">
                        Product
                    </a>
                    </li>

                    <li>
                    <a class="transition hover:text-red-800/75" href="#">
                        About Us
                    </a>
                    </li>

                    <li>
                    <a class="transition hover:text-red-800/75" href="#">
                        Promo
                    </a>
                    </li>

                    <li>
                    <a class="transition hover:text-red-800/75" href="#">
                        Blog
                    </a>
                    </li>

                </ul>
                </nav>
            </div>

            <!-- search bar -->
            <div>
                <label for="Search" class="relative">
                <input type="text" id="Search" placeholder="" class="peer mt-0.5 rounded-lg border border-gray-200 shadow-sm sm:text-sm p-2 lg:w-[50vh] md:w-[25vh]">

                <span class="absolute inset-y-0 start-3 -translate-y-5 bg-white px-0.5 text-sm font-medium text-gray-700 transition-transform peer-placeholder-shown:translate-y-0 peer-focus:-translate-y-5">
                    Cari Produk
                </span>
                </label>
            </div>

            <!-- Action Btn -->
            <div class="flex items-center lg:space-x-4 relative space-x-2">
                <!-- Btn Cart (Keranjang) -->
                <button class="relative inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 text-sm font-medium leading-none text-gray-90">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span class="absolute -top-1 -right-1 bg-[#563232] text-white text-[10px] font-bold rounded-full p-1">2</span>
                </button>

                <!-- Btn Notifications -->
                <button class="relative inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 text-sm font-medium leading-none text-gray-90">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute -top-1 -right-1 bg-[#563232] text-white text-[10px] font-bold rounded-full p-1">2</span>
                </button>

                <!-- Btn Account -->
                <div class="relative">
                    <button id="userDropdownButton1"
                        class="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 text-sm font-medium leading-none text-gray-90">
                        <span class="material-symbols-outlined">account_circle</span>
                        <span class="material-symbols-outlined">keyboard_arrow_down</span>
                    </button>

                    <!-- Dropdown Account -->
                    <div id="userDropdown1"
                        class="hidden absolute right-0 mt-2 z-50 w-56 divide-y divide-gray-100 overflow-hidden rounded-lg bg-white antialiased shadow transition-all duration-200 ease-in-out">
                        <ul class="p-2 text-start text-sm font-medium text-gray-900">
                            <li><a href="#" class="block rounded-md px-3 py-2 hover:bg-gray-100">My Account</a></li>
                            <li><a href="#" class="block rounded-md px-3 py-2 hover:bg-gray-100">My Orders</a></li>
                            <li><a href="#" class="block rounded-md px-3 py-2 hover:bg-gray-100">Settings</a></li>
                        </ul>

                        <div class="p-2 text-sm font-medium text-gray-900">
                            <button class="w-full flex items-center justify-between rounded-md px-3 py-2 hover:bg-gray-100">
                                Logout
                                <span class="material-symbols-outlined">logout</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- humberger menu -->
            <div class="block md:hidden">
                <button class="toggle-menu rounded-sm bg-gray-200 p-2 text-gray-600 transition hover:text-gray-600/75">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <!-- menu di sini -->
                <div id="mobile-menu" class="hidden absolute top-16 left-0 w-full bg-gray-50 border-t border-gray-200 transform transition-all duration-300 ease-in-out md:static md:flex md:gap-6 md:w-auto md:border-none">
                    <a href="#" class="block px-4 py-2 font-semibold text-gray-700 hover:bg-gray-100 md:hover:bg-transparent">Beranda</a>
                    <a href="#" class="block px-4 py-2 font-semibold text-gray-700 hover:bg-gray-100 md:hover:bg-transparent">Produk</a>
                    <a href="#" class="block px-4 py-2 font-semibold text-gray-700 hover:bg-gray-100 md:hover:bg-transparent">Tentang</a>
                    <a href="#" class="block px-4 py-2 font-semibold text-gray-700 hover:bg-gray-100 md:hover:bg-transparent">Kontak</a>
                </div>
            </div>

        </div>
    </header>

    <script src="/assets/js/users/navbar.js"></script>
</body>
</html>