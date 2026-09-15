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
        "SELECT * FROM sertifikasis WHERE tenaga_kerja_id = ?", [$w['id']]
    );
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
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <button onclick="openAddModal()" class="btn-gold-primary px-5 py-2.5 text-xs uppercase tracking-wider gap-2 cursor-pointer w-full sm:w-auto justify-center">
                <i data-lucide="plus-circle" class="w-4 h-4"></i><span>Tambah Tenaga Kerja</span>
            </button>
            <a href="<?= BASE_URL ?>/export.php" class="btn-teal-outline px-4 py-2.5 text-xs uppercase tracking-wider gap-2 cursor-pointer">
                <i data-lucide="download" class="w-4 h-4"></i><span class="hidden sm:inline">Ekspor CSV</span>
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
<div id="detail-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-teal-50/50">
            <div>
                <h3 id="detail-nama" class="text-xl font-black text-gray-900"></h3>
                <p id="detail-nik" class="text-xs text-gray-400 font-bold"></p>
            </div>
            <button onclick="document.getElementById('detail-modal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div id="detail-content" class="p-6 overflow-y-auto space-y-4 text-xs font-medium"></div>
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

<script>
// --- Modal helpers ---
function openAddModal() { document.getElementById('add-modal').classList.remove('hidden'); }
function closeAddModal() { document.getElementById('add-modal').classList.add('hidden'); }
function closeEditModal() { document.getElementById('edit-modal').classList.add('hidden'); }
function closeCertModal() { document.getElementById('cert-modal').classList.add('hidden'); }

function openDetailModal(w) {
    document.getElementById('detail-nama').innerText = w.nama;
    document.getElementById('detail-nik').innerText = 'NIK: ' + (w.nik || '-') + ' • Usia: ' + (w.usia || '-');
    document.getElementById('detail-content').innerHTML = `
        <div class="grid grid-cols-2 gap-3 p-4 rounded-2xl bg-gray-50">
            <div><span class="text-gray-400">Unit Layanan:</span> <div class="font-bold text-gray-900">${w.unit_nama || w.unit || '-'}</div></div>
            <div><span class="text-gray-400">Perusahaan Mitra:</span> <div class="font-bold text-gray-900">${w.nama_perusahaan || '-'}</div></div>
            <div><span class="text-gray-400">Jabatan:</span> <div class="font-bold text-gray-900">${w.jabatan_terakhir || '-'}</div></div>
            <div><span class="text-gray-400">Fungsi:</span> <div class="font-bold text-gray-900">${w.fungsi_pekerjaan || '-'}</div></div>
            <div><span class="text-gray-400">Status Kontrak:</span> <div class="font-black text-primary">${w.status_tenaga_kerja} (${w.skema_tenaga_kerja || '-'})</div></div>
            <div><span class="text-gray-400">No. HP:</span> <div class="font-bold text-gray-900">${w.no_telepon || '-'}</div></div>
            <div><span class="text-gray-400">Pendidikan:</span> <div class="font-bold text-gray-900">${w.pendidikan_terakhir || '-'} ${w.jurusan ? '(' + w.jurusan + ')' : ''}</div></div>
            <div><span class="text-gray-400">Alamat:</span> <div class="font-bold text-gray-900">${w.alamat_domisili || '-'}</div></div>
            <div><span class="text-gray-400">BPJS Kesehatan:</span> <div class="font-bold text-gray-900">${w.nomor_bpjs_kesehatan || '-'}</div></div>
            <div><span class="text-gray-400">BPJS Ketenagakerjaan:</span> <div class="font-bold text-gray-900">${w.nomor_bpjs_ketenagakerjaan || '-'}</div></div>
        </div>
        <div class="mt-4">
            <div class="font-black text-gray-800 text-xs mb-2">Riwayat Sertifikasi:</div>
            ${w.sertifikasis && w.sertifikasis.length > 0
                ? w.sertifikasis.map(c => `<div class="p-3 rounded-xl border border-teal-100 bg-teal-50/40 mb-2"><div class="font-bold text-gray-900">${c.judul_sertifikasi}</div><div class="text-[11px] text-gray-400">No: ${c.nomor_sertifikat || '-'}</div></div>`).join('')
                : '<div class="text-gray-400 text-xs">Belum ada sertifikasi terdaftar.</div>'}
        </div>`;
    document.getElementById('detail-modal').classList.remove('hidden');
    lucide.createIcons();
}

function openEditModal(w) {
    document.getElementById('edit-id').value = w.id;
    document.getElementById('edit-subtitle').innerText = 'ID: ' + w.id + ' — ' + w.nama;
    // Fill all fields
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

function openCertModal(w) {
    document.getElementById('cert-worker-name').innerText = w.nama;
    document.getElementById('cert-tk-id').value = w.id;
    let listHtml = w.sertifikasis && w.sertifikasis.length > 0
        ? w.sertifikasis.map(c => `
            <div class="p-3 rounded-2xl border border-gray-200 flex items-center justify-between">
                <div>
                    <div class="font-bold text-gray-900 text-xs">${c.judul_sertifikasi}</div>
                    <div class="text-[11px] text-gray-400">No: ${c.nomor_sertifikat || '-'}</div>
                    ${c.gambar_sertifikat_url ? `<a href="${c.gambar_sertifikat_url}" target="_blank" class="text-primary text-[11px] font-bold underline">Lihat Gambar</a>` : ''}
                </div>
                <form method="POST" action="<?= BASE_URL ?>/actions/sertifikasi-delete.php">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="id" value="${c.id}">
                    <button type="submit" class="text-rose-500 hover:text-rose-700 p-1" onclick="return confirm('Hapus sertifikasi ini?')">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>`).join('')
        : '<div class="text-xs text-gray-400 text-center py-4">Belum ada sertifikasi.</div>';
    document.getElementById('cert-list').innerHTML = listHtml;
    document.getElementById('cert-modal').classList.remove('hidden');
    lucide.createIcons();
}

if (new URLSearchParams(window.location.search).get('tambah') === '1') openAddModal();
</script>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>
