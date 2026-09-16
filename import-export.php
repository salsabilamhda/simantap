<?php
// import-export.php
// Dialihkan langsung ke halaman Data Tenaga Kerja karena fitur Import & Export telah disatukan di sana.
require_once __DIR__ . '/config.php';
header('Location: ' . BASE_URL . '/tenaga-kerja.php');
exit;
