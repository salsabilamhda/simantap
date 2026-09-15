<?php
// includes/layout-head.php
// Panggil di awal setiap halaman: include 'includes/layout-head.php';
// Variabel yang harus diset sebelum include ini:
//   $pageTitle (string) — judul halaman
require_once dirname(__DIR__) . '/db.php';
session_start_safe();
$flash = flash_get();
$pageTitle = $pageTitle ?? 'SIMANTAP';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> — SIMANTAP | Sistem Manajemen Data Tenaga Kerja</title>
    <meta name="description" content="SIMANTAP — Sistem Manajemen Data Tenaga Kerja Outsourcing PLN UP3 & ULP Ponorogo">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        primary: '#2BA8A2', primaryDark: '#1E8C86', primaryLight: '#3CC4BD',
                        primaryBg: '#E8F6F5', accentGold: '#FFD23F', accentGoldDark: '#C9A227',
                        accentCream: '#FFF8E7', coral: '#EF6C4A', sky: '#5DADE2', surfaceBase: '#EFF8F7',
                    },
                    boxShadow: {
                        'card-custom': '0 4px 20px -2px rgba(43, 168, 162, 0.08)',
                        'teal-glow': '0 4px 20px 0 rgba(43, 168, 162, 0.30)',
                        'gold-glow': '0 4px 20px 0 rgba(255, 210, 63, 0.40)',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #EFF8F7; font-family: 'Plus Jakarta Sans', sans-serif; }
        .btn-gold-primary {
            background: linear-gradient(135deg, #FFD23F 0%, #FFC107 100%);
            color: #2C3E50; font-weight: 800; border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(255,210,63,0.4); transition: all 0.2s ease;
            display:inline-flex; align-items:center;
        }
        .btn-gold-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(255,210,63,0.55); }
        .btn-teal-outline {
            background: #FFFFFF; color: #1E8C86; font-weight: 700;
            border: 1.5px solid #2BA8A2; border-radius: 9999px; transition: all 0.2s ease;
            display:inline-flex; align-items:center;
        }
        .btn-teal-outline:hover { background: #E8F6F5; }
    </style>
</head>
<body class="min-h-screen text-gray-800 flex flex-col md:flex-row">
