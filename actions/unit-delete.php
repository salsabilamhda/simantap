<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$id = (int) post('id');
if (!$id) {
    flash_set('error', 'Unit tidak valid.');
    redirect(BASE_URL . '/master-data-unit.php');
}

db_exec('DELETE FROM unit_layanans WHERE id = ?', [$id]);
flash_set('success', 'Unit Layanan berhasil dihapus.');
redirect(BASE_URL . '/master-data-unit.php');