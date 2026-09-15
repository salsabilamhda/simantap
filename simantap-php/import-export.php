<?php
$pageTitle = 'Impor & Ekspor Excel';
require_once __DIR__ . '/db.php';
$totalRecords = (int) db_val("SELECT COUNT(*) FROM tenaga_kerjas");
$unitCounts   = db_query("
    SELECT u.nama, COUNT(tk.id) AS jumlah
    FROM unit_layanans u
    LEFT JOIN tenaga_kerjas tk ON tk.unit_layanan_id = u.id
    GROUP BY u.id ORDER BY u.nama
");
include __DIR__ . '/includes/layout-head.php';
include __DIR__ . '/includes/layout-sidebar.php';
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Impor &amp; Ekspor Data Excel</h1>
            <p class="text-xs text-gray-400 font-medium">Sinkronisasi data tenaga kerja dengan format template 30 kolom acuan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Export Card -->
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-primary flex items-center justify-center mb-4">
                    <i data-lucide="file-output" class="w-6 h-6"></i>
                </div>
                <h2 class="text-lg font-black text-gray-900 mb-2">Ekspor Database ke Excel (CSV)</h2>
                <p class="text-xs text-gray-500 font-medium leading-relaxed mb-6">
                    Unduh seluruh data tenaga kerja (<strong><?= h($totalRecords) ?> personil</strong>) lengkap dengan 30 kolom format asli, status kepegawaian, nomor BPJS, dan DPLK.
                </p>
                <div class="space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                        <span>Kompatibel langsung dengan Microsoft Excel &amp; Google Sheets</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                        <span>NIK dan nomor jaminan diawali petik agar angka nol tidak hilang</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                        <span>Encoding UTF-8 BOM agar karakter Indonesia tampil benar di Excel</span>
                    </div>
                </div>

                <!-- Per unit count -->
                <div class="space-y-2">
                    <?php foreach ($unitCounts as $uc): ?>
                    <div class="flex items-center justify-between text-xs px-3 py-2 rounded-xl bg-gray-50">
                        <span class="text-gray-600 font-medium"><?= h($uc['nama']) ?></span>
                        <span class="font-black text-primaryDark"><?= h($uc['jumlah']) ?> personil</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/export.php" class="btn-gold-primary py-3 px-6 text-xs uppercase tracking-wider gap-2 justify-center mt-6">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Unduh File Data Tenaga Kerja</span>
            </a>
        </div>

        <!-- Info Card -->
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                    <i data-lucide="info" class="w-6 h-6"></i>
                </div>
                <h2 class="text-lg font-black text-gray-900 mb-2">Struktur Kolom Database</h2>
                <p class="text-xs text-gray-500 font-medium leading-relaxed mb-4">
                    Data diorganisasikan berdasarkan format sheet <code class="bg-gray-100 px-1 rounded">Duplikat</code> pada template acuan:
                </p>
                <div class="p-4 rounded-2xl bg-gray-50 text-xs space-y-2 text-gray-600">
                    <div><strong>Identitas:</strong> No, Perjanjian, Perusahaan, Nama, NIK, Tempat/Tgl Lahir, Usia, Pendidikan, Jurusan, Telp, Email, JK, Domisili, Kota, Provinsi</div>
                    <div class="pt-1"><strong>Pekerjaan:</strong> Jabatan, Fungsi, Unit, Unit Layanan, BPJS Kesehatan &amp; Naker, DPLK, No PKWT, Tanggal Masuk, Status, Skema</div>
                    <div class="pt-1"><strong>Sertifikasi:</strong> Jumlah sertifikat terdaftar</div>
                </div>

                <div class="mt-4 p-4 rounded-2xl bg-amber-50 border border-amber-200">
                    <div class="text-xs font-black text-amber-800 mb-2 flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i> Fitur Import Excel
                    </div>
                    <p class="text-xs text-amber-700">Fitur import dari file .xlsx memerlukan library PHP tambahan. Untuk sementara, gunakan fitur Tambah Manual atau hubungi admin sistem untuk import batch via SQL.</p>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-400 font-medium flex items-center justify-between">
                <span>Database: MySQL (phpMyAdmin)</span>
                <span class="text-primary font-bold">Total: <?= h($totalRecords) ?> Data</span>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>
