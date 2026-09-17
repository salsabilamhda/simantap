<?php
// actions/login-process.php
require_once dirname(__DIR__) . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/login.php');
}

csrf_verify();

$username = post('username');
$password = post('password');

if ($username === '' || $password === '') {
    flash_set('error', 'Username dan password wajib diisi.');
    redirect(BASE_URL . '/login.php');
}

// Cari user berdasarkan username ATAU email
$user = db_row(
    "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1",
    [$username, $username]
);

if (!$user || !password_verify($password, $user['password'])) {
    flash_set('error', 'Username atau password yang Anda masukkan salah.');
    redirect(BASE_URL . '/login.php');
}

if (($user['status'] ?? 'Aktif') !== 'Aktif') {
    flash_set('error', 'Akun Anda dinonaktifkan. Silakan hubungi Super Admin untuk mengaktifkan kembali.');
    redirect(BASE_URL . '/login.php');
}

// Berhasil login
auth_login($user);
flash_set('success', 'Selamat datang kembali, ' . ($user['name'] ?: 'Administrator') . '!');
redirect(BASE_URL . '/dashboard.php');
