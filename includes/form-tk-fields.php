<?php
// includes/form-tk-fields.php
// Digunakan di modal Tambah DAN Edit Tenaga Kerja
// Variabel $unitLayanans dan $perusahaans harus sudah ada
?>
<!-- 1. Identitas Pribadi -->
<div>
    <div class="text-xs font-black text-gray-400 uppercase tracking-wider mb-3">1. Identitas Pribadi</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap *</label>
            <input type="text" name="nama" required class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">NIK (16 Digit)</label>
            <input type="text" name="nik" maxlength="16" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
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
            <label class="block text-xs font-bold text-gray-700 mb-1">No. Telepon / WhatsApp</label>
            <input type="text" name="no_telepon" placeholder="Contoh: 0812-xxxx-xxxx" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Domisili</label>
            <textarea name="alamat_domisili" rows="2" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary"></textarea>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Kota / Kabupaten</label>
            <input type="text" name="kota_kabupaten" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Provinsi</label>
            <input type="text" name="provinsi" value="Jawa Timur" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
        </div>
    </div>
</div>

<!-- 2. Penempatan & Hubungan Kerja -->
<div>
    <div class="text-xs font-black text-gray-400 uppercase tracking-wider mb-3">2. Penempatan &amp; Hubungan Kerja</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Unit Layanan *</label>
            <select name="unit_layanan_id" required class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700">
                <?php foreach ($unitLayanans as $u): ?>
                    <option value="<?= h($u['id']) ?>"><?= h($u['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Perusahaan Mitra</label>
            <select name="nama_perusahaan" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700">
                <option value="">— Pilih Perusahaan —</option>
                <?php foreach ($perusahaans as $p): ?>
                    <option value="<?= h($p['nama']) ?>"><?= h($p['nama']) ?></option>
                <?php endforeach; ?>
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
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nomor Perjanjian</label>
            <input type="text" name="nomor_perjanjian" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Masuk Kerja</label>
            <input type="date" name="tanggal_masuk_kerja" class="w-full px-3.5 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs font-medium focus:outline-none focus:border-primary">
        </div>
    </div>
</div>

<!-- 3. Jaminan Sosial & Dokumen -->
<div>
    <div class="text-xs font-black text-gray-400 uppercase tracking-wider mb-3">3. Jaminan Sosial &amp; Perjanjian</div>
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
