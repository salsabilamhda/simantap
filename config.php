<?php
// ============================================================
// SIMANTAP — Konfigurasi Database & Base URL
// ============================================================

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'simantap');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Auto-detect BASE_URL agar fleksibel (bisa via php -S localhost:8000 atau Laragon / XAMPP)
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if (basename($scriptDir) === 'actions') {
        $scriptDir = dirname($scriptDir);
    }
    $scriptDir = ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/');
    define('BASE_URL', $scheme . '://' . $host . $scriptDir);
}

// Folder aset sertifikat relatif dari root project
define('UPLOAD_DIR', __DIR__ . '/assets/sertifikat/');
define('UPLOAD_URL', BASE_URL . '/assets/sertifikat/');
