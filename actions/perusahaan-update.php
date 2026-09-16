<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$id = (int) post('id');
$nama = post('nama');
$nomorPerjanjian = post('nomor_perjanjian');

if (!$id || !$nama) {
    flash_set('error', 'ID dan nama perusahaan wajib diisi.');
    redirect(BASE_URL . '/master-data-perusahaan.php');
}

db_exec('UPDATE perusahaans SET nama = ?, nomor_perjanjian = ?, updated_at = NOW() WHERE id = ?', [$nama, $nomorPerjanjian ?: null, $id]);
flash_set('success', "Perusahaan $nama berhasil diperbarui!");
redirect(BASE_URL . '/master-data-perusahaan.php');