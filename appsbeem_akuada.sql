-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Okt 2026 pada 05.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `appsbeem_akuada`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `icon` varchar(100) NOT NULL DEFAULT 'category',
  `description` text DEFAULT NULL,
  `sort_order` int(5) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Alat Tulis Kantor & Kertas', 'alat-tulis-kantor-kertas', 'description', 'Kertas A4/F4, pulpen korporat, ordner, binder, toner, dan perlengkapan meja kerja kantor.', 1, '2026-10-27 14:12:11', '2026-10-27 14:12:11'),
(2, 'Komputer & IT Infrastructure', 'komputer-it-infrastructure', 'devices', 'Laptop bisnis enterprise, PC Desktop, Server, Rackmount, Networking Cisco/Mikrotik, UPS industri.', 2, '2026-10-27 14:12:11', '2026-10-27 14:12:11'),
(3, 'Perlengkapan K3 & Safety APD', 'k3-safety-apd', 'health_and_safety', 'Helm proyek SNI, Rompi safety reflektif, Safety shoes, Kacamata pelindung, APAR, P3K kit.', 3, '2026-10-27 14:12:11', '2026-10-27 14:12:11'),
(4, 'Hygiene, Cleaning & Fasilitas', 'hygiene-cleaning-fasilitas', 'cleaning_services', 'Sabun cuci tangan curah 5L, cairan disinfektan, mesin vacuum industri, tempat sampah pilah kantor.', 4, '2026-10-27 14:12:11', '2026-10-27 14:12:11'),
(5, 'Mesin Kantor & MRO Maintenance', 'mesin-kantor-mro', 'precision_manufacturing', 'Printer heavy-duty, mesin penghancur kertas (paper shredder), laminator, perkakas teknisi, power tools.', 5, '2026-10-27 14:12:11', '2026-10-27 14:12:11'),
(6, 'Pantry & Breakroom Korporat', 'pantry-breakroom-korporat', 'coffee', 'Kopi biji/bubuk hotel & resto, dispenser galon otomatis, cangkir dan peralatan makan pantry korporat.', 6, '2026-10-27 14:12:11', '2026-10-27 14:12:11'),
(7, 'Furnitur & Workstation Kantor', 'furnitur-workstation-kantor', 'chair', 'Meja kantor modular, kursi kerja ergonomis bergaransi resmi, lemari arsip metal anti-karat.', 7, '2026-10-27 14:12:11', '2026-10-27 14:12:11'),
(8, 'Room Aminities', 'room-aminities', 'room', 'Kebutuhan aminitis kamar hotel', 1, '2026-10-29 14:27:19', '2026-10-29 14:27:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `companies`
--

CREATE TABLE `companies` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` enum('vendor','buyer') NOT NULL DEFAULT 'buyer',
  `company_name` varchar(255) NOT NULL,
  `legal_type` varchar(50) NOT NULL DEFAULT 'PT',
  `npwp` varchar(50) DEFAULT NULL,
  `nib` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `pic_name` varchar(150) DEFAULT NULL,
  `pic_phone` varchar(50) DEFAULT NULL,
  `pic_email` varchar(150) DEFAULT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bank_account_holder` varchar(150) DEFAULT NULL,
  `credit_limit_top` decimal(15,2) NOT NULL DEFAULT 100000000.00,
  `credit_used_top` decimal(15,2) NOT NULL DEFAULT 0.00,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 2.50,
  `is_verified` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `companies`
--

INSERT INTO `companies` (`id`, `type`, `company_name`, `legal_type`, `npwp`, `nib`, `address`, `city`, `province`, `phone`, `email`, `pic_name`, `pic_phone`, `pic_email`, `bank_name`, `bank_account_number`, `bank_account_holder`, `credit_limit_top`, `credit_used_top`, `commission_rate`, `is_verified`, `created_at`, `updated_at`) VALUES
(1, 'vendor', 'Caltic Baru', 'CV', '000000000000000', '000000000000000', 'Yogyakarta', 'Yogyakarta', 'DKI Jakarta', '089510101008', 'calticbaru@gmail.com', 'Pak Haryadi', '089510101008', 'calticbaru@gmail.com', NULL, NULL, NULL, 0.00, 0.00, 2.50, 1, '2026-10-29 14:21:57', '2026-10-29 14:21:57'),
(2, 'buyer', 'Hotel Dafam Semarang', 'PT', '0000000000000000', '0000000000000000', 'Jl. Imam Bonjol 188 Semarang', 'Semarang', 'DKI Jakarta', '', 'puch@dafamsemarang.com', 'Fajar Kunn', '089510101008', 'puch@dafamsemarang.com', NULL, NULL, NULL, 1000000000.00, 0.00, 0.00, 1, '2026-10-29 14:24:01', '2026-10-29 14:24:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-10-27-000001', 'App\\Database\\Migrations\\CreateAkuAdaTables', 'default', 'App', 1793110167, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) UNSIGNED NOT NULL,
  `order_number` varchar(100) NOT NULL,
  `po_number` varchar(100) DEFAULT NULL,
  `customer_company_id` int(11) UNSIGNED NOT NULL,
  `vendor_company_id` int(11) UNSIGNED NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ppn_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_vendor_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vendor_net_payout` decimal(15,2) NOT NULL DEFAULT 0.00,
  `shipping_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('top_14','top_30','top_60','top_90','corporate_po','bank_va','escrow') NOT NULL DEFAULT 'top_30',
  `payment_status` enum('unpaid','pending_approval','paid','due','overdue') NOT NULL DEFAULT 'unpaid',
  `order_status` varchar(50) DEFAULT 'po_issued',
  `tax_invoice_required` tinyint(1) NOT NULL DEFAULT 1,
  `customer_notes` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `bast_number` varchar(100) DEFAULT NULL,
  `invoice_number` varchar(100) DEFAULT NULL,
  `vendor_invoice_number` varchar(100) DEFAULT NULL,
  `tax_invoice_number` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `vendor_fee_percent` decimal(5,2) NOT NULL DEFAULT 2.50,
  `vendor_fee_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vendor_net_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ppn_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_status_logs`
--

CREATE TABLE `order_status_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `vendor_company_id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `uom` varchar(50) NOT NULL DEFAULT 'Pcs',
  `description` text DEFAULT NULL,
  `spec_details` text DEFAULT NULL,
  `price_regular` decimal(15,2) NOT NULL,
  `moq` int(11) NOT NULL DEFAULT 1,
  `weight_gram` int(11) NOT NULL DEFAULT 1000,
  `stock` int(11) NOT NULL DEFAULT 100,
  `lead_time_days` int(5) NOT NULL DEFAULT 2,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 1,
  `ppn_percent` decimal(5,2) NOT NULL DEFAULT 11.00,
  `vendor_fee_percent` decimal(5,2) DEFAULT 2.50,
  `tkdn_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_sample_available` tinyint(1) NOT NULL DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','draft') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_price_tiers`
--

CREATE TABLE `product_price_tiers` (
  `id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `min_qty` int(11) NOT NULL,
  `max_qty` int(11) DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rfqs`
--

CREATE TABLE `rfqs` (
  `id` int(11) UNSIGNED NOT NULL,
  `rfq_number` varchar(100) NOT NULL,
  `customer_company_id` int(11) UNSIGNED NOT NULL,
  `vendor_company_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `target_delivery_date` date DEFAULT NULL,
  `status` enum('submitted','in_review','quoted','negotiating','approved','rejected','po_issued') NOT NULL DEFAULT 'submitted',
  `total_estimate` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rfq_items`
--

CREATE TABLE `rfq_items` (
  `id` int(11) UNSIGNED NOT NULL,
  `rfq_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `uom` varchar(50) NOT NULL,
  `target_unit_price` decimal(15,2) DEFAULT NULL,
  `vendor_offered_price` decimal(15,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) UNSIGNED NOT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(50) NOT NULL DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES
(1, 'general', 'app_name', 'AkuAda', 'string', 'Nama Resmi Marketplace B2B', '2026-10-27 14:12:11'),
(2, 'general', 'app_tagline', 'Solusi Terlengkap Pengadaan Operasional Perusahaan Anda', 'string', 'Tagline Utama', '2026-10-27 14:12:11'),
(3, 'general', 'contact_email', 'b2b@akuada.co.id', 'string', 'Email Layanan Pengadaan Korporat', '2026-10-27 14:12:11'),
(4, 'general', 'contact_phone', '(021) 5088-9900', 'string', 'Call Center Pengadaan B2B', '2026-10-27 14:12:11'),
(5, 'tax', 'ppn_rate', '11', 'number', 'Tarif PPN Standar (%)', '2026-10-27 14:12:11'),
(6, 'tax', 'pph_22_rate', '1.5', 'number', 'Tarif PPh Pasal 22 untuk Pengadaan Instansi / BUMN (%)', '2026-10-27 14:12:11'),
(7, 'payment', 'allowed_top_terms', '14,30,60,90', 'string', 'Opsi Term of Payment (Hari)', '2026-10-27 14:12:11'),
(8, 'payment', 'default_credit_limit', '100000000', 'number', 'Plafon Kredit Default Pengajuan Baru (Rp)', '2026-10-27 14:12:11'),
(9, 'design', 'primary_color', '#6366f1', 'string', 'Warna Utama Ungu/Indigo', '2026-10-27 14:12:11'),
(10, 'design', 'secondary_color', '#2563eb', 'string', 'Warna Kedua Biru', '2026-10-27 14:12:11'),
(11, 'design', 'accent_color', '#ec4899', 'string', 'Warna Aksen Pink/Magenta', '2026-10-27 14:12:11'),
(12, 'fee', 'vendor_fee_default_percent', '2.5', 'number', 'Tarif Platform Fee Vendor Standar (%)', '2026-10-27 22:14:49'),
(13, 'fee', 'vendor_fee_min_amount', '0', 'number', 'Nominal Minimum Fee Platform per Transaksi (Rp)', '2026-10-27 22:14:49'),
(14, 'fee', 'vendor_fee_calc_basis', 'dpp', 'string', 'Dasar Pengenaan Fee (dpp: Sebelum PPN, gross: Setelah PPN)', '2026-10-27 22:14:49'),
(15, 'fee', 'vendor_fee_settlement_term', 'H+3 BAST', 'string', 'Jadwal Settlement Hak Bersih ke Rekening Vendor', '2026-10-27 22:14:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','vendor','customer') NOT NULL DEFAULT 'customer',
  `phone` varchar(50) DEFAULT NULL,
  `company_id` int(11) UNSIGNED DEFAULT NULL,
  `position` varchar(100) DEFAULT 'Procurement Lead',
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `company_id`, `position`, `avatar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator AkuAda', 'admin@akuada.co.id', '$2y$10$.qc01UlHD3rS8Z7sKNGriu7SQc1JMrc62JaYIJTtKbB2RdmaKGquW', 'admin', '081100011122', NULL, 'Head of B2B Operations', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80', 'active', '2026-10-27 14:12:11', '2026-10-27 14:12:11'),
(2, 'Pak Haryadi (Caltic Baru)', 'calticbaru@gmail.com', '$2y$10$iDLCLrYkKDyhZV67t4UTFOMUbUgKHu2CKnqf/RqpHWuTxrc8xWndy', 'vendor', '089510101008', 1, 'Supplier Lead', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80', 'active', '2026-10-29 14:21:57', '2026-10-29 14:21:57'),
(3, 'Fajar Kunn (Hotel Dafam Semarang)', 'puch@dafamsemarang.com', '$2y$10$68SnoLLGtwmOjov/IPNJte.ntm/VYDUsyuwP90PGx6ILvw2KibayC', 'customer', '089510101008', 2, 'Procurement ', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80', 'active', '2026-10-29 14:24:02', '2026-10-29 14:24:02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indeks untuk tabel `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indeks untuk tabel `product_price_tiers`
--
ALTER TABLE `product_price_tiers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rfqs`
--
ALTER TABLE `rfqs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rfq_number` (`rfq_number`);

--
-- Indeks untuk tabel `rfq_items`
--
ALTER TABLE `rfq_items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `order_status_logs`
--
ALTER TABLE `order_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `product_price_tiers`
--
ALTER TABLE `product_price_tiers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rfqs`
--
ALTER TABLE `rfqs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rfq_items`
--
ALTER TABLE `rfq_items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
