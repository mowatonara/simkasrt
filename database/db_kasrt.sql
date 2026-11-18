-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Schema-only export for local development.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simkasrt`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_warga`
--

CREATE TABLE `data_warga` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(120) NOT NULL,
  `nik` char(16) NOT NULL,
  `no_hp` varchar(25) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `blok` varchar(20) NOT NULL,
  `no_rumah` varchar(20) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `diubah_pada` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_iuran`
--

CREATE TABLE `jenis_iuran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nominal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `frekuensi` enum('bulanan','sekali') NOT NULL DEFAULT 'bulanan',
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tagihan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pesan` varchar(500) NOT NULL,
  `dibaca_pada` datetime DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tagihan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `data_warga_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_iuran_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `metode` enum('transfer','qris','tunai') NOT NULL,
  `jumlah` decimal(14,2) NOT NULL,
  `bukti_file` varchar(255) NOT NULL,
  `status` enum('menunggu','terverifikasi','ditolak') NOT NULL DEFAULT 'menunggu',
  `alasan_penolakan` varchar(500) DEFAULT NULL,
  `dibuat_oleh` bigint(20) UNSIGNED NOT NULL,
  `diverifikasi_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diverifikasi_pada` datetime DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

CREATE TABLE `tagihan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data_warga_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_iuran_id` bigint(20) UNSIGNED NOT NULL,
  `periode` date NOT NULL COMMENT 'Gunakan tanggal pertama pada bulan tagihan',
  `jatuh_tempo` date NOT NULL,
  `nominal` decimal(14,2) NOT NULL,
  `status` enum('belum_bayar','menunggu_verifikasi','lunas') NOT NULL DEFAULT 'belum_bayar',
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_kas`
--

CREATE TABLE `transaksi_kas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pembayaran_id` bigint(20) UNSIGNED DEFAULT NULL,
  `arah` enum('masuk','keluar') NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `deskripsi` varchar(500) NOT NULL,
  `jumlah` decimal(14,2) NOT NULL,
  `tanggal` date NOT NULL,
  `dicatat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data_warga_id` bigint(20) UNSIGNED DEFAULT NULL,
  `username` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nama` varchar(120) NOT NULL,
  `role` enum('admin','warga') NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `diubah_pada` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_warga`
--
ALTER TABLE `data_warga`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_data_warga_nik` (`nik`),
  ADD KEY `idx_data_warga_nama` (`nama`),
  ADD KEY `idx_data_warga_lokasi` (`blok`,`no_rumah`);

--
-- Indexes for table `jenis_iuran`
--
ALTER TABLE `jenis_iuran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_jenis_iuran_nama` (`nama`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifikasi_user_baca` (`user_id`,`dibaca_pada`,`dibuat_pada`),
  ADD KEY `fk_notifikasi_tagihan` (`tagihan_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pembayaran_status` (`status`,`dibuat_pada`),
  ADD KEY `idx_pembayaran_warga` (`data_warga_id`,`tanggal_bayar`),
  ADD KEY `idx_pembayaran_tagihan` (`tagihan_id`,`status`),
  ADD KEY `fk_pembayaran_jenis_iuran` (`jenis_iuran_id`),
  ADD KEY `fk_pembayaran_pembuat` (`dibuat_oleh`),
  ADD KEY `fk_pembayaran_verifikator` (`diverifikasi_oleh`);

--
-- Indexes for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_tagihan_warga_iuran_periode` (`data_warga_id`,`jenis_iuran_id`,`periode`),
  ADD KEY `idx_tagihan_status_periode` (`status`,`periode`),
  ADD KEY `fk_tagihan_jenis_iuran` (`jenis_iuran_id`),
  ADD KEY `fk_tagihan_pembuat` (`dibuat_oleh`);

--
-- Indexes for table `transaksi_kas`
--
ALTER TABLE `transaksi_kas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_transaksi_pembayaran` (`pembayaran_id`),
  ADD KEY `idx_transaksi_tanggal_arah` (`tanggal`,`arah`),
  ADD KEY `fk_transaksi_user` (`dicatat_oleh`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_users_username` (`username`),
  ADD UNIQUE KEY `uk_users_data_warga` (`data_warga_id`),
  ADD KEY `idx_users_role` (`role`,`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_warga`
--
ALTER TABLE `data_warga`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis_iuran`
--
ALTER TABLE `jenis_iuran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi_kas`
--
ALTER TABLE `transaksi_kas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_notifikasi_tagihan` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notifikasi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_jenis_iuran` FOREIGN KEY (`jenis_iuran_id`) REFERENCES `jenis_iuran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembayaran_pembuat` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembayaran_tagihan` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembayaran_verifikator` FOREIGN KEY (`diverifikasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembayaran_warga` FOREIGN KEY (`data_warga_id`) REFERENCES `data_warga` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `fk_tagihan_data_warga` FOREIGN KEY (`data_warga_id`) REFERENCES `data_warga` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tagihan_jenis_iuran` FOREIGN KEY (`jenis_iuran_id`) REFERENCES `jenis_iuran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tagihan_pembuat` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_kas`
--
ALTER TABLE `transaksi_kas`
  ADD CONSTRAINT `fk_transaksi_pembayaran` FOREIGN KEY (`pembayaran_id`) REFERENCES `pembayaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`dicatat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_data_warga` FOREIGN KEY (`data_warga_id`) REFERENCES `data_warga` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
