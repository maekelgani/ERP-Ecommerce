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
    <link rel="icon" href="../../assets/img/Nanocomp.png" type="image/x-icon">
    <link rel="stylesheet" href="/../../src/output.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>