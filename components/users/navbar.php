<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar - Guest</title>
    <link rel="stylesheet" href="/src/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body>
<!-- Navbar Users -->
    <header class="bg-white border-b shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
        <!-- Logo -->
        <a href="#" class="text-xl font-bold text-[#563232]">BrandName</a>

        <!-- Menu Desktop -->
        <nav class="hidden md:flex items-center gap-6">
            <a href="#" class="text-gray-700 hover:text-[#563232]">Home</a>
            <a href="#" class="text-gray-700 hover:text-[#563232]">Products</a>
            <a href="#" class="text-gray-700 hover:text-[#563232]">About</a>
        </nav>

        <!-- Action Buttons -->
        <div class="hidden md:flex items-center gap-4">
            <!-- Cart -->
            <button class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6 text-gray-700 hover:text-[#563232]">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.09.836l.383 1.382m0 0L6.75 14.25h10.5l1.5-6H6.819m-1.5-3h13.431a.75.75 0 01.729.911l-1.5 6a.75.75 0 01-.729.589H6.75m0 0L5.25 4.5m1.5 9.75a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm10.5 0a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
            </svg>
            <span class="absolute -top-2 -right-2 bg-[#563232] text-white text-xs font-bold rounded-lg px-1.5">2</span>
            </button>

            <!-- Notification -->
            <button class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6 text-gray-700 hover:text-[#563232]">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a2 2 0 01-1.714 2.918h-2.286a2 2 0 01-1.714-2.918L9 13V8a5 5 0 1110 0v5l-.143 4.082z" />
            </svg>
            <span class="absolute -top-2 -right-2 bg-[#563232] text-white text-xs font-bold rounded-lg px-1.5">3</span>
            </button>

            <!-- Profile Dropdown -->
            <div class="relative">
            <button id="profileBtn" class="w-8 h-8 rounded-full overflow-hidden border border-gray-300">
                <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Profile" class="w-full h-full object-cover">
            </button>

            <!-- Dropdown Menu -->
            <div id="profileDropdown" class="absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-lg hidden">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
            </div>
        </div>

        <!-- Mobile Menu Button -->
        <button id="menuBtn" class="md:hidden flex items-center justify-center w-10 h-10 rounded-md hover:bg-gray-100">
            <svg id="menuIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden border-t bg-white shadow-inner">
        <nav class="flex flex-col p-4 space-y-3">
        <a href="#" class="text-gray-700 hover:text-[#563232]">Home</a>
        <a href="#" class="text-gray-700 hover:text-[#563232]">Products</a>
        <a href="#" class="text-gray-700 hover:text-[#563232]">About</a>
        <hr>
        <div class="flex items-center justify-between">
            <div class="flex gap-3">
            <!-- Icons -->
            <button>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6 text-gray-700 hover:text-[#563232]">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.09.836l.383 1.382m0 0L6.75 14.25h10.5l1.5-6H6.819m-1.5-3h13.431a.75.75 0 01.729.911l-1.5 6a.75.75 0 01-.729.589H6.75m0 0L5.25 4.5m1.5 9.75a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm10.5 0a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                </svg>
            </button>

            <button>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6 text-gray-700 hover:text-[#563232]">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a2 2 0 01-1.714 2.918h-2.286a2 2 0 01-1.714-2.918L9 13V8a5 5 0 1110 0v5l-.143 4.082z" />
                </svg>
            </button>
            </div>

            <!-- Profile -->
            <div class="relative">
            <button id="profileBtnMobile" class="w-8 h-8 rounded-full overflow-hidden border border-gray-300">
                <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Profile" class="w-full h-full object-cover">
            </button>

            <div id="profileDropdownMobile" class="absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-lg hidden">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
            </div>
            </div>
        </div>
        </nav>
    </div>
    </header>

    <script>
    // Toggle profile dropdown desktop
    const profileBtn = document.getElementById("profileBtn");
    const profileDropdown = document.getElementById("profileDropdown");

    profileBtn.addEventListener("click", () => {
        profileDropdown.classList.toggle("hidden");
    });

    // Toggle mobile menu
    const menuBtn = document.getElementById("menuBtn");
    const mobileMenu = document.getElementById("mobileMenu");

    menuBtn.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden");
    });

    // Toggle profile dropdown mobile
    const profileBtnMobile = document.getElementById("profileBtnMobile");
    const profileDropdownMobile = document.getElementById("profileDropdownMobile");

    profileBtnMobile.addEventListener("click", () => {
        profileDropdownMobile.classList.toggle("hidden");
    });

    // Tutup dropdown jika klik di luar
    document.addEventListener("click", (e) => {
        if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileDropdown.classList.add("hidden");
        }
        if (!profileBtnMobile.contains(e.target) && !profileDropdownMobile.contains(e.target)) {
        profileDropdownMobile.classList.add("hidden");
        }
    });
    </script>

</body>
</html>