<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMANTAP') — Sistem Manajemen Data Tenaga Kerja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
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
                        'card-custom': '0 4px 20px -2px rgba(43, 168, 162, 0.08)',
                        'teal-glow': '0 4px 20px 0 rgba(43, 168, 162, 0.30)',
                        'gold-glow': '0 4px 20px 0 rgba(255, 210, 63, 0.40)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #EFF8F7;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-gold-primary {
            background: linear-gradient(135deg, #FFD23F 0%, #FFC107 100%);
            color: #2C3E50;
            font-weight: 800;
            border-radius: 9999px;
            box-shadow: 0 4px 14px rgba(255, 210, 63, 0.4);
            transition: all 0.2s ease;
        }
        .btn-gold-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255, 210, 63, 0.55);
        }
        .btn-teal-outline {
            background: #FFFFFF;
            color: #1E8C86;
            font-weight: 700;
            border: 1.5px solid #2BA8A2;
            border-radius: 9999px;
            transition: all 0.2s ease;
        }
        .btn-teal-outline:hover {
            background: #E8F6F5;
            color: #1E8C86;
        }
    </style>
</head>
<body class="min-h-screen text-gray-800 flex flex-col md:flex-row">

    <!-- Sidebar Mobile Overlay -->
    <div id="mobile-sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

    <!-- Sidebar -->
    <aside id="main-sidebar" class="fixed md:sticky top-0 left-0 h-screen w-72 bg-white border-r border-teal-100 flex flex-col z-50 transition-transform -translate-x-full md:translate-x-0 shrink-0">
        <!-- Logo Header -->
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-primary to-primaryDark shadow-teal-glow text-white font-black text-xl flex items-center justify-center">
                    S
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xl font-black tracking-tight text-primaryDark">SIMANTAP</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">v1.0</span>
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Data Tenaga Kerja</p>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="md:hidden p-1.5 text-gray-400 hover:text-gray-600 rounded-lg">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Unit Badge Banner -->
        <div class="mx-4 mt-4 p-2.5 rounded-2xl bg-[#FFF8E7] border border-[#FFD23F]/50 flex items-center gap-2 text-xs font-bold text-amber-800">
            <i data-lucide="sparkles" class="w-4 h-4 text-amber-600 shrink-0"></i>
            <span>Wilayah UP3 & 4 ULP Ponorogo</span>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-6">
            <!-- Section 1 -->
            <div>
                <div class="px-3 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</div>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-primaryDark text-white shadow-teal-glow' : 'text-gray-600 hover:bg-teal-50 hover:text-primaryDark' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Dashboard</span>
                        </div>
                    </a>

                    <a href="{{ route('tenaga-kerja.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('tenaga-kerja.*') ? 'bg-primaryDark text-white shadow-teal-glow' : 'text-gray-600 hover:bg-teal-50 hover:text-primaryDark' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            <span>Data Tenaga Kerja</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-black {{ request()->routeIs('tenaga-kerja.*') ? 'bg-accentGold text-gray-900' : 'bg-amber-100 text-amber-800' }}">
                            {{ \App\Models\TenagaKerja::count() ?? 198 }}
                        </span>
                    </a>

                    <a href="{{ route('import-export') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('import-export') ? 'bg-primaryDark text-white shadow-teal-glow' : 'text-gray-600 hover:bg-teal-50 hover:text-primaryDark' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                            <span>Impor & Ekspor Excel</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section 2: Master Data -->
            <div>
                <div class="px-3 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Master Data</div>
                <div class="space-y-1">
                    <a href="{{ route('master-data.unit-layanan') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('master-data.unit-layanan') ? 'bg-primaryDark text-white shadow-teal-glow' : 'text-gray-600 hover:bg-teal-50 hover:text-primaryDark' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="building-2" class="w-4 h-4"></i>
                            <span>Unit Layanan</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-black {{ request()->routeIs('master-data.unit-layanan') ? 'bg-accentGold text-gray-900' : 'bg-amber-100 text-amber-800' }}">5</span>
                    </a>

                    <a href="{{ route('master-data.perusahaan') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('master-data.perusahaan') ? 'bg-primaryDark text-white shadow-teal-glow' : 'text-gray-600 hover:bg-teal-50 hover:text-primaryDark' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="briefcase" class="w-4 h-4"></i>
                            <span>Perusahaan Mitra</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Section 3: Sistem -->
            <div>
                <div class="px-3 text-[11px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Sistem & Akses</div>
                <div class="space-y-1">
                    <a href="{{ route('pengaturan.admin') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-bold transition-all {{ request()->routeIs('pengaturan.admin') ? 'bg-primaryDark text-white shadow-teal-glow' : 'text-gray-600 hover:bg-teal-50 hover:text-primaryDark' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            <span>Kelola Admin</span>
                        </div>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Footer Profile -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-teal-800 text-white font-black flex items-center justify-center text-sm shadow-xs">
                    SA
                </div>
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

        <!-- Toast Notifications -->
        @if(session('success'))
            <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 shadow-xs">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3 shadow-xs">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                <span class="text-sm font-bold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Page Body -->
        <main class="flex-1 p-4 sm:p-8">
            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();

        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
