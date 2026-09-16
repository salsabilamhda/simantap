<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$id = (int) post('id');
if (!$id) {
    flash_set('error', 'ID tidak valid.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

$tk = db_row("SELECT nama FROM tenaga_kerjas WHERE id = ?", [$id]);
if (!$tk) {
    flash_set('error', 'Data tenaga kerja tidak ditemukan.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

// Hapus file sertifikat terkait
$serts = db_query("SELECT gambar_sertifikat_url FROM sertifikasis WHERE tenaga_kerja_id = ?", [$id]);
foreach ($serts as $s) {
    if ($s['gambar_sertifikat_url'] && file_exists(UPLOAD_DIR . basename($s['gambar_sertifikat_url']))) {
        @unlink(UPLOAD_DIR . basename($s['gambar_sertifikat_url']));
    }
}

// Hapus sertifikasi dan tenaga kerja (cascade via PHP)
db_exec("DELETE FROM sertifikasis WHERE tenaga_kerja_id = ?", [$id]);
db_exec("DELETE FROM tenaga_kerjas WHERE id = ?", [$id]);

flash_set('success', "Data tenaga kerja {$tk['nama']} berhasil dihapus.");
redirect(BASE_URL . '/tenaga-kerja.php');
