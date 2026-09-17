<?php
// login.php — Halaman Login Administrator SIMANTAP
require_once __DIR__ . '/db.php';

session_start_safe();

// Jika admin sudah login, langsung arahkan ke dashboard
if (auth_check()) {
    redirect(BASE_URL . '/dashboard.php');
}

$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator — SIMANTAP</title>
    <meta name="description" content="Masuk ke Sistem Manajemen Data Tenaga Kerja Outsourcing PLN UP3 & ULP Ponorogo">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        primary: '#2BA8A2',
                        primaryDark: '#1E8C86',
                        primaryLight: '#3CC4BD',
                        primaryBg: '#E8F6F5',
                        accentGold: '#FFD23F',
                        accentGoldDark: '#C9A227',
                        accentCream: '#FFF8E7',
                        coral: '#EF6C4A',
                        sky: '#5DADE2',
                        surfaceBase: '#EFF8F7',
                    },
                    boxShadow: {
                        'card-custom': '0 10px 40px -10px rgba(43, 168, 162, 0.15)',
                        'teal-glow': '0 4px 25px 0 rgba(43, 168, 162, 0.35)',
                        'gold-glow': '0 4px 20px 0 rgba(255, 210, 63, 0.45)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #EFF8F7;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: 
                radial-gradient(at 0% 0%, rgba(43, 168, 162, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(255, 210, 63, 0.08) 0px, transparent 50%);
        }
        .btn-gold-primary {
            background: linear-gradient(135deg, #FFD23F 0%, #FFC107 100%);
            color: #2C3E50;
            font-weight: 800;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(255, 210, 63, 0.4);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-gold-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 210, 63, 0.55);
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 antialiased selection:bg-teal-100 selection:text-teal-900">

    <!-- Container Utama -->
    <div class="w-full max-w-md my-8">
        
        <!-- Header Brand -->
        <div class="text-center mb-7">
            <div class="inline-flex items-center justify-center mb-3">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-primaryDark via-primary to-primaryLight shadow-teal-glow text-white font-black text-2xl flex items-center justify-center border-2 border-white/60">
                    S
                </div>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">SIMANTAP</h1>
            <p class="text-xs sm:text-sm font-semibold text-teal-800 mt-0.5">Sistem Manajemen Data Tenaga Kerja</p>
            <div class="inline-block mt-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-[11px] font-extrabold tracking-wide uppercase">
                PLN UP3 &amp; ULP Ponorogo
            </div>
        </div>

        <!-- Card Form Login -->
        <div class="glass-panel border border-teal-100/80 rounded-3xl p-6 sm:p-8 shadow-card-custom">
            
            <div class="mb-6">
                <h2 class="text-lg font-black text-gray-900">Masuk Akun Admin</h2>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Gunakan kredensial akun administrator untuk mengelola sistem.</p>
            </div>

            <!-- Flash Alert -->
            <?php if ($flash): ?>
                <?php $isSuccess = ($flash['type'] === 'success'); ?>
                <div class="mb-5 p-3.5 rounded-2xl text-xs font-bold flex items-start gap-2.5 <?= $isSuccess ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800' ?>">
                    <i data-lucide="<?= $isSuccess ? 'check-circle-2' : 'alert-circle' ?>" class="w-4 h-4 mt-0.5 shrink-0 <?= $isSuccess ? 'text-emerald-600' : 'text-rose-600' ?>"></i>
                    <span class="leading-relaxed"><?= h($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?= BASE_URL ?>/actions/login-process.php" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Field Username -->
                <div>
                    <label for="username" class="block text-xs font-bold text-gray-700 mb-1.5">
                        Username atau Email <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="username" name="username" required autofocus autocomplete="username"
                            placeholder="Contoh: 123456"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50/80 border border-gray-200 text-xs font-medium text-gray-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>

                <!-- Field Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-gray-700">
                            Password <span class="text-rose-500">*</span>
                        </label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            placeholder="Masukkan password"
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-gray-50/80 border border-gray-200 text-xs font-medium text-gray-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none" title="Lihat/Sembunyikan Password">
                            <i id="password-toggle-icon" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit" class="w-full btn-gold-primary py-3 text-xs uppercase tracking-wider gap-2">
                        <span>Masuk ke Sistem</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>

        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-xs text-gray-400 font-medium">
            &copy; <?= date('Y') ?> SIMANTAP — UP3 Ponorogo. Hak Cipta Dilindungi.
        </div>
    </div>

    <script>
        // Inisialisasi ikon Lucide
        lucide.createIcons();

        // Toggle intip password
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('password-toggle-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
