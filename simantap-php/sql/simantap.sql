-- ============================================================
-- SIMANTAP — Schema Database MySQL
-- Jalankan file ini di phpMyAdmin untuk setup fresh install
-- ============================================================

CREATE DATABASE IF NOT EXISTS `simantap` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `simantap`;

-- ============================================================
-- Tabel: unit_layanans (Master Data)
-- ============================================================
CREATE TABLE IF NOT EXISTS `unit_layanans` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode`       VARCHAR(50)     NOT NULL UNIQUE,
  `nama`       VARCHAR(100)    NOT NULL,
  `unit_induk` VARCHAR(100)    DEFAULT NULL,
  `color_hex`  VARCHAR(20)     DEFAULT '#2BA8A2',
  `created_at` TIMESTAMP       NULL DEFAULT NULL,
  `updated_at` TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: perusahaans (Master Data)
-- ============================================================
CREATE TABLE IF NOT EXISTS `perusahaans` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`               VARCHAR(255)    NOT NULL,
  `nomor_perjanjian`   VARCHAR(255)    DEFAULT NULL,
  `created_at`         TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`         TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: tenaga_kerjas (Data Utama)
-- ============================================================
CREATE TABLE IF NOT EXISTS `tenaga_kerjas` (
  `id`                        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_urut`                   VARCHAR(20)     DEFAULT NULL,
  `nomor_perjanjian`          VARCHAR(255)    DEFAULT NULL,
  `nama_perusahaan`           VARCHAR(255)    DEFAULT NULL,
  `nama`                      VARCHAR(255)    NOT NULL,
  `nik`                       VARCHAR(20)     DEFAULT NULL,
  `tempat_lahir`              VARCHAR(255)    DEFAULT NULL,
  `tanggal_lahir`             DATE            DEFAULT NULL,
  `pendidikan_terakhir`       VARCHAR(100)    DEFAULT NULL,
  `jurusan`                   VARCHAR(255)    DEFAULT NULL,
  `no_telepon`                VARCHAR(50)     DEFAULT NULL,
  `email`                     VARCHAR(255)    DEFAULT NULL,
  `jenis_kelamin`             VARCHAR(20)     DEFAULT 'LAKI',
  `alamat_domisili`           TEXT            DEFAULT NULL,
  `kota_kabupaten`            VARCHAR(100)    DEFAULT NULL,
  `provinsi`                  VARCHAR(100)    DEFAULT NULL,
  `jabatan_terakhir`          VARCHAR(255)    DEFAULT NULL,
  `fungsi_pekerjaan`          VARCHAR(255)    DEFAULT NULL,
  `unit`                      VARCHAR(255)    DEFAULT NULL,
  `unit_layanan_id`           BIGINT UNSIGNED DEFAULT NULL,
  `nomor_bpjs_kesehatan`      VARCHAR(100)    DEFAULT NULL,
  `nomor_bpjs_ketenagakerjaan`VARCHAR(100)    DEFAULT NULL,
  `nomor_dplk`                VARCHAR(100)    DEFAULT NULL,
  `bank_dplk`                 VARCHAR(100)    DEFAULT NULL,
  `no_perjanjian_kerja`       VARCHAR(255)    DEFAULT NULL,
  `tanggal_masuk_kerja`       DATE            DEFAULT NULL,
  `status_tenaga_kerja`       VARCHAR(20)     DEFAULT 'PKWTT',
  `skema_tenaga_kerja`        VARCHAR(100)    DEFAULT 'PEMBORONGAN',
  `created_at`                TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`                TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_unit_layanan` (`unit_layanan_id`),
  CONSTRAINT `fk_unit_layanan` FOREIGN KEY (`unit_layanan_id`) REFERENCES `unit_layanans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: sertifikasis (Sertifikat per Tenaga Kerja)
-- ============================================================
CREATE TABLE IF NOT EXISTS `sertifikasis` (
  `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenaga_kerja_id`      BIGINT UNSIGNED NOT NULL,
  `nomor_sertifikat`     VARCHAR(255)    DEFAULT NULL,
  `judul_sertifikasi`    VARCHAR(255)    NOT NULL,
  `gambar_sertifikat_url`VARCHAR(500)    DEFAULT NULL,
  `file_path`            VARCHAR(500)    DEFAULT NULL,
  `tanggal_terbit`       DATE            DEFAULT NULL,
  `tanggal_kadaluarsa`   DATE            DEFAULT NULL,
  `created_at`           TIMESTAMP       NULL DEFAULT NULL,
  `updated_at`           TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sertifikasi_tk` (`tenaga_kerja_id`),
  CONSTRAINT `fk_sertifikasi_tk` FOREIGN KEY (`tenaga_kerja_id`) REFERENCES `tenaga_kerjas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: users (Admin & Akses)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)    NOT NULL,
  `email`      VARCHAR(255)    NOT NULL UNIQUE,
  `password`   VARCHAR(255)    NOT NULL,
  `role`       VARCHAR(50)     DEFAULT 'admin',
  `status`     VARCHAR(50)     DEFAULT 'Aktif',
  `created_at` TIMESTAMP       NULL DEFAULT NULL,
  `updated_at` TIMESTAMP       NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed: Unit Layanan Awal (5 Unit)
-- ============================================================
INSERT IGNORE INTO `unit_layanans` (`kode`, `nama`, `unit_induk`, `color_hex`, `created_at`, `updated_at`) VALUES
('UP3_PONOROGO',  'UP3 Ponorogo',  'UP3 Ponorogo', '#1E8C86', NOW(), NOW()),
('ULP_BALONG',    'ULP Balong',    'UP3 Ponorogo', '#2BA8A2', NOW(), NOW()),
('ULP_PACITAN',   'ULP Pacitan',   'UP3 Ponorogo', '#FFD23F', NOW(), NOW()),
('ULP_PONOROGO',  'ULP Ponorogo',  'UP3 Ponorogo', '#5DADE2', NOW(), NOW()),
('ULP_TRENGGALEK','ULP Trenggalek','UP3 Ponorogo', '#EF6C4A', NOW(), NOW());

-- ============================================================
-- Seed: Perusahaan Mitra Awal
-- ============================================================
INSERT IGNORE INTO `perusahaans` (`nama`, `nomor_perjanjian`, `created_at`, `updated_at`) VALUES
('PT ANUGERAH PUTRA PERMANA', '1211,Pj/DAN,00,07/F04000000/2024', NOW(), NOW()),
('PT MITRA KARYA MANDIRI',    NULL, NOW(), NOW()),
('PT BUMI NUSANTARA JAYA',    NULL, NOW(), NOW());
