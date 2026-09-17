<?php
// actions/logout.php
require_once dirname(__DIR__) . '/db.php';

auth_logout();

// Mulai session baru khusus untuk menyampaikan flash message logout
session_start_safe();
flash_set('success', 'Anda telah berhasil keluar dari sistem.');

redirect(BASE_URL . '/login.php');
