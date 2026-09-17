<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$name     = post('name');
$email    = post('email');
$password = post('password');
$role     = post('role', 'admin');
$status   = post('status', 'Aktif');

if (empty($name) || empty($email) || empty($password)) {
    flash_set('error', 'Nama, email, dan password wajib diisi.');
    redirect(BASE_URL . '/pengaturan-admin.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash_set('error', 'Format email tidak valid.');
    redirect(BASE_URL . '/pengaturan-admin.php');
}

if (strlen($password) < 6) {
    flash_set('error', 'Password minimal 6 karakter.');
    redirect(BASE_URL . '/pengaturan-admin.php');
}

// Cek email duplikat
$exists = db_val("SELECT COUNT(*) FROM users WHERE email = ?", [$email]);
if ($exists > 0) {
    flash_set('error', "Email $email sudah digunakan.");
    redirect(BASE_URL . '/pengaturan-admin.php');
}

if (!in_array($role, ['admin', 'superadmin'])) {
    $role = 'admin';
}

if (!in_array($status, ['Aktif', 'Nonaktif'])) {
    $status = 'Aktif';
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

db_exec(
    "INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
    [$name, $email, $hashedPassword, $role, $status]
);

flash_set('success', "Akun admin $name berhasil dibuat!");
redirect(BASE_URL . '/pengaturan-admin.php');
