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
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Impor &amp; Ekspor Excel</h1>
            <p class="text-xs text-gray-400 font-medium">Kelola data tenaga kerja dengan Excel tanpa Composer</p>
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

        <!-- Import Card -->
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                    <i data-lucide="file-up" class="w-6 h-6"></i>
                </div>
                <h2 class="text-lg font-black text-gray-900 mb-2">Import Data Tenaga Kerja</h2>
                <p class="text-xs text-gray-500 font-medium leading-relaxed mb-4">File dibaca di browser menggunakan SheetJS. Tidak perlu install Composer atau PhpSpreadsheet. Semua kolom pada template wajib ada dan wajib diisi.</p>
                <button type="button" id="download-template" class="btn-teal-outline py-2 px-4 text-xs gap-2 justify-center w-full mb-4">
                    <i data-lucide="file-down" class="w-4 h-4"></i><span>Unduh Template Import</span>
                </button>
                <label class="block text-xs font-black text-gray-700 mb-2">Pilih file `.xlsx` atau `.xls`</label>
                <input id="excel-file" type="file" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="block w-full text-xs text-gray-600 file:mr-3 file:rounded-full file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:text-xs file:font-bold file:text-primaryDark">
                <div id="import-message" class="hidden mt-3 rounded-xl px-3 py-2 text-xs font-bold"></div>
                <div id="preview-wrap" class="hidden mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-black text-gray-700">Preview</span>
                        <span id="preview-count" class="text-[11px] text-gray-400 font-bold"></span>
                    </div>
                    <div class="overflow-auto max-h-48 rounded-xl border border-gray-100">
                        <table class="w-full text-[10px] text-left" id="preview-table"><thead class="bg-teal-50"></thead><tbody class="divide-y divide-gray-100"></tbody></table>
                    </div>
                </div>
            </div>
            <button type="button" id="process-import" disabled class="btn-gold-primary py-3 px-6 text-xs uppercase tracking-wider gap-2 justify-center mt-5 disabled:opacity-40 disabled:cursor-not-allowed">
                <i data-lucide="database" class="w-4 h-4"></i><span>Proses Import</span>
            </button>
            <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-400 font-medium flex items-center justify-between">
                <span>Database: MySQL (phpMyAdmin)</span>
                <span class="text-primary font-bold">Total: <?= h($totalRecords) ?> Data</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
const importCsrf = <?= json_encode(csrf_token()) ?>;
const importHeaders = ['NO', 'NOMOR PERJANJIAN', 'NAMA PERUSAHAAN', 'NAMA', 'NIK', 'TEMPAT LAHIR', 'TANGGAL TAHUN LAHIR', 'NO TELEPON (WA)', 'EMAIL', 'JENIS KELAMIN', 'ALAMAT DOMISILI', 'KOTA/KABUPATEN', 'PROVINSI', 'JABATAN TERAKHIR', 'UNIT', 'UNIT LAYANAN', 'NOMOR SERTIFIKAT (SERTIFIKASI WAJIB)', 'JUDUL SERTIFIKASI', 'NOMOR BPJS KESEHATAN', 'NOMOR BPJS KETENAGAKERJAAN', 'NOMOR DPLK', 'BANK DPLK', 'NOMOR PERJANJIAN KERJA PKWT/PKWTT', 'TANGGAL MASUK KERJA', 'STATUS TENAGA KERJA (PKWT/PKWTT)', 'SKEMA TENAGA KERJA (PEMBORONGAN / VENDOR BASED)'];
let importRows = [];

function importMessage(text, success = false) {
    const box = document.getElementById('import-message');
    box.textContent = text;
    box.className = `mt-3 rounded-xl px-3 py-2 text-xs font-bold ${success ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'}`;
}

function renderImportPreview(rows) {
    importRows = rows;
    const visibleHeaders = importHeaders.slice(0, 8);
    document.querySelector('#preview-table thead').innerHTML = `<tr>${visibleHeaders.map((header) => `<th class="px-2 py-2 whitespace-nowrap">${header}</th>`).join('')}</tr>`;
    document.querySelector('#preview-table tbody').innerHTML = rows.slice(0, 20).map((row) => `<tr>${visibleHeaders.map((header) => `<td class="px-2 py-1 whitespace-nowrap">${String(row[header] ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]))}</td>`).join('')}</tr>`).join('');
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
    const reader = new FileReader();
    reader.onload = (loadEvent) => {
        try {
            const workbook = XLSX.read(loadEvent.target.result, {type: 'array', cellDates: true});
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const rows = XLSX.utils.sheet_to_json(sheet, {defval: '', raw: false});
            const normalizedRows = rows.map((row) => Object.fromEntries(Object.entries(row).map(([key, value]) => [key.replace(/^\uFEFF/, '').trim().toUpperCase(), String(value).replace(/^'/, '').trim()]))).filter((row) => Object.values(row).some(Boolean));
            const missingHeaders = importHeaders.filter((header) => !Object.keys(normalizedRows[0] || {}).includes(header));
            if (missingHeaders.length) throw new Error(`Kolom wajib belum ada: ${missingHeaders.join(', ')}`);
            renderImportPreview(normalizedRows);
            importMessage(`${normalizedRows.length} baris siap diproses. Periksa preview terlebih dahulu.`, normalizedRows.length > 0);
        } catch (error) {
            renderImportPreview([]);
            importMessage(error.message || 'File Excel tidak dapat dibaca.');
        }
    };
    reader.readAsArrayBuffer(file);
});

document.getElementById('process-import').addEventListener('click', async () => {
    const button = document.getElementById('process-import');
    button.disabled = true;
    button.querySelector('span').textContent = 'Memproses...';
    try {
        const response = await fetch('actions/import-excel.php', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-Token': importCsrf}, body: JSON.stringify({rows: importRows})});
        const result = await response.json();
        importMessage(result.message, response.ok && result.success);
        if (response.ok && result.success) { renderImportPreview([]); document.getElementById('excel-file').value = ''; }
    } catch (error) {
        importMessage('Server tidak dapat dihubungi.');
    } finally {
        button.disabled = importRows.length === 0;
        button.querySelector('span').textContent = 'Proses Import';
    }
});
</script>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>
