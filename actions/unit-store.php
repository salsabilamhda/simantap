<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$kode      = post('kode');
$nama      = post('nama');
$unitInduk = post('unit_induk', 'UP3 Ponorogo');
$colorHex  = post('color_hex', '#2BA8A2');

if (empty($kode) || empty($nama)) {
    flash_set('error', 'Kode dan Nama unit tidak boleh kosong.');
    redirect(BASE_URL . '/master-data-unit.php');
}

// Cek duplikasi kode
$exists = db_val("SELECT COUNT(*) FROM unit_layanans WHERE kode = ?", [$kode]);
if ($exists > 0) {
    flash_set('error', "Kode unit '$kode' sudah digunakan.");
    redirect(BASE_URL . '/master-data-unit.php');
}

db_exec(
    "INSERT INTO unit_layanans (kode, nama, unit_induk, color_hex, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())",
    [$kode, $nama, $unitInduk, $colorHex]
);

flash_set('success', "Unit Layanan $nama berhasil ditambahkan!");
redirect(BASE_URL . '/master-data-unit.php');
