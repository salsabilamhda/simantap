<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$nama            = post('nama');
$nomorPerjanjian = post('nomor_perjanjian');

if (empty($nama)) {
    flash_set('error', 'Nama perusahaan tidak boleh kosong.');
    redirect(BASE_URL . '/master-data-perusahaan.php');
}

db_exec(
    "INSERT INTO perusahaans (nama, nomor_perjanjian, created_at, updated_at) VALUES (?, ?, NOW(), NOW())",
    [$nama, $nomorPerjanjian ?: null]
);

flash_set('success', "Perusahaan $nama berhasil ditambahkan!");
redirect(BASE_URL . '/master-data-perusahaan.php');
