<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$id       = (int) post('id');
$name     = post('name');
$email    = post('email');
$password = post('password');
$role     = post('role', 'admin');
$status   = post('status', 'Aktif');

if (!$id || empty($name) || empty($email)) {
    flash_set('error', 'ID, nama, dan email wajib diisi.');
    redirect(BASE_URL . '/pengaturan-admin.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash_set('error', 'Format email tidak valid.');
    redirect(BASE_URL . '/pengaturan-admin.php');
}

// Cek keberadaan user
$user = db_row("SELECT * FROM users WHERE id = ?", [$id]);
if (!$user) {
    flash_set('error', 'Akun admin tidak ditemukan.');
    redirect(BASE_URL . '/pengaturan-admin.php');
}

// Cek email duplikat selain dirinya sendiri
$exists = db_val("SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?", [$email, $id]);
if ($exists > 0) {
    flash_set('error', "Email $email sudah digunakan oleh akun lain.");
    redirect(BASE_URL . '/pengaturan-admin.php');
}

if (!in_array($role, ['admin', 'superadmin'])) {
    $role = 'admin';
}

if (!in_array($status, ['Aktif', 'Nonaktif'])) {
    $status = 'Aktif';
}

// Jika password diisi, validasi dan update password
if (!empty($password)) {
    if (strlen($password) < 6) {
        flash_set('error', 'Password baru minimal 6 karakter.');
        redirect(BASE_URL . '/pengaturan-admin.php');
    }
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    db_exec(
        "UPDATE users SET name = ?, email = ?, password = ?, role = ?, status = ?, updated_at = NOW() WHERE id = ?",
        [$name, $email, $hashedPassword, $role, $status, $id]
    );
} else {
    db_exec(
        "UPDATE users SET name = ?, email = ?, role = ?, status = ?, updated_at = NOW() WHERE id = ?",
        [$name, $email, $role, $status, $id]
    );
}

flash_set('success', "Akun admin $name berhasil diperbarui!");
redirect(BASE_URL . '/pengaturan-admin.php');
