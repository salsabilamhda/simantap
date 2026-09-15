@extends('layouts.app')

@section('title', 'Data Tenaga Kerja')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Data Tenaga Kerja</h1>
            <p class="text-xs text-gray-400 font-medium">Kelola dan pantau seluruh data personil outsourcing & mitra (MySQL Laravel 10)</p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <button onclick="openAddModal()" class="btn-gold-primary px-5 py-2.5 text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer w-full sm:w-auto">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Tenaga Kerja</span>
            </button>
            <a href="{{ route('import-export.export') }}" class="btn-teal-outline px-4 py-2.5 text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Ekspor Excel</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-3xl p-5 border border-teal-100 shadow-card-custom">
        <form method="GET" action="{{ route('tenaga-kerja.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search -->
            <div class="sm:col-span-6 relative">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIK, atau jabatan..." class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary focus:bg-white transition-all">
            </div>

            <!-- Unit Filter -->
            <div class="sm:col-span-3">
                <select name="unit" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 focus:outline-none focus:border-primary">
                    <option value="ALL">Semua Unit Layanan</option>
                    @foreach($unitLayanans as $u)
                        <option value="{{ $u->id }}" {{ request('unit') == $u->id ? 'selected' : '' }}>{{ $u->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-2">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 focus:outline-none focus:border-primary">
                    <option value="ALL">Semua Status</option>
                    <option value="PKWTT" {{ request('status') == 'PKWTT' ? 'selected' : '' }}>PKWTT (Tetap)</option>
                    <option value="PKWT" {{ request('status') == 'PKWT' ? 'selected' : '' }}>PKWT (Kontrak)</option>
                </select>
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2.5 rounded-2xl bg-primary hover:bg-primaryDark text-white text-xs font-bold transition-colors flex items-center justify-center">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl border border-teal-100 shadow-card-custom overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-teal-50/50 border-b border-teal-100 text-gray-400 uppercase tracking-wider font-extrabold">
                        <th class="py-4 px-4 w-12 text-center">No</th>
                        <th class="py-4 px-4">Nama Tenaga Kerja</th>
                        <th class="py-4 px-4">NIK & Usia</th>
                        <th class="py-4 px-4">Unit Layanan</th>
                        <th class="py-4 px-4">Jabatan Terakhir</th>
                        <th class="py-4 px-4">Status & Skema</th>
                        <th class="py-4 px-4">Sertifikat</th>
                        <th class="py-4 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($tenagaKerjaList as $index => $row)
                        <tr class="hover:bg-teal-50/40 transition-colors">
                            <td class="py-4 px-4 text-center font-bold text-gray-400">
                                {{ $tenagaKerjaList->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-black text-gray-900 text-sm">{{ $row->nama }}</div>
                                <div class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                                    <i data-lucide="phone" class="w-3 h-3"></i>
                                    <span>{{ $row->no_telepon ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-extrabold text-gray-700 tracking-wide">{{ $row->nik ?: '-' }}</div>
                                <div class="text-[11px] text-primary font-bold mt-0.5">{{ $row->usia }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-gray-800">
                                    {{ $row->unitLayanan ? $row->unitLayanan->nama : $row->unit }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-800">{{ $row->jabatan_terakhir ?: '-' }}</div>
                                <div class="text-[11px] text-gray-400">{{ $row->fungsi_pekerjaan ?: '-' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black {{ $row->status_tenaga_kerja == 'PKWTT' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                                    {{ $row->status_tenaga_kerja }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1 font-bold">{{ $row->skema_tenaga_kerja }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <button onclick='openCertModal(@json($row))' class="px-2.5 py-1 rounded-xl bg-teal-50 hover:bg-teal-100 text-primaryDark text-[11px] font-black border border-teal-200 flex items-center gap-1 transition-colors">
                                    <i data-lucide="award" class="w-3.5 h-3.5 text-primary"></i>
                                    <span>{{ $row->sertifikasis->count() }} File</span>
                                </button>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Detail -->
                                    <button onclick='openDetailModal(@json($row))' title="Lihat Detail Lengkap" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-teal-100 text-gray-600 hover:text-primaryDark flex items-center justify-center transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Edit -->
                                    <button onclick='openEditModal(@json($row))' title="Edit Data" class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 flex items-center justify-center transition-colors">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('tenaga-kerja.destroy', $row->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tenaga kerja {{ $row->nama }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Data" class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 font-bold">
                                Tidak ada data tenaga kerja yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold text-gray-500">
            <div>
                Menampilkan {{ $tenagaKerjaList->firstItem() ?? 0 }} - {{ $tenagaKerjaList->lastItem() ?? 0 }} dari {{ $tenagaKerjaList->total() }} tenaga kerja
            </div>
            <div>
                {{ $tenagaKerjaList->links() }}
            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Tenaga Kerja -->
<div id="add-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-in fade-in">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-teal-50/50">
            <div>
                <h3 class="text-lg font-black text-primaryDark">Tambah Tenaga Kerja Baru</h3>
                <p class="text-xs text-gray-400">Isi formulir lengkap sesuai format database 30 kolom</p>
            </div>
            <button onclick="closeAddModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('tenaga-kerja.store') }}" class="flex-1 overflow-y-auto p-6 space-y-6">
            @csrf
            
            <!-- Tab Group: Identitas Pribadi -->
            <div>
                <div class="text-xs font-black text-gray-400 uppercase tracking-wider mb-3">1. Identitas Pribadi</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="nama" required class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">NIK (16 Digit) *</label>
                        <input type="text" name="nik" required maxlength="16" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700">
                            <option value="LAKI">LAKI-LAKI</option>
                            <option value="PEREMPUAN">PEREMPUAN</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700">
                            <option value="SMA">SMA</option>
                            <option value="SMK" selected>SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jurusan</label>
                        <input type="text" name="jurusan" placeholder="Contoh: TEKNIK LISTRIK" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">No. Telepon / WhatsApp</label>
                        <input type="text" name="no_telepon" placeholder="Contoh: 0812-xxxx-xxxx" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Domisili</label>
                        <textarea name="alamat_domisili" rows="2" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary"></textarea>
                    </div>
                </div>
            </div>

            <!-- Tab Group: Penempatan & Pekerjaan -->
            <div>
                <div class="text-xs font-black text-gray-400 uppercase tracking-wider mb-3">2. Penempatan & Hubungan Kerja</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Unit Layanan *</label>
                        <select name="unit_layanan_id" required class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700">
                            @foreach($unitLayanans as $u)
                                <option value="{{ $u->id }}">{{ $u->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Perusahaan Mitra</label>
                        <select name="nama_perusahaan" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700">
                            @foreach($perusahaans as $p)
                                <option value="{{ $p->nama }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jabatan Terakhir</label>
                        <input type="text" name="jabatan_terakhir" placeholder="Contoh: PETUGAS YANTEK" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Fungsi Pekerjaan</label>
                        <input type="text" name="fungsi_pekerjaan" placeholder="Contoh: Pelayanan Teknik" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Status Tenaga Kerja</label>
                        <select name="status_tenaga_kerja" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700">
                            <option value="PKWTT">PKWTT (Tetap)</option>
                            <option value="PKWT">PKWT (Waktu Tertentu)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Skema Tenaga Kerja</label>
                        <select name="skema_tenaga_kerja" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700">
                            <option value="PEMBORONGAN">PEMBORONGAN</option>
                            <option value="VOLUME BASED">VOLUME BASED</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab Group: BPJS & Dokumen -->
            <div>
                <div class="text-xs font-black text-gray-400 uppercase tracking-wider mb-3">3. Jaminan Sosial & Perjanjian</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">No. BPJS Kesehatan</label>
                        <input type="text" name="nomor_bpjs_kesehatan" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">No. BPJS Ketenagakerjaan</label>
                        <input type="text" name="nomor_bpjs_ketenagakerjaan" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nomor DPLK</label>
                        <input type="text" name="nomor_dplk" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Bank DPLK</label>
                        <input type="text" name="bank_dplk" value="BNI" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-full border border-gray-300 text-xs font-bold text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" class="btn-gold-primary px-6 py-2.5 text-xs uppercase tracking-wider">
                    Simpan Tenaga Kerja
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Tenaga Kerja -->
<div id="detail-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-teal-50/50">
            <div>
                <h3 id="detail-nama" class="text-xl font-black text-gray-900"></h3>
                <p id="detail-nik" class="text-xs text-gray-400 font-bold"></p>
            </div>
            <button onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div id="detail-content" class="p-6 overflow-y-auto space-y-4 text-xs font-medium"></div>
    </div>
</div>

<!-- Modal Kelola Sertifikasi -->
<div id="cert-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-teal-50/50">
            <div>
                <h3 class="text-lg font-black text-gray-900">Sertifikasi Tenaga Kerja</h3>
                <p id="cert-worker-name" class="text-xs text-teal-700 font-bold"></p>
            </div>
            <button onclick="closeCertModal()" class="w-8 h-8 rounded-full bg-white text-gray-400 hover:text-gray-600 flex items-center justify-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-6">
            <!-- Form Upload Sertifikat Baru -->
            <form id="cert-form" method="POST" enctype="multipart/form-data" class="p-4 rounded-2xl bg-teal-50/40 border border-teal-100 space-y-3">
                @csrf
                <div class="text-xs font-black text-primaryDark uppercase">Tambah Bukti Sertifikat Baru</div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Judul Sertifikasi *</label>
                    <input type="text" name="judul_sertifikasi" required placeholder="Contoh: Kompetensi K3 Distribusi" class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Nomor Sertifikat</label>
                    <input type="text" name="nomor_sertifikat" placeholder="Contoh: SERT-K3-2024-xxx" class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Upload Gambar Sertifikat</label>
                    <input type="file" name="gambar_sertifikat" accept="image/*" class="w-full text-xs text-gray-500">
                </div>
                <button type="submit" class="btn-gold-primary px-4 py-2 text-xs uppercase tracking-wider w-full">
                    Upload & Simpan Sertifikat
                </button>
            </form>

            <!-- List Sertifikat -->
            <div id="cert-list" class="space-y-3"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('add-modal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('add-modal').classList.add('hidden');
    }

    function openDetailModal(worker) {
        document.getElementById('detail-nama').innerText = worker.nama;
        document.getElementById('detail-nik').innerText = 'NIK: ' + (worker.nik || '-') + ' • Usia: ' + (worker.usia || '-');
        
        let html = `
            <div class="grid grid-cols-2 gap-3 p-4 rounded-2xl bg-gray-50">
                <div><span class="text-gray-400">Unit Layanan:</span> <div class="font-bold text-gray-900">${worker.unit_layanan ? worker.unit_layanan.nama : worker.unit}</div></div>
                <div><span class="text-gray-400">Perusahaan Mitra:</span> <div class="font-bold text-gray-900">${worker.nama_perusahaan || '-'}</div></div>
                <div><span class="text-gray-400">Jabatan:</span> <div class="font-bold text-gray-900">${worker.jabatan_terakhir || '-'}</div></div>
                <div><span class="text-gray-400">Fungsi:</span> <div class="font-bold text-gray-900">${worker.fungsi_pekerjaan || '-'}</div></div>
                <div><span class="text-gray-400">Status Kontrak:</span> <div class="font-black text-primary">${worker.status_tenaga_kerja} (${worker.skema_tenaga_kerja})</div></div>
                <div><span class="text-gray-400">No. HP:</span> <div class="font-bold text-gray-900">${worker.no_telepon || '-'}</div></div>
                <div><span class="text-gray-400">Pendidikan:</span> <div class="font-bold text-gray-900">${worker.pendidikan_terakhir || '-'} ${worker.jurusan ? '(' + worker.jurusan + ')' : ''}</div></div>
                <div><span class="text-gray-400">Alamat:</span> <div class="font-bold text-gray-900">${worker.alamat_domisili || '-'}</div></div>
                <div><span class="text-gray-400">BPJS Kesehatan:</span> <div class="font-bold text-gray-900">${worker.nomor_bpjs_kesehatan || '-'}</div></div>
                <div><span class="text-gray-400">BPJS Ketenagakerjaan:</span> <div class="font-bold text-gray-900">${worker.nomor_bpjs_ketenagakerjaan || '-'}</div></div>
            </div>
            <div class="mt-4">
                <div class="font-black text-gray-800 text-xs mb-2">Riwayat Sertifikasi:</div>
                ${worker.sertifikasis && worker.sertifikasis.length > 0 ? 
                    worker.sertifikasis.map(c => `
                        <div class="p-3 rounded-xl border border-teal-100 bg-teal-50/40 mb-2">
                            <div class="font-bold text-gray-900">${c.judul_sertifikasi}</div>
                            <div class="text-[11px] text-gray-400">No: ${c.nomor_sertifikat || '-'}</div>
                        </div>
                    `).join('') : '<div class="text-gray-400 text-xs">Belum ada sertifikasi terdaftar.</div>'}
            </div>
        `;
        document.getElementById('detail-content').innerHTML = html;
        document.getElementById('detail-modal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detail-modal').classList.add('hidden');
    }

    function openCertModal(worker) {
        document.getElementById('cert-worker-name').innerText = worker.nama;
        document.getElementById('cert-form').action = `/tenaga-kerja/${worker.id}/sertifikasi`;
        
        let listHtml = worker.sertifikasis && worker.sertifikasis.length > 0 ?
            worker.sertifikasis.map(c => `
                <div class="p-3 rounded-2xl border border-gray-200 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-gray-900 text-xs">${c.judul_sertifikasi}</div>
                        <div class="text-[11px] text-gray-400">No: ${c.nomor_sertifikat || '-'}</div>
                    </div>
                    <form method="POST" action="/sertifikasi/${c.id}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-rose-500 hover:text-rose-700 p-1">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            `).join('') : '<div class="text-xs text-gray-400 text-center py-4">Belum ada sertifikasi.</div>';
        
        document.getElementById('cert-list').innerHTML = listHtml;
        document.getElementById('cert-modal').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeCertModal() {
        document.getElementById('cert-modal').classList.add('hidden');
    }

    // Auto open add modal if ?tambah=true
    if (new URLSearchParams(window.location.search).get('tambah') === 'true') {
        openAddModal();
    }
</script>
@endpush
@endsection
