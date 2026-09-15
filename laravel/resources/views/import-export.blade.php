@extends('layouts.app')

@section('title', 'Impor & Ekspor Excel')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Impor & Ekspor Data Excel</h1>
            <p class="text-xs text-gray-400 font-medium">Sinkronisasi data tenaga kerja dengan format template 30 kolom acuan</p>
        </div>
    </div>

    <!-- 2 Column Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Export Card -->
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-primary flex items-center justify-center mb-4">
                    <i data-lucide="file-output" class="w-6 h-6"></i>
                </div>
                <h2 class="text-lg font-black text-gray-900 mb-2">Ekspor Database ke Excel (CSV)</h2>
                <p class="text-xs text-gray-500 font-medium leading-relaxed mb-6">
                    Unduh seluruh data tenaga kerja ({{ $totalRecords }} personil) lengkap dengan 30 kolom format asli, status kepegawaian, nomor BPJS, dan DPLK.
                </p>

                <div class="space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                        <span>Kompatibel langsung dengan Microsoft Excel & Google Sheets</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                        <span>NIK dan nomor jaminan diawali petik agar angka nol tidak hilang</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('import-export.export') }}" class="btn-gold-primary py-3 px-6 text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Unduh File Data Tenaga Kerja</span>
            </a>
        </div>

        <!-- Info / Import Guide Card -->
        <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                    <i data-lucide="info" class="w-6 h-6"></i>
                </div>
                <h2 class="text-lg font-black text-gray-900 mb-2">Struktur Kolom Database</h2>
                <p class="text-xs text-gray-500 font-medium leading-relaxed mb-4">
                    Data diorganisasikan berdasarkan format sheet <code>Duplikat</code> pada template acuan:
                </p>

                <div class="p-4 rounded-2xl bg-gray-50 text-xs space-y-1.5 text-gray-600">
                    <div><strong>Identitas:</strong> No, Perjanjian, Perusahaan, Nama, NIK, Tempat/Tgl Lahir, Usia, Pendidikan, Jurusan, Telp, Email, JK, Domisili, Kota, Provinsi</div>
                    <div class="pt-1"><strong>Pekerjaan:</strong> Jabatan, Fungsi, Unit, Unit Layanan, BPJS Kesehatan & Naker, DPLK, No PKWT, Tanggal Masuk, Status, Skema</div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-400 font-medium flex items-center justify-between">
                <span>Database: MySQL (Laragon)</span>
                <span class="text-primary font-bold">Total: {{ $totalRecords }} Data</span>
            </div>
        </div>
    </div>
</div>
@endsection
