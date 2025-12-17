-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 17, 2025 at 08:10 AM
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
  `id_alamat` int NOT NULL,
  `id_customer` int NOT NULL,
  `label_alamat` varchar(50) DEFAULT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `nomor_hp` varchar(20) NOT NULL,
  `alamat_lengkap` text NOT NULL,
  `kelurahan` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kota` varchar(100) NOT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `kode_pos` varchar(10) NOT NULL,
  `default_alamat` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `address_book`
--

INSERT INTO `address_book` (`id_alamat`, `id_customer`, `label_alamat`, `nama_penerima`, `nomor_hp`, `alamat_lengkap`, `kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `default_alamat`) VALUES
(1, 1, 'Rumah', 'Fauzan Eldianzah', '081234567890', 'Perumahan Bukit Waringin Blok H4 No. 10 RT 08/RW 014', 'CIMANGGIS', 'BOJONG GEDE', 'KABUPATEN BOGOR', 'JAWA BARAT', '16920', 1),
(2, 1, 'Kantor', 'Fauzan Eldianzah', '081234567890', 'PT Maju Jaya Sejahtera\nJl. Teknologi Raya No. 88, Gedung Inovasi Lantai 3', 'CIPINANG MUARA', 'JATINEGARA', 'KOTA JAKARTA TIMUR', 'DKI JAKARTA', '13420', 0),
(3, 2, 'Rumah', 'Akmal Dwi Saputra', '089576893421', 'Jalan Gg. Noble Blok J4 no.05 RT 13/RW 02', 'KARADENAN', 'CIBINONG', 'KABUPATEN BOGOR', 'JAWA BARAT', '16920', 1),
(4, 7, 'Rumah', 'Faizal Ardi', '081239230945', 'Perumahan Bukit Waringin Blok G6 No. 04 RT 04/RW 014', 'CIMANGGIS', 'BOJONG GEDE', 'KABUPATEN BOGOR', 'JAWA BARAT', '16920', 0),
(5, 7, 'Kantor', 'Faizal Ardi', '081290413082', 'PT Digital Solu Filantropi\nJl. Teknologi Raya No. 88, Gedung Inovasi Lantai 3', 'SENAYAN', 'KEBAYORAN BARU', 'KOTA JAKARTA SELATAN', 'DKI JAKARTA', '13420', 1);

-- --------------------------------------------------------

--
-- Table structure for table `administrators`
--

CREATE TABLE `administrators` (
  `id_admin` int NOT NULL,
  `id_role` int DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `remember_expires` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password_updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Administrator accounts with profile management';

--
-- Dumping data for table `administrators`
--

INSERT INTO `administrators` (`id_admin`, `id_role`, `nama_lengkap`, `email`, `password_hash`, `photo`, `remember_token`, `remember_expires`, `is_active`, `last_login`, `created_at`, `updated_at`, `username`, `phone`, `password_updated_at`) VALUES
(1, 1, 'Fajar Nano Komputer', 'fajarnanokomp@gmail.com', '$2y$10$astJh04o3V9LU2jtOsDKhuDoKu.JptPrj8KPZkRnCdnKY5.pgR6r6', NULL, '1123718b95e030ed178ab02f24c23458a6030c16070086af6467fb97975dd117', '2026-01-15 14:28:43', 1, '2025-12-14 21:36:05', '2025-11-25 08:38:19', '2025-12-16 21:28:43', 'nanocomp', '081234567890', '2025-12-01 01:42:22'),
(2, 2, 'Faizal Ardi', 'faizalardi@gmail.com', '$2y$10$g55WWArkauw.WR2wyA5.rOdS3KS/HGCxN3CbYzM6onKijcgcH19jC', NULL, NULL, NULL, 1, '2025-11-30 18:54:35', '2025-11-25 08:38:19', '2025-11-30 18:54:35', NULL, NULL, NULL),
(3, 2, 'Maekel Gani', 'maekelgani@gmail.com', '$2y$10$CF0hB8G7NMEemnjWpFeQpuhGzOaW4fzuZNvj5Moqwd5r2id6Wzve2', NULL, NULL, NULL, 1, '2025-11-25 16:51:00', '2025-11-25 08:38:19', '2025-11-29 12:11:41', NULL, NULL, NULL),
(4, 2, 'Isfahan Kaefal', 'isfahankaefal@gmail.com', '$2y$10$MxnS65hsh9C8iTxSZhLYWuSqyERCIukMTqHIczUq.EBj4x1bEW4Pu', NULL, NULL, NULL, 1, '2025-11-25 16:59:25', '2025-11-25 08:38:19', '2025-11-30 18:16:02', NULL, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id_permission` int NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  `permission_name` varchar(150) NOT NULL,
  `permission_description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_permissions`
--

INSERT INTO `admin_permissions` (`id_permission`, `permission_key`, `permission_name`, `permission_description`) VALUES
(1, 'view_dashboard', 'Lihat Dashboard', NULL),
(2, 'view_analytics', 'Lihat Analitik', NULL),
(3, 'manage_products', 'Kelola Produk', NULL),
(4, 'manage_categories', 'Kelola Kategori', NULL),
(5, 'manage_brands', 'Kelola Brand', NULL),
(6, 'view_orders', 'Lihat Pesanan', NULL),
(7, 'manage_orders', 'Kelola Pesanan', NULL),
(8, 'manage_campaigns', 'Kelola Campaign', NULL),
(9, 'manage_product_discounts', 'Kelola Diskon Produk', NULL),
(10, 'manage_vouchers', 'Kelola Voucher', NULL),
(11, 'view_promo_monitoring', 'Lihat Monitoring Promo', NULL),
(12, 'view_promo_reports', 'Lihat Laporan Promo', NULL),
(13, 'manage_return_products', 'Kelola Return Produk', NULL),
(14, 'manage_crm', 'Kelola CRM', NULL),
(15, 'manage_roles', 'Kelola Role', NULL),
(16, 'manage_permissions', 'Kelola Permission', NULL),
(17, 'assign_role_permissions', 'Atur Hak Akses Role', NULL),
(18, 'manage_admin_users', 'Kelola Pengguna Administrator', NULL),
(19, 'manage_web_management', 'Kelola Web Management', NULL),
(20, 'view_reports', 'Lihat Laporan', NULL),
(21, 'view_customers', 'Lihat Data Customer', NULL),
(22, 'manage_customers', 'Kelola Data Customer', NULL),
(23, 'view_customer_activity', 'Lihat Aktivitas Customer', NULL),
(24, 'view_customer_feedback', 'Lihat Feedback Customer', NULL),
(25, 'view_crm_dashboard', 'Lihat Dashboard CRM', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id_role` int NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `role_description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_roles`
--

INSERT INTO `admin_roles` (`id_role`, `role_name`, `role_description`) VALUES
(1, 'super_admin', 'Akses penuh ke seluruh sistem'),
(2, 'admin', 'Akses operasional utama tanpa kontrol sistem kritis');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id_category` int NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id_category`, `nama_kategori`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Hardware', 'hardware', 1, '2025-12-15 00:24:34', '2025-12-15 00:24:34'),
(2, 'Tips & Tutorial', 'tips-tutorial', 1, '2025-12-15 00:24:34', '2025-12-15 00:24:34'),
(3, 'Berita', 'berita', 1, '2025-12-15 00:24:34', '2025-12-15 00:24:34'),
(4, 'Review', 'review', 1, '2025-12-15 00:24:34', '2025-12-15 00:24:34'),
(5, 'Promo', 'promo', 1, '2025-12-15 00:24:34', '2025-12-15 00:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id_post` int NOT NULL,
  `id_admin` int NOT NULL,
  `id_category` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `excerpt` text,
  `konten` longtext NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `status` enum('draft','publish') DEFAULT 'draft',
  `views` int DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id_post`, `id_admin`, `id_category`, `judul`, `slug`, `excerpt`, `konten`, `thumbnail`, `status`, `views`, `published_at`, `created_at`, `updated_at`) VALUES
(4, 1, 1, 'Perbedaan GPU RTX dan GTX untuk Gaming', 'perbedaan-gpu-rtx-dan-gtx-untuk-gaming', 'Mengenal Perbedaan GPU RTX dan GTX untuk Kebutuhan Gaming Modern: Dalam dunia gaming modern, GPU (Graphics Processing Unit) menjadi komponen kunci yang sangat menentukan kualitas visual dan performa permainan. NVIDIA sebagai salah satu produsen GPU terbesar di dunia memiliki dua lini populer yang sering dibandingkan oleh gamer, yaitu GTX dan RTX. Artikel ini akan membahas secara lengkap perbedaan GPU RTX dan GTX, serta membantu Anda menentukan pilihan terbaik sesuai kebutuhan gaming saat ini.', 'Apa Itu GPU NVIDIA GTX?\n\nSeri NVIDIA GeForce GTX merupakan lini GPU yang telah hadir lebih dulu sebelum RTX. GPU GTX berfokus pada rasterization tradisional, yaitu teknik rendering grafis konvensional yang digunakan oleh sebagian besar game sebelum era ray tracing.', 'blog_137d5c46139947c3_1765871172.jpg', 'publish', 3, '2025-12-16 07:46:15', '2025-12-16 14:46:15', '2025-12-17 12:48:09'),
(5, 1, 2, 'Cara Merakit PC Gaming untuk Pemula 2025', 'cara-merakit-pc-gaming-untuk-pemula-2025', 'Merakit PC gaming sendiri di tahun 2025 menjadi pilihan populer bagi banyak gamer, terutama pemula yang ingin mendapatkan performa maksimal sesuai budget. Selain lebih hemat, merakit PC juga memberi fleksibilitas dalam memilih komponen dan memudahkan upgrade di masa depan. Artikel ini akan membahas panduan lengkap dan mudah dipahami tentang cara merakit PC gaming untuk pemula di tahun 2025.', 'Mengapa Merakit PC Gaming Sendiri?\n\nSebelum masuk ke tahap perakitan, penting untuk mengetahui keuntungannya:\n- Lebih hemat biaya dibanding PC rakitan pabrikan\n-  Bebas memilih spesifikasi sesuai kebutuhan\n- Mudah di-upgrade ke depannya\n- Menambah pengetahuan hardware komputer', 'blog_6e94a3fcd73a81db_1765871514.jpg', 'publish', 6, '2025-12-16 07:52:22', '2025-12-16 14:52:22', '2025-12-17 15:01:13'),
(6, 1, 3, 'NVIDIA GeForce RTX 50 Series Resmi Diumumkan', 'nvidia-geforce-rtx-50-series-resmi-diumumkan', 'NVIDIA kembali menggebrak dunia teknologi dengan resmi mengumumkan NVIDIA GeForce RTX 50 Series, generasi terbaru GPU yang dirancang untuk menghadirkan lompatan besar dalam performa gaming, grafis realistis, dan pemrosesan berbasis AI. Kehadiran seri RTX 50 menjadi tonggak penting bagi gamer, content creator, dan profesional yang membutuhkan performa grafis kelas atas di era modern.', 'Sekilas Tentang NVIDIA GeForce RTX 50 Series: GeForce RTX 50 Series merupakan penerus langsung dari RTX 40 Series dan dibangun untuk menjawab kebutuhan game generasi terbaru, resolusi tinggi, serta teknologi AI yang semakin kompleks. NVIDIA memposisikan seri ini sebagai GPU next-generation yang tidak hanya fokus pada gaming, tetapi juga produktivitas dan komputasi AI.', 'blog_130010319e1b73cc_1765909043.jpg', 'publish', 4, '2025-12-17 01:15:57', '2025-12-17 01:15:57', '2025-12-17 15:02:05'),
(7, 1, 4, 'Review Montech XR: Casing Budget Rasa Premium', 'review-montech-xr-casing-budget-rasa-premium', 'Pasar casing PC semakin kompetitif, terutama di segmen budget hingga mid-range. Salah satu produk yang belakangan menarik perhatian adalah Montech XR, sebuah casing PC yang menawarkan desain modern, airflow optimal, dan fitur yang biasanya ditemui di kelas harga lebih tinggi. Dalam artikel ini, kita akan membahas review lengkap Montech XR, mulai dari desain, build quality, airflow, hingga apakah casing ini layak disebut budget rasa premium.', 'Sekilas Tentang Montech XR\n\nMontech dikenal sebagai brand yang fokus menghadirkan produk PC dengan value tinggi. Montech XR diposisikan sebagai casing entry–mid level yang menargetkan gamer dan PC builder pemula hingga menengah yang menginginkan tampilan elegan tanpa harus mengeluarkan biaya besar.\n\n- Segmentasi pengguna Montech XR:\n- PC gaming entry hingga mid-range\n- Builder pemula\n- Pengguna yang mengutamakan airflow dan estetika minimalis', 'blog_af0d0175f86f2b77_1765909168.jpg', 'publish', 3, '2025-12-17 01:19:56', '2025-12-17 01:19:56', '2025-12-17 15:02:07');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id_brand` varchar(16) NOT NULL,
  `nama_brand` varchar(100) NOT NULL,
  `desc_brand` text,
  `logo_brand` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id_brand`, `nama_brand`, `desc_brand`, `logo_brand`, `website`) VALUES
('BRD001', 'AMD', 'AMD adalah perusahaan teknologi yang memproduksi prosesor, kartu grafis, dan solusi komputasi lain-lainnya.', 'brand_91f946a1f7ae1eef_1764680650.png', 'https://www.amd.com'),
('BRD002', 'Skyworth', 'Skyworth adalah merek elektronik internasional dari Tiongkok. Mereka membuat berbagai produk seperti monitor, televisi, layar/display, perangkat elektronik konsumen, dan perangkat rumah tangga pintar', 'brand_6335351868515127_1764332885.png', 'https://www.skyworth.com/'),
('BRD003', 'Corsair', 'Corsair menawarkan rangkaian produk yang luas, termasuk memori RAM, SSD, casing PC, power supply (PSU), sistem cooling, peripheral gaming, hingga kursi dan aksesoris streaming. Setiap produk dirancang dengan presisi dan inovasi untuk menghadirkan pengalaman komputasi yang stabil, responsif, dan maksimal.', 'brand_03fa1bd9afc31cc1_1764687046.png', 'https://www.corsair.com/us/en'),
('BRD004', 'NVIDIA', 'NVIDIA adalah brand teknologi terkemuka yang dikenal dengan jajaran GPU berperforma tinggi untuk gaming, desain grafis, hingga komputasi AI. Dengan inovasi seperti arsitektur RTX, ray tracing, dan DLSS, NVIDIA menghadirkan kualitas visual yang lebih realistis dan efisiensi kinerja terbaik. Produk NVIDIA menjadi pilihan utama bagi gamer, kreator, dan profesional yang membutuhkan performa grafis maksimal.', 'brand_d25099cc9188ab8f_1764686296.png', 'https://www.nvidia.com/en-us/'),
('BRD005', 'ASUS', 'ASUS adalah perusahaan teknologi asal Taiwan yang bergerak di bidang hardware komputer dan elektronik konsumen. ASUS membuat berbagai produk seperti motherboard, laptop, desktop, monitor, kartu grafis, router, dan perangkat jaringan. ASUS dikenal luas sebagai produsen motherboard terkemuka di dunia dan sebagai brand besar di segmen gaming dan PC konsumen', 'brand_0fd8540190578271_1764680613.webp', 'https://rog.asus.com/id/'),
('BRD006', 'ASUS ROG', 'ROG adalah merek gaming dari ASUS yang fokus pada perangkat keras berkinerja tinggi untuk gamer dan enthusiast. Merek ini menawarkan produk seperti laptop gaming, motherboard, kartu grafis, monitor, router, dan aksesori gaming lainnya. ROG mengedepankan performa tinggi, inovasi, pendinginan optimal, dan fitur overclocking. ROG sangat dikenal di komunitas gamer global dan jadi pilihan banyak orang yang ingin PC atau laptop powerful.', 'brand_3eebfe97eda479ab_1764686912.png', 'https://rog.asus.com/id/'),
('BRD007', 'MSI', '𝗦𝗽𝗲𝗰𝗶𝗳𝗶𝗰𝗮𝘁𝗶𝗼𝗻𝘀 :\r\n\r\nGPU Engine Specs:\r\nCUDA Cores : 3584\r\nBoost Clock (MHz) : 1807\r\n\r\nMemory Specs:\r\nMemory Clock : 15Gbps\r\nStandard Memory Config : 12GB\r\nMemory Interface : GDDR6\r\nMemory Interface Width : 192-bit\r\nMemory Bandwidth (GB/sec) : 360\r\n\r\nDisplay Support:\r\nMulti Monitor : Yes\r\nMaxmium Digital Resolution : 7680x4320\r\nHDCP : 2.3\r\nStandard Display Connectors : 1x HDMI 2.1, 3x DisplayPort 1.4a\r\nInternalAudio Input for HDMI : Internal\r\n\r\nStandard Graphics Card Dimensions:\r\nLength : 235mm\r\nHeight : 124mm\r\nWidth : 42mm\r\n\r\nThermal and Power Spec:\r\nMinimum System Power Requirement (W) : 550\r\nSupplementary Power Connectors : 8-pin x1\r\nBeta\r\n0 / 0\r\nused queries\r\n1', 'brand_9a9abeed57d2e437_1764680670.webp', 'https://id.msi.com/index.php'),
('BRD008', 'MONTECH', 'Montech adalah merek hardware PC asal Taiwan. Montech fokus pada produk untuk gamer dan perakit PC seperti casing, power supply, pendingin, keyboard mekanik, pendingin udara/lembab, dan aksesori PC lainnya. Montech berdiri sejak 2016 dengan misi memberi produk dengan desain inovatif, kualitas baik, harga terjangkau, mudah digunakan, dan aman. Montech kini menjual produknya ke banyak negara di seluruh dunia.', 'brand_e48db7e51c7ee413_1764552928.png', 'https://www.montechpc.com/'),
('BRD009', 'KingBank', 'KingBank adalah merek dari perusahaan Shenzhen KingBank Technology Co., Ltd.. Brand ini menghasilkan modul memori RAM dan SSD untuk komputer pribadi. Mereka fokus pada performa tinggi, kompatibilitas luas (Intel maupun AMD), dan harga relatif terjangkau. Produk KingBank sering dipilih oleh pengguna PC dan gamer yang ingin upgrade RAM atau storage dengan budget moderat.', 'brand_cf9b5bc6f22d7db3_1764686270.png', 'https://www.kingbank.com/en/'),
('BRD010', 'ASRock', 'ASRock adalah produsen perangkat hardware komputer terkemuka yang dikenal dengan inovasi, kualitas, dan performa tinggi. Berfokus pada motherboard, mini PC, dan industrial PC, ASRock menghadirkan produk dengan desain efisien, fitur lengkap, serta stabilitas yang andal untuk pengguna harian hingga enthusiast.\r\nMengusung motto “Creativity, Consideration, Cost-effectiveness”, ASRock terus menghadirkan teknologi terbaru dengan harga yang tetap kompetitif, menjadikannya pilihan favorit para gamer, builder PC, dan profesional di seluruh dunia.', 'brand_a69ca67eccff084e_1764686492.webp', 'https://www.asrock.com/index.us.asp'),
('BRD011', 'GIGABYTE', 'GIGABYTE adalah salah satu brand teknologi terkemuka di dunia yang dikenal dengan produk berkualitas tinggi untuk kebutuhan gaming, profesional, dan komputasi harian. Fokus utama GIGABYTE mencakup motherboard, kartu grafis, laptop gaming AORUS, peripheral, dan perangkat komputer lainnya yang dirancang dengan performa kuat, fitur inovatif, serta ketahanan yang teruji.\r\nDengan pengalaman puluhan tahun dalam industri hardware, GIGABYTE selalu menghadirkan solusi teknologi yang andal, stabil, dan siap mendukung pengguna dalam pekerjaan berat, gaming kompetitif, maupun penggunaan sehari-hari.', 'brand_faf6b5f38ba55be5_1764686539.webp', 'https://www.gigabyte.com/id'),
('BRD012', 'Intel', 'Intel adalah perusahaan teknologi global yang dikenal sebagai produsen prosesor terkemuka untuk komputer desktop, laptop, dan server. Dengan inovasi berkelanjutan pada arsitektur CPU, efisiensi daya, dan performa komputasi, Intel menjadi pilihan utama untuk kebutuhan kerja, gaming, dan produktivitas. Selain prosesor, Intel juga menghadirkan beragam teknologi pendukung seperti grafis terintegrasi, chipset, dan solusi jaringan berkualitas tinggi.', 'brand_45f411a9fb5582ea_1764686979.webp', 'https://www.intel.co.id/content/www/id/id/homepage.html'),
('BRD013', 'Lian li', 'Lian Li adalah brand premium yang dikenal sebagai produsen casing PC dan aksesori berkualitas tinggi dengan desain elegan serta material kokoh berbahan aluminium. Produk Lian Li mengutamakan estetika, airflow optimal, dan kemudahan perakitan, menjadikannya pilihan favorit para perakit PC, gamer, dan enthusiast yang menginginkan build rapi dan profesional.', 'brand_6cfdae5c710da1e7_1764687723.png', 'https://lian-li.com/');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id_cart` varchar(10) NOT NULL,
  `id_customer` int NOT NULL,
  `id_product` varchar(10) NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `harga_satuan` decimal(12,2) DEFAULT NULL,
  `tanggal_ditambahkan` datetime DEFAULT CURRENT_TIMESTAMP,
  `tgl_diubah` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id_cart`, `id_customer`, `id_product`, `jumlah`, `harga_satuan`, `tanggal_ditambahkan`, `tgl_diubah`) VALUES
('CRT0000005', 7, 'PRD005', 1, 57429000.00, '2025-12-08 13:42:36', '2025-12-14 14:04:57'),
('CRT0000008', 7, 'PRD012', 1, 89349000.00, '2025-12-14 14:06:18', '2025-12-14 14:06:18'),
('CRT0000010', 1, 'PRD003', 1, 1299000.00, '2025-12-16 22:08:57', '2025-12-17 12:33:44'),
('CRT0000011', 1, 'PRD012', 1, 89349000.00, '2025-12-17 11:32:28', '2025-12-17 11:32:28'),
('CRT0000012', 1, 'PRD010', 1, 3888900.00, '2025-12-17 12:02:13', '2025-12-17 12:02:13');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id_customer` int NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `google_email` varchar(100) DEFAULT NULL,
  `google_name` varchar(100) DEFAULT NULL,
  `login_type` enum('regular','google') DEFAULT 'regular',
  `remember_token` varchar(255) DEFAULT NULL,
  `remember_expires` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `email_verified` tinyint(1) DEFAULT '0',
  `email_verified_at` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id_customer`, `nama_lengkap`, `email`, `no_telp`, `password_hash`, `google_id`, `google_email`, `google_name`, `login_type`, `remember_token`, `remember_expires`, `is_active`, `email_verified`, `email_verified_at`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'Fauzan Eldianzah', 'fauzan.customer@gmail.com', '081234567810', '$2y$10$QArwIUUQKH9XeT/2YAszdONm1kkz75Dwaj0udRsB1y7tNwpSU0HIm', NULL, NULL, NULL, 'regular', NULL, NULL, 1, 0, NULL, 'customer_1_1765615083_906cac87.jpg', '2025-11-28 18:18:48', '2025-12-13 15:38:03'),
(2, 'Akmal Dwi Saputra', 'akmal.customer@gmail.com', '089577658894', '$2y$10$14VOQUECTKXo7NE6DeVn5Od5.ho5aJir2iRxVm3Kzl16FG9KupSK6', NULL, NULL, NULL, 'regular', NULL, NULL, 1, 0, NULL, NULL, '2025-11-30 16:27:34', '2025-11-30 16:27:34'),
(3, 'Jason Susanto', 'susanto.customer@gmail.com', '081246379958', '$2y$10$E0Z/97QeW3WFUwFuexZ8D.W6O09hmW7EB/gCV9A5JP.I1TZ6yuZS.', NULL, NULL, NULL, 'regular', NULL, NULL, 0, 0, NULL, NULL, '2025-11-30 16:41:05', '2025-11-30 16:41:57'),
(7, 'Muhamad Faizal Ardiansyah', 'faizalardi2016@gmail.com', '81290413082', '$2y$10$5VwN0M9pZCy/a0NLowhinOyq/rwaLC/GOwp00RE6nNoBbvh2ARQlu', '109385904418557831706', 'faizalardi2016@gmail.com', 'Muhamad Faizal Ardiansyah', 'google', NULL, NULL, 1, 1, '2025-12-04 19:07:59', 'google_profile_6931799f1b454_1764850079.jpg', '2025-12-04 19:07:59', '2025-12-14 21:17:54'),
(8, '0728_Muhamad Faizal Ardiansyah', 'mhmdfaizalardi@gmail.com', NULL, NULL, '114145390091505445484', 'mhmdfaizalardi@gmail.com', '0728_Muhamad Faizal Ardiansyah', 'google', NULL, NULL, 1, 1, '2025-12-10 13:37:29', 'google_profile_69391529751f4_1765348649.jpg', '2025-12-10 13:37:29', '2025-12-14 21:15:35'),
(9, 'Faizal Ardi', 'mhfaizalardillia22@gmail.com', '081291203984', '$2y$10$m8micOQf4IefkSng2uFtYeC84XnBiKQsUTYRz3pjWuLyj5JBiHd6y', NULL, NULL, NULL, 'regular', NULL, NULL, 1, 0, NULL, NULL, '2025-12-17 13:50:21', '2025-12-17 13:50:21');

-- --------------------------------------------------------

--
-- Table structure for table `diskon`
--

CREATE TABLE `diskon` (
  `id_diskon` varchar(10) NOT NULL,
  `id_product` varchar(10) DEFAULT NULL,
  `nama_diskon` varchar(100) DEFAULT NULL,
  `tipe_diskon` enum('persen','nominal') DEFAULT 'persen',
  `nilai_diskon` decimal(12,2) NOT NULL,
  `harga_setelah_diskon` decimal(12,2) DEFAULT NULL,
  `tanggal_mulai` datetime DEFAULT NULL,
  `tanggal_berakhir` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kampanye_produk`
--

CREATE TABLE `kampanye_produk` (
  `id` int NOT NULL,
  `id_kampanye` varchar(16) NOT NULL,
  `id_produk` varchar(10) NOT NULL,
  `id_diskon` varchar(16) DEFAULT NULL,
  `prioritas` int DEFAULT '0',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kampanye_produk`
--

INSERT INTO `kampanye_produk` (`id`, `id_kampanye`, `id_produk`, `id_diskon`, `prioritas`, `dibuat_pada`) VALUES
(8, 'CMP0000000002', 'PRD003', NULL, 1, '2025-12-17 09:33:46');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` varchar(10) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi_kategori` text,
  `icon_kategori` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi_kategori`, `icon_kategori`) VALUES
('KTG001', 'Casing', 'Casing PC adalah komponen tempat semua perangkat komputer dipasang. Casing menjaga hardware tetap rapi, terlindung dari debu, dan memudahkan aliran udara untuk pendinginan.', 'cat_icon_692adf2aa6ac4.png'),
('KTG002', 'Monitor', 'Monitor adalah perangkat output yang menampilkan visual dari komputer, termasuk teks, gambar, dan video.', 'cat_icon_692adf32abcb0.png'),
('KTG003', 'Processor', 'Processor adalah otak dari komputer yang menangani semua perhitungan dan instruksi.', 'cat_icon_692adf948d85b.jpg'),
('KTG004', 'PSU', 'Power Supply Unit (PSU) adalah komponen penting dalam sistem komputer yang berfungsi mengubah arus listrik dari sumber eksternal menjadi daya yang stabil untuk seluruh perangkat di dalam PC. PSU memastikan komponen seperti motherboard, prosesor, kartu grafis, dan perangkat penyimpanan mendapatkan suplai listrik yang aman dan konsisten.', 'cat_icon_692ef4341c14f.png'),
('KTG005', 'Graphic Card', 'Graphic Card (GPU/VGA) adalah komponen yang memproses tampilan visual pada komputer, penting untuk gaming, desain, dan pekerjaan grafis berat. Di kategori ini tersedia berbagai pilihan GPU dari entry-level hingga high-end dari brand ternama seperti NVIDIA dan AMD, lengkap dengan spesifikasi untuk kebutuhan rakit PC Anda.', 'cat_icon_692ef56239c1c.png'),
('KTG006', 'RAM', 'RAM adalah komponen memori utama pada komputer yang menyimpan data sementara saat komputer berjalan. RAM menentukan kemampuan multitasking dan kecepatan akses aplikasi. Semakin besar kapasitas RAM, semakin lancar komputer menjalankan banyak program sekaligus. RAM tersedia dalam berbagai tipe, kecepatan, dan ukuran untuk mendukung motherboard dan kebutuhan pengguna.', 'cat_icon_692c722d174e7.webp'),
('KTG007', 'Motherboard', 'Motherboard adalah papan utama komputer yang menghubungkan prosesor, RAM, storage, dan komponen lainnya agar bekerja dengan optimal. Di kategori ini tersedia berbagai motherboard untuk Intel dan AMD dengan beragam ukuran serta fitur modern sesuai kebutuhan Anda.', 'cat_icon_692c70d3729d6.jpg'),
('KTG008', 'Storage', 'Storage adalah media penyimpanan data pada komputer, mulai dari SSD, HDD, hingga NVMe berkecepatan tinggi. Di kategori ini tersedia berbagai pilihan storage dengan kapasitas dan performa berbeda untuk kebutuhan harian, gaming, maupun profesional.', 'cat_icon_692ef3bc784bd.png'),
('KTG009', 'Fan Cases', 'Fan Case adalah kipas tambahan untuk casing komputer yang berfungsi menjaga suhu tetap stabil dan memastikan aliran udara optimal. Di kategori ini tersedia berbagai fan dengan ukuran, kecepatan, dan pencahayaan RGB yang dapat disesuaikan untuk performa dan tampilan PC yang lebih maksimal.', 'cat_icon_692ef23cd9372.jpg'),
('KTG010', 'Cooler', 'CPU Cooler adalah perangkat pendingin prosesor yang berfungsi menjaga suhu CPU tetap stabil agar performa tetap optimal. Di kategori ini tersedia berbagai pilihan pendingin, mulai dari air cooler hingga liquid cooler, dengan beragam ukuran dan fitur untuk kebutuhan harian, gaming, maupun profesional.', 'cat_icon_692c758e8a8b9.jpg'),
('KTG011', 'Thermal Paste', 'Thermal paste adalah bahan penghantar panas khusus yang digunakan untuk mengoptimalkan transfer panas antara prosesor (CPU), kartu grafis (GPU), atau komponen elektronik lainnya dengan sistem pendinginnya. Dengan mengisi celah mikro pada permukaan logam, thermal paste membantu menjaga suhu komponen tetap stabil sehingga performa tetap maksimal, lebih dingin, dan lebih tahan lama.', 'cat_icon_692ed73fc2ac5.png'),
('KTG012', 'PC Ready', 'PC Ready adalah komputer rakitan yang sudah dirakit, dites, dan dioptimalkan sehingga langsung siap digunakan tanpa perlu konfigurasi tambahan. Setiap komponen dipilih untuk memberikan performa yang stabil baik untuk gaming, editing, desain, pekerjaan kantor, hingga multitasking harian. Setiap unit telah melalui proses pengecekan hardware, pemasangan sistem operasi (opsional), serta pengujian suhu dan performa untuk memastikan kualitas terbaik. Dengan PC Ready, Anda cukup buka kotak, nyalakan, dan langsung pakai.', 'cat_icon_692ed7f56d0bb.png'),
('KTG013', 'Networking', 'Networking adalah proses menghubungkan berbagai perangkat—seperti komputer, server, router, switch, dan perangkat IoT ke dalam satu jaringan agar dapat saling berkomunikasi dan bertukar data. Dengan sistem networking yang baik, transfer informasi menjadi lebih cepat, aman, dan efisien, baik untuk kebutuhan rumah, kantor, bisnis, maupun data center.', 'cat_icon_692ef0831545d.png'),
('KTG014', 'Gaming Gear', 'Gaming Gear adalah perlengkapan khusus yang dirancang untuk meningkatkan pengalaman bermain game dengan kenyamanan, presisi, dan performa maksimal. Mulai dari keyboard mekanikal, mouse gaming berpresisi tinggi, headset dengan suara jernih, hingga mousepad, kursi gaming, dan aksesoris pendukung lainnya setiap perangkat dibuat untuk memberikan respon cepat, kontrol lebih akurat, serta kenyamanan saat bermain dalam durasi panjang.', 'cat_icon_692ef776cefe7.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `log_blog`
--

CREATE TABLE `log_blog` (
  `id` bigint NOT NULL,
  `aksi` varchar(50) DEFAULT NULL,
  `id_post` int DEFAULT NULL,
  `id_admin` int DEFAULT NULL,
  `pesan` text,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_promo`
--

CREATE TABLE `log_promo` (
  `id` bigint NOT NULL,
  `level` varchar(20) DEFAULT 'info',
  `aksi` varchar(50) DEFAULT NULL,
  `entitas` varchar(50) DEFAULT NULL,
  `id_entitas` varchar(32) DEFAULT NULL,
  `id_admin` int DEFAULT NULL,
  `pesan` text,
  `meta` json DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id_notifikasi` varchar(36) NOT NULL,
  `id_customer` int NOT NULL,
  `id_order` varchar(32) DEFAULT NULL,
  `tipe_notifikasi` enum('order','payment','shipment','promo','system') DEFAULT 'system',
  `judul_pesan` varchar(150) NOT NULL,
  `isi_pesan` text NOT NULL,
  `status_baca` enum('dibaca','belum_dibaca') DEFAULT 'belum_dibaca',
  `tanggal_dikirim` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id_order` varchar(32) NOT NULL,
  `id_customer` int NOT NULL,
  `tanggal_order` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_harga` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_diskon` decimal(12,2) DEFAULT '0.00',
  `total_ongkir` decimal(12,2) DEFAULT '0.00',
  `total_bayar` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status_order` enum('pending','dikonfirmasi','diproses','dikirim','selesai','dibatalkan') DEFAULT 'pending',
  `catatan_order` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `trg_kembalikan_stok_order_batal` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    IF NEW.status_order = 'dibatalkan' AND OLD.status_order <> 'dibatalkan' THEN
        UPDATE products p
        JOIN order_detail od ON od.id_product = p.id_product
        SET p.stok = p.stok + od.jumlah
        WHERE od.id_order = NEW.id_order;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_notifikasi_status_order` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    IF NEW.status_order <> OLD.status_order THEN
        INSERT INTO notification (
            id_notifikasi,
            id_customer,
            id_order,
            tipe_notifikasi,
            judul_pesan,
            isi_pesan,
            status_baca,
            tanggal_dikirim
        ) VALUES (
            UUID(),
            NEW.id_customer,
            NEW.id_order,
            'order',
            CONCAT('Status pesanan berubah: ', NEW.status_order),
            CONCAT('Pesanan #', NEW.id_order, ' kini berstatus: ', NEW.status_order),
            'belum_dibaca',
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id_detail` varchar(32) NOT NULL,
  `id_order` varchar(32) NOT NULL,
  `id_product` varchar(10) NOT NULL,
  `nama_product` varchar(150) NOT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `diskon_satuan` decimal(12,2) DEFAULT '0.00',
  `harga_setelah_diskon` decimal(12,2) NOT NULL,
  `jumlah` int NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `order_detail`
--
DELIMITER $$
CREATE TRIGGER `trg_kurangi_stok_order_detail` AFTER INSERT ON `order_detail` FOR EACH ROW BEGIN
    UPDATE products
    SET stok = stok - NEW.jumlah
    WHERE id_product = NEW.id_product;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_order_total_after_delete` AFTER DELETE ON `order_detail` FOR EACH ROW BEGIN
    UPDATE orders o
    LEFT JOIN (
        SELECT id_order,
               COALESCE(SUM(subtotal),0) AS sum_subtotal,
               COALESCE(SUM(diskon_satuan * jumlah),0) AS sum_diskon
        FROM order_detail
        WHERE id_order = OLD.id_order
        GROUP BY id_order
    ) od_sum ON od_sum.id_order = o.id_order
    SET o.total_harga = COALESCE(od_sum.sum_subtotal, 0),
        o.total_diskon = COALESCE(od_sum.sum_diskon, 0),
        o.total_bayar = COALESCE(od_sum.sum_subtotal, 0) + o.total_ongkir
    WHERE o.id_order = OLD.id_order;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_order_total_after_insert` AFTER INSERT ON `order_detail` FOR EACH ROW BEGIN
    UPDATE orders o
    LEFT JOIN (
        SELECT id_order,
               COALESCE(SUM(subtotal),0) AS sum_subtotal,
               COALESCE(SUM(diskon_satuan * jumlah),0) AS sum_diskon
        FROM order_detail
        WHERE id_order = NEW.id_order
        GROUP BY id_order
    ) od_sum ON od_sum.id_order = o.id_order
    SET o.total_harga = COALESCE(od_sum.sum_subtotal, 0),
        o.total_diskon = COALESCE(od_sum.sum_diskon, 0),
        o.total_bayar = COALESCE(od_sum.sum_subtotal, 0) + o.total_ongkir
    WHERE o.id_order = NEW.id_order;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_update_order_total_after_update` AFTER UPDATE ON `order_detail` FOR EACH ROW BEGIN
    UPDATE orders o
    LEFT JOIN (
        SELECT id_order,
               COALESCE(SUM(subtotal),0) AS sum_subtotal,
               COALESCE(SUM(diskon_satuan * jumlah),0) AS sum_diskon
        FROM order_detail
        WHERE id_order = NEW.id_order
        GROUP BY id_order
    ) od_sum ON od_sum.id_order = o.id_order
    SET o.total_harga = COALESCE(od_sum.sum_subtotal, 0),
        o.total_diskon = COALESCE(od_sum.sum_diskon, 0),
        o.total_bayar = COALESCE(od_sum.sum_subtotal, 0) + o.total_ongkir
    WHERE o.id_order = NEW.id_order;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id_payment` varchar(32) NOT NULL,
  `id_order` varchar(32) NOT NULL,
  `metode_pembayaran` enum('transfer_bank','ewallet','cod','kartu_kredit') NOT NULL,
  `nama_bank` varchar(100) DEFAULT NULL,
  `nomor_rekening` varchar(50) DEFAULT NULL,
  `atas_nama` varchar(100) DEFAULT NULL,
  `status_pembayaran` enum('pending','verifikasi','berhasil','gagal') DEFAULT 'pending',
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `total_bayar` decimal(12,2) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `catatan_pembayaran` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penggunaan_voucher`
--

CREATE TABLE `penggunaan_voucher` (
  `id_penggunaan` bigint NOT NULL,
  `id_voucher` varchar(16) NOT NULL,
  `id_pengguna` int DEFAULT NULL,
  `id_pesanan` varchar(32) DEFAULT NULL,
  `digunakan_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `jumlah_diskon` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `penggunaan_voucher`
--
DELIMITER $$
CREATE TRIGGER `trg_cek_kuota_per_user` BEFORE INSERT ON `penggunaan_voucher` FOR EACH ROW BEGIN
    DECLARE jml INT DEFAULT 0;
    DECLARE batas INT DEFAULT NULL;

    SELECT COUNT(*) INTO jml
    FROM penggunaan_voucher
    WHERE id_pengguna = NEW.id_pengguna
      AND id_voucher = NEW.id_voucher;

    SELECT kuota_per_pengguna INTO batas
    FROM voucher
    WHERE id_voucher = NEW.id_voucher
    LIMIT 1;

    IF batas IS NOT NULL AND batas > 0 AND jml >= batas THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Voucher sudah mencapai batas penggunaan per pengguna.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_tambah_kuota_voucher` AFTER INSERT ON `penggunaan_voucher` FOR EACH ROW BEGIN
    UPDATE voucher
    SET kuota_terpakai = kuota_terpakai + 1
    WHERE id_voucher = NEW.id_voucher;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id_product` varchar(10) NOT NULL,
  `nama_product` varchar(150) NOT NULL,
  `deskripsi_speksifikasi` text,
  `harga` decimal(12,2) NOT NULL,
  `stok` int DEFAULT '0',
  `id_kategori` varchar(10) DEFAULT NULL,
  `id_brand` varchar(16) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `berat_gram` int DEFAULT '0',
  `status_produk` enum('tersedia','habis','nonaktif') DEFAULT 'tersedia',
  `tanggal_ditambahkan` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id_product`, `nama_product`, `deskripsi_speksifikasi`, `harga`, `stok`, `id_kategori`, `id_brand`, `gambar`, `berat_gram`, `status_produk`, `tanggal_ditambahkan`) VALUES
('PRD001', 'AMD Ryzen 7 9700X | Ryzen 7 9000 Series 8 Core AM5 - Box', '𝗦𝗽𝗲𝗰𝗶𝗳𝗶𝗰𝗮𝘁𝗶𝗼𝗻𝘀 :\r\nFamily\r\n- Ryzen\r\nSeries\r\n- Ryzen 9000 Series\r\nForm Factor\r\n- Desktops , Boxed Processor\r\nMarket Segment\r\n- Enthusiast Desktop\r\n\r\nAMD PRO Technologies\r\n- No\r\nRegional Availability\r\n- Global\r\nFormer Codename\r\n- Granite Ridge AM5\r\nArchitecture\r\n- Zen 5\r\n# of CPU Cores\r\n- 8\r\nMultithreading (SMT)\r\n- Yes\r\n# of Threads\r\n- 16\r\nMax. Boost Clock\r\n- Up to 5.5 GHz\r\nBase Clock\r\n- 3.8 GHz\r\nL1 Cache\r\n- 640 KB\r\nL2 Cache\r\n- 8 MB\r\nL3 Cache\r\n- 32 MB\r\nDefault TDP\r\n- 65W\r\nProcessor Technology for CPU Cores\r\n- TSMC 4nm FinFET\r\nProcessor Technology for I/O Die\r\n- TSMC 6nm FinFET\r\nPackage Die Count\r\n- 2\r\nUnlocked for Overclocking\r\n- Yes\r\nAMD EXPO Memory Overclocking Technology\r\n- Yes\r\nPrecision Boost Overdrive\r\n- Yes\r\nCurve Optimizer Voltage Offsets\r\n- Yes\r\nAMD Ryzen Master Support\r\n- Yes\r\nSystem Memory Type\r\n- DDR5\r\nMemory Channels\r\n- 2\r\nMax. Memory\r\n- 192 GB\r\nCPU Socket\r\n- AM5\r\nThermal Solution (PIB)\r\n- Not Included\r\nMax. Operating Temperature (Tjmax)\r\n- 95C\r\n*OS Support\r\n- Windows 11 - 64-Bit Edition , Windows 10 - 64-Bit Edition , RHEL x86 64-Bit , Ubuntu x86 64-Bit\r\nBeta\r\n0 / 0\r\nused queries\r\n1', 5789000.00, 18, 'KTG003', 'BRD001', 'product_25777e9054ca3413_1764333318.jpeg', 200, 'tersedia', '2025-11-28 18:18:48'),
('PRD002', 'Corsair CX Series™ CX550 – 550 Watt 80 PLUS Bronze ATX Power Supply', 'Specifications :\r\nATX12V Version\r\n- v2.31\r\nCable Type\r\n- Type 4\r\nContinuous Power W\r\n- 550 Watts\r\nATX Connector\r\n- 1\r\nEPS Connector\r\n- 1\r\nSATA Connector\r\n- 3\r\nPCIe Connector\r\n- 2\r\nPSU Form Factor\r\n- ATX\r\nFan Bearing Technology\r\n- Sleeve', 919000.00, 36, 'KTG004', 'BRD003', 'product_70d3119869c5938c_1764333308.jpg', 2280, 'tersedia', '2025-11-28 19:35:08'),
('PRD003', 'SKYWORTH Gaming Monitor 24 INCH H24G30F FAST IPS FHD 1080P 1MS 180HZ', '- 24-inch IPS flat panel\r\n- Full HD 1920x1080 resolution\r\n- 16:9 aspect ratio\r\n- Brightness: 300 cd/m²\r\n- Refresh rate: up to 200Hz overclocked\r\n- Response time: 5ms\r\n- HDR10 support\r\n- 8-bit color depth\r\n- 99% sRGB color gamut coverage\r\n- No built-in speaker\r\n- 3.5mm audio out port\r\n- Connectivity: HDMI 2.0 x2, DisplayPort 1.4 x1\r\n- VESA mount compatible: 100x100 mm\r\n- Non-ergonomic stand\r\n- Power consumption: not specified\r\n- Net product weight: 2.9kg\r\n- Packaging dimensions: 61 × 10.5 × 40.5 cm\r\n- Volume weight: 5kg\r\n \r\n- Package includes:\r\n- Skyworth H24G30F monitor\r\n- Power adapter and cable\r\n- DisplayPort cable', 1799000.00, 11, 'KTG002', 'BRD002', 'product_3cd44ebcd958c755_1764333680.png', 4400, 'tersedia', '2025-11-28 19:41:20'),
('PRD004', 'MSI GeForce RTX 5070 Ti 16G GAMING TRIO OC', 'Specifications :\r\nModel Name\r\n- G507T-16GTC\r\n\r\nGraphics Processing Unit\r\n- NVIDIA GeForce RTX 5070 Ti\r\n\r\nInterface\r\n- PCI Express Gen 5\r\n\r\nCore Clocks\r\n- Extreme Performance: 2580 MHz (MSI Center)\r\n- Boost: 2572 MHz (GAMING & SILENT Mode)\r\n\r\nCUDA CORES\r\n- 8960 Units\r\n\r\nMemory Speed\r\n- 28 Gbps\r\n\r\nMemory\r\n- 16GB GDDR7\r\n\r\nMemory Bus\r\n- 256-bit\r\n\r\nOutput\r\n- DisplayPort x 3 (v2.1b)\r\n- HDMI x 1 (As specified in HDMI 2.1b: up to 4K 480Hz or 8K 120Hz with DSC, Gaming VRR, HDR)\r\n\r\nHDCP Support\r\n- Y\r\n\r\nPower consumption\r\n- 300W\r\n\r\nPower connectors\r\n- 16-pin x 1 (ATX 3.1 PSU recommended)\r\n\r\nRecommended PSU\r\n- 750W\r\n\r\nCard Dimension (mm)\r\n- 338 x 140 x 50 mm\r\n\r\nDirectX Version Support\r\n- 12 Ultimate\r\n\r\nOpenGL Version Support\r\n- 4.6\r\n\r\nMaximum Displays\r\n- 4\r\n\r\nG-SYNC technology\r\n- Y\r\n\r\nDigital Maximum Resolution\r\n- 7680 x 4320\r\nBeta\r\n0 / 0\r\nused queries\r\n1', 18099000.00, 16, 'KTG005', 'BRD004', 'product_6f1e210dea8b29cf_1764414158.jpeg', 1833, 'tersedia', '2025-11-29 18:01:51'),
('PRD005', 'ASUS ROG Astral GeForce RTX 5090 32GB GDDR7 OC Edition', 'Specifications :\r\nGraphic Engine\r\n- NVIDIA GeForce RTX 5090\r\nAI Performance\r\n- 3593 TOPs\r\nBus Standard\r\n- PCI Express 5.0\r\nOpenGL\r\n- OpenGL4.6\r\nVideo Memory\r\n- 32GB GDDR7\r\nEngine Clock\r\n- OC mode: 2610 MHz\r\n- Default mode: 2580 MHz(Boost clock)\r\nCUDA Core\r\n- 21760\r\nMemory Speed\r\n- 28 Gbps\r\nMemory Interface\r\n- 512-bit\r\nResolution\r\n- Digital Max Resolution 7680 x 4320\r\nInterface\r\n- Yes x 2 (Native HDMI 2.1b)\r\n- Yes x 3 (Native DisplayPort 2.1b)\r\n- HDCP Support Yes (2.3)\r\nMaximum Display Support\r\n- 4\r\nNVlink/ Crossfire Support\r\n- No\r\nAccessories\r\n- 1 x Speedsetup Manual\r\n- 1 x ROG Graphics Card Holder\r\n- 1 x ROG Velcro Hook & Loop\r\n- 1 x ROG Magnet\r\n- 1 x ROG Graphics Card Keycap\r\n- 1 x ROG PCB Ruler\r\n- 1 x Thank You Card\r\n- 1 x Adapter Cable (1 to 4)​\r\nSoftware\r\n- ASUS GPU Tweak III & MuseTree & GeForce Game Ready Driver & Studio Driver: please download all software from the support site.\r\nDimensions\r\n- 357.6 x 149.3 x 76 mm\r\n- 14.1 x 5.9 x 3 inch\r\nRecommended PSU\r\n- 1000W\r\nPower Connectors\r\n- 1 x 16-pin\r\nSlot\r\n- 3.8 Slot\r\nBeta\r\n0 / 0\r\nused queries\r\n1', 57429000.00, 54, 'KTG005', 'BRD006', 'product_58c5846ec3856138_1764419222.jpeg', 3038, 'tersedia', '2025-11-29 19:20:37'),
('PRD006', 'MSI GeForce RTX 3060 VENTUS 2X 12G OC  GeForce RTX 3060 12GB GDDR6', '𝗦𝗽𝗲𝗰𝗶𝗳𝗶𝗰𝗮𝘁𝗶𝗼𝗻𝘀 :\r\n\r\nGPU Engine Specs:\r\nCUDA Cores : 3584\r\nBoost Clock (MHz) : 1807\r\n\r\nMemory Specs:\r\nMemory Clock : 15Gbps\r\nStandard Memory Config : 12GB\r\nMemory Interface : GDDR6\r\nMemory Interface Width : 192-bit\r\nMemory Bandwidth (GB/sec) : 360\r\n\r\nDisplay Support:\r\nMulti Monitor : Yes\r\nMaxmium Digital Resolution : 7680x4320\r\nHDCP : 2.3\r\nStandard Display Connectors : 1x HDMI 2.1, 3x DisplayPort 1.4a\r\nInternalAudio Input for HDMI : Internal\r\n\r\nStandard Graphics Card Dimensions:\r\nLength : 235mm\r\nHeight : 124mm\r\nWidth : 42mm\r\n\r\nThermal and Power Spec:\r\nMinimum System Power Requirement (W) : 550\r\nSupplementary Power Connectors : 8-pin x1\r\nBeta\r\n0 / 0\r\nused queries\r\n1', 5179000.00, 14, 'KTG005', 'BRD007', 'product_f9bdb2a270e3074c_1764419600.jpeg', 675, 'tersedia', '2025-11-29 19:33:20'),
('PRD007', 'MONTECH XR', 'Spefications :\r\nColor\r\n- Black\r\n- White\r\n\r\nDimensions(L*M*H)\r\n- 435*230*450mm(Case)/525*290*510mm(Carton)\r\n\r\nMB Support\r\n- ATX,Micro-ATX,Mini-ITX\r\n\r\nFront I/O\r\n- Type-C*1/USB3.0*2/Mic*1/Audio*1/Reset Button/Power Button\r\n\r\nPCI Slots\r\n- 7\r\n\r\nCompatibility/Maximum\r\n- CPU Cooler : 175mm\r\n- GPU : 420mm\r\n- PSU : 230mm ATX\r\n\r\nDrive Bay\r\n- 3.5HDD : 2\r\n- 2.5SSD : 2\r\n\r\nPre-installed Fan(s)\r\n- Side : 120mm*2\r\n- rear : 120mm*1\r\n\r\nFan Support\r\n- Top : 120mm*3/140mm*2\r\n- Side : 120mm*2\r\n- PSU shroud : 120mm*3\r\n- Rear : 120mm*1/140mm*1\r\n\r\nRadiator Support\r\n- Top : 360/240/140/120mm\r\n\r\nDust Filters\r\n- Side, Bottom, Top\r\nBeta\r\n0 / 0\r\nused queries\r\n1', 859000.00, 25, 'KTG001', 'BRD008', 'product_72cf6284c8ef1d08_1764420245.jpg', 12000, 'tersedia', '2025-11-29 19:44:05'),
('PRD008', 'KingBank Sharp Blade RGB DDR5 32GB Kit (2 x 16GB) 6400 MT/s CL30 White A-Die', '𝗦𝗽𝗲𝗰𝗶𝗳𝗶𝗰𝗮𝘁𝗶𝗼𝗻𝘀 :\r\n\r\nModule Spec\r\n- DDR5 288 pin U DIMM\'\r\n\r\nCapacity\r\n- 32GB(16GBx2)\r\n\r\nFrequency\r\n- 6400MHz\r\n\r\nVoltage\r\n- 1.4V\r\n\r\nCAS latency\r\n- 32-39-39-80\r\n\r\nHeatsink material\r\n- Aluminum Alloy\r\n\r\nSupported System\'\r\n- Intel XMP 3.0\r\n\r\nProduct Size\r\n- 133.8mmx41.8mmx8mm\r\n\r\nChip\r\n- SK Hynix A-die', 4339000.00, 22, 'KTG006', 'BRD009', 'product_7a470c9afeec921a_1764420855.jpeg', 400, 'tersedia', '2025-11-29 19:54:15'),
('PRD009', 'KingBank Sharp Blade RGB DDR5 32GB Kit (2 x 16GB) 6000 MT/s CL28 White A-Die', '𝗦𝗽𝗲𝗰𝗶𝗳𝗶𝗰𝗮𝘁𝗶𝗼𝗻𝘀 :\r\nModule Spec\r\n\r\n- DDR5 288 pin U DIMM\r\nCapacity\r\n\r\n- 32GB(16GBx2)\r\nFrequency\r\n\r\n- 6000MHz\r\nCAS latency\r\n\r\n- 28\r\nHeatsink material\r\n\r\n- Aluminum Alloy\r\nSupported System\'\r\n\r\n- Intel XMP 3.0 / AMD EXPO\r\nProduct Size\r\n\r\n- 133.8mmx41.8mmx8mm', 4559000.00, 22, 'KTG006', 'BRD009', 'product_4a816e634068837b_1764420931.jpeg', 400, 'tersedia', '2025-11-29 19:55:31'),
('PRD010', 'KINGBANK SoarBlade RGB 32GB (16GBx2) DDR5 6000MHz CL36 Memory Kit - White', 'Specifications :\r\n\r\nPrice\r\nRp1.769.000\r\n\r\nModule Spec\r\n- DDR5 288 pin U DIMM\r\n\r\nCapacity\r\n- 32GB (16GBx2)\r\n\r\nFrequency\r\n- 6000\r\n\r\nVoltage\r\n- 1.35V\r\n\r\nCAS latency\r\n- CL36\r\n\r\nHeatsink Material\r\n- Aluminum Alloy\r\n\r\nSupported System\r\n- Intel XMP 3.0/AMD EXPO\r\n\r\nProduct Size\r\n- 133.3mmx41.8mmx8.1mm\r\n\r\nBeta\r\n0 / 0\r\nused queries\r\n1', 3888900.00, 46, 'KTG006', 'BRD009', 'product_9af7b52afd65217a_1764421067.jpeg', 400, 'tersedia', '2025-11-29 19:57:47'),
('PRD011', 'AMD Ryzen 5 5600 - AM4 BOX', 'Specifications :\r\n\r\nPlatform\r\n- Desktop\r\n\r\nMarket Segment\r\n- Mainstream Desktop\r\n\r\nProduct Family\r\n- AMD Ryzen Processors\r\n\r\nProduct Line\r\n- AMD Ryzen 5 Desktop Processors\r\n\r\nConsumer Use\r\n- Yes\r\n\r\nRegional Availability\r\n- Global, China, NA, EMEA, APJ, LATAM\r\n\r\nFormer Codename\r\n- \"Vermeer\"\r\n\r\nArchitecture\r\n- \"Zen 3\"\r\n\r\n# of CPU Cores\r\n- 6\r\n\r\nMultithreading (SMT)\r\n- Yes\r\n\r\n# of Threads\r\n- 12\r\n\r\nMax. Boost Clock\r\n- Up to 4.4GHz\r\n\r\nBase Clock\r\n- 3.5GHz\r\n\r\nL1 Cache\r\n- 384KB\r\n\r\nL2 Cache\r\n- 3MB\r\n\r\nL3 Cache\r\n- 32MB\r\n\r\nDefault TDP\r\n- 65W\r\n\r\nProcessor Technology for CPU Cores\r\n- TSMC 7nm FinFET\r\n\r\nProcessor Technology for I/O Die\r\n- 12nm (Globalfoundries)\r\n\r\nCPU Compute Die (CCD) Size\r\n- 74mm²\r\n\r\nI/O Die (IOD) Size\r\n- 125mm²\r\n\r\nPackage Die Count\r\n- 2\r\n\r\nUnlocked for Overclocking\r\n- Yes\r\n\r\nCPU Socket\r\n- AM4\r\n\r\nSocket Count\r\n- 1P\r\n\r\nSupporting Chipsets\r\n- X570\r\n- X470\r\n- X370\r\n- B550\r\n- B450\r\n- B350\r\n- A520\r\n\r\nCPU Boost Technology\r\n- Precision Boost 2\r\n\r\nInstruction Set\r\n- x86-64\r\n\r\nSupported Extensions\r\n- AES, AMD-V, AVX, AVX2, FMA3, MMX(+), SHA, SSE, SSE2, SSE3, SSE4.1, SSE4.2, SSE4A, SSSE3, x86-64\r\n\r\nThermal Solution (PIB)\r\n- AMD Wraith Stealth\r\n\r\nMax. Operating Temperature (Tjmax)\r\n- 90°C\r\n\r\nLaunch Date\r\n- 4/4/2022\r\n\r\nOS Support\r\n- Windows 11 - 64-Bit Edition\r\n- Windows 10 - 64-Bit Edition\r\n- RHEL x86 64-Bit\r\n- Ubuntu x86 64-Bit\r\n- Operating System (OS) support will vary by manufacturer.\r\n\r\nBeta\r\n0 / 0\r\nused queries\r\n1', 1689000.00, 123, 'KTG003', 'BRD001', 'product_94f54dbaca74de07_1764442165.jpg', 600, 'tersedia', '2025-11-30 01:49:25'),
('PRD012', 'Asus ROG Strix x Hatsune Miku Limited Edition PC Bundle', 'ROG Strix x Hatsune Miku Limited Edition PC Bundle adalah paket eksklusif komponen PC premium hasil kolaborasi ASUS ROG dengan karakter virtual idol legendaris Hatsune Miku.\r\nBundle ini dirancang untuk gamer, kreator konten, dan kolektor yang menginginkan performa kelas atas dengan desain anime futuristik berwarna turquoise–pink khas Miku.\r\nSetiap komponen dilengkapi RGB Aura Sync, ilustrasi resmi Hatsune Miku, serta material premium yang menjamin performa tinggi, pendinginan optimal, dan tampilan showcase yang mencuri perhatian.\r\nIsi Bundle\r\n•	Motherboard ROG Strix Hatsune Miku Edition\r\n•	Casing ROG Strix Hatsune Miku Edition (Tempered Glass)\r\n•	Graphics Card ROG Strix Hatsune Miku Edition\r\n•	ROG Strix RGB Cooling Fans (3x)\r\n•	ROG Strix Liquid Cooler LCD Hatsune Miku Edition\r\nSpesifikasi Utama\r\nASUS MOTHERBOARD ROG STRIX X870E-H GAMING WIFI 7 HATSUNE MIKU EDITION DDR5 AM5 /AMD HATSUNE MIKU EDITION 3Y\r\n•	Chipset: Intel (LGA1700 – seri Strix)\r\n•	Form Factor: ATX\r\n•	Memory: Hingga 128GB DDR5\r\n•	Storage: M.2 NVMe Gen 4, SATA 6Gb/s\r\n•	RGB: ASUS Aura Sync (Hatsune Miku Theme)\r\nASUS PC CASE E-ATX ROG STRIX HELIOS II (4X 140MM BLACK FAN) HATSUNE MIKU EDITION 2Y\r\n•	Tipe: Mid Tower\r\n•	Material: Steel + Tempered Glass\r\n•	Support Motherboard: ATX / mATX / Mini-ITX\r\n•	Fan Support: Hingga 6 fan\r\n•	Radiator Support: Hingga 360mm\r\n•	Desain: Ilustrasi resmi Hatsune Miku\r\n ASUS VGA NVIDIA GEFORCE ROG ASTRAL RTX 5080 16GB GDDR7 OC HATSUNE MIKU EDITION 3Y\r\n•	Seri: ROG Strix Hatsune Miku Edition\r\n•	Cooling: Triple Fan Axial-tech\r\n•	RGB: Aura Sync\r\n•	Output: HDMI, DisplayPort\r\n•	Fokus: Gaming & Content Creation kelas high-end\r\nASUS CPU AIO COOLER ROG RYUO IV 360 ARGB HATSUNE MIKU EDITION (360MM RADIATOR + 3X ARGB FANS + 6.67 INCH AMOLED DISPLAY) HATSUNE MIKU EDITION 6Y\r\n•	Radiator: 360mm\r\n•	Fan: 3x RGB Fans\r\n•	Layar: LCD Display Custom Hatsune Miku Animation\r\n•	Socket Support: Intel LGA1700 / AMD AM5\r\n•	Teknologi: AIO Liquid Cooling Performance\r\nRGB Fans\r\n•	Jumlah: 3 Unit\r\n•	Size: 120mm\r\n•	Lighting: Addressable RGB Aura Sync\r\n•	Airflow: High Performance\r\nPeripheral & Aksesori Tambahan (NEW)\r\nASUS Gaming Monitor 27” ROG Strix XG27ACMEG-G – Hatsune Miku Edition\r\n•	Panel: Fast IPS\r\n•	Ukuran Layar: 27 Inch\r\n•	Resolusi: 2K QHD (2560×1440)\r\n•	Refresh Rate: Hingga 260Hz\r\n•	Response Time: 0.3ms\r\n•	HDR: HDR10\r\n•	Konektivitas: HDMI, DisplayPort, USB-C\r\n•	Ergonomic Stand: Tilt, Swivel, Height Adjust, Pivot\r\n•	Desain: ROG Strix x Hatsune Miku Limited Edition\r\n•	Garansi: 3 Tahun\r\nASUS External SSD Case ROG Strix Arion – Hatsune Miku Edition\r\n•	Interface: USB-C (USB 3.2 Gen 2)\r\n•	Kecepatan Transfer: Up to 1250 MB/s\r\n•	Support SSD: M.2 NVMe\r\n•	Material: Aluminium Alloy (Heatsink Design)\r\n•	RGB: Aura Sync\r\n•	Desain Eksklusif Hatsune Miku\r\nASUS PSU ROG Thor 1200W Platinum III – Hatsune Miku Edition\r\n•	Daya: 1200 Watt\r\n•	Sertifikasi: 80+ Platinum\r\n•	Standar: ATX 3.1\r\n•	Konektor GPU: 12V-2×6\r\n•	Modular: Fully Modular\r\n•	Fitur: OLED Power Display\r\n•	Cooling: ROG Axial-tech Fan\r\n•	Garansi: 10 Tahun\r\nASUS Wireless Mouse TUF Gaming Mini – Hatsune Miku Edition\r\n•	Koneksi: Wireless\r\n•	Sensor: High Precision Gaming Sensor\r\n•	Desain: Lightweight & Compact\r\n•	Tombol: Programmable Buttons\r\n•	Cocok untuk: Gaming & produktivitas mobile\r\nASUS Gaming Headset TUF Gaming H1 Gen II – Hatsune Miku Edition\r\n•	Driver: 40mm ASUS Essence Driver\r\n•	Koneksi: USB\r\n•	Audio: Virtual Surround Sound\r\n•	Microphone: AI Noise-Canceling\r\n•	Bobot: Lightweight Design\r\n•	Desain: TUF Gaming x Hatsune Miku\r\nASUS Gaming Keyboard TUF Gaming K3 Gen II – Hatsune Miku Edition\r\n•	Switch: Mechanical (Gaming Grade)\r\n•	Lighting: RGB Backlight\r\n•	Layout: Full Size\r\n•	Durability: Military-grade build quality\r\n•	Desain: Exclusive Hatsune Miku Artwork\r\nASUS Mouse Pad TUF Gaming P1 – Hatsune Miku Edition\r\n•	Material: Gaming-grade Fabric Surface\r\n•	Base: Anti-Slip Rubber\r\n•	Edge: Anti-Fray Stitching\r\n•	Ukuran: Medium (Desk Friendly)\r\n•	Desain: Limited Edition Hatsune Miku', 89349000.00, 0, 'KTG012', 'BRD006', 'product_22158a90da35389e_1765372761.png', 12000, 'habis', '2025-12-10 20:19:21');

-- --------------------------------------------------------

--
-- Table structure for table `promo_diskon`
--

CREATE TABLE `promo_diskon` (
  `id_diskon` varchar(16) NOT NULL,
  `id_produk` varchar(10) DEFAULT NULL,
  `id_kampanye` varchar(16) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `harga_awal` decimal(12,2) DEFAULT NULL,
  `harga_diskon` decimal(12,2) DEFAULT NULL,
  `jenis` enum('persen','nominal') DEFAULT 'persen',
  `nilai` decimal(12,2) DEFAULT NULL,
  `stok_promo` int DEFAULT NULL,
  `stok_terpakai` int DEFAULT '0',
  `maks_qty_per_pengguna` int DEFAULT NULL,
  `mulai_pada` datetime DEFAULT NULL,
  `selesai_pada` datetime DEFAULT NULL,
  `status` enum('aktif','nonaktif','terjadwal','berakhir') DEFAULT 'terjadwal',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `promo_diskon`
--

INSERT INTO `promo_diskon` (`id_diskon`, `id_produk`, `id_kampanye`, `label`, `harga_awal`, `harga_diskon`, `jenis`, `nilai`, `stok_promo`, `stok_terpakai`, `maks_qty_per_pengguna`, `mulai_pada`, `selesai_pada`, `status`, `dibuat_pada`, `diperbarui_pada`) VALUES
('DSK75B220E5', 'PRD008', NULL, 'Diskon Akhir Tahun Hingga 60% Produk RAM', 4339000.00, 1735600.00, 'persen', 60.00, 10, 0, 1, '2025-12-17 01:48:00', '2025-12-31 01:47:00', 'aktif', '2025-12-17 01:47:19', '2025-12-17 01:48:48'),
('DSK9D622DAB', 'PRD009', NULL, 'Diskon Akhir Tahun Hingga 60% Produk RAM', 4559000.00, 1823600.00, 'persen', 60.00, 10, 0, 1, '2025-12-17 01:48:00', '2025-12-31 01:47:00', 'aktif', '2025-12-17 01:47:19', '2025-12-17 01:48:48'),
('DSKDC18E439', 'PRD010', NULL, 'Diskon Akhir Tahun Hingga 60% Produk RAM', 3888900.00, 1555560.00, 'persen', 60.00, 10, 0, 1, '2025-12-17 01:48:00', '2025-12-31 01:47:00', 'aktif', '2025-12-17 01:47:19', '2025-12-17 01:48:48'),
('DSKEDEBE3DB', 'PRD003', NULL, 'Diskon Monitor 550rb', 1799000.00, 1249000.00, 'nominal', 550000.00, 10, 0, 1, '2025-12-16 23:27:00', '2025-12-31 23:26:00', 'aktif', '2025-12-16 23:26:46', '2025-12-17 13:03:24');

--
-- Triggers `promo_diskon`
--
DELIMITER $$
CREATE TRIGGER `trg_hitung_harga_setelah_diskon_ins` BEFORE INSERT ON `promo_diskon` FOR EACH ROW BEGIN
    IF NEW.jenis = 'persen' THEN
        SET NEW.harga_diskon = NEW.harga_awal - (NEW.harga_awal * NEW.nilai / 100);
    ELSE
        SET NEW.harga_diskon = NEW.harga_awal - NEW.nilai;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hitung_harga_setelah_diskon_upd` BEFORE UPDATE ON `promo_diskon` FOR EACH ROW BEGIN
    IF NEW.jenis = 'persen' THEN
        SET NEW.harga_diskon = NEW.harga_awal - (NEW.harga_awal * NEW.nilai / 100);
    ELSE
        SET NEW.harga_diskon = NEW.harga_awal - NEW.nilai;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `promo_kampanye`
--

CREATE TABLE `promo_kampanye` (
  `id_kampanye` varchar(16) NOT NULL,
  `judul` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `deskripsi` text,
  `banner` varchar(255) DEFAULT NULL,
  `tipe` enum('diskon_produk','voucher','flash_sale','bundle','gratis_ongkir') NOT NULL,
  `mulai_pada` datetime NOT NULL,
  `selesai_pada` datetime NOT NULL,
  `kuota_total` int DEFAULT NULL,
  `kuota_terpakai` int DEFAULT '0',
  `status` enum('draf','aktif','berakhir','nonaktif') DEFAULT 'draf',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `promo_kampanye`
--

INSERT INTO `promo_kampanye` (`id_kampanye`, `judul`, `slug`, `deskripsi`, `banner`, `tipe`, `mulai_pada`, `selesai_pada`, `kuota_total`, `kuota_terpakai`, `status`, `dibuat_pada`, `diperbarui_pada`) VALUES
('CMP0000000001', 'Diskon Akhir Tahun Hingga 85%', 'diskon-akhir-tahun-hingga-85', 'Diskon Akhir Tahun Hingga 85%', 'campaign_5a59f08f87c5dd30_1764553744.png', 'diskon_produk', '2025-12-01 10:48:00', '2025-12-31 23:59:00', 80, 0, 'aktif', '2025-12-01 08:49:04', '2025-12-02 10:08:48'),
('CMP0000000002', 'Flash Sale Akhir Tahun Produk Skyworth', 'flash-sale-akhir-tahun-produk-skyworth', 'Flash Sale Akhir Tahun Produk Skyworth', 'campaign_fc37d5b090501208_1764556257.png', 'flash_sale', '2025-12-01 09:30:00', '2025-12-31 09:30:00', 20, 0, 'aktif', '2025-12-01 09:30:57', '2025-12-14 19:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `promo_statistik`
--

CREATE TABLE `promo_statistik` (
  `id` bigint NOT NULL,
  `tanggal` date NOT NULL,
  `id_kampanye` varchar(16) DEFAULT NULL,
  `id_voucher` varchar(16) DEFAULT NULL,
  `total_penggunaan` int DEFAULT '0',
  `total_diskon` decimal(14,2) DEFAULT '0.00',
  `total_pendapatan` decimal(14,2) DEFAULT '0.00',
  `jumlah_transaksi` int DEFAULT '0',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` varchar(32) NOT NULL,
  `id_product` varchar(10) NOT NULL,
  `id_customer` int NOT NULL,
  `id_order` varchar(32) DEFAULT NULL,
  `rating` int NOT NULL,
  `komentar` text,
  `foto_review` varchar(255) DEFAULT NULL,
  `tanggal_review` datetime DEFAULT CURRENT_TIMESTAMP,
  `status_review` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `review`
--
DELIMITER $$
CREATE TRIGGER `trg_validasi_review_produk` BEFORE INSERT ON `review` FOR EACH ROW BEGIN
    DECLARE jml INT DEFAULT 0;

    SELECT COUNT(*) INTO jml
    FROM order_detail od
    JOIN orders o ON o.id_order = od.id_order
    WHERE od.id_product = NEW.id_product
      AND o.id_customer = NEW.id_customer
      AND o.status_order = 'selesai';

    IF jml = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Review gagal: pengguna belum membeli produk ini atau pesanan belum selesai.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id_role_permission` int NOT NULL,
  `id_role` int NOT NULL,
  `id_permission` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id_role_permission`, `id_role`, `id_permission`) VALUES
(16, 1, 1),
(15, 1, 2),
(10, 1, 3),
(5, 1, 4),
(3, 1, 5),
(17, 1, 6),
(7, 1, 7),
(4, 1, 8),
(9, 1, 9),
(13, 1, 10),
(18, 1, 11),
(19, 1, 12),
(11, 1, 13),
(6, 1, 14),
(12, 1, 15),
(8, 1, 16),
(1, 1, 17),
(2, 1, 18),
(14, 1, 19),
(20, 1, 20),
(461, 1, 21),
(457, 1, 22),
(459, 1, 23),
(460, 1, 24),
(458, 1, 25),
(471, 2, 1),
(472, 2, 2),
(481, 2, 3),
(482, 2, 4),
(483, 2, 5),
(473, 2, 6),
(484, 2, 7),
(485, 2, 8),
(486, 2, 9),
(487, 2, 10),
(474, 2, 11),
(475, 2, 12),
(488, 2, 13),
(489, 2, 14),
(476, 2, 20),
(477, 2, 21),
(490, 2, 22),
(478, 2, 23),
(479, 2, 24),
(480, 2, 25);

-- --------------------------------------------------------

--
-- Table structure for table `shipment`
--

CREATE TABLE `shipment` (
  `id_shipment` varchar(32) NOT NULL,
  `id_order` varchar(32) NOT NULL,
  `id_alamat` int NOT NULL,
  `jasa_pengiriman` varchar(100) NOT NULL,
  `no_resi` varchar(100) DEFAULT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `nomor_hp_penerima` varchar(20) NOT NULL,
  `alamat_pengiriman` text NOT NULL,
  `kota` varchar(100) NOT NULL,
  `kode_pos` varchar(10) NOT NULL,
  `ongkir` decimal(12,2) NOT NULL,
  `estimasi_hari` int DEFAULT NULL,
  `status_pengiriman` enum('pending','dikemas','dikirim','dalam_perjalanan','tiba','diterima') DEFAULT 'pending',
  `tanggal_dikirim` datetime DEFAULT NULL,
  `tanggal_diterima` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_locations`
--

CREATE TABLE `store_locations` (
  `id_toko` varchar(10) NOT NULL,
  `nama_toko` varchar(100) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `alamat` text NOT NULL,
  `provinsi` varchar(100) NOT NULL,
  `kota_kabupaten` varchar(100) NOT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kelurahan` varchar(100) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `jam_buka` time DEFAULT NULL,
  `jam_tutup` time DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `store_locations`
--

INSERT INTO `store_locations` (`id_toko`, `nama_toko`, `no_telepon`, `alamat`, `provinsi`, `kota_kabupaten`, `kecamatan`, `kelurahan`, `kode_pos`, `jam_buka`, `jam_tutup`, `is_active`, `created_at`, `updated_at`) VALUES
('TKO0001', 'Nano Komputer - Jakarta Pusat', '0816765803', 'Mangga Dua Mall, Jl. Mangga Dua Raya No.47A-B Lantai 21', 'DKI JAKARTA', 'KOTA JAKARTA PUSAT', 'SAWAH BESAR', 'MANGGA DUA SELATAN', '10730', '08:00:00', '18:00:00', 1, '2025-12-12 12:59:04', '2025-12-15 00:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id_ticket` varchar(10) NOT NULL,
  `id_customer` int DEFAULT NULL,
  `nama_pengaju` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `subjek` varchar(200) NOT NULL,
  `kategori` enum('General','Garansi & Servis','Aktivasi Akun','Komplain','Pertanyaan Produk','Status Pesanan') NOT NULL DEFAULT 'General',
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Open','In Progress','Resolved','Closed') NOT NULL DEFAULT 'Open',
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium',
  `assigned_to` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id_ticket`, `id_customer`, `nama_pengaju`, `email`, `no_telepon`, `subjek`, `kategori`, `message`, `attachment`, `status`, `priority`, `assigned_to`, `created_at`, `updated_at`) VALUES
('TKT00001', NULL, 'Fauzan Eldianzah', 'fauzan.customer@gmail.com', '081234567890', 'Kendala Login Akun Dinonaktifkan', 'Aktivasi Akun', 'Yth. Tim Support/Administrator\r\nDengan hormat,\r\n\r\nSaya yang bertanda tangan di bawah ini:\r\n\r\nNama: Fauzan Eldianzah\r\nEmail/Username: fauzan.customer@gmail.com\r\n\r\nDengan ini mengajukan permohonan untuk aktivasi ulang akun saya yang saat ini dinonaktifkan. Saya menyadari bahwa akun tersebut mungkin dinonaktifkan karena alasan tertentu, dan saya memohon kesediaan pihak terkait untuk meninjau kembali status akun saya.\r\n\r\nSaya berkomitmen untuk mematuhi seluruh ketentuan dan kebijakan yang berlaku. Apabila diperlukan informasi atau dokumen tambahan, saya siap untuk melengkapinya.\r\n\r\nDemikian permohonan ini saya sampaikan. Atas perhatian dan bantuannya, saya ucapkan terima kasih.\r\n\r\nHormat saya,', NULL, 'Open', 'Medium', NULL, '2025-12-16 17:06:57', '2025-12-16 17:06:57');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

CREATE TABLE `ticket_replies` (
  `id_reply` int NOT NULL,
  `id_ticket` varchar(10) NOT NULL,
  `id_admin` int DEFAULT NULL,
  `id_customer` int DEFAULT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `is_internal_note` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `id_voucher` varchar(16) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `judul` varchar(191) DEFAULT NULL,
  `deskripsi` text,
  `jenis` enum('diskon_persen','diskon_nominal','gratis_ongkir','cashback') NOT NULL,
  `nilai` decimal(12,2) DEFAULT NULL,
  `minimal_belanja` decimal(12,2) DEFAULT '0.00',
  `maksimal_diskon` decimal(12,2) DEFAULT NULL,
  `mulai_pada` datetime DEFAULT NULL,
  `selesai_pada` datetime DEFAULT NULL,
  `kuota_total` int DEFAULT NULL,
  `kuota_terpakai` int DEFAULT '0',
  `kuota_per_pengguna` int DEFAULT NULL,
  `terbatas_produk` json DEFAULT NULL,
  `terbatas_kategori` json DEFAULT NULL,
  `status` enum('aktif','nonaktif','terjadwal','berakhir') DEFAULT 'terjadwal',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`id_voucher`, `kode`, `judul`, `deskripsi`, `jenis`, `nilai`, `minimal_belanja`, `maksimal_diskon`, `mulai_pada`, `selesai_pada`, `kuota_total`, `kuota_terpakai`, `kuota_per_pengguna`, `terbatas_produk`, `terbatas_kategori`, `status`, `dibuat_pada`, `diperbarui_pada`) VALUES
('VCH0000000001', '7HYFASLL', 'Flash Sale Akhir Tahun', 'Flash Sale', 'diskon_nominal', 1000000.00, 2.00, 2000000.00, '2025-11-30 19:30:00', '2025-12-31 23:59:00', 20, 0, 1, NULL, NULL, 'aktif', '2025-11-30 02:34:03', '2025-12-14 12:48:48'),
('VCH0000000002', '8ZMF5XBE', 'Diskon Monitor 250rb', 'Untuk pembelian monitor ', 'diskon_nominal', 250000.00, 1.00, 250000.00, '2025-12-16 23:05:00', '2025-12-31 23:05:00', 10, 0, 1, NULL, NULL, 'aktif', '2025-12-16 23:05:54', '2025-12-17 01:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id_wishlist` varchar(32) NOT NULL,
  `id_customer` int NOT NULL,
  `id_product` varchar(10) NOT NULL,
  `tanggal_ditambahkan` datetime DEFAULT CURRENT_TIMESTAMP,
  `tgl_diubah` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id_wishlist`, `id_customer`, `id_product`, `tanggal_ditambahkan`, `tgl_diubah`) VALUES
('WSH0000004', 7, 'PRD008', '2025-12-08 14:57:05', '2025-12-08 14:57:05'),
('WSH0000012', 8, 'PRD011', '2025-12-13 14:02:50', '2025-12-13 14:02:50'),
('WSH0000013', 2, 'PRD002', '2025-12-13 14:04:18', '2025-12-13 14:04:18'),
('WSH0000014', 2, 'PRD007', '2025-12-13 14:04:20', '2025-12-13 14:04:20'),
('WSH0000015', 1, 'PRD001', '2025-12-17 01:48:15', '2025-12-17 01:48:15'),
('WSH0000016', 1, 'PRD003', '2025-12-17 01:48:33', '2025-12-17 01:48:33'),
('WSH0000017', 1, 'PRD010', '2025-12-17 09:58:04', '2025-12-17 09:58:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address_book`
--
ALTER TABLE `address_book`
  ADD PRIMARY KEY (`id_alamat`),
  ADD KEY `idx_alamat_customer` (`id_customer`),
  ADD KEY `idx_customer_address` (`id_customer`,`default_alamat`);

--
-- Indexes for table `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_admin_email` (`email`),
  ADD KEY `idx_admin_photo` (`photo`),
  ADD KEY `idx_admin_username` (`username`),
  ADD KEY `fk_admin_role` (`id_role`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id_permission`),
  ADD UNIQUE KEY `permission_key` (`permission_key`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id_category`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id_post`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_blog_admin` (`id_admin`),
  ADD KEY `idx_blog_status` (`status`),
  ADD KEY `idx_blog_published` (`published_at`),
  ADD KEY `idx_blog_category` (`id_category`);

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
  ADD KEY `idx_cart_customer` (`id_customer`),
  ADD KEY `idx_cart_product` (`id_product`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id_customer`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`),
  ADD KEY `idx_customer_email` (`email`),
  ADD KEY `idx_customer_google_id` (`google_id`),
  ADD KEY `idx_customer_login_type` (`login_type`);

--
-- Indexes for table `diskon`
--
ALTER TABLE `diskon`
  ADD PRIMARY KEY (`id_diskon`),
  ADD KEY `idx_diskon_product` (`id_product`);

--
-- Indexes for table `kampanye_produk`
--
ALTER TABLE `kampanye_produk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_kp` (`id_kampanye`,`id_produk`),
  ADD KEY `fk_kp_diskon` (`id_diskon`),
  ADD KEY `idx_kp_kampanye` (`id_kampanye`),
  ADD KEY `idx_kp_produk` (`id_produk`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `log_blog`
--
ALTER TABLE `log_blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_promo`
--
ALTER TABLE `log_promo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_admin` (`id_admin`),
  ADD KEY `idx_log_promo_level` (`level`),
  ADD KEY `idx_log_promo_entitas` (`entitas`,`id_entitas`),
  ADD KEY `idx_log_promo_waktu` (`dibuat_pada`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `idx_notifikasi_customer` (`id_customer`),
  ADD KEY `idx_notifikasi_order` (`id_order`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `idx_order_customer` (`id_customer`),
  ADD KEY `idx_order_status` (`status_order`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `idx_order_detail_order` (`id_order`),
  ADD KEY `idx_order_detail_product` (`id_product`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id_payment`),
  ADD UNIQUE KEY `id_order` (`id_order`),
  ADD KEY `idx_payment_order` (`id_order`),
  ADD KEY `idx_payment_status` (`status_pembayaran`);

--
-- Indexes for table `penggunaan_voucher`
--
ALTER TABLE `penggunaan_voucher`
  ADD PRIMARY KEY (`id_penggunaan`),
  ADD KEY `idx_pv_voucher` (`id_voucher`),
  ADD KEY `idx_pv_pengguna` (`id_pengguna`),
  ADD KEY `idx_pv_pesanan` (`id_pesanan`),
  ADD KEY `idx_pv_waktu` (`digunakan_pada`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `idx_product_kategori` (`id_kategori`),
  ADD KEY `idx_product_brand` (`id_brand`),
  ADD KEY `idx_product_status` (`status_produk`);

--
-- Indexes for table `promo_diskon`
--
ALTER TABLE `promo_diskon`
  ADD PRIMARY KEY (`id_diskon`),
  ADD KEY `idx_promo_diskon_produk` (`id_produk`),
  ADD KEY `idx_promo_diskon_kampanye` (`id_kampanye`),
  ADD KEY `idx_promo_diskon_status` (`status`),
  ADD KEY `idx_promo_diskon_waktu` (`mulai_pada`,`selesai_pada`);

--
-- Indexes for table `promo_kampanye`
--
ALTER TABLE `promo_kampanye`
  ADD PRIMARY KEY (`id_kampanye`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_kampanye_tipe` (`tipe`),
  ADD KEY `idx_kampanye_waktu` (`mulai_pada`,`selesai_pada`),
  ADD KEY `idx_kampanye_status` (`status`),
  ADD KEY `idx_kampanye_slug` (`slug`);

--
-- Indexes for table `promo_statistik`
--
ALTER TABLE `promo_statistik`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_stat_tanggal` (`tanggal`),
  ADD KEY `idx_stat_kampanye` (`id_kampanye`),
  ADD KEY `idx_stat_voucher` (`id_voucher`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `idx_review_product` (`id_product`),
  ADD KEY `idx_review_customer` (`id_customer`),
  ADD KEY `idx_review_order` (`id_order`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id_role_permission`),
  ADD UNIQUE KEY `uq_role_perm` (`id_role`,`id_permission`),
  ADD KEY `id_permission` (`id_permission`);

--
-- Indexes for table `shipment`
--
ALTER TABLE `shipment`
  ADD PRIMARY KEY (`id_shipment`),
  ADD KEY `fk_shipment_address` (`id_alamat`),
  ADD KEY `idx_shipment_order` (`id_order`),
  ADD KEY `idx_shipment_status` (`status_pengiriman`);

--
-- Indexes for table `store_locations`
--
ALTER TABLE `store_locations`
  ADD PRIMARY KEY (`id_toko`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id_ticket`),
  ADD KEY `idx_customer` (`id_customer`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_kategori` (`kategori`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_assigned_to` (`assigned_to`);

--
-- Indexes for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD PRIMARY KEY (`id_reply`),
  ADD KEY `idx_ticket` (`id_ticket`),
  ADD KEY `idx_admin` (`id_admin`),
  ADD KEY `idx_customer` (`id_customer`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id_voucher`),
  ADD UNIQUE KEY `kode` (`kode`),
  ADD KEY `idx_voucher_kode` (`kode`),
  ADD KEY `idx_voucher_waktu` (`mulai_pada`,`selesai_pada`),
  ADD KEY `idx_voucher_status` (`status`),
  ADD KEY `idx_voucher_jenis` (`jenis`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD UNIQUE KEY `uq_wishlist` (`id_customer`,`id_product`),
  ADD KEY `idx_wishlist_customer` (`id_customer`),
  ADD KEY `idx_wishlist_product` (`id_product`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address_book`
--
ALTER TABLE `address_book`
  MODIFY `id_alamat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id_permission` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id_category` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id_post` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id_customer` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kampanye_produk`
--
ALTER TABLE `kampanye_produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `log_blog`
--
ALTER TABLE `log_blog`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_promo`
--
ALTER TABLE `log_promo`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penggunaan_voucher`
--
ALTER TABLE `penggunaan_voucher`
  MODIFY `id_penggunaan` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promo_statistik`
--
ALTER TABLE `promo_statistik`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id_role_permission` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=491;

--
-- AUTO_INCREMENT for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  MODIFY `id_reply` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address_book`
--
ALTER TABLE `address_book`
  ADD CONSTRAINT `fk_address_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE CASCADE;

--
-- Constraints for table `administrators`
--
ALTER TABLE `administrators`
  ADD CONSTRAINT `fk_admin_role` FOREIGN KEY (`id_role`) REFERENCES `admin_roles` (`id_role`) ON DELETE SET NULL;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `fk_blog_admin` FOREIGN KEY (`id_admin`) REFERENCES `administrators` (`id_admin`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_blog_category` FOREIGN KEY (`id_category`) REFERENCES `blog_categories` (`id_category`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE;

--
-- Constraints for table `diskon`
--
ALTER TABLE `diskon`
  ADD CONSTRAINT `fk_diskon_product` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE;

--
-- Constraints for table `kampanye_produk`
--
ALTER TABLE `kampanye_produk`
  ADD CONSTRAINT `fk_kp_diskon` FOREIGN KEY (`id_diskon`) REFERENCES `promo_diskon` (`id_diskon`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_kp_kampanye` FOREIGN KEY (`id_kampanye`) REFERENCES `promo_kampanye` (`id_kampanye`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kp_produk` FOREIGN KEY (`id_produk`) REFERENCES `products` (`id_product`) ON DELETE CASCADE;

--
-- Constraints for table `log_promo`
--
ALTER TABLE `log_promo`
  ADD CONSTRAINT `fk_log_admin` FOREIGN KEY (`id_admin`) REFERENCES `administrators` (`id_admin`) ON DELETE SET NULL;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notif_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notif_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE CASCADE;

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `fk_od_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_od_product` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE;

--
-- Constraints for table `penggunaan_voucher`
--
ALTER TABLE `penggunaan_voucher`
  ADD CONSTRAINT `fk_pv_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `customers` (`id_customer`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pv_voucher` FOREIGN KEY (`id_voucher`) REFERENCES `voucher` (`id_voucher`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_brand` FOREIGN KEY (`id_brand`) REFERENCES `brand` (`id_brand`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_products_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL;

--
-- Constraints for table `promo_diskon`
--
ALTER TABLE `promo_diskon`
  ADD CONSTRAINT `fk_pd_kampanye` FOREIGN KEY (`id_kampanye`) REFERENCES `promo_kampanye` (`id_kampanye`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pd_produk` FOREIGN KEY (`id_produk`) REFERENCES `products` (`id_product`) ON DELETE SET NULL;

--
-- Constraints for table `promo_statistik`
--
ALTER TABLE `promo_statistik`
  ADD CONSTRAINT `fk_stat_kampanye` FOREIGN KEY (`id_kampanye`) REFERENCES `promo_kampanye` (`id_kampanye`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_stat_voucher` FOREIGN KEY (`id_voucher`) REFERENCES `voucher` (`id_voucher`) ON DELETE SET NULL;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_review_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `admin_roles` (`id_role`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`id_permission`) REFERENCES `admin_permissions` (`id_permission`) ON DELETE CASCADE;

--
-- Constraints for table `shipment`
--
ALTER TABLE `shipment`
  ADD CONSTRAINT `fk_shipment_address` FOREIGN KEY (`id_alamat`) REFERENCES `address_book` (`id_alamat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_shipment_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `fk_ticket_admin` FOREIGN KEY (`assigned_to`) REFERENCES `administrators` (`id_admin`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ticket_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD CONSTRAINT `fk_reply_admin` FOREIGN KEY (`id_admin`) REFERENCES `administrators` (`id_admin`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reply_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reply_ticket` FOREIGN KEY (`id_ticket`) REFERENCES `support_tickets` (`id_ticket`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `event_update_promo_status` ON SCHEDULE EVERY 5 MINUTE STARTS '2025-11-28 18:18:48' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- promo_kampanye
    UPDATE promo_kampanye
    SET status = CASE
        WHEN status = 'nonaktif' THEN 'nonaktif'
        WHEN NOW() < mulai_pada THEN 'draf'
        WHEN NOW() >= mulai_pada AND NOW() <= selesai_pada THEN 'aktif'
        WHEN NOW() > selesai_pada THEN 'berakhir'
        ELSE status
    END
    WHERE status NOT IN ('nonaktif');

    -- promo_diskon
    UPDATE promo_diskon
    SET status = CASE
        WHEN status = 'nonaktif' THEN 'nonaktif'
        WHEN mulai_pada IS NOT NULL AND NOW() < mulai_pada THEN 'terjadwal'
        WHEN mulai_pada IS NOT NULL AND NOW() >= mulai_pada AND NOW() <= selesai_pada THEN 'aktif'
        WHEN selesai_pada IS NOT NULL AND NOW() > selesai_pada THEN 'berakhir'
        ELSE status
    END
    WHERE status NOT IN ('nonaktif');

    -- voucher
    UPDATE voucher
    SET status = CASE
        WHEN status = 'nonaktif' THEN 'nonaktif'
        WHEN mulai_pada IS NOT NULL AND NOW() < mulai_pada THEN 'terjadwal'
        WHEN mulai_pada IS NOT NULL AND NOW() >= mulai_pada AND NOW() <= selesai_pada THEN 'aktif'
        WHEN selesai_pada IS NOT NULL AND NOW() > selesai_pada THEN 'berakhir'
        ELSE status
    END
    WHERE status NOT IN ('nonaktif');
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
