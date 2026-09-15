<?php
// ============================================================
// SIMANTAP — Konfigurasi Database
// Sesuaikan dengan konfigurasi server/hosting Anda
// ============================================================

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'simantap');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Base URL (tanpa trailing slash)
// Contoh lokal: 'http://localhost/simantap-php'
// Contoh server: 'https://namadomain.com/simantap'
define('BASE_URL', 'http://localhost/simantap-php');

// Folder upload relatif dari root project
define('UPLOAD_DIR', __DIR__ . '/uploads/sertifikat/');
define('UPLOAD_URL', BASE_URL . '/uploads/sertifikat/');
