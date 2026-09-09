# PRD — Sistem Manajemen Data Tenaga Kerja

**Versi:** 1.0
**Tanggal:** 9 September 2026
**Sumber Data Acuan:** `TEMPLATE_TENAGA_KERJA_UPDATE_JULI_2026_SESUAI_DATABASE.xlsx`

---

## 1. Ringkasan Eksekutif

Aplikasi web internal untuk mengelola data tenaga kerja outsourcing/mitra (198+ record saat ini, tersebar di 5 Unit Layanan: ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, UP3 Ponorogo). Aplikasi menggantikan pengelolaan manual via Excel dengan sistem CRUD berbasis web yang mendukung:

- Login admin
- Kelola data tenaga kerja (CRUD penuh)
- Kelola sertifikasi per tenaga kerja (1 orang bisa punya banyak sertifikasi + upload gambar sertifikat)
- Import & export data ke/dari Excel (kompatibel dengan format template asli)
- Dashboard ringkasan (setara Sheet1 pivot: jumlah tenaga kerja per Unit Layanan)

**Stack:** Next.js (App Router) + Firebase (Auth, Firestore) + Cloudinary (penyimpanan gambar sertifikat) + Vercel (hosting gratis).

> **Update:** Penyimpanan gambar sertifikat menggunakan **Cloudinary**, bukan Firebase Storage. Sejak 3 Februari 2026, Firebase mewajibkan paket berbayar (Blaze) dengan kartu kredit terdaftar untuk mengakses Cloud Storage — termasuk project baru. Cloudinary dipilih karena gratis tanpa kartu kredit, kuota jauh lebih lega (~25GB/bulan), dan punya fitur auto-resize/compress gambar bawaan. Firestore & Firebase Auth tetap dipakai seperti rencana semula.

---

## 2. Latar Belakang & Tujuan

Saat ini data tenaga kerja dikelola manual di Excel, rawan duplikasi, sulit dicari, dan tidak ada riwayat sertifikasi yang terpusat (bukti sertifikat masih terpisah dari data). Tujuan proyek:

1. Sentralisasi data tenaga kerja dalam satu database yang bisa diakses & diedit banyak admin secara real-time.
2. Digitalisasi bukti sertifikasi (nomor + gambar sertifikat) yang terhubung langsung ke masing-masing tenaga kerja.
3. Tetap kompatibel dengan alur kerja lama lewat fitur import/export Excel.
4. Biaya operasional rendah/nol (hosting gratis di Vercel, Firebase free tier).

---

## 3. Ruang Lingkup

### 3.1 In-Scope (MVP)
- Autentikasi admin (login/logout)
- CRUD data tenaga kerja
- CRUD sertifikasi per tenaga kerja (multi-sertifikasi + upload gambar)
- Import data dari file Excel (`.xlsx`)
- Export data ke Excel (sesuai struktur kolom template asli)
- Dashboard ringkasan jumlah tenaga kerja per Unit Layanan (setara Sheet1)
- Pencarian & filter data (per Unit Layanan, Perusahaan, Status Kontrak, dll.)
- Master data Unit Layanan & Perusahaan

### 3.2 Out-of-Scope (Fase Berikutnya / Nice-to-have)
- Notifikasi otomatis sertifikat/kontrak akan kedaluwarsa
- Approval workflow multi-level
- Multi-role granular (misalnya admin per Unit Layanan saja)
- Audit log perubahan data
- Aplikasi mobile native

---

## 4. Pengguna & Role

| Role | Deskripsi | Akses |
|---|---|---|
| **Super Admin** | Pengelola utama sistem | Semua fitur + kelola akun admin lain |
| **Admin** | Operator harian (HR/Admin ULP/UP3) | CRUD tenaga kerja & sertifikasi, import/export |

Login menggunakan Firebase Authentication (email + password). Tidak ada akses publik/tanpa login — seluruh aplikasi berada di balik autentikasi.

---

## 5. Struktur Data (Data Model)

Struktur ini diturunkan langsung dari 30 kolom sheet **Duplikat**, dikelompokkan menjadi beberapa entitas agar sertifikasi bisa 1-ke-banyak.

### 5.1 Collection `tenagaKerja`
| Field | Tipe | Sumber Kolom Excel | Keterangan |
|---|---|---|---|
| id | string (auto) | - | Firestore doc ID |
| nomorPerjanjian | string | Nomor Perjanjian | No. perjanjian kerja sama perusahaan |
| namaPerusahaan | string | Nama Perusahaan | Bisa dropdown dari master `perusahaan` |
| nama | string | Nama | |
| nik | string | NIK | Validasi 16 digit |
| tempatLahir | string | Tempat Lahir | |
| tanggalLahir | date | Tanggal Tahun Lahir | Usia dihitung otomatis (bukan disimpan statis) |
| pendidikanTerakhir | string | Pendidikan Terakhir | Dropdown: SD/SMP/SMA/SMK/D3/S1/S2 |
| jurusan | string | Jurusan | |
| noTelepon | string | No Telepon (WA) | |
| email | string | Email | |
| jenisKelamin | enum | Jenis Kelamin | LAKI / PEREMPUAN |
| alamatDomisili | string | Alamat Domisili | |
| kotaKabupaten | string | Kota/Kabupaten | |
| provinsi | string | Provinsi | |
| jabatanTerakhir | string | Jabatan Terakhir | |
| fungsiPekerjaan | string | Fungsi Pekerjaan | |
| unit | string | Unit | |
| unitLayananId | string (ref) | Unit Layanan | Referensi ke master `unitLayanan` |
| noBpjsKesehatan | string | Nomor BPJS Kesehatan | |
| noBpjsKetenagakerjaan | string | Nomor BPJS Ketenagakerjaan | |
| noDplk | string | Nomor DPLK | |
| bankDplk | string | Bank DPLK | |
| noPerjanjianKerja | string | Nomor perjanjian kerja PKWT/PKWTT | |
| tanggalMasukKerja | date | Tanggal masuk kerja | |
| statusTenagaKerja | enum | Status Tenaga Kerja | PKWT / PKWTT |
| skemaTenagaKerja | enum | Skema Tenaga Kerja | Pemborongan / Volume Based |
| createdAt / updatedAt | timestamp | - | Metadata sistem |

> Catatan: kolom **No** (nomor urut) dan **Usia di Tahun 2026** dari Excel tidak disimpan statis — nomor urut mengikuti urutan tampilan, dan usia dihitung otomatis dari tanggal lahir agar selalu akurat kapan pun diakses.

### 5.2 Sub-collection `tenagaKerja/{id}/sertifikasi`
Mendukung 1 tenaga kerja → banyak sertifikasi.

| Field | Tipe | Sumber Kolom Excel | Keterangan |
|---|---|---|---|
| id | string (auto) | - | |
| nomorSertifikat | string | Nomor Sertifikat (Sertifikasi Wajib) | |
| judulSertifikasi | string | Judul Sertifikasi | |
| gambarSertifikatUrl | string (URL) | (baru) | Hasil upload ke Cloudinary, disimpan sebagai URL |
| cloudinaryPublicId | string | (baru) | ID aset di Cloudinary, dibutuhkan untuk hapus/ganti gambar |
| tanggalTerbit | date | (baru, opsional) | Untuk kebutuhan tracking masa berlaku |
| tanggalKadaluarsa | date | (baru, opsional) | Untuk fitur reminder di fase berikutnya |
| createdAt | timestamp | - | |

### 5.3 Collection `unitLayanan` (master data)
`{ id, nama, unitInduk }` — diisi awal: ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, UP3 Ponorogo.

### 5.4 Collection `perusahaan` (master data)
`{ id, nama, nomorPerjanjian }` — memudahkan dropdown & konsistensi penulisan nama perusahaan.

### 5.5 Collection `admins`
`{ uid, nama, email, role: 'superadmin' | 'admin', createdAt }` — terhubung ke Firebase Auth UID.

---

## 6. Fitur & Kebutuhan Fungsional

### 6.1 Autentikasi
- Login via email/password (Firebase Auth)
- Proteksi semua route dengan middleware/guard — redirect ke `/login` jika belum autentikasi
- Super Admin dapat menambah/menonaktifkan akun admin lain

### 6.2 Dashboard
- Kartu ringkasan: total tenaga kerja, jumlah per status (PKWT/PKWTT), jumlah per Unit Layanan (replikasi Sheet1)
- Grafik sederhana (bar chart) distribusi per Unit Layanan & jenis kelamin
- Daftar tenaga kerja yang baru ditambahkan/diubah

### 6.3 Manajemen Data Tenaga Kerja
- **List**: tabel dengan pagination, pencarian (nama/NIK), filter (Unit Layanan, Perusahaan, Status, Skema)
- **Create**: form input lengkap sesuai model 5.1, dengan validasi (NIK 16 digit, email format, no. HP format)
- **Detail**: halaman profil tenaga kerja menampilkan seluruh data + daftar sertifikasi miliknya
- **Update**: edit seluruh field
- **Delete**: hapus data (dengan konfirmasi; sertifikasi terkait ikut terhapus)

### 6.4 Manajemen Sertifikasi
- Dari halaman detail tenaga kerja: tombol "Tambah Sertifikasi"
- Form: nomor sertifikat, judul sertifikasi, upload gambar (jpg/png/pdf), tanggal terbit (opsional)
- Gambar diupload ke Cloudinary (via API route Next.js agar API secret tidak terekspos ke client), URL & public ID hasil upload disimpan di Firestore
- List sertifikasi per orang ditampilkan sebagai kartu/galeri dengan preview gambar
- Edit & hapus per sertifikasi (hapus di Firestore sekaligus hapus aset di Cloudinary menggunakan `cloudinaryPublicId`)

### 6.5 Import Excel
- Upload file `.xlsx` sesuai template kolom pada sheet **Duplikat**
- Preview data sebelum disimpan (tampilkan baris yang error/tidak valid, misal NIK ganda atau kosong)
- Opsi: **tambah data baru** atau **timpa/update berdasarkan NIK**
- Ringkasan hasil import: berhasil / gagal / dilewati

### 6.6 Export Excel
- Export seluruh data atau hasil filter saat ini
- Format output kolom mengikuti struktur asli (No, Nomor Perjanjian, Nama Perusahaan, ... dst)
- Kolom sertifikasi (Nomor Sertifikat, Judul Sertifikasi) digabung jika 1 orang punya lebih dari 1 sertifikasi (dipisah dengan koma/baris baru), agar tetap kompatibel dengan format 1 baris = 1 orang

### 6.7 Master Data
- CRUD sederhana untuk Unit Layanan dan Perusahaan (agar dropdown selalu konsisten & tidak typo)

---

## 7. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan |
|---|---|
| Keamanan | Firestore Security Rules: hanya user terautentikasi (role admin) yang bisa read/write. Data sensitif (NIK, BPJS) tidak diekspos ke endpoint publik. |
| Responsif | Tampilan optimal di desktop & tablet (prioritas admin bekerja dari komputer/laptop) |
| Performa | List data pakai pagination/virtualization agar tetap ringan meski data bertambah ribuan baris |
| Ketersediaan | Hosting di Vercel (uptime tinggi, auto-scaling gratis untuk trafik kecil-menengah) |
| Biaya | Tetap dalam batas free tier Vercel & Firebase Spark selama volume data & trafik rendah (lihat catatan §9) |
| Backup | Export Excel berkala berfungsi juga sebagai backup manual; opsional: scheduled export via Cloud Function di fase lanjut |

---

## 8. Arsitektur Teknis

**Frontend & Backend logic:** Next.js 14+ (App Router, TypeScript), Tailwind CSS + shadcn/ui untuk komponen UI konsisten.

**Database:** Firebase Firestore (NoSQL) — cocok untuk struktur data tenaga kerja + sub-collection sertifikasi.

**Autentikasi:** Firebase Authentication (Email/Password).

**Penyimpanan file:** Cloudinary — untuk gambar/scan sertifikat. Upload dilakukan lewat API route Next.js (server-side) yang memanggil Cloudinary API menggunakan API secret tersimpan di environment variable Vercel, sehingga kredensial tidak terekspos ke browser.

**Import/Export Excel:** library `xlsx` (SheetJS) untuk parsing & generate file di sisi client/server.

**Deployment:** Vercel (frontend + API routes Next.js) — terhubung ke Firebase project via environment variables (API key publik Firebase aman untuk diekspos di client, keamanan tetap dijaga lewat Firestore Security Rules) dan ke Cloudinary via API key/secret di environment variable server-side.

```
[Browser Admin] → [Next.js App di Vercel]
                         │
             ┌───────────┼──────────────┐
             ▼           ▼              ▼
     Firebase Auth   Firestore    API Route Next.js
     (login admin)  (data TK &    (proxy upload) → Cloudinary
                     sertifikasi)                  (gambar sertifikat)
```

---

## 9. Catatan Biaya & Batasan Free Tier

- **Vercel Hobby (gratis):** cukup untuk trafik internal skala kecil-menengah.
- **Firebase Spark (gratis, tanpa kartu kredit):** Firestore (1GiB storage, 50rb baca/hari, 20rb tulis/hari) & Auth (50rb pengguna aktif) cukup longgar untuk ±200–2000 record data tenaga kerja. Sejak 3 Februari 2026, **Firebase Storage tidak lagi tersedia di paket Spark** — karena itu, gambar sertifikat disimpan di Cloudinary, bukan Firebase Storage, sehingga project ini bisa tetap 100% gratis tanpa perlu mendaftarkan kartu kredit ke Google.
- **Cloudinary (gratis, tanpa kartu kredit):** kuota ±25GB storage & bandwidth/bulan — jauh lebih dari cukup untuk ratusan file gambar sertifikat, dengan fitur auto-resize/compress bawaan agar file besar tidak cepat menghabiskan kuota.

---

## 10. Struktur Menu Aplikasi

```
/login
/dashboard                     → ringkasan & statistik
/tenaga-kerja                  → list + search + filter
/tenaga-kerja/tambah           → form tambah
/tenaga-kerja/[id]             → detail + daftar sertifikasi
/tenaga-kerja/[id]/edit        → form edit
/tenaga-kerja/import           → upload & preview import Excel
/master-data/unit-layanan
/master-data/perusahaan
/pengaturan/admin              → kelola akun admin (super admin only)
```

---

## 11. Rencana Bertahap (Roadmap)

| Fase | Fitur | Estimasi |
|---|---|---|
| **Fase 1 — MVP** | Setup Next.js + Firebase, login admin, CRUD tenaga kerja, dashboard dasar | 1–2 minggu |
| **Fase 2** | Modul sertifikasi (upload gambar), export Excel | 1 minggu |
| **Fase 3** | Import Excel + validasi & preview | 1 minggu |
| **Fase 4 (opsional)** | Notifikasi kedaluwarsa sertifikat/kontrak, audit log, role granular per Unit Layanan | menyusul |

---

## 12. Asumsi & Pertanyaan Terbuka

1. Kolom **Fungsi Pekerjaan** di data existing masih kosong semua — apakah tetap perlu ditampilkan sebagai field wajib?
2. Apakah 1 sertifikat bisa dipakai lebih dari 1 orang, atau selalu unik per orang? (asumsi saat ini: unik per orang, disimpan sebagai sub-collection)
3. Apakah dibutuhkan riwayat perubahan data (log siapa mengubah apa)? (masuk fase lanjut jika ya)
4. Berapa perkiraan pertambahan data tenaga kerja per tahun? (memengaruhi keputusan paket Firebase)

---

*Dokumen ini dapat disesuaikan lebih lanjut sebelum development dimulai.*
