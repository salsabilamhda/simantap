# SIMANTAP — Sistem Manajemen Data Tenaga Kerja

Aplikasi web internal modern untuk sentralisasi dan pengelolaan data tenaga kerja outsourcing/mitra serta riwayat sertifikasi di lingkungan kerja unit layanan (ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, dan UP3 Ponorogo).

---

## 📌 Latar Belakang & Tujuan

Sebelumnya, data tenaga kerja dikelola secara manual melalui berkas Excel. Hal ini menimbulkan tantangan berupa risiko duplikasi, kesulitan pencarian, serta berkas bukti sertifikasi yang tercecer terpisah dari data profil tenaga kerja.

**SIMANTAP** hadir untuk:
1. **Sentralisasi Data**: Mengintegrasikan seluruh data tenaga kerja ke dalam satu database terpusat yang dapat diakses dan diperbarui secara *real-time* oleh admin.
2. **Digitalisasi Sertifikasi**: Menghubungkan riwayat serta berkas bukti fisik sertifikat (multi-sertifikasi) langsung ke profil masing-masing tenaga kerja.
3. **Kompatibilitas Penuh**: Mendukung alur kerja impor dan ekspor data Excel yang kompatibel dengan format template eksisting.
4. **Efisiensi Biaya Operasional**: Mengoptimalkan arsitektur serverless (*free tier*) menggunakan Next.js di Vercel, Firebase (Auth & Firestore), dan Cloudinary.

---

## ✨ Fitur Utama

### 1. 🔐 Autentikasi & Manajemen Admin
- Autentikasi berbasis email & kata sandi via **Firebase Authentication**.
- Pembagian peran pengguna:
  - **Super Admin**: Akses penuh ke seluruh fitur dan manajemen akun admin lain.
  - **Admin**: Akses operasional harian (CRUD data tenaga kerja, sertifikasi, impor & ekspor).
- Proteksi route berbasis middleware untuk memastikan seluruh data internal terlindungi.

### 2. 📊 Dashboard Ringkasan & Analitik
- Ringkasan metrik total tenaga kerja, distribusi per status kerja (PKWT/PKWTT), dan skema kerja.
- Distribusi tenaga kerja per Unit Layanan (replikasi fungsi pivot Sheet1 template).
- Visualisasi grafik komposisi tenaga kerja berdasarkan unit layanan dan jenis kelamin.
- Feed aktivitas penambahan dan pembaruan data terbaru.

### 3. 👥 Manajemen Data Tenaga Kerja (CRUD)
- **Daftar & Pencarian**: Tabel data dengan pagination, pencarian fleksibel (Nama, NIK), dan filter dinamis (Unit Layanan, Perusahaan, Status, Skema).
- **Form Input & Validasi**: Validasi ketat untuk format NIK (16 digit), nomor telepon/WhatsApp, email, serta kalkulasi otomatis usia berdasarkan tanggal lahir.
- **Detail Profil**: Tampilan profil komprehensif mencakup data pribadi, kepegawaian, BPJS, DPLK, dan daftar sertifikasi yang dimiliki.
- **Pembaruan & Penghapusan**: Fleksibilitas edit data dan proteksi konfirmasi saat penghapusan.

### 4. 📜 Manajemen Sertifikasi (Multi-Sertifikasi)
- Mendukung relasi 1 tenaga kerja memiliki banyak sertifikasi.
- Input nomor sertifikat, judul sertifikasi, tanggal terbit, dan masa berlaku.
- Unggah berkas/foto bukti sertifikat ke **Cloudinary** dengan pratinjau galeri gambar.
- Sinkronisasi otomatis penghapusan aset di Cloudinary ketika sertifikasi dihapus.

### 5. 📥 Impor & Ekspor Excel
- **Impor Data**: Unggah berkas `.xlsx` sesuai template, dilengkapi dengan validasi data, pratinjau sebelum simpan, dan opsi untuk menimpa data berdasarkan NIK.
- **Ekspor Data**: Mengunduh data tenaga kerja (seluruhnya atau hasil filter) ke format Excel dengan struktur kolom yang sesuai template acuan.

### 6. ⚙️ Master Data
- Pengelolaan master data Unit Layanan dan Perusahaan Mitra untuk menjamin konsistensi data dan dropdown pilihan.

---

## 🛠️ Arsitektur & Teknologi

```
[Browser Admin] ────────► [Next.js App (Vercel)]
                                │
        ┌───────────────────────┼───────────────────────┐
        ▼                       ▼                       ▼
Firebase Authentication   Firebase Firestore    API Route (Server Proxy)
 (Login & Sesi Admin)    (Data TK & Sertifikasi)        │
                                                        ▼
                                                    Cloudinary
                                              (Penyimpanan Sertifikat)
```

| Komponen | Teknologi | Keterangan |
|---|---|---|
| **Frontend Framework** | Next.js 14+ (App Router, TypeScript) | Performa tinggi dengan React Server Components |
| **Styling & UI** | Tailwind CSS + shadcn/ui | Desain modern, bersih, dan konsisten |
| **Database** | Firebase Firestore | Database NoSQL dokumen real-time |
| **Autentikasi** | Firebase Authentication | Manajemen sesi login aman |
| **Media Storage** | Cloudinary | Penyimpanan gambar sertifikat gratis dengan optimasi otomatis |
| **Excel Parser/Generator**| SheetJS (`xlsx`) | Pemrosesan impor dan ekspor file Excel |
| **Deployment** | Vercel | Hosting serverless global dengan integrasi Git otomatis |

---

## 🗂️ Struktur Data Utama

- **`tenagaKerja`**: Dokumen utama penyimpan biodata, informasi kontrak kerja, nomor perjanjian, BPJS Kesehatan & Ketenagakerjaan, DPLK, dsb.
- **`tenagaKerja/{id}/sertifikasi`**: Sub-koleksi untuk menyimpan riwayat sertifikasi dan URL gambar dari Cloudinary.
- **`unitLayanan`**: Master data unit (ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, UP3 Ponorogo).
- **`perusahaan`**: Master data perusahaan mitra dan nomor perjanjian kerja sama.
- **`admins`**: Data pengguna sistem dan role-nya (Super Admin / Admin).

---

## 🗺️ Struktur Rute / Halaman

```bash
/login                          # Halaman masuk sistem
/dashboard                      # Dashboard analitik & ringkasan statistik
/tenaga-kerja                   # Daftar data tenaga kerja + pencarian & filter
/tenaga-kerja/tambah            # Form penambahan data tenaga kerja
/tenaga-kerja/[id]              # Profil detail tenaga kerja & sertifikasi
/tenaga-kerja/[id]/edit         # Form ubah data tenaga kerja
/tenaga-kerja/import            # Unggah & pratinjau impor Excel
/master-data/unit-layanan       # Kelola master Unit Layanan
/master-data/perusahaan         # Kelola master Perusahaan
/pengaturan/admin               # Manajemen akun admin (Super Admin only)
```

---

## 🚀 Rencana Pengembangan (Roadmap)

- [ ] **Fase 1 (MVP)**: Inisialisasi Next.js, konfigurasi Firebase Auth & Firestore, implementasi autentikasi, CRUD data tenaga kerja, dan dashboard ringkasan.
- [ ] **Fase 2**: Modul sertifikasi lengkap (upload gambar via Cloudinary, galeri pratinjau) dan fitur ekspor Excel.
- [ ] **Fase 3**: Fitur impor Excel dengan validasi format kolom, pencegahan NIK ganda, dan pratinjau interaktif.
- [ ] **Fase 4 (Lanjutan)**: Notifikasi pengingat kedaluwarsa kontrak/sertifikat, pencatatan audit log aktivitas, dan peran admin spesifik per unit.

---

## 💻 Panduan Menjalankan Proyek (Lokal)

### 1. Kloning Repositori
```bash
git clone https://github.com/salsabilamhda/simantap.git
cd simantap
```

### 2. Instalasi Dependensi
```bash
npm install
# atau
pnpm install
# atau
yarn install
```

### 3. Konfigurasi Variabel Lingkungan
Buat berkas `.env.local` pada root direktori dan sesuaikan kredensial berikut:

```env
# Firebase Client SDK
NEXT_PUBLIC_FIREBASE_API_KEY=your_firebase_api_key
NEXT_PUBLIC_FIREBASE_AUTH_DOMAIN=your_firebase_auth_domain
NEXT_PUBLIC_FIREBASE_PROJECT_ID=your_firebase_project_id
NEXT_PUBLIC_FIREBASE_STORAGE_BUCKET=your_firebase_storage_bucket
NEXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID=your_firebase_messaging_sender_id
NEXT_PUBLIC_FIREBASE_APP_ID=your_firebase_app_id

# Cloudinary (Server-side Upload)
CLOUDINARY_CLOUD_NAME=your_cloudinary_cloud_name
CLOUDINARY_API_KEY=your_cloudinary_api_key
CLOUDINARY_API_SECRET=your_cloudinary_api_secret
```

### 4. Menjalankan Server Pengembangan
```bash
npm run dev
```
Buka [http://localhost:3000](http://localhost:3000) pada browser Anda.

---

## 📄 Lisensi & Hak Penggunaan
Proyek ini dikembangkan secara khusus untuk kebutuhan internal manajemen operasional data ketenagakerjaan.