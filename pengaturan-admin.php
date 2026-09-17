<?php
$pageTitle = 'Kelola Admin';
require_once __DIR__ . '/db.php';

$admins = db_query("SELECT * FROM users ORDER BY created_at DESC");
$totalAdmin = count($admins);
$totalSuper = count(array_filter($admins, fn($a) => ($a['role'] ?? '') === 'superadmin'));
$totalOps   = count(array_filter($admins, fn($a) => ($a['role'] ?? '') === 'admin'));
$totalAktif = count(array_filter($admins, fn($a) => ($a['status'] ?? 'Aktif') === 'Aktif'));

include __DIR__ . '/includes/layout-head.php';
include __DIR__ . '/includes/layout-sidebar.php';
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2.5">
                <span>Kelola Akun Admin</span>
                <span class="text-xs font-extrabold px-2.5 py-1 rounded-full bg-teal-50 text-primary border border-teal-200">
                    <?= $totalAdmin ?> Pengguna
                </span>
            </h1>
            <p class="text-xs text-gray-400 font-medium mt-1">Manajemen akun pengguna berwenang untuk hak akses sistem database SIMANTAP</p>
        </div>
        <button type="button" onclick="openAddAdminModal()" class="btn-gold-primary px-4 py-2.5 text-xs uppercase tracking-wider gap-2 shadow-gold-glow">
            <i data-lucide="user-plus" class="w-4 h-4"></i><span>Tambah Admin</span>
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-teal-100 shadow-card-custom flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-primary flex items-center justify-center font-bold">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Admin</div>
                <div class="text-lg font-black text-gray-900 leading-tight"><?= $totalAdmin ?></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-teal-100 shadow-card-custom flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Super Admin</div>
                <div class="text-lg font-black text-gray-900 leading-tight"><?= $totalSuper ?></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-teal-100 shadow-card-custom flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="user-cog" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Operasional</div>
                <div class="text-lg font-black text-gray-900 leading-tight"><?= $totalOps ?></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-teal-100 shadow-card-custom flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Akun Aktif</div>
                <div class="text-lg font-black text-gray-900 leading-tight"><?= $totalAktif ?></div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-4 border border-teal-100 shadow-card-custom flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="relative flex-1">
            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input type="text" id="admin-search-input" placeholder="Cari nama atau email admin..." 
                   class="w-full pl-10 pr-4 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:outline-none focus:border-primary transition-all">
        </div>
        <div class="flex items-center gap-2">
            <select id="role-filter" class="px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 focus:bg-white focus:outline-none focus:border-primary">
                <option value="">Semua Role</option>
                <option value="superadmin">Super Admin</option>
                <option value="admin">Admin Operasional</option>
            </select>
            <select id="status-filter" class="px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 focus:bg-white focus:outline-none focus:border-primary">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Nonaktif">Nonaktif</option>
            </select>
        </div>
    </div>

    <!-- Admin List Card -->
    <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom overflow-hidden">
        <?php if (empty($admins)): ?>
            <div class="py-12 text-center text-gray-400 text-xs font-medium flex flex-col items-center justify-center gap-2">
                <i data-lucide="users" class="w-10 h-10 text-gray-300"></i>
                <span>Belum ada akun admin terdaftar.</span>
            </div>
        <?php else: ?>
        <div class="divide-y divide-gray-100" id="admin-list-container">
            <?php foreach ($admins as $admin): 
                $adminSafe = [
                    'id'       => $admin['id'],
                    'name'     => $admin['name'],
                    'username' => $admin['username'] ?? '',
                    'email'    => $admin['email'],
                    'role'     => $admin['role'],
                    'status'   => $admin['status'] ?? 'Aktif',
                ];
                $isSuperAdmin = ($admin['role'] === 'superadmin');
                $isActive = (($admin['status'] ?? 'Aktif') === 'Aktif');
            ?>
            <div class="admin-item py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors"
                 data-name="<?= h(strtolower($admin['name'])) ?>"
                 data-username="<?= h(strtolower($admin['username'] ?? '')) ?>"
                 data-email="<?= h(strtolower($admin['email'])) ?>"
                 data-role="<?= h($admin['role']) ?>"
                 data-status="<?= h($admin['status'] ?? 'Aktif') ?>">
                
                <!-- Info Kiri -->
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl <?= $isSuperAdmin ? 'bg-amber-100 text-amber-800' : 'bg-teal-50 text-primary' ?> font-black text-sm flex items-center justify-center shrink-0">
                        <?= h(strtoupper(substr($admin['name'], 0, 2))) ?>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-gray-900 text-sm"><?= h($admin['name']) ?></span>
                            <?php if ($isSuperAdmin): ?>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-800">SUPER</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-xs text-gray-400 flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5 font-medium">
                            <?php if (!empty($admin['username'])): ?>
                                <span class="flex items-center gap-1 font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-100">
                                    <i data-lucide="user" class="w-3 h-3 text-teal-600"></i>
                                    <?= h($admin['username']) ?>
                                </span>
                                <span class="text-gray-300">•</span>
                            <?php endif; ?>
                            <span class="flex items-center gap-1">
                                <i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i>
                                <?= h($admin['email']) ?>
                            </span>
                            <span class="text-gray-300">•</span>
                            <span class="flex items-center gap-1 text-gray-400">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                Dibuat: <?= format_tanggal($admin['created_at']) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bagian Kanan: Badge & Actions -->
                <div class="flex items-center gap-3 sm:justify-end flex-wrap">
                    <!-- Badges -->
                    <span class="px-2.5 py-1 rounded-full text-xs font-black <?= $isSuperAdmin ? 'bg-amber-50 text-amber-800 border border-amber-300' : 'bg-teal-50 text-teal-800 border border-teal-200' ?>">
                        <?= $isSuperAdmin ? 'Super Admin' : 'Admin Operasional' ?>
                    </span>
                    
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 <?= $isActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= $isActive ? 'bg-emerald-500' : 'bg-rose-500' ?>"></span>
                        <?= h($admin['status'] ?? 'Aktif') ?>
                    </span>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-1.5 pl-2 border-l border-gray-100">
                        <button type="button" 
                                onclick='openEditAdminModal(<?= json_encode($adminSafe, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 text-xs font-black inline-flex items-center gap-1.5 transition-colors"
                                title="Edit Akun Admin">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            <span>Edit</span>
                        </button>

                        <?php if ($totalAdmin > 1): ?>
                        <form method="POST" action="<?= BASE_URL ?>/actions/admin-delete.php" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin <?= h(addslashes($admin['name'])) ?>? Tindakan ini tidak dapat dibatalkan.')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= h($admin['id']) ?>">
                            <button type="submit" 
                                    class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-black inline-flex items-center gap-1.5 transition-colors"
                                    title="Hapus Akun Admin">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                        <?php else: ?>
                        <button type="button" disabled 
                                class="px-3 py-1.5 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed text-xs font-black inline-flex items-center gap-1.5"
                                title="Tidak dapat menghapus admin satu-satunya">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Hapus</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="no-filter-results" class="py-12 text-center text-gray-400 text-xs font-medium hidden">
            Tidak ada akun admin yang cocok dengan pencarian / filter.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div id="add-admin-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div>
                <h3 class="font-black text-gray-900 text-base">Tambah Akun Admin</h3>
                <p class="text-xs text-gray-400 font-medium">Buat kredensial admin baru untuk SIMANTAP</p>
            </div>
            <button type="button" onclick="closeAddAdminModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/actions/admin-store.php" class="space-y-3">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Username Login</label>
                <input type="text" name="username" placeholder="Contoh: 123456 (kredensial username login)" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" required placeholder="budi@simantap.id" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Password <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="password" name="password" id="add-admin-password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full pl-3.5 pr-10 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:outline-none focus:border-primary">
                    <button type="button" onclick="togglePasswordVisibility('add-admin-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Role Hak Akses</label>
                    <select name="role" class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold focus:bg-white focus:outline-none focus:border-primary">
                        <option value="admin">Admin Operasional</option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Status Akun</label>
                    <select name="status" class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold focus:bg-white focus:outline-none focus:border-primary">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="pt-2 flex items-center gap-2">
                <button type="button" onclick="closeAddAdminModal()" class="w-1/3 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 text-center">Batal</button>
                <button type="submit" class="w-2/3 btn-gold-primary py-2.5 text-xs uppercase tracking-wider justify-center">Buat Akun Admin</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Admin -->
<div id="edit-admin-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div>
                <h3 class="font-black text-gray-900 text-base">Edit Akun Admin</h3>
                <p class="text-xs text-gray-400 font-medium">Perbarui informasi data atau hak akses admin</p>
            </div>
            <button type="button" onclick="closeEditAdminModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/actions/admin-update.php" class="space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="edit-admin-id">
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit-admin-name" required class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Username Login</label>
                <input type="text" name="username" id="edit-admin-username" placeholder="Contoh: 123456 (kredensial username login)" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" id="edit-admin-email" required class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:outline-none focus:border-primary">
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-gray-700">Password Baru</label>
                    <span class="text-[10px] text-gray-400">Kosongkan jika tidak diubah</span>
                </div>
                <div class="relative">
                    <input type="password" name="password" id="edit-admin-password" minlength="6" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full pl-3.5 pr-10 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium focus:bg-white focus:outline-none focus:border-primary">
                    <button type="button" onclick="togglePasswordVisibility('edit-admin-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Role Hak Akses</label>
                    <select name="role" id="edit-admin-role" class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold focus:bg-white focus:outline-none focus:border-primary">
                        <option value="admin">Admin Operasional</option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Status Akun</label>
                    <select name="status" id="edit-admin-status" class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold focus:bg-white focus:outline-none focus:border-primary">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="pt-2 flex items-center gap-2">
                <button type="button" onclick="closeEditAdminModal()" class="w-1/3 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 text-center">Batal</button>
                <button type="submit" class="w-2/3 btn-gold-primary py-2.5 text-xs uppercase tracking-wider justify-center">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddAdminModal() {
    document.getElementById('add-admin-modal').classList.remove('hidden');
    if (window.lucide) lucide.createIcons();
}

function closeAddAdminModal() {
    document.getElementById('add-admin-modal').classList.add('hidden');
}

function openEditAdminModal(admin) {
    document.getElementById('edit-admin-id').value = admin.id;
    document.getElementById('edit-admin-name').value = admin.name || '';
    document.getElementById('edit-admin-username').value = admin.username || '';
    document.getElementById('edit-admin-email').value = admin.email || '';
    document.getElementById('edit-admin-password').value = '';
    document.getElementById('edit-admin-role').value = admin.role || 'admin';
    document.getElementById('edit-admin-status').value = admin.status || 'Aktif';
    document.getElementById('edit-admin-modal').classList.remove('hidden');
    if (window.lucide) lucide.createIcons();
}

function closeEditAdminModal() {
    document.getElementById('edit-admin-modal').classList.add('hidden');
}

function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) icon.setAttribute('data-lucide', 'eye-off');
    } else {
        input.type = 'password';
        if (icon) icon.setAttribute('data-lucide', 'eye');
    }
    if (window.lucide) lucide.createIcons();
}

// Live Search and Filter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('admin-search-input');
    const roleFilter = document.getElementById('role-filter');
    const statusFilter = document.getElementById('status-filter');
    const items = document.querySelectorAll('.admin-item');
    const noResults = document.getElementById('no-filter-results');

    function applyFilter() {
        const query = (searchInput?.value || '').toLowerCase().trim();
        const selectedRole = (roleFilter?.value || '').toLowerCase();
        const selectedStatus = (statusFilter?.value || '');

        let visibleCount = 0;

        items.forEach(function(item) {
            const name = item.getAttribute('data-name') || '';
            const username = item.getAttribute('data-username') || '';
            const email = item.getAttribute('data-email') || '';
            const role = item.getAttribute('data-role') || '';
            const status = item.getAttribute('data-status') || '';

            const matchesQuery = !query || name.includes(query) || username.includes(query) || email.includes(query);
            const matchesRole = !selectedRole || role === selectedRole;
            const matchesStatus = !selectedStatus || status === selectedStatus;

            if (matchesQuery && matchesRole && matchesStatus) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (noResults) {
            if (visibleCount === 0 && items.length > 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (roleFilter) roleFilter.addEventListener('change', applyFilter);
    if (statusFilter) statusFilter.addEventListener('change', applyFilter);
});
</script>

<?php include __DIR__ . '/includes/layout-footer.php'; ?>
