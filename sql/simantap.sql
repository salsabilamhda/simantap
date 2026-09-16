-- SIMANTAP database schema
-- Compatible with MySQL 5.7+ / MariaDB 10.4+

CREATE DATABASE IF NOT EXISTS `simantap`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `simantap`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) NOT NULL DEFAULT 'admin',
  `status` VARCHAR(20) NOT NULL DEFAULT 'Aktif',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `unit_layanans` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode` VARCHAR(30) NOT NULL,
  `nama` VARCHAR(150) NOT NULL,
  `unit_induk` VARCHAR(150) DEFAULT NULL,
  `color_hex` VARCHAR(7) NOT NULL DEFAULT '#2BA8A2',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_unit_layanans_kode` (`kode`),
  KEY `idx_unit_layanans_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `perusahaans` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(200) NOT NULL,
  `nomor_perjanjian` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_perusahaans_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tenaga_kerjas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_urut` INT UNSIGNED DEFAULT NULL,
  `nomor_perjanjian` VARCHAR(100) DEFAULT NULL,
  `nama_perusahaan` VARCHAR(200) DEFAULT NULL,
  `nama` VARCHAR(200) NOT NULL,
  `nik` VARCHAR(30) DEFAULT NULL,
  `tempat_lahir` VARCHAR(100) DEFAULT NULL,
  `tanggal_lahir` DATE DEFAULT NULL,
  `pendidikan_terakhir` VARCHAR(20) DEFAULT NULL,
  `jurusan` VARCHAR(150) DEFAULT NULL,
  `no_telepon` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(190) DEFAULT NULL,
  `jenis_kelamin` VARCHAR(20) DEFAULT NULL,
  `alamat_domisili` TEXT DEFAULT NULL,
  `kota_kabupaten` VARCHAR(100) DEFAULT NULL,
  `provinsi` VARCHAR(100) DEFAULT NULL,
  `jabatan_terakhir` VARCHAR(150) DEFAULT NULL,
  `fungsi_pekerjaan` VARCHAR(150) DEFAULT NULL,
  `unit` VARCHAR(150) DEFAULT NULL,
  `unit_layanan_id` INT UNSIGNED DEFAULT NULL,
  `nomor_bpjs_kesehatan` VARCHAR(50) DEFAULT NULL,
  `nomor_bpjs_ketenagakerjaan` VARCHAR(50) DEFAULT NULL,
  `nomor_dplk` VARCHAR(50) DEFAULT NULL,
  `bank_dplk` VARCHAR(100) DEFAULT NULL,
  `no_perjanjian_kerja` VARCHAR(100) DEFAULT NULL,
  `tanggal_masuk_kerja` DATE DEFAULT NULL,
  `status_tenaga_kerja` VARCHAR(30) NOT NULL DEFAULT 'PKWTT',
  `skema_tenaga_kerja` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tenaga_kerjas_nama` (`nama`),
  KEY `idx_tenaga_kerjas_nik` (`nik`),
  KEY `idx_tenaga_kerjas_unit_layanan` (`unit_layanan_id`),
  KEY `idx_tenaga_kerjas_status` (`status_tenaga_kerja`),
  CONSTRAINT `fk_tenaga_kerjas_unit_layanan`
    FOREIGN KEY (`unit_layanan_id`) REFERENCES `unit_layanans` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sertifikasis` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenaga_kerja_id` INT UNSIGNED NOT NULL,
  `nomor_sertifikat` VARCHAR(150) DEFAULT NULL,
  `judul_sertifikasi` VARCHAR(255) DEFAULT NULL,
  `gambar_sertifikat_url` VARCHAR(500) DEFAULT NULL,
  `tanggal_terbit` DATE DEFAULT NULL,
  `tanggal_kadaluarsa` DATE DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sertifikasis_tenaga_kerja` (`tenaga_kerja_id`),
  CONSTRAINT `fk_sertifikasis_tenaga_kerja`
    FOREIGN KEY (`tenaga_kerja_id`) REFERENCES `tenaga_kerjas` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sinkronisasi untuk database lama: judul sertifikat boleh kosong saat upload gambar.
ALTER TABLE `sertifikasis`
  MODIFY `judul_sertifikasi` VARCHAR(255) DEFAULT NULL;

-- Seed data awal agar aplikasi dapat langsung digunakan setelah import.
INSERT IGNORE INTO `users` (`name`, `email`, `password`, `role`, `status`)
VALUES ('Administrator SIMANTAP', 'admin@simantap.id', '$2y$10$4BFGZ9Eb0s8K3XuiIM5AMOf4mc.D215nuQ3vGPWDhkBp0R8zKuOf6', 'superadmin', 'Aktif');

INSERT IGNORE INTO `unit_layanans` (`kode`, `nama`, `unit_induk`, `color_hex`)
VALUES ('UP3-PON', 'UP3 Ponorogo', 'UP3 Ponorogo', '#2BA8A2');

INSERT IGNORE INTO `perusahaans` (`nama`, `nomor_perjanjian`)
VALUES ('Contoh Perusahaan Mitra', NULL);
