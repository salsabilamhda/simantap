<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$id = (int) post('id');
if (!$id) {
    flash_set('error', 'ID tidak valid.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

$cert = db_row("SELECT * FROM sertifikasis WHERE id = ?", [$id]);
if (!$cert) {
    flash_set('error', 'Sertifikasi tidak ditemukan.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

// Hapus file dari server
if ($cert['gambar_sertifikat_url']) {
    $fileName = basename($cert['gambar_sertifikat_url']);
    $filePath = UPLOAD_DIR . $fileName;
    if (file_exists($filePath)) {
        @unlink($filePath);
    }
}

db_exec("DELETE FROM sertifikasis WHERE id = ?", [$id]);

flash_set('success', 'Sertifikasi berhasil dihapus.');
redirect(BASE_URL . '/tenaga-kerja.php');
