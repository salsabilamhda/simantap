<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$id = (int) post('id');
$kode = post('kode');
$nama = post('nama');
$unitInduk = post('unit_induk', 'UP3 Ponorogo');
$colorHex = post('color_hex', '#2BA8A2');

if (!$id || !$kode || !$nama) {
    flash_set('error', 'ID, kode, dan nama unit wajib diisi.');
    redirect(BASE_URL . '/master-data-unit.php');
}

$exists = db_val('SELECT COUNT(*) FROM unit_layanans WHERE kode = ? AND id <> ?', [$kode, $id]);
if ($exists > 0) {
    flash_set('error', "Kode unit '$kode' sudah digunakan.");
    redirect(BASE_URL . '/master-data-unit.php');
}

db_exec('UPDATE unit_layanans SET kode = ?, nama = ?, unit_induk = ?, color_hex = ?, updated_at = NOW() WHERE id = ?', [$kode, $nama, $unitInduk, $colorHex, $id]);
flash_set('success', "Unit Layanan $nama berhasil diperbarui!");
redirect(BASE_URL . '/master-data-unit.php');