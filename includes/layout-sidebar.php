<?php
// includes/layout-sidebar.php
// Include SETELAH layout-head.php
// Lalu di akhir halaman include layout-footer.php
$tkCount = db_val("SELECT COUNT(*) FROM tenaga_kerjas") ?: 0;
$unitCount = db_val("SELECT COUNT(*) FROM unit_layanans") ?: 0;

function nav_active(string $page): string {
    global $currentPage;
    return $currentPage === $page
        ? 'bg-primaryDark text-white shadow-teal-glow'
        : 'text-gray-600 hover:bg-teal-50 hover:text-primaryDark';
}
?>

<!-- Sidebar Mobile Overlay -->
<div id="mobile-sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

<!-- Sidebar -->
<aside id="main-sidebar" class="fixed md:sticky top-0 left-0 h-screen w-72 bg-white border-r border-teal-100 flex flex-col z-50 transition-transform -translate-x-full md:translate-x-0 shrink-0">
    <!-- Logo Header -->
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <a href="<?= BASE_URL ?>/dashboard.php" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-primary to-primaryDark shadow-teal-glow text-white font-black text-xl flex items-center justify-center">S</div>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="text-xl font-black tracking-tight text-primaryDark">SIMANTAP</span>
                </div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Data Tenaga Kerja</p>
            </div>
        </a>
        <button onclick="toggleSidebar()" class="md:hidden p-1.5 text-gray-400 hover:text-gray-600 rounded-lg">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-6">
        <div>
            <div class="px-3 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</div>
            <div class="space-y-1">
                <a href="<?= BASE_URL ?>/dashboard.php" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all <?= nav_active('dashboard.php') ?>">
                    <div class="flex items-center gap-3"><i data-lucide="layout-dashboard" class="w-4 h-4"></i><span>Dashboard</span></div>
                </a>
                <a href="<?= BASE_URL ?>/tenaga-kerja.php" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all <?= nav_active('tenaga-kerja.php') ?>">
                    <div class="flex items-center gap-3"><i data-lucide="users" class="w-4 h-4"></i><span>Data Tenaga Kerja</span></div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-black <?= $currentPage === 'tenaga-kerja.php' ? 'bg-accentGold text-gray-900' : 'bg-amber-100 text-amber-800' ?>"><?= h($tkCount) ?></span>
                </a>
            </div>
        </div>

        <div>
            <div class="px-3 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Master Data</div>
            <div class="space-y-1">
                <a href="<?= BASE_URL ?>/master-data-unit.php" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all <?= nav_active('master-data-unit.php') ?>">
                    <div class="flex items-center gap-3"><i data-lucide="building-2" class="w-4 h-4"></i><span>Unit Layanan</span></div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-black bg-amber-100 text-amber-800"><?= h($unitCount) ?></span>
                </a>
                <a href="<?= BASE_URL ?>/master-data-perusahaan.php" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all <?= nav_active('master-data-perusahaan.php') ?>">
                    <div class="flex items-center gap-3"><i data-lucide="briefcase" class="w-4 h-4"></i><span>Perusahaan Mitra</span></div>
                </a>
            </div>
        </div>

        <div>
            <div class="px-3 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Sistem &amp; Akses</div>
            <div class="space-y-1">
                <a href="<?= BASE_URL ?>/pengaturan-admin.php" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all <?= nav_active('pengaturan-admin.php') ?>">
                    <div class="flex items-center gap-3"><i data-lucide="shield-check" class="w-4 h-4"></i><span>Kelola Admin</span></div>
                </a>
            </div>
        </div>
    </nav>

    <!-- Footer Profile -->
    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-teal-800 text-white font-black flex items-center justify-center text-sm">SA</div>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-black text-gray-900 truncate">Super Admin</div>
                <div class="text-[11px] text-gray-400 truncate">admin@simantap.id</div>
            </div>
        </div>
    </div>
</aside>

<!-- Main Content Area -->
<div class="flex-1 flex flex-col min-w-0 min-h-screen">
    <!-- Top Navbar -->
    <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-teal-100 px-4 sm:px-8 py-3.5 flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="md:hidden p-2 text-gray-600 hover:bg-teal-50 rounded-xl">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <div>
                <div class="text-xs font-bold text-teal-600 tracking-wide uppercase">Wilayah Kerja UP3 Ponorogo</div>
                <div class="text-sm font-black text-gray-900">Sistem Database Tenaga Kerja Mitra</div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Database MySQL Aktif</span>
            </div>
        </div>
    </header>

    <!-- Flash Notification -->
    <?php if ($flash): ?>
        <?php $isSuccess = $flash['type'] === 'success'; ?>
        <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-2xl <?= $isSuccess ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800' ?> flex items-center gap-3 shadow-xs">
            <i data-lucide="<?= $isSuccess ? 'check-circle-2' : 'alert-circle' ?>" class="w-5 h-5 <?= $isSuccess ? 'text-emerald-600' : 'text-rose-600' ?> shrink-0"></i>
            <span class="text-sm font-bold"><?= h($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Page Body starts here — close in layout-footer.php -->
    <main class="flex-1 p-4 sm:p-8">
