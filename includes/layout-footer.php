    </main>
</div>

<!-- Modal Konfirmasi Logout Kustom & Menarik -->
<div id="logout-confirm-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden opacity-0 transition-opacity duration-200">
    <div id="logout-modal-backdrop" onclick="closeLogoutModal()" class="fixed inset-0"></div>
    <div id="logout-modal-card" class="relative z-10 bg-white rounded-3xl max-w-sm w-full p-6 sm:p-7 shadow-2xl border border-teal-100/80 text-center transform transition-transform duration-200 scale-95">
        
        <!-- Icon Bulat dengan Efek Soft Glow -->
        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center mx-auto mb-4 shadow-sm">
            <i data-lucide="log-out" class="w-8 h-8"></i>
        </div>
        
        <h3 class="text-lg font-black text-gray-900 tracking-tight mb-1.5">Konfirmasi Keluar</h3>
        <p class="text-xs text-gray-500 font-medium leading-relaxed mb-6">
            Apakah Anda yakin ingin keluar dari sistem <strong class="text-gray-800 font-bold">SIMANTAP</strong>? Anda harus masuk kembali untuk mengelola data.
        </p>

        <!-- Tombol Aksi -->
        <div class="flex items-center gap-3">
            <button type="button" onclick="closeLogoutModal()" 
                class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all cursor-pointer">
                Batal
            </button>
            <a href="<?= BASE_URL ?>/actions/logout.php" 
                class="flex-1 py-2.5 px-4 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white text-xs font-extrabold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                <span>Ya, Keluar</span>
            </a>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    function toggleSidebar() {
        const sidebar = document.getElementById('main-sidebar');
        const backdrop = document.getElementById('mobile-sidebar-backdrop');
        sidebar.classList.toggle('-translate-x-full');
        backdrop.classList.toggle('hidden');
    }

    // Handler Modal Logout Kustom
    function openLogoutModal(e) {
        if (e && e.preventDefault) e.preventDefault();
        const modal = document.getElementById('logout-confirm-modal');
        const card = document.getElementById('logout-modal-card');
        if (!modal) return;
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            if (card) {
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }
        });
        if (window.lucide) lucide.createIcons();
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logout-confirm-modal');
        const card = document.getElementById('logout-modal-card');
        if (!modal) return;
        modal.classList.add('opacity-0');
        if (card) {
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLogoutModal();
        }
    });
</script>
</body>
</html>
