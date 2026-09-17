<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$id = (int) post('id');
if (!$id) {
    flash_set('error', 'ID admin tidak valid.');
    redirect(BASE_URL . '/pengaturan-admin.php');
}

// Cek keberadaan user
$user = db_row("SELECT * FROM users WHERE id = ?", [$id]);
if (!$user) {
    flash_set('error', 'Akun admin tidak ditemukan.');
    redirect(BASE_URL . '/pengaturan-admin.php');
}

// Cegah penghapusan jika hanya ada 1 admin di sistem
$totalUsers = (int) db_val("SELECT COUNT(*) FROM users");
if ($totalUsers <= 1) {
    flash_set('error', 'Tidak dapat menghapus akun admin terakhir di sistem.');
    redirect(BASE_URL . '/pengaturan-admin.php');
}

db_exec("DELETE FROM users WHERE id = ?", [$id]);

flash_set('success', "Akun admin {$user['name']} berhasil dihapus.");
redirect(BASE_URL . '/pengaturan-admin.php');
