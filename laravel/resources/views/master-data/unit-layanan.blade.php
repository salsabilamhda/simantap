@extends('layouts.app')

@section('title', 'Master Data Unit Layanan')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Master Data Unit Layanan</h1>
            <p class="text-xs text-gray-400 font-medium">Daftar 5 Unit Layanan PLN UP3 & ULP di wilayah kerja Ponorogo</p>
        </div>
        <button onclick="document.getElementById('add-unit-modal').classList.remove('hidden')" class="btn-gold-primary px-4 py-2.5 text-xs uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Unit</span>
        </button>
    </div>

    <!-- Units Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($units as $u)
            <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom relative overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-white text-xs shadow-xs" style="background-color: {{ $u->color_hex }}">
                        {{ substr($u->kode, 0, 3) }}
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-black bg-teal-50 text-primary border border-teal-200">
                        {{ $u->tenaga_kerjas_count }} Tenaga Kerja
                    </span>
                </div>
                <div class="font-black text-gray-900 text-base">{{ $u->nama }}</div>
                <div class="text-xs text-gray-400 font-bold mt-0.5">Kode: {{ $u->kode }}</div>
                <div class="text-xs text-gray-500 mt-2 font-medium">Unit Induk: {{ $u->unit_induk }}</div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal Tambah Unit -->
<div id="add-unit-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-black text-gray-900 text-base">Tambah Unit Layanan</h3>
            <button onclick="document.getElementById('add-unit-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('master-data.unit-layanan.store') }}" class="space-y-3">
            @csrf
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
                <input type="text" name="color_hex" value="#2BA8A2" required class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs">
            </div>
            <button type="submit" class="btn-gold-primary py-2.5 text-xs uppercase tracking-wider w-full mt-4">
                Simpan Unit Layanan
            </button>
        </form>
    </div>
</div>
@endsection
