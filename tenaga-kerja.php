<?php
$pageTitle = 'Data Tenaga Kerja';
require_once __DIR__ . '/db.php';

// --- Filters ---
$search = get_param('q');
$unitFilter  = get_param('unit');
$statusFilter = get_param('status');
$page   = max(1, (int) get_param('page', '1'));
$perPage = 10;
$offset  = ($page - 1) * $perPage;

// --- Build WHERE clause ---
$where = ['1=1'];
$params = [];
if ($search !== '') {
    $where[] = "(tk.nama LIKE ? OR tk.nik LIKE ? OR tk.jabatan_terakhir LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
}
if ($unitFilter !== '' && $unitFilter !== 'ALL') {
    $where[] = "tk.unit_layanan_id = ?";
    $params[] = $unitFilter;
}
if ($statusFilter !== '' && $statusFilter !== 'ALL') {
    $where[] = "tk.status_tenaga_kerja = ?";
    $params[] = $statusFilter;
}
$whereStr = implode(' AND ', $where);

// --- Count ---
$total = (int) db_val("SELECT COUNT(*) FROM tenaga_kerjas tk WHERE $whereStr", $params);
$totalPages = max(1, (int) ceil($total / $perPage));

// --- Fetch paginated list ---
$workers = db_query("
    SELECT tk.*, u.nama AS unit_nama,
        (SELECT COUNT(*) FROM sertifikasis s WHERE s.tenaga_kerja_id = tk.id) AS sertifikasi_count
    FROM tenaga_kerjas tk
    LEFT JOIN unit_layanans u ON u.id = tk.unit_layanan_id
    WHERE $whereStr
    ORDER BY tk.id ASC
    LIMIT $perPage OFFSET $offset
", $params);

// For each worker, get sertifikasi JSON
foreach ($workers as &$w) {
    $w['sertifikasis'] = db_query(
        "SELECT * FROM sertifikasis WHERE tenaga_kerja_id = ? ORDER BY id DESC", [$w['id']]
    );
    foreach ($w['sertifikasis'] as &$s) {
        if (!empty($s['gambar_sertifikat_url'])) {
            if (!preg_match('~^https?://~i', $s['gambar_sertifikat_url'])) {
                $s['gambar_sertifikat_url'] = UPLOAD_URL . basename($s['gambar_sertifikat_url']);
            }
        }
    }
    unset($s);
    $w['usia'] = hitung_usia($w['tanggal_lahir']);
}
unset($w);

$unitLayanans = db_query("SELECT * FROM unit_layanans ORDER BY nama");
$perusahaans  = db_query("SELECT * FROM perusahaans ORDER BY nama");

include __DIR__ . '/includes/layout-head.php';
include __DIR__ . '/includes/layout-sidebar.php';

// Helper for pagination URL
function paginationUrl(int $p): string {
    $params = $_GET;
    $params['page'] = $p;
    return '?' . http_build_query($params);
}
?>

<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Data Tenaga Kerja</h1>
            <p class="text-xs text-gray-400 font-medium">Kelola dan pantau seluruh data personil outsourcing &amp; mitra (PHP Native + MySQL)</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <button onclick="openAddModal()" class="btn-gold-primary px-4 py-2.5 text-xs uppercase tracking-wider gap-2 cursor-pointer flex-1 sm:flex-initial justify-center shadow-xs">
                <i data-lucide="plus-circle" class="w-4 h-4"></i><span>Tambah Data</span>
            </button>
            <button onclick="openImportModal()" class="btn-teal-outline px-4 py-2.5 text-xs uppercase tracking-wider gap-2 cursor-pointer flex-1 sm:flex-initial justify-center bg-white shadow-xs hover:bg-teal-50">
                <i data-lucide="file-up" class="w-4 h-4 text-teal-600"></i><span>Import Excel</span>
            </button>
            <a href="<?= BASE_URL ?>/export.php" class="btn-teal-outline px-4 py-2.5 text-xs uppercase tracking-wider gap-2 cursor-pointer flex-1 sm:flex-initial justify-center bg-white shadow-xs hover:bg-teal-50" title="Unduh data tenaga kerja (CSV/Excel)">
                <i data-lucide="download" class="w-4 h-4 text-teal-600"></i><span>Ekspor Data</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-3xl p-5 border border-teal-100 shadow-card-custom">
        <form method="GET" action="tenaga-kerja.php" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6 relative">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="q" value="<?= h($search) ?>" placeholder="Cari nama, NIK, atau jabatan..." class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary focus:bg-white transition-all">
            </div>
            <div class="sm:col-span-3">
                <select name="unit" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 focus:outline-none focus:border-primary">
                    <option value="ALL">Semua Unit Layanan</option>
                    <?php foreach ($unitLayanans as $u): ?>
                        <option value="<?= h($u['id']) ?>" <?= $unitFilter == $u['id'] ? 'selected' : '' ?>><?= h($u['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="sm:col-span-2">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 focus:outline-none focus:border-primary">
                    <option value="ALL">Semua Status</option>
                    <option value="PKWTT" <?= $statusFilter === 'PKWTT' ? 'selected' : '' ?>>PKWTT (Tetap)</option>
                    <option value="PKWT"  <?= $statusFilter === 'PKWT'  ? 'selected' : '' ?>>PKWT (Kontrak)</option>
                </select>
            </div>
            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2.5 rounded-2xl bg-primary hover:bg-primaryDark text-white text-xs font-bold transition-colors flex items-center justify-center">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-teal-100 shadow-card-custom overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-teal-50/50 border-b border-teal-100 text-gray-400 uppercase tracking-wider font-extrabold">
                        <th class="py-4 px-4 w-12 text-center">No</th>
                        <th class="py-4 px-4">Nama Tenaga Kerja</th>
                        <th class="py-4 px-4">NIK &amp; Usia</th>
                        <th class="py-4 px-4">Unit Layanan</th>
                        <th class="py-4 px-4">Jabatan Terakhir</th>
                        <th class="py-4 px-4">Status &amp; Skema</th>
                        <th class="py-4 px-4">Sertifikat</th>
                        <th class="py-4 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    <?php if (empty($workers)): ?>
                        <tr><td colspan="8" class="py-12 text-center text-gray-400 font-bold">Tidak ada data tenaga kerja yang sesuai filter.</td></tr>
                    <?php else:
                        $rowNo = $offset + 1;
                        foreach ($workers as $row): ?>
                        <tr class="hover:bg-teal-50/40 transition-colors">
                            <td class="py-4 px-4 text-center font-bold text-gray-400"><?= $rowNo++ ?></td>
                            <td class="py-4 px-4">
                                <div class="font-black text-gray-900 text-sm"><?= h($row['nama']) ?></div>
                                <div class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                                    <i data-lucide="phone" class="w-3 h-3"></i>
                                    <span><?= h($row['no_telepon'] ?: '-') ?></span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-extrabold text-gray-700 tracking-wide"><?= h($row['nik'] ?: '-') ?></div>
                                <div class="text-[11px] text-primary font-bold mt-0.5"><?= h($row['usia']) ?></div>
                            </td>
                            <td class="py-4 px-4"><span class="font-bold text-gray-800"><?= h($row['unit_nama'] ?: $row['unit']) ?></span></td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-800"><?= h($row['jabatan_terakhir'] ?: '-') ?></div>
                                <div class="text-[11px] text-gray-400"><?= h($row['fungsi_pekerjaan'] ?: '-') ?></div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black <?= $row['status_tenaga_kerja'] === 'PKWTT' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-300' ?>">
                                    <?= h($row['status_tenaga_kerja']) ?>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1 font-bold"><?= h($row['skema_tenaga_kerja']) ?></div>
                            </td>
                            <td class="py-4 px-4">
                                <button onclick='openCertModal(<?= json_encode($row) ?>)' class="px-2.5 py-1 rounded-xl bg-teal-50 hover:bg-teal-100 text-primaryDark text-[11px] font-black border border-teal-200 flex items-center gap-1 transition-colors">
                                    <i data-lucide="award" class="w-3.5 h-3.5 text-primary"></i>
                                    <span><?= h($row['sertifikasi_count']) ?> File</span>
                                </button>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick='openDetailModal(<?= json_encode($row) ?>)' title="Lihat Detail" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-teal-100 text-gray-600 hover:text-primaryDark flex items-center justify-center transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick='openEditModal(<?= json_encode($row) ?>)' title="Edit Data" class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 flex items-center justify-center transition-colors">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    <form method="POST" action="<?= BASE_URL ?>/actions/tk-delete.php" onsubmit="return confirm('Hapus data <?= h(addslashes($row['nama'])) ?>?');" class="inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= h($row['id']) ?>">
                                        <button type="submit" title="Hapus" class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold text-gray-500">
            <div>Menampilkan <?= min($offset + 1, $total) ?>–<?= min($offset + $perPage, $total) ?> dari <?= $total ?> tenaga kerja</div>
            <?php if ($totalPages > 1): ?>
            <div class="flex items-center gap-1">
                <?php if ($page > 1): ?>
                    <a href="<?= paginationUrl($page - 1) ?>" class="px-3 py-1.5 rounded-xl bg-white border border-gray-200 hover:bg-teal-50 text-gray-600 transition-colors">&laquo;</a>
                <?php endif; ?>
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <a href="<?= paginationUrl($i) ?>" class="px-3 py-1.5 rounded-xl border transition-colors <?= $i === $page ? 'bg-primaryDark text-white border-primaryDark' : 'bg-white border-gray-200 hover:bg-teal-50 text-gray-600' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="<?= paginationUrl($page + 1) ?>" class="px-3 py-1.5 rounded-xl bg-white border border-gray-200 hover:bg-teal-50 text-gray-600 transition-colors">&raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: Tambah Tenaga Kerja -->
<!-- ============================================================ -->
<div id="add-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-teal-50/50">
            <div>
                <h3 class="text-lg font-black text-primaryDark">Tambah Tenaga Kerja Baru</h3>
                <p class="text-xs text-gray-400">Isi formulir lengkap sesuai format database</p>
            </div>
            <button onclick="closeAddModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/actions/tk-store.php" class="flex-1 overflow-y-auto p-6 space-y-6">
            <?= csrf_field() ?>
            <?php include __DIR__ . '/includes/form-tk-fields.php'; ?>
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-full border border-gray-300 text-xs font-bold text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="submit" class="btn-gold-primary px-6 py-2.5 text-xs uppercase tracking-wider">Simpan Tenaga Kerja</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: Edit Tenaga Kerja -->
<!-- ============================================================ -->
<div id="edit-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-amber-50/50">
            <div>
                <h3 class="text-lg font-black text-amber-700">Edit Data Tenaga Kerja</h3>
                <p id="edit-subtitle" class="text-xs text-gray-400"></p>
            </div>
            <button onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <form id="edit-form" method="POST" action="<?= BASE_URL ?>/actions/tk-update.php" class="flex-1 overflow-y-auto p-6 space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="edit-id">
            <?php include __DIR__ . '/includes/form-tk-fields.php'; ?>
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-full border border-gray-300 text-xs font-bold text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="submit" class="btn-gold-primary px-6 py-2.5 text-xs uppercase tracking-wider">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: Detail Tenaga Kerja -->
<!-- ============================================================ -->
<div id="detail-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden" onclick="closeDetailModal(event)">
    <div class="bg-white rounded-3xl w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-teal-100 animate-in fade-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-5 sm:p-6 border-b border-teal-100 flex items-start sm:items-center justify-between gap-3 bg-gradient-to-r from-teal-50/90 via-teal-50/40 to-white">
            <div class="flex items-center gap-3.5">
                <div id="detail-avatar" class="w-12 h-12 rounded-2xl bg-teal-600 text-white font-black text-base flex items-center justify-center shadow-teal-glow shrink-0 uppercase">
                    TK
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 id="detail-nama" class="text-xl font-black text-gray-900 tracking-tight"></h3>
                        <span id="detail-status-badge"></span>
                    </div>
                    <p id="detail-nik" class="text-xs text-gray-500 font-bold mt-0.5"></p>
                </div>
            </div>
            <button onclick="document.getElementById('detail-modal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-700 flex items-center justify-center shadow-xs transition-colors shrink-0">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Body -->
        <div id="detail-content" class="p-5 sm:p-6 overflow-y-auto space-y-4 text-xs font-medium bg-gray-50/40"></div>

        <!-- Footer -->
        <div class="p-4 sm:p-5 border-t border-gray-100 bg-white flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
            <button type="button" onclick="document.getElementById('detail-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-100 transition-colors text-center">
                Tutup
            </button>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openCertFromDetail()" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-primaryDark text-xs font-bold border border-teal-200 flex items-center justify-center gap-1.5 transition-colors">
                    <i data-lucide="award" class="w-4 h-4 text-primary"></i><span>Kelola Sertifikasi</span>
                </button>
                <button type="button" onclick="openEditFromDetail()" class="flex-1 sm:flex-initial btn-gold-primary px-5 py-2.5 text-xs uppercase tracking-wider gap-1.5 justify-center">
                    <i data-lucide="pencil" class="w-4 h-4"></i><span>Edit Data</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: Sertifikasi -->
<!-- ============================================================ -->
<div id="cert-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-teal-50/50">
            <div>
                <h3 class="text-lg font-black text-gray-900">Sertifikasi Tenaga Kerja</h3>
                <p id="cert-worker-name" class="text-xs text-teal-700 font-bold"></p>
            </div>
            <button onclick="closeCertModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div class="p-6 overflow-y-auto space-y-6">
            <form id="cert-form" method="POST" action="<?= BASE_URL ?>/actions/sertifikasi-store.php" enctype="multipart/form-data" class="p-4 rounded-2xl bg-teal-50/40 border border-teal-100 space-y-3">
                <?= csrf_field() ?>
                <input type="hidden" name="tenaga_kerja_id" id="cert-tk-id">
                <div class="text-xs font-black text-primaryDark uppercase">Tambah Bukti Sertifikat Baru</div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Judul Sertifikasi *</label>
                    <input type="text" name="judul_sertifikasi" required placeholder="Contoh: Kompetensi K3 Distribusi" class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Nomor Sertifikat</label>
                    <input type="text" name="nomor_sertifikat" placeholder="Contoh: SERT-K3-2024-xxx" class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Upload Gambar Sertifikat (JPG/PNG, maks 5MB)</label>
                    <input type="file" name="gambar_sertifikat" accept="image/*" class="w-full text-xs text-gray-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Terbit</label>
                    <input type="date" name="tanggal_terbit" class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs">
                </div>
                <button type="submit" class="btn-gold-primary px-4 py-2 text-xs uppercase tracking-wider w-full justify-center">Upload &amp; Simpan Sertifikat</button>
            </form>
            <div id="cert-list" class="space-y-3"></div>
        </div>
    </div>
</div>

<!-- MODAL: Preview Gambar Sertifikat -->
<div id="certificate-image-modal" class="fixed inset-0 z-[60] bg-black/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 hidden" onclick="closeCertificateImage(event)">
    <div class="relative max-w-4xl max-h-[92vh] flex flex-col items-center w-full" onclick="event.stopPropagation()">
        <div class="w-full flex items-center justify-between mb-3 text-white">
            <div class="flex items-center gap-2">
                <i data-lucide="award" class="w-4 h-4 text-accentGold"></i>
                <span id="certificate-image-title" class="text-xs font-black tracking-wide">Lampiran Gambar Sertifikat</span>
            </div>
            <div class="flex items-center gap-2">
                <a id="certificate-image-download" href="" target="_blank" download class="px-3 py-1 rounded-xl bg-white/20 hover:bg-white/30 text-white text-[11px] font-bold flex items-center gap-1 transition-colors">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i><span>Unduh</span>
                </a>
                <button type="button" onclick="closeCertificateImage()" title="Tutup gambar" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div class="bg-gray-900 rounded-2xl overflow-hidden shadow-2xl p-2 border border-white/10 flex items-center justify-center max-h-[80vh] w-full">
            <img id="certificate-image-preview" src="" alt="Gambar sertifikat" class="block max-w-full max-h-[78vh] rounded-xl object-contain bg-black/30">
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: Import Tenaga Kerja via Excel -->
<!-- ============================================================ -->
<div id="import-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <!-- Header -->
        <div class="p-6 border-b border-teal-100 flex items-center justify-between bg-gradient-to-r from-teal-50/70 to-teal-50/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-100/80 text-primaryDark flex items-center justify-center">
                    <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900">Import Data Tenaga Kerja via Excel</h3>
                    <p class="text-xs text-gray-400 font-medium">Unggah file .xlsx atau .xls untuk menambah banyak data sekaligus</p>
                </div>
            </div>
            <button onclick="closeImportModal()" class="w-8 h-8 rounded-full bg-white border border-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            <!-- Step Guide & Download Template Banner -->
            <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/70 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
                    <div class="text-xs text-amber-900 leading-relaxed">
                        <span class="font-black">Petunjuk Import:</span> Gunakan format kolom template resmi. Kolom <strong>UNIT LAYANAN</strong> harus cocok dengan master data unit layanan. Tanggal lahir &amp; tanggal masuk kerja diformat <code>DD/MM/YYYY</code> atau tanggal Excel standar.
                    </div>
                </div>
                <button type="button" id="download-template" class="btn-gold-primary px-4 py-2 text-xs uppercase tracking-wider gap-2 shrink-0 justify-center">
                    <i data-lucide="file-down" class="w-4 h-4"></i><span>Unduh Template</span>
                </button>
            </div>

            <!-- Upload Area -->
            <div class="border-2 border-dashed border-teal-200 hover:border-primary rounded-3xl p-6 bg-teal-50/20 text-center transition-colors">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mb-3">
                    <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                </div>
                <label for="excel-file" class="cursor-pointer">
                    <span class="text-sm font-black text-primaryDark hover:underline">Pilih file spreadsheet (.xlsx / .xls)</span>
                    <p class="text-[11px] text-gray-400 mt-1">Klik untuk memilih file Excel dari perangkat Anda</p>
                </label>
                <input id="excel-file" type="file" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="hidden">
                <div id="file-chosen-name" class="mt-2 text-xs font-black text-teal-800 hidden"></div>
            </div>

            <!-- Status / Alert Box -->
            <div id="import-message" class="hidden rounded-2xl px-4 py-3 text-xs font-bold"></div>

            <!-- Preview Table -->
            <div id="preview-wrap" class="hidden space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-gray-800">Preview Data Excel</span>
                        <span id="preview-count" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-teal-100 text-teal-800"></span>
                    </div>
                    <span class="text-[11px] text-gray-400 font-medium">Menampilkan hingga 20 baris pertama</span>
                </div>
                <div class="overflow-x-auto max-h-60 rounded-2xl border border-teal-100 shadow-xs">
                    <table class="w-full text-[11px] text-left" id="preview-table">
                        <thead class="bg-teal-50 text-gray-600 font-bold sticky top-0"></thead>
                        <tbody class="divide-y divide-gray-100 bg-white font-medium"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 sm:p-6 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
            <button type="button" onclick="closeImportModal()" class="px-5 py-2.5 rounded-full border border-gray-300 text-xs font-bold text-gray-600 hover:bg-gray-100 transition-colors">Tutup</button>
            <button type="button" id="process-import" disabled class="btn-gold-primary py-2.5 px-6 text-xs uppercase tracking-wider gap-2 justify-center disabled:opacity-40 disabled:cursor-not-allowed">
                <i data-lucide="database" class="w-4 h-4"></i><span>Proses Simpan ke Database</span>
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
// --- Modal helpers ---
function openAddModal() { document.getElementById('add-modal').classList.remove('hidden'); }
function closeAddModal() { document.getElementById('add-modal').classList.add('hidden'); }
function openEditModal(w) {
    document.getElementById('edit-id').value = w.id;
    document.getElementById('edit-subtitle').innerText = 'ID: ' + w.id + ' — ' + w.nama;
    const f = document.getElementById('edit-form');
    const fields = ['nama','nik','nomor_perjanjian','nama_perusahaan','tempat_lahir','tanggal_lahir',
        'pendidikan_terakhir','jurusan','no_telepon','email','jenis_kelamin','alamat_domisili',
        'kota_kabupaten','provinsi','jabatan_terakhir','fungsi_pekerjaan','unit','unit_layanan_id',
        'nomor_bpjs_kesehatan','nomor_bpjs_ketenagakerjaan','nomor_dplk','bank_dplk',
        'no_perjanjian_kerja','tanggal_masuk_kerja','status_tenaga_kerja','skema_tenaga_kerja'];
    fields.forEach(name => {
        const el = f.querySelector('[name="' + name + '"]');
        if (el && w[name] != null) el.value = w[name];
    });
    document.getElementById('edit-modal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('edit-modal').classList.add('hidden'); }
function closeCertModal() { document.getElementById('cert-modal').classList.add('hidden'); }

let currentDetailWorker = null;

function closeDetailModal(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('detail-modal').classList.add('hidden');
}

function openCertificateImage(url, title = 'Lampiran Gambar Sertifikat') {
    if (!url) return;
    document.getElementById('certificate-image-preview').src = url;
    const titleEl = document.getElementById('certificate-image-title');
    if (titleEl) titleEl.innerText = title;
    const downloadEl = document.getElementById('certificate-image-download');
    if (downloadEl) downloadEl.href = url;
    document.getElementById('certificate-image-modal').classList.remove('hidden');
    lucide.createIcons();
}

function closeCertificateImage(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('certificate-image-modal').classList.add('hidden');
    document.getElementById('certificate-image-preview').src = '';
}

function openImportModal() {
    document.getElementById('import-modal').classList.remove('hidden');
    lucide.createIcons();
}
function closeImportModal() {
    document.getElementById('import-modal').classList.add('hidden');
}

function escHtml(str) {
    if (str == null || str === '') return '-';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function formatTanggalIndo(str) {
    if (!str || str === '0000-00-00') return '-';
    try {
        const parts = str.split('-');
        if (parts.length === 3) {
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const day = parseInt(parts[2], 10);
            const mon = parseInt(parts[1], 10) - 1;
            const year = parts[0];
            if (bulan[mon] && !isNaN(day)) return `${day} ${bulan[mon]} ${year}`;
        }
        return str;
    } catch (e) {
        return str;
    }
}

function openDetailModal(w) {
    currentDetailWorker = w;
    const avatarEl = document.getElementById('detail-avatar');
    if (avatarEl) {
        const initials = (w.nama || 'TK').trim().split(/\s+/).slice(0, 2).map(n => n[0]).join('').toUpperCase();
        avatarEl.innerText = initials || 'TK';
    }
    
    document.getElementById('detail-nama').innerText = w.nama || 'Tanpa Nama';
    
    const usiaText = w.usia ? ` • Usia: ${w.usia}` : '';
    document.getElementById('detail-nik').innerText = `NIK: ${w.nik || '-'}${usiaText}`;
    
    const badgeStatus = document.getElementById('detail-status-badge');
    if (badgeStatus) {
        const isPkwtt = (w.status_tenaga_kerja || '').toUpperCase().includes('PKWTT');
        badgeStatus.className = `px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider ${
            isPkwtt ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300'
        }`;
        badgeStatus.innerText = `${w.status_tenaga_kerja || 'STATUS -'}${w.skema_tenaga_kerja ? ' (' + w.skema_tenaga_kerja + ')' : ''}`;
    }

    // Phone & Email with clickable actions
    const phoneClean = (w.no_telepon || '').replace(/[^0-9]/g, '');
    const waLink = phoneClean.length >= 8 
        ? (phoneClean.startsWith('0') ? '62' + phoneClean.slice(1) : phoneClean)
        : null;

    const phoneHtml = w.no_telepon 
        ? `<div class="flex items-center gap-2">
             <span class="font-bold text-gray-900">${escHtml(w.no_telepon)}</span>
             ${waLink ? `<a href="https://wa.me/${waLink}" target="_blank" title="Hubungi via WhatsApp" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-[10px] font-bold border border-emerald-200 transition-colors"><i data-lucide="message-circle" class="w-3 h-3"></i>WhatsApp</a>` : ''}
           </div>`
        : '<span class="font-bold text-gray-400">-</span>';

    const emailHtml = w.email 
        ? `<a href="mailto:${escHtml(w.email)}" class="font-bold text-teal-700 hover:underline inline-flex items-center gap-1.5"><i data-lucide="mail" class="w-3.5 h-3.5 text-teal-600"></i>${escHtml(w.email)}</a>`
        : '<span class="font-bold text-gray-400">-</span>';

    // Sertifikasi items HTML
    const certCount = (w.sertifikasis && w.sertifikasis.length) || 0;
    let certListHtml = '';
    if (certCount > 0) {
        certListHtml = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                ${w.sertifikasis.map((c, idx) => `
                    <div class="p-4 rounded-2xl border border-teal-100 bg-white shadow-xs flex flex-col justify-between gap-3 relative overflow-hidden group hover:border-primary transition-all">
                        <div class="space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-xl bg-teal-50 text-primary flex items-center justify-center shrink-0">
                                        <i data-lucide="award" class="w-4 h-4"></i>
                                    </div>
                                    <div class="font-black text-gray-900 text-xs leading-snug">
                                        ${escHtml(c.judul_sertifikasi || 'Sertifikasi Kompetensi')}
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 text-primaryDark shrink-0">
                                    #${idx + 1}
                                </span>
                            </div>
                            <div class="text-[11px] text-gray-500 space-y-1 pl-9">
                                <div><span class="text-gray-400">No. Sertifikat:</span> <strong class="text-gray-800 font-bold">${escHtml(c.nomor_sertifikat)}</strong></div>
                                ${c.tanggal_terbit ? `<div><span class="text-gray-400">Tgl Terbit:</span> <strong class="text-gray-700 font-semibold">${formatTanggalIndo(c.tanggal_terbit)}</strong></div>` : ''}
                                ${c.tanggal_kadaluarsa ? `<div><span class="text-gray-400">Berlaku Hingga:</span> <strong class="text-gray-700 font-semibold">${formatTanggalIndo(c.tanggal_kadaluarsa)}</strong></div>` : ''}
                            </div>
                        </div>

                        ${c.gambar_sertifikat_url ? `
                            <div class="mt-1 pt-3 border-t border-gray-100 space-y-2">
                                <div class="text-[11px] font-bold text-gray-700 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5"><i data-lucide="image" class="w-3.5 h-3.5 text-teal-600"></i>Dokumen / Gambar Sertifikat:</span>
                                    <span class="text-[10px] text-teal-600 font-bold">Klik gambar untuk memperbesar</span>
                                </div>
                                <div class="relative group/img rounded-xl overflow-hidden border border-teal-200/70 bg-gray-50 cursor-pointer shadow-xs aspect-[4/3] flex items-center justify-center" onclick='openCertificateImage(${JSON.stringify(c.gambar_sertifikat_url)}, ${JSON.stringify(c.judul_sertifikasi)})'>
                                    <img src="${escHtml(c.gambar_sertifikat_url)}" alt="Sertifikat ${escHtml(c.judul_sertifikasi)}" class="w-full h-full object-contain p-1 group-hover/img:scale-105 transition-transform duration-200">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <span class="px-3 py-1.5 rounded-full bg-white text-gray-900 text-[11px] font-black shadow-md flex items-center gap-1.5">
                                            <i data-lucide="zoom-in" class="w-3.5 h-3.5"></i> Perbesar
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 pt-1">
                                    <button type="button" onclick='openCertificateImage(${JSON.stringify(c.gambar_sertifikat_url)}, ${JSON.stringify(c.judul_sertifikasi)})' class="flex-1 py-1.5 px-2.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-primaryDark text-[11px] font-bold flex items-center justify-center gap-1.5 transition-colors border border-teal-200">
                                        <i data-lucide="maximize-2" class="w-3.5 h-3.5"></i><span>Lihat Penuh</span>
                                    </button>
                                    <a href="${escHtml(c.gambar_sertifikat_url)}" target="_blank" download class="py-1.5 px-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 text-[11px] font-bold flex items-center justify-center gap-1 transition-colors border border-gray-200" title="Buka gambar di tab baru / Unduh">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        ` : `
                            <div class="mt-1 pt-3 border-t border-gray-100">
                                <div class="p-2.5 rounded-xl bg-gray-50 border border-dashed border-gray-200 text-gray-400 text-[11px] flex items-center gap-2">
                                    <i data-lucide="image-off" class="w-4 h-4 text-gray-400 shrink-0"></i>
                                    <span>Belum ada lampiran gambar untuk sertifikat ini.</span>
                                </div>
                            </div>
                        `}
                    </div>
                `).join('')}
            </div>
        `;
    } else {
        certListHtml = `
            <div class="p-6 rounded-2xl border border-dashed border-gray-200 bg-white flex flex-col items-center justify-center text-center">
                <div class="w-11 h-11 rounded-2xl bg-teal-50 text-primary flex items-center justify-center mb-2 shadow-xs">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <div class="text-xs font-bold text-gray-800">Belum Ada Riwayat Sertifikasi</div>
                <div class="text-[11px] text-gray-400 mt-1 max-w-sm">Tenaga kerja ini belum memiliki data sertifikasi kompetensi. Anda dapat mengunggah bukti sertifikat melalui tombol di bawah.</div>
                <button type="button" onclick="openCertFromDetail()" class="mt-3 px-3.5 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-primaryDark text-xs font-black border border-teal-200 flex items-center gap-1.5 transition-colors shadow-xs">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i><span>Tambah Sertifikasi &amp; Gambar</span>
                </button>
            </div>
        `;
    }

    document.getElementById('detail-content').innerHTML = `
        <!-- Section 1: Profil & Identitas Pribadi -->
        <div class="rounded-2xl border border-teal-100 bg-white p-5 shadow-card-custom space-y-3">
            <div class="flex items-center gap-2 text-primary font-black text-xs uppercase tracking-wider pb-2 border-b border-teal-50">
                <i data-lucide="user" class="w-4 h-4"></i>
                <span>Identitas Pribadi &amp; Kontak</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 text-xs">
                <div>
                    <span class="text-gray-400 text-[11px] block">NIK:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.nik)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Tempat, Tanggal Lahir:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.tempat_lahir)}, ${formatTanggalIndo(w.tanggal_lahir)} ${w.usia ? '(' + escHtml(w.usia) + ')' : ''}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Jenis Kelamin:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.jenis_kelamin)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Pendidikan Terakhir:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.pendidikan_terakhir)}${w.jurusan ? ' (' + escHtml(w.jurusan) + ')' : ''}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Nomor Telepon / WhatsApp:</span>
                    ${phoneHtml}
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Email:</span>
                    ${emailHtml}
                </div>
                <div class="sm:col-span-2">
                    <span class="text-gray-400 text-[11px] block">Alamat Domisili:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.alamat_domisili)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Kota / Kabupaten &amp; Provinsi:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.kota_kabupaten)}${w.provinsi ? ', ' + escHtml(w.provinsi) : ''}</span>
                </div>
            </div>
        </div>

        <!-- Section 2: Pekerjaan & Penempatan -->
        <div class="rounded-2xl border border-teal-100 bg-white p-5 shadow-card-custom space-y-3">
            <div class="flex items-center gap-2 text-primary font-black text-xs uppercase tracking-wider pb-2 border-b border-teal-50">
                <i data-lucide="briefcase" class="w-4 h-4"></i>
                <span>Pekerjaan, Penempatan &amp; Kontrak</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 text-xs">
                <div>
                    <span class="text-gray-400 text-[11px] block">Unit Layanan (Induk):</span>
                    <span class="font-bold text-gray-900">${escHtml(w.unit_nama || w.unit)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Unit / Lokasi Kerja:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.unit)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Perusahaan Mitra:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.nama_perusahaan)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Jabatan Terakhir:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.jabatan_terakhir)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Fungsi Pekerjaan:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.fungsi_pekerjaan)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Status Hubungan Kerja:</span>
                    <span class="font-black text-primaryDark">${escHtml(w.status_tenaga_kerja)} (${escHtml(w.skema_tenaga_kerja || '-')})</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">No. Perjanjian Kerja (SPK):</span>
                    <span class="font-bold text-gray-900">${escHtml(w.no_perjanjian_kerja)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Tanggal Masuk Kerja (TMT):</span>
                    <span class="font-bold text-gray-900">${formatTanggalIndo(w.tanggal_masuk_kerja)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">No. Perjanjian Perusahaan Mitra:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.nomor_perjanjian)}</span>
                </div>
            </div>
        </div>

        <!-- Section 3: Jaminan Sosial & Finansial -->
        <div class="rounded-2xl border border-teal-100 bg-white p-5 shadow-card-custom space-y-3">
            <div class="flex items-center gap-2 text-primary font-black text-xs uppercase tracking-wider pb-2 border-b border-teal-50">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Jaminan Sosial &amp; Keuangan</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 text-xs">
                <div>
                    <span class="text-gray-400 text-[11px] block">BPJS Kesehatan:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.nomor_bpjs_kesehatan)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">BPJS Ketenagakerjaan:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.nomor_bpjs_ketenagakerjaan)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Nomor DPLK:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.nomor_dplk)}</span>
                </div>
                <div>
                    <span class="text-gray-400 text-[11px] block">Bank DPLK:</span>
                    <span class="font-bold text-gray-900">${escHtml(w.bank_dplk)}</span>
                </div>
            </div>
        </div>

        <!-- Section 4: Riwayat Sertifikasi & Gambar Sertifikat -->
        <div class="rounded-2xl border border-teal-100 bg-teal-50/30 p-5 shadow-card-custom space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-teal-100">
                <div class="flex items-center gap-2 text-primaryDark font-black text-xs uppercase tracking-wider">
                    <i data-lucide="award" class="w-4 h-4"></i>
                    <span>Riwayat Sertifikasi Kompetensi (${certCount})</span>
                </div>
                <button type="button" onclick="openCertFromDetail()" class="px-3 py-1 rounded-xl bg-teal-50 hover:bg-teal-100 text-primaryDark text-[11px] font-black border border-teal-200 flex items-center gap-1 transition-colors">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i><span>Kelola Sertifikasi</span>
                </button>
            </div>
            ${certListHtml}
        </div>
    `;

    document.getElementById('detail-modal').classList.remove('hidden');
    lucide.createIcons();
}

function openCertFromDetail() {
    if (!currentDetailWorker) return;
    document.getElementById('detail-modal').classList.add('hidden');
    openCertModal(currentDetailWorker);
}

function openEditFromDetail() {
    if (!currentDetailWorker) return;
    document.getElementById('detail-modal').classList.add('hidden');
    openEditModal(currentDetailWorker);
}

function openCertModal(w) {
    document.getElementById('cert-worker-name').innerText = w.nama;
    document.getElementById('cert-tk-id').value = w.id;
    let listHtml = w.sertifikasis && w.sertifikasis.length > 0
        ? w.sertifikasis.map(c => `
            <div class="p-3.5 rounded-2xl border border-gray-200 bg-white shadow-xs flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    ${c.gambar_sertifikat_url ? `
                        <div class="w-12 h-12 rounded-xl overflow-hidden border border-teal-100 bg-gray-50 shrink-0 cursor-pointer shadow-xs" onclick='openCertificateImage(${JSON.stringify(c.gambar_sertifikat_url)}, ${JSON.stringify(c.judul_sertifikasi)})' title="Klik untuk memperbesar">
                            <img src="${escHtml(c.gambar_sertifikat_url)}" alt="${escHtml(c.judul_sertifikasi)}" class="w-full h-full object-cover">
                        </div>
                    ` : `
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-primary flex items-center justify-center shrink-0">
                            <i data-lucide="award" class="w-6 h-6"></i>
                        </div>
                    `}
                    <div class="min-w-0">
                        <div class="font-bold text-gray-900 text-xs truncate">${escHtml(c.judul_sertifikasi)}</div>
                        <div class="text-[11px] text-gray-400">No: ${escHtml(c.nomor_sertifikat)}</div>
                        ${c.gambar_sertifikat_url ? `<button type="button" onclick='openCertificateImage(${JSON.stringify(c.gambar_sertifikat_url)}, ${JSON.stringify(c.judul_sertifikasi)})' class="text-primary text-[11px] font-bold underline inline-flex items-center gap-1 mt-0.5"><i data-lucide="image" class="w-3 h-3"></i>Lihat Gambar</button>` : ''}
                    </div>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/actions/sertifikasi-delete.php">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="id" value="${c.id}">
                    <button type="submit" class="text-rose-500 hover:text-rose-700 p-2 rounded-xl hover:bg-rose-50 transition-colors" onclick="return confirm('Hapus sertifikasi ini?')">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>`).join('')
        : '<div class="text-xs text-gray-400 text-center py-4">Belum ada sertifikasi.</div>';
    document.getElementById('cert-list').innerHTML = listHtml;
    document.getElementById('cert-modal').classList.remove('hidden');
    lucide.createIcons();
}

// --- Import Excel via SheetJS ---
const importCsrf = <?= json_encode(csrf_token()) ?>;
const importHeaders = ['NO', 'NOMOR PERJANJIAN', 'NAMA PERUSAHAAN', 'NAMA', 'NIK', 'TEMPAT LAHIR', 'TANGGAL TAHUN LAHIR', 'NO TELEPON (WA)', 'EMAIL', 'JENIS KELAMIN', 'ALAMAT DOMISILI', 'KOTA/KABUPATEN', 'PROVINSI', 'JABATAN TERAKHIR', 'FUNGSI PEKERJAAN', 'UNIT', 'UNIT LAYANAN', 'NOMOR SERTIFIKAT (SERTIFIKASI WAJIB)', 'JUDUL SERTIFIKASI', 'NOMOR BPJS KESEHATAN', 'NOMOR BPJS KETENAGAKERJAAN', 'NOMOR DPLK', 'BANK DPLK', 'NOMOR PERJANJIAN KERJA PKWT/PKWTT', 'TANGGAL MASUK KERJA', 'STATUS TENAGA KERJA (PKWT/PKWTT)', 'SKEMA TENAGA KERJA (PEMBORONGAN / VENDOR BASED)'];
const optionalImportHeaders = new Set(['NO TELEPON (WA)', 'FUNGSI PEKERJAAN', 'NOMOR SERTIFIKAT (SERTIFIKASI WAJIB)', 'JUDUL SERTIFIKASI', 'NOMOR BPJS KESEHATAN', 'NOMOR BPJS KETENAGAKERJAAN']);
let importRows = [];

function importMessage(text, success = false) {
    const box = document.getElementById('import-message');
    box.textContent = text;
    box.className = `rounded-2xl px-4 py-3 text-xs font-bold ${success ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'}`;
}

function renderImportPreview(rows) {
    importRows = rows;
    const visibleHeaders = importHeaders.slice(0, 8);
    document.querySelector('#preview-table thead').innerHTML = `<tr>${visibleHeaders.map((header) => `<th class="px-3 py-2 whitespace-nowrap">${header}</th>`).join('')}</tr>`;
    document.querySelector('#preview-table tbody').innerHTML = rows.slice(0, 20).map((row) => `<tr>${visibleHeaders.map((header) => `<td class="px-3 py-1.5 whitespace-nowrap">${String(row[header] ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]))}</td>`).join('')}</tr>`).join('');
    document.getElementById('preview-wrap').classList.toggle('hidden', rows.length === 0);
    document.getElementById('preview-count').textContent = `${rows.length} baris`;
    document.getElementById('process-import').disabled = rows.length === 0;
}

document.getElementById('download-template').addEventListener('click', () => {
    const workbook = XLSX.utils.book_new();
    const sheet = XLSX.utils.aoa_to_sheet([importHeaders, ['01', '1211/PJ/2024', 'PT CONTOH', 'CONTOH NAMA', '3500000000000001', 'PONOROGO', '17/06/1982', '081234567890', 'contoh@email.com', 'LAKI', 'ALAMAT CONTOH', 'PONOROGO', 'JAWA TIMUR', 'JABATAN CONTOH', 'UP3 PONOROGO', 'UP3 Ponorogo', 'SERT-001', 'Sertifikasi Wajib', '0000000001', '0000000002', '0000000003', 'BNI', 'PKWT-001', '01/01/2025', 'PKWT', 'PEMBORONGAN']]);
    XLSX.utils.book_append_sheet(workbook, sheet, 'Tenaga Kerja');
    XLSX.writeFile(workbook, 'template-import-tenaga-kerja.xlsx');
});

document.getElementById('excel-file').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (!file) return;
    const nameBadge = document.getElementById('file-chosen-name');
    nameBadge.textContent = 'File: ' + file.name;
    nameBadge.classList.remove('hidden');

    const reader = new FileReader();
    reader.onload = (loadEvent) => {
        try {
            const workbook = XLSX.read(loadEvent.target.result, {type: 'array', cellDates: true});
            const headerAliases = {
                'SKEMA TENAGA KERJA (PEMBORONGAN / VOLUME BASED)': 'SKEMA TENAGA KERJA (PEMBORONGAN / VENDOR BASED)',
            };
            const normalizeHeader = (key) => {
                const normalizedKey = String(key).replace(/^\uFEFF/, '').trim().toUpperCase();
                return headerAliases[normalizedKey] || normalizedKey;
            };
            const selectedSheet = workbook.SheetNames.map((sheetName) => {
                const sheet = workbook.Sheets[sheetName];
                const previewRows = XLSX.utils.sheet_to_json(sheet, {header: 1, defval: '', raw: false});
                const headerRow = previewRows.findIndex((row) => {
                    const headers = row.map(normalizeHeader);
                    return importHeaders.filter((header) => headers.includes(header)).length >= 3;
                });
                return {sheet, headerRow};
            }).find(({headerRow}) => headerRow >= 0);
            if (!selectedSheet) throw new Error('Tidak ditemukan sheet Excel yang berisi header data tenaga kerja.');
            const rows = XLSX.utils.sheet_to_json(selectedSheet.sheet, {defval: '', raw: false, range: selectedSheet.headerRow});
            const normalizedRows = rows.map((row) => Object.fromEntries(Object.entries(row).map(([key, value]) => [normalizeHeader(key), String(value).replace(/^'/, '').trim()]))).filter((row) => Object.values(row).some(Boolean));
            const missingHeaders = importHeaders.filter((header) => !optionalImportHeaders.has(header) && !Object.keys(normalizedRows[0] || {}).includes(header));
            if (missingHeaders.length) throw new Error(`Kolom wajib belum ada: ${missingHeaders.join(', ')}`);
            renderImportPreview(normalizedRows);
            importMessage(`${normalizedRows.length} baris siap diproses. Periksa preview terlebih dahulu sebelum menyimpan.`, true);
        } catch (error) {
            renderImportPreview([]);
            importMessage(error.message || 'File Excel tidak dapat dibaca.', false);
        }
    };
    reader.readAsArrayBuffer(file);
});

document.getElementById('process-import').addEventListener('click', async () => {
    const button = document.getElementById('process-import');
    button.disabled = true;
    button.querySelector('span').textContent = 'Memproses...';
    try {
        const response = await fetch('actions/import-excel.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-Token': importCsrf},
            body: JSON.stringify({rows: importRows})
        });
        const result = await response.json();
        importMessage(result.message, response.ok && result.success);
        if (response.ok && result.success) {
            renderImportPreview([]);
            document.getElementById('excel-file').value = '';
            document.getElementById('file-chosen-name').classList.add('hidden');
            setTimeout(() => {
                window.location.href = '<?= BASE_URL ?>/tenaga-kerja.php';
            }, 1200);
        }
    } catch (error) {
        importMessage('Server tidak dapat dihubungi.', false);
    } finally {
        button.disabled = importRows.length === 0;
        button.querySelector('span').textContent = 'Proses Simpan ke Database';
    }
});

// Auto-open modal tambah jika ada parameter ?tambah=1
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('tambah') === '1') openAddModal();
</script>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>
