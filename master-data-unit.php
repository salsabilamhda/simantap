<?php
$pageTitle = 'Master Data Unit Layanan';
require_once __DIR__ . '/db.php';
$units = db_query("
    SELECT u.*, COUNT(tk.id) AS tenaga_kerjas_count
    FROM unit_layanans u
    LEFT JOIN tenaga_kerjas tk ON tk.unit_layanan_id = u.id
    GROUP BY u.id ORDER BY u.nama
");
include __DIR__ . '/includes/layout-head.php';
include __DIR__ . '/includes/layout-sidebar.php';
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Master Data Unit Layanan</h1>
            <p class="text-xs text-gray-400 font-medium">Daftar 5 Unit Layanan PLN UP3 &amp; ULP di wilayah kerja Ponorogo</p>
        </div>
        <button onclick="document.getElementById('add-unit-modal').classList.remove('hidden')" class="btn-gold-primary px-4 py-2.5 text-xs uppercase tracking-wider gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i><span>Tambah Unit</span>
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($units as $u):
            $color = $u['color_hex'] ?? '#2BA8A2';
            $kode  = $u['kode'] ?? '???';
        ?>
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-white text-xs shadow-xs" style="background-color: <?= h($color) ?>">
                    <?= h(strtoupper(substr($kode, 0, 3))) ?>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-teal-50 text-primary border border-teal-200">
                    <?= h($u['tenaga_kerjas_count']) ?> Tenaga Kerja
                </span>
            </div>
            <div class="font-black text-gray-900 text-base"><?= h($u['nama']) ?></div>
            <div class="text-xs text-gray-400 font-bold mt-0.5">Kode: <?= h($kode) ?></div>
            <div class="text-xs text-gray-500 mt-2 font-medium">Unit Induk: <?= h($u['unit_induk'] ?? '-') ?></div>
                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                    <button type="button" onclick='openEditUnitModal(<?= json_encode($u, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="flex-1 py-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 text-xs font-black">Edit</button>
                    <form method="POST" action="<?= BASE_URL ?>/actions/unit-delete.php" class="flex-1" onsubmit="return confirm('Hapus unit ini? Data tenaga kerja akan tetap ada, tetapi unitnya dikosongkan.')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= h($u['id']) ?>">
                        <button type="submit" class="w-full py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-black">Hapus</button>
                    </form>
                </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Edit Unit -->
<div id="edit-unit-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-black text-gray-900 text-base">Edit Unit Layanan</h3>
            <button type="button" onclick="closeEditUnitModal()" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/actions/unit-update.php" class="space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="edit-unit-id">
            <div><label class="block text-xs font-bold text-gray-700 mb-1">Kode Unit</label><input type="text" name="kode" id="edit-unit-kode" required class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs"></div>
            <div><label class="block text-xs font-bold text-gray-700 mb-1">Nama Unit Layanan</label><input type="text" name="nama" id="edit-unit-nama" required class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs"></div>
            <div><label class="block text-xs font-bold text-gray-700 mb-1">Unit Induk</label><input type="text" name="unit_induk" id="edit-unit-induk" required class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs"></div>
            <div><label class="block text-xs font-bold text-gray-700 mb-1">Warna Aksen (Hex)</label><input type="color" name="color_hex" id="edit-unit-color" class="h-9 w-14 rounded-lg border border-gray-200 cursor-pointer"></div>
            <button type="submit" class="btn-gold-primary py-2.5 text-xs uppercase tracking-wider w-full justify-center">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
function openEditUnitModal(unit) {
    document.getElementById('edit-unit-id').value = unit.id;
    document.getElementById('edit-unit-kode').value = unit.kode || '';
    document.getElementById('edit-unit-nama').value = unit.nama || '';
    document.getElementById('edit-unit-induk').value = unit.unit_induk || '';
    document.getElementById('edit-unit-color').value = unit.color_hex || '#2BA8A2';
    document.getElementById('edit-unit-modal').classList.remove('hidden');
}
function closeEditUnitModal() { document.getElementById('edit-unit-modal').classList.add('hidden'); }
</script>

<!-- Modal Tambah Unit -->
<div id="add-unit-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-black text-gray-900 text-base">Tambah Unit Layanan</h3>
            <button onclick="document.getElementById('add-unit-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/actions/unit-store.php" class="space-y-3">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Kode Unit</label>
                <input type="text" name="kode" required placeholder="Contoh: ULP_SAMPUNG" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Unit Layanan</label>
                <input type="text" name="nama" required placeholder="Contoh: ULP Sampung" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Unit Induk</label>
                <input type="text" name="unit_induk" value="UP3 Ponorogo" required class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Warna Aksen (Hex)</label>
                <div class="flex gap-2">
                    <input type="color" name="color_hex" value="#2BA8A2" class="h-9 w-14 rounded-lg border border-gray-200 cursor-pointer">
                    <input type="text" id="color-hex-text" value="#2BA8A2" placeholder="#2BA8A2" class="flex-1 px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs" onchange="document.querySelector('[name=color_hex]').value=this.value">
                </div>
            </div>
            <button type="submit" class="btn-gold-primary py-2.5 text-xs uppercase tracking-wider w-full mt-4 justify-center">Simpan Unit Layanan</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>
