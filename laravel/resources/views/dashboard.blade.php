@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- Hero Welcome Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-white via-teal-50 to-white rounded-3xl p-6 sm:p-8 border border-teal-200 shadow-card-custom">
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-300 text-amber-800 text-xs font-black mb-3">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i>
                    <span>Sistem Manajemen Data Tenaga Kerja Terintegrasi (Laravel 10 + MySQL)</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-primaryDark tracking-tight leading-tight">
                    Selamat Datang di SIMANTAP
                </h1>
                <p class="mt-2 text-sm text-gray-600 font-medium leading-relaxed">
                    Pusat kendali dan monitoring data tenaga kerja outsourcing/mitra di wilayah kerja 5 Unit Layanan (ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, dan UP3 Ponorogo).
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <a href="{{ route('tenaga-kerja.index') }}?tambah=true" class="btn-gold-primary px-5 py-3 text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer flex-1 md:flex-initial">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Tambah Tenaga Kerja</span>
                </a>
                <a href="{{ route('import-export') }}" class="btn-teal-outline px-5 py-3 text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer flex-1 md:flex-initial">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    <span>Impor / Ekspor</span>
                </a>
            </div>
        </div>

        <!-- Decorative Blurs -->
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-amber-300/20 blur-2xl pointer-events-none"></div>
    </div>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Tenaga Kerja -->
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom relative overflow-hidden group hover:border-primary transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-primary flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-teal-50 text-primary border border-teal-200">100% Aktif</span>
            </div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Tenaga Kerja</div>
            <div class="text-3xl font-black text-gray-900 mt-1">{{ $totalTenagaKerja }}</div>
            <div class="text-xs text-gray-400 font-medium mt-1">Tercatat di seluruh unit</div>
        </div>

        <!-- Status PKWTT -->
        <div class="bg-white rounded-3xl p-6 border border-amber-100 shadow-card-custom relative overflow-hidden group hover:border-amber-400 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-50 text-amber-800 border border-amber-300">
                    {{ $totalTenagaKerja > 0 ? round(($totalPkwtt / $totalTenagaKerja) * 100, 1) : 0 }}% Mayoritas
                </span>
            </div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status PKWTT</div>
            <div class="text-3xl font-black text-gray-900 mt-1">{{ $totalPkwtt }}</div>
            <div class="text-xs text-gray-400 font-medium mt-1">Perjanjian Kerja Tetap</div>
        </div>

        <!-- Status PKWT -->
        <div class="bg-white rounded-3xl p-6 border border-rose-100 shadow-card-custom relative overflow-hidden group hover:border-rose-400 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-rose-50 text-rose-600 border border-rose-200">
                    {{ $totalTenagaKerja > 0 ? round(($totalPkwt / $totalTenagaKerja) * 100, 1) : 0 }}% Kontrak
                </span>
            </div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status PKWT</div>
            <div class="text-3xl font-black text-gray-900 mt-1">{{ $totalPkwt }}</div>
            <div class="text-xs text-gray-400 font-medium mt-1">Perjanjian Waktu Tertentu</div>
        </div>

        <!-- Unit Layanan -->
        <div class="bg-white rounded-3xl p-6 border border-sky-100 shadow-card-custom relative overflow-hidden group hover:border-sky-400 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-sky-50 text-sky-600 border border-sky-200">5 Lokasi</span>
            </div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Unit Layanan</div>
            <div class="text-3xl font-black text-gray-900 mt-1">{{ $unitCount }}</div>
            <div class="text-xs text-gray-400 font-medium mt-1">ULP & UP3 Ponorogo</div>
        </div>
    </div>

    <!-- Section 2: Distribution & Partner Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Distribution per Unit Layanan (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-black text-gray-900">Distribusi Unit Layanan</h2>
                    <p class="text-xs text-gray-400 font-medium">Replikasi Pivot Sheet1 Acuan Database Excel</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-teal-50 text-primaryDark border border-teal-200">
                    Total: {{ $totalTenagaKerja }} Personil
                </span>
            </div>

            <div class="space-y-4">
                @foreach($units as $unit)
                    @php
                        $pct = $totalTenagaKerja > 0 ? round(($unit->count / $totalTenagaKerja) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $unit->color_hex }}"></span>
                                <span class="text-gray-800">{{ $unit->nama }}</span>
                            </div>
                            <div class="text-gray-500">
                                <span class="text-gray-400 font-normal">{{ $unit->pkwtt_count }} PKWTT / {{ $unit->pkwt_count }} PKWT</span>
                                <span class="ml-2 font-black text-gray-900">{{ $unit->count }} ({{ $pct }}%)</span>
                            </div>
                        </div>
                        <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden flex">
                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%; background-color: {{ $unit->color_hex }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Partner Info (1 Col) -->
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <i data-lucide="briefcase" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Ringkasan Mitra</h2>
                        <p class="text-[11px] text-gray-400">Penyedia Jasa & Demografi</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-teal-50/50 border border-teal-100 mb-4">
                    <div class="text-[11px] font-bold text-gray-400 uppercase">Perusahaan Penyedia Utama</div>
                    <div class="text-sm font-black text-primaryDark mt-0.5">PT ANUGERAH PUTRA PERMANA</div>
                    <div class="text-xs text-gray-500 mt-1">No. Kontrak: 1211,Pj/DAN,00,07/F04000000/2024</div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50">
                        <span class="text-gray-500 font-medium">Framework Backend:</span>
                        <span class="font-black text-rose-600">Laravel 10.x</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50">
                        <span class="text-gray-500 font-medium">Database Server:</span>
                        <span class="font-black text-teal-700">MySQL / Laragon</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50">
                        <span class="text-gray-500 font-medium">Skema Mayoritas:</span>
                        <span class="font-black text-gray-800">Pemborongan</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('tenaga-kerja.index') }}" class="w-full py-2.5 rounded-2xl bg-teal-50 text-primaryDark hover:bg-teal-100 text-xs font-black flex items-center justify-center gap-2 transition-colors">
                    <span>Lihat Seluruh Tenaga Kerja</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Section 3: Recent Workers Table Preview -->
    <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-black text-gray-900">Data Tenaga Kerja Terkini</h2>
                <p class="text-xs text-gray-400 font-medium">Data yang tersimpan di MySQL database SIMANTAP</p>
            </div>
            <a href="{{ route('tenaga-kerja.index') }}" class="text-xs font-extrabold text-primary hover:text-primaryDark flex items-center gap-1">
                <span>Buka Manajemen Tabel</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 uppercase tracking-wider font-extrabold">
                        <th class="py-3 px-3">Nama & NIK</th>
                        <th class="py-3 px-3">Unit Layanan</th>
                        <th class="py-3 px-3">Jabatan</th>
                        <th class="py-3 px-3">Pendidikan</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">Sertifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($recentWorkers as $w)
                        <tr class="hover:bg-teal-50/40 transition-colors">
                            <td class="py-3.5 px-3">
                                <div class="font-extrabold text-gray-900">{{ $w->nama }}</div>
                                <div class="text-[11px] text-gray-400">NIK: {{ $w->nik ?: '-' }}</div>
                            </td>
                            <td class="py-3.5 px-3 font-bold text-gray-700">
                                {{ $w->unitLayanan ? $w->unitLayanan->nama : $w->unit }}
                            </td>
                            <td class="py-3.5 px-3 text-gray-600">
                                {{ $w->jabatan_terakhir ?: '-' }}
                            </td>
                            <td class="py-3.5 px-3 text-gray-600">
                                {{ $w->pendidikan_terakhir ?: '-' }} {{ $w->jurusan ? '(' . $w->jurusan . ')' : '' }}
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $w->status_tenaga_kerja == 'PKWTT' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $w->status_tenaga_kerja }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200">
                                    {{ $w->sertifikasis->count() }} Sertifikat
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400 font-medium">
                                Belum ada data. Silakan jalankan migrasi database dan seeder.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
