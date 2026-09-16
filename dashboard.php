<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/db.php';

// --- Data queries ---
$totalTenagaKerja = (int) db_val("SELECT COUNT(*) FROM tenaga_kerjas");
$totalPkwtt       = (int) db_val("SELECT COUNT(*) FROM tenaga_kerjas WHERE status_tenaga_kerja = 'PKWTT'");
$totalPkwt        = (int) db_val("SELECT COUNT(*) FROM tenaga_kerjas WHERE status_tenaga_kerja = 'PKWT'");
$unitCount        = (int) db_val("SELECT COUNT(*) FROM unit_layanans");

$units = db_query("
    SELECT u.*,
        COUNT(tk.id) AS jumlah,
        SUM(CASE WHEN tk.status_tenaga_kerja = 'PKWTT' THEN 1 ELSE 0 END) AS pkwtt_count,
        SUM(CASE WHEN tk.status_tenaga_kerja = 'PKWT'  THEN 1 ELSE 0 END) AS pkwt_count
    FROM unit_layanans u
    LEFT JOIN tenaga_kerjas tk ON tk.unit_layanan_id = u.id
    GROUP BY u.id
    ORDER BY jumlah DESC
");

$recentWorkers = db_query("
    SELECT tk.*, u.nama AS unit_nama,
        (SELECT COUNT(*) FROM sertifikasis s WHERE s.tenaga_kerja_id = tk.id) AS sertifikasi_count
    FROM tenaga_kerjas tk
    LEFT JOIN unit_layanans u ON u.id = tk.unit_layanan_id
    ORDER BY tk.id DESC
    LIMIT 5
");

$totalSertifikasi = (int) db_val("SELECT COUNT(*) FROM sertifikasis");
$totalPemborongan = (int) db_val("SELECT COUNT(*) FROM tenaga_kerjas WHERE skema_tenaga_kerja LIKE '%PEMBORONGAN%'");
$totalVolumeBased = (int) db_val("SELECT COUNT(*) FROM tenaga_kerjas WHERE skema_tenaga_kerja LIKE '%VOLUME%' OR skema_tenaga_kerja LIKE '%VENDOR%'");
$mitraUtama       = db_val("SELECT nama_perusahaan FROM tenaga_kerjas WHERE nama_perusahaan IS NOT NULL AND nama_perusahaan != '' GROUP BY nama_perusahaan ORDER BY COUNT(*) DESC LIMIT 1") ?: 'PT ANUGERAH PUTRA PERMANA';

include __DIR__ . '/includes/layout-head.php';
include __DIR__ . '/includes/layout-sidebar.php';
?>

<div class="space-y-8 max-w-7xl mx-auto">

    <!-- Hero Welcome Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-white via-teal-50 to-white rounded-3xl p-6 sm:p-8 border border-teal-200 shadow-card-custom">
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <h1 class="text-2xl sm:text-4xl font-black text-primaryDark tracking-tight leading-tight">Selamat Datang di SIMANTAP</h1>
                <p class="mt-2 text-sm text-gray-600 font-medium leading-relaxed">
                    Pusat kendali dan monitoring data tenaga kerja outsourcing/mitra di wilayah kerja 5 Unit Layanan (ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, dan UP3 Ponorogo).
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <a href="<?= BASE_URL ?>/tenaga-kerja.php?tambah=1" class="btn-gold-primary px-5 py-3 text-xs uppercase tracking-wider gap-2 flex-1 md:flex-initial justify-center">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i><span>Tambah Tenaga Kerja</span>
                </a>
                <a href="<?= BASE_URL ?>/tenaga-kerja.php" class="btn-teal-outline px-5 py-3 text-xs uppercase tracking-wider gap-2 flex-1 md:flex-initial justify-center">
                    <i data-lucide="users" class="w-4 h-4"></i><span>Data Tenaga Kerja</span>
                </a>
            </div>
        </div>
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-amber-300/20 blur-2xl pointer-events-none"></div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom hover:border-primary transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-primary flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-teal-50 text-primary border border-teal-200">100% Aktif</span>
            </div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Tenaga Kerja</div>
            <div class="text-3xl font-black text-gray-900 mt-1"><?= h($totalTenagaKerja) ?></div>
            <div class="text-xs text-gray-400 font-medium mt-1">Tercatat di seluruh unit</div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-amber-100 shadow-card-custom hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-50 text-amber-800 border border-amber-300">
                    <?= $totalTenagaKerja > 0 ? round(($totalPkwtt / $totalTenagaKerja) * 100, 1) : 0 ?>% Mayoritas
                </span>
            </div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status PKWTT</div>
            <div class="text-3xl font-black text-gray-900 mt-1"><?= h($totalPkwtt) ?></div>
            <div class="text-xs text-gray-400 font-medium mt-1">Perjanjian Kerja Tetap</div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-rose-100 shadow-card-custom hover:border-rose-400 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-rose-50 text-rose-600 border border-rose-200">
                    <?= $totalTenagaKerja > 0 ? round(($totalPkwt / $totalTenagaKerja) * 100, 1) : 0 ?>% Kontrak
                </span>
            </div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status PKWT</div>
            <div class="text-3xl font-black text-gray-900 mt-1"><?= h($totalPkwt) ?></div>
            <div class="text-xs text-gray-400 font-medium mt-1">Perjanjian Waktu Tertentu</div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-sky-100 shadow-card-custom hover:border-sky-400 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-sky-50 text-sky-600 border border-sky-200">5 Lokasi</span>
            </div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Unit Layanan</div>
            <div class="text-3xl font-black text-gray-900 mt-1"><?= h($unitCount) ?></div>
            <div class="text-xs text-gray-400 font-medium mt-1">ULP &amp; UP3 Ponorogo</div>
        </div>
    </div>

    <!-- Distribution Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart per Unit -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-black text-gray-900">Distribusi Unit Layanan</h2>
                    <p class="text-xs text-gray-400 font-medium">Replikasi Pivot Sheet1 Acuan Database Excel</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-teal-50 text-primaryDark border border-teal-200">Total: <?= h($totalTenagaKerja) ?> Personil</span>
            </div>
            <div class="space-y-4">
                <?php foreach ($units as $unit):
                    $pct = $totalTenagaKerja > 0 ? round(($unit['jumlah'] / $totalTenagaKerja) * 100) : 0;
                    $color = $unit['color_hex'] ?? '#2BA8A2';
                ?>
                <div>
                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color: <?= h($color) ?>"></span>
                            <span class="text-gray-800"><?= h($unit['nama']) ?></span>
                        </div>
                        <div class="text-gray-500">
                            <span class="text-gray-400 font-normal"><?= h($unit['pkwtt_count']) ?> PKWTT / <?= h($unit['pkwt_count']) ?> PKWT</span>
                            <span class="ml-2 font-black text-gray-900"><?= h($unit['jumlah']) ?> (<?= $pct ?>%)</span>
                        </div>
                    </div>
                    <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500" style="width: <?= $pct ?>%; background-color: <?= h($color) ?>"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Operational Summary & Quick Access (Column 3) -->
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom flex flex-col justify-between space-y-5">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-black text-gray-900">Ringkasan Operasional</h2>
                        <p class="text-xs text-gray-400 font-medium">Skema kerja &amp; kepatuhan berkas</p>
                    </div>
                    <div class="w-9 h-9 rounded-2xl bg-teal-50 text-primary flex items-center justify-center">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                    </div>
                </div>

                <!-- Mitra Info -->
                <div class="p-3 rounded-2xl bg-teal-50/40 border border-teal-100/80 mb-4">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Perusahaan Mitra Utama</div>
                    <div class="text-xs font-black text-gray-900 mt-0.5 truncate flex items-center gap-1.5" title="<?= h($mitraUtama) ?>">
                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-primary shrink-0"></i>
                        <span class="truncate"><?= h($mitraUtama) ?></span>
                    </div>
                </div>

                <!-- Skema Tenaga Kerja Progress -->
                <div class="space-y-3">
                    <div class="text-[11px] font-extrabold text-gray-400 uppercase tracking-wider">Komposisi Skema Kerja</div>
                    
                    <?php
                        $pctPemborongan = $totalTenagaKerja > 0 ? round(($totalPemborongan / $totalTenagaKerja) * 100, 1) : 0;
                        $pctVolume      = $totalTenagaKerja > 0 ? round(($totalVolumeBased / $totalTenagaKerja) * 100, 1) : 0;
                    ?>
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span class="text-gray-700 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span>Pemborongan</span>
                            </span>
                            <span class="text-gray-900 font-black"><?= h($totalPemborongan) ?> <span class="text-gray-400 font-normal">(<?= $pctPemborongan ?>%)</span></span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full transition-all duration-500" style="width: <?= $pctPemborongan ?>%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-xs font-bold mb-1">
                            <span class="text-gray-700 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                <span>Volume Based</span>
                            </span>
                            <span class="text-gray-900 font-black"><?= h($totalVolumeBased) ?> <span class="text-gray-400 font-normal">(<?= $pctVolume ?>%)</span></span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-rose-500 rounded-full transition-all duration-500" style="width: <?= $pctVolume ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Sertifikat Info Mini Card -->
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i data-lucide="award" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-xs font-black text-gray-900"><?= h($totalSertifikasi) ?> Sertifikat</div>
                            <div class="text-[10px] text-gray-400 font-medium">Terverifikasi di sistem</div>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/tenaga-kerja.php" class="text-[11px] font-bold text-primary hover:underline">Kelola</a>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="pt-3 border-t border-gray-100 space-y-2">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Aksi Cepat</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="<?= BASE_URL ?>/export.php" class="p-2.5 rounded-xl bg-gray-50 hover:bg-teal-50 border border-gray-200 text-gray-700 hover:text-primaryDark text-xs font-bold flex items-center gap-2 transition-colors">
                        <i data-lucide="download" class="w-4 h-4 text-teal-600"></i>
                        <span>Ekspor Data</span>
                    </a>
                    <a href="<?= BASE_URL ?>/master-data-unit.php" class="p-2.5 rounded-xl bg-gray-50 hover:bg-teal-50 border border-gray-200 text-gray-700 hover:text-primaryDark text-xs font-bold flex items-center gap-2 transition-colors">
                        <i data-lucide="map-pin" class="w-4 h-4 text-teal-600"></i>
                        <span>Master Unit</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Workers Table -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-teal-100 shadow-card-custom">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-lg font-black text-gray-900">Data Tenaga Kerja Terkini</h2>
                <p class="text-xs text-gray-500 font-medium">5 data terbaru yang tersimpan di database SIMANTAP</p>
            </div>
            <a href="<?= BASE_URL ?>/tenaga-kerja.php" class="self-start sm:self-auto text-xs font-extrabold text-primary hover:text-primaryDark flex items-center gap-1 whitespace-nowrap">
                <span>Buka Manajemen Tabel</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="w-full min-w-[980px] table-fixed text-left text-xs font-sans">
                <colgroup>
                    <col class="w-[7%]">
                    <col class="w-[15%]">
                    <col class="w-[15%]">
                    <col class="w-[14%]">
                    <col class="w-[19%]">
                    <col class="w-[15%]">
                    <col class="w-[15%]">
                </colgroup>
                <thead class="bg-teal-50/70">
                    <tr class="border-b border-teal-100 text-gray-500 uppercase tracking-wider text-[10px] font-extrabold">
                        <th class="py-3 px-4 text-center">No</th>
                        <th class="py-3 px-4">Nama Tenaga Kerja</th>
                        <th class="py-3 px-4">NIK &amp; Usia</th>
                        <th class="py-3 px-4">Unit Layanan</th>
                        <th class="py-3 px-4">Jabatan Terakhir</th>
                        <th class="py-3 px-4">Status &amp; Skema</th>
                        <th class="py-3 px-4">Sertifikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    <?php if (empty($recentWorkers)): ?>
                        <tr><td colspan="7" class="py-10 text-center text-gray-500 font-medium">Belum ada data. Silakan tambahkan tenaga kerja.</td></tr>
                    <?php else: foreach ($recentWorkers as $index => $w): ?>
                        <tr class="hover:bg-teal-50/40 transition-colors align-top">
                            <td class="py-3.5 px-4 text-center font-bold text-gray-400"><?= $index + 1 ?></td>
                            <td class="py-3.5 px-4 overflow-hidden">
                                <div class="font-black text-gray-900 text-sm whitespace-normal break-words leading-snug"><?= h($w['nama']) ?></div>
                                <div class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5 whitespace-normal break-words leading-snug">
                                    <i data-lucide="phone" class="w-3 h-3"></i>
                                    <span><?= h($w['no_telepon'] ?: '-') ?></span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 overflow-hidden">
                                <div class="font-extrabold text-gray-700 tracking-wide whitespace-normal break-all leading-snug"><?= h($w['nik'] ?: '-') ?></div>
                                <div class="text-[11px] text-primary font-bold mt-0.5 whitespace-normal break-words leading-snug"><?= h(hitung_usia($w['tanggal_lahir'])) ?></div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-gray-800 whitespace-normal break-words leading-snug overflow-hidden"><?= h($w['unit_nama'] ?: $w['unit'] ?: '-') ?></td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-gray-800 whitespace-normal break-words leading-snug"><?= h($w['jabatan_terakhir'] ?: '-') ?></div>
                                <?php if (!empty($w['fungsi_pekerjaan'])): ?>
                                    <div class="text-[11px] text-gray-400 whitespace-normal break-words leading-snug mt-0.5"><?= h($w['fungsi_pekerjaan']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black <?= $w['status_tenaga_kerja'] === 'PKWTT' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-300' ?>">
                                    <?= h($w['status_tenaga_kerja']) ?>
                                </span>
                                <div class="text-[10px] text-gray-400 mt-1 font-bold whitespace-normal break-words leading-snug"><?= h($w['skema_tenaga_kerja'] ?: '-') ?></div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex px-2.5 py-1 rounded-xl bg-teal-50 text-primaryDark text-[11px] font-black border border-teal-200 items-center gap-1 whitespace-nowrap">
                                    <i data-lucide="award" class="w-3.5 h-3.5 text-primary"></i>
                                    <?= h($w['sertifikasi_count']) ?> File
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>
