    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Flex Layout</title>
    </head>
    <body class="h-screen flex flex-col">

    <!-- Navbar -->
    <div class="h-[60px] bg-blue-200 flex items-center justify-center border border-black">
        Navbar
    </div>

    <!-- Body -->
    <div class="flex flex-1">

        <!-- Sidebar -->
        <div class="w-[250px] bg-blue-300 flex items-center justify-center border border-black">
        Sidebar
        </div>

        <!-- Main -->
        <div class="flex-1 bg-blue-400 flex items-center justify-center border border-black">
        Main
        </div>

    </div>

    </body>
    </html>
