-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 22, 2025 at 04:12 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nanocomp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `address_book`
--

CREATE TABLE `address_book` (
  `id_alamat` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pengguna` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `label_alamat` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_penerima` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nomor_hp` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat_lengkap` text COLLATE utf8mb4_general_ci NOT NULL,
  `kelurahan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kecamatan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kota` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `provinsi` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `default_alamat` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE `admin_user` (
  `id_admin` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_admin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password_admin` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_admin` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'admin',
  `tanggal_dibuat` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id_brand` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_brand` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `desc_brand` text COLLATE utf8mb4_general_ci,
  `logo_brand` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id_cart` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pengguna` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_product` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `harga_satuan` decimal(12,2) DEFAULT NULL,
  `tanggal_ditambahkan` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `diskon`
--

CREATE TABLE `diskon` (
  `id_diskon` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_product` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_diskon` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipe_diskon` enum('persen','nominal') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'persen',
  `nilai_diskon` decimal(12,2) NOT NULL,
  `harga_setelah_diskon` decimal(12,2) DEFAULT NULL,
  `tanggal_mulai` datetime DEFAULT NULL,
  `tanggal_berakhir` datetime DEFAULT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi_kategori` text COLLATE utf8mb4_general_ci,
  `icon_kategori` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id_notifikasi` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pengguna` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_order` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipe_notifikasi` enum('order','payment','shipment','promo','system') COLLATE utf8mb4_general_ci DEFAULT 'system',
  `judul_pesan` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `isi_pesan` text COLLATE utf8mb4_general_ci NOT NULL,
  `status_baca` enum('dibaca','belum_dibaca') COLLATE utf8mb4_general_ci DEFAULT 'belum_dibaca',
  `tanggal_dikirim` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id_order` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pengguna` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_order` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_harga` decimal(12,2) NOT NULL,
  `total_diskon` decimal(12,2) DEFAULT '0.00',
  `total_ongkir` decimal(12,2) DEFAULT '0.00',
  `total_bayar` decimal(12,2) NOT NULL,
  `status_order` enum('pending','dikonfirmasi','diproses','dikirim','selesai','dibatalkan') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `catatan_order` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id_detail` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_order` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_product` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_product` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `diskon_satuan` decimal(12,2) DEFAULT '0.00',
  `harga_setelah_diskon` decimal(12,2) NOT NULL,
  `jumlah` int NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id_payment` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_order` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `metode_pembayaran` enum('transfer_bank','ewallet','cod','kartu_kredit') COLLATE utf8mb4_general_ci NOT NULL,
  `nama_bank` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nomor_rekening` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `atas_nama` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_pembayaran` enum('pending','verifikasi','berhasil','gagal') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `total_bayar` decimal(12,2) NOT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `catatan_pembayaran` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna_user`
--

CREATE TABLE `pengguna_user` (
  `id_pengguna` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_pengguna` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password_pengguna` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nomor_hp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_daftar` datetime DEFAULT CURRENT_TIMESTAMP,
  `status_akun` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id_product` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_product` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi_speksifikasi` text COLLATE utf8mb4_general_ci,
  `harga` decimal(12,2) NOT NULL,
  `stok` int DEFAULT '0',
  `id_kategori` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_brand` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `berat_gram` int DEFAULT '0',
  `status_produk` enum('tersedia','habis','nonaktif') COLLATE utf8mb4_general_ci DEFAULT 'tersedia',
  `tanggal_ditambahkan` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_product` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pengguna` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_order` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rating` int NOT NULL,
  `komentar` text COLLATE utf8mb4_general_ci,
  `foto_review` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_review` datetime DEFAULT CURRENT_TIMESTAMP,
  `status_review` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending'
) ;

-- --------------------------------------------------------

--
-- Table structure for table `shipment`
--

CREATE TABLE `shipment` (
  `id_shipment` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_order` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_alamat` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jasa_pengiriman` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `no_resi` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_penerima` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nomor_hp_penerima` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat_pengiriman` text COLLATE utf8mb4_general_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `ongkir` decimal(12,2) NOT NULL,
  `estimasi_hari` int DEFAULT NULL,
  `status_pengiriman` enum('pending','dikemas','dikirim','dalam_perjalanan','tiba','diterima') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `tanggal_dikirim` datetime DEFAULT NULL,
  `tanggal_diterima` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id_wishlist` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pengguna` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `id_product` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_ditambahkan` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address_book`
--
ALTER TABLE `address_book`
  ADD PRIMARY KEY (`id_alamat`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email_admin` (`email_admin`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id_brand`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id_cart`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_product` (`id_product`);

--
-- Indexes for table `diskon`
--
ALTER TABLE `diskon`
  ADD PRIMARY KEY (`id_diskon`),
  ADD KEY `id_product` (`id_product`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_order` (`id_order`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_product` (`id_product`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id_payment`),
  ADD UNIQUE KEY `unique_order_payment` (`id_order`),
  ADD KEY `id_order` (`id_order`);

--
-- Indexes for table `pengguna_user`
--
ALTER TABLE `pengguna_user`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_brand` (`id_brand`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_order` (`id_order`);

--
-- Indexes for table `shipment`
--
ALTER TABLE `shipment`
  ADD PRIMARY KEY (`id_shipment`),
  ADD UNIQUE KEY `unique_order_shipment` (`id_order`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_alamat` (`id_alamat`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD UNIQUE KEY `unique_wishlist` (`id_pengguna`,`id_product`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_product` (`id_product`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address_book`
--
ALTER TABLE `address_book`
  ADD CONSTRAINT `address_book_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna_user` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna_user` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `diskon`
--
ALTER TABLE `diskon`
  ADD CONSTRAINT `diskon_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna_user` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna_user` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`id_brand`) REFERENCES `brand` (`id_brand`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna_user` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_3` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `shipment`
--
ALTER TABLE `shipment`
  ADD CONSTRAINT `shipment_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shipment_ibfk_2` FOREIGN KEY (`id_alamat`) REFERENCES `address_book` (`id_alamat`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna_user` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
