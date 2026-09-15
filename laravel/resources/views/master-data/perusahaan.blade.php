@extends('layouts.app')

@section('title', 'Master Data Perusahaan Mitra')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Master Data Perusahaan Mitra</h1>
            <p class="text-xs text-gray-400 font-medium">Daftar vendor penyedia jasa outsourcing yang bermitra dengan PLN</p>
        </div>
        <button onclick="document.getElementById('add-company-modal').classList.remove('hidden')" class="btn-gold-primary px-4 py-2.5 text-xs uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Perusahaan</span>
        </button>
    </div>

    <!-- Companies List -->
    <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom">
        <div class="divide-y divide-gray-100">
            @foreach($companies as $c)
                <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center font-black text-sm">
                            <i data-lucide="briefcase" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="font-extrabold text-gray-900 text-sm">{{ $c->nama }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">No. Perjanjian: {{ $c->nomor_perjanjian ?: '-' }}</div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Mitra Aktif
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal Tambah Perusahaan -->
<div id="add-company-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-black text-gray-900 text-base">Tambah Perusahaan Mitra</h3>
            <button onclick="document.getElementById('add-company-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('master-data.perusahaan.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Perusahaan</label>
                <input type="text" name="nama" required placeholder="Contoh: PT ANUGERAH PUTRA PERMANA" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nomor Perjanjian / Kontrak</label>
                <input type="text" name="nomor_perjanjian" placeholder="Contoh: 1211,Pj/DAN,00,07/..." class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs">
            </div>
            <button type="submit" class="btn-gold-primary py-2.5 text-xs uppercase tracking-wider w-full mt-4">
                Simpan Perusahaan
            </button>
        </form>
    </div>
</div>
@endsection
