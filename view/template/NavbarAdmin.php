<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/src/output.css">
</head>
<body>
        <header class="bg-gray-300/30 p-2 flex items-center backdrop-blur-2xl justify-between">
            <div class="flex items-center gap-4 pl-5 ">
                <p class="font-base text-lg">Selamat Datang, <strong>{nama}</strong> di Admin Panel</p>
            </div>
            <!-- Bagian Navbar action Menu -->
            <!-- Notification ICON -->
            <nav class="relative flex items-center gap-4 mr-12">
                <button class="relative p-2 rounded-md hover:bg-gray-100 outline-0 hover:outline-2 hover:outline-blue-500
                focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="material-symbols-outlined">notifications_active</span>
                </button>
            <!-- Dropdown Notification -->
                <div id="notifications-dropdown" class="dropdown-content hidden"> <!--Masih gw buat Hidden aja-->
                    <div class="px-4 py-2 text-sm font-medium text-gray-900 border-b border-gray-200">Notifications</div>
                    <div class="py-1">
                        <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-medium">New order received</p>
                                <p class="text-xs text-gray-500">Order #12345 - 5 minutes ago</p>
                            </div>
                        </div>
                        <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-medium">Low stock alert</p>
                                <p class="text-xs text-gray-500">Product ABC - 15 minutes ago</p>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Settings ICON -->
                <button class="relative p-2 rounded-md hover:bg-gray-100 outline-0 hover:outline-2 hover:outline-blue-500
                focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="material-symbols-outlined">settings</span>
                </button>
                <!-- Dropdown Settings -->
                <div id="settings-dropdown" class="dropdown-content hidden"> <!--Masih gw buat Hidden aja-->
                    <div class="px-4 py-6 text-sm font-medium text-gray-900 border-b border-gray-200">Settings</div>
                    <div class="py-1">
                        <ul>
                            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                <a class="" href="/">Store Settings</a></li>
                            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                <a class="" href="/">Preferences</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Users Icons -->
                <button class="relative p-2 rounded-md hover:bg-gray-100 outline-0 hover:outline-2 hover:outline-blue-500
                focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="material-symbols-outlined">contacts_product</span>
                </button>
                <div id="settings-dropdown" class="dropdown-content hidden"> <!--Masih gw buat Hidden aja-->
                    <div class="px-4 py-6 text-sm font-medium text-gray-900 border-b border-gray-200">Settings</div>
                    <div class="py-1">
                        <ul>
                            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                <a class="" href="/">Profile</a></li>
                            <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                <a class="" href="/">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
</body>
</html>
        