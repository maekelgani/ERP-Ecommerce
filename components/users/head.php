<?php
// pada head.php ini, jika $pageTitle tidak didefinisikan nilai nya, maka gunakan default "Home" sebagai judul halaman
if (!isset($pageTitle)) {
    $pageTitle = "Home";
}
// Meta description per halaman
if (!isset($metaDescription)) {
    $metaDescription = "Nano Komputer - Toko komputer terpercaya dengan harga terjangkau. Jual PC Gaming, Laptop, Komponen Komputer, dan Aksesoris Gaming.";
}
// Jika homepage, tampilkan hanya nama toko
$title = ($pageTitle === "Home")
    ? "Nano Komputer - Toko Komputer Terpercaya"
    : $pageTitle . " - Nano Komputer";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" href="../../assets/img/logo-nano-transparant.png" type="image/x-icon">
    <link rel="stylesheet" href="../../src/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- jsPDF for PDF Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --color-surface: #ffffff;
            --color-surface-alt: #f9fafb;
            --color-on-surface: #4b5563;
            --color-on-surface-strong: #111827;
            --color-info: #0ea5e9;
            --color-on-info: #ffffff;
            --color-success: #22c55e;
            --color-on-success: #ffffff;
            --color-warning: #f59e0b;
            --color-on-warning: #ffffff;
            --color-danger: #ef4444;
            --color-on-danger: #ffffff;
            --color-outline: #d1d5db;
            --radius-radius: 0.5rem;
        }

        .wishlist-active {
            background-color: #fef2f2 !important;
        }

        .wishlist-active svg {
            fill: currentColor !important;
            color: #ef4444 !important;
        }

        @keyframes animate-pulse-scale {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        .animate-pulse-scale {
            animation: animate-pulse-scale 0.3s ease-in-out;
        }
    </style>

</head>