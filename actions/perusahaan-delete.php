<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$id = (int) post('id');
if (!$id) {
    flash_set('error', 'Perusahaan tidak valid.');
    redirect(BASE_URL . '/master-data-perusahaan.php');
}

db_exec('DELETE FROM perusahaans WHERE id = ?', [$id]);
flash_set('success', 'Perusahaan mitra berhasil dihapus.');
redirect(BASE_URL . '/master-data-perusahaan.php');