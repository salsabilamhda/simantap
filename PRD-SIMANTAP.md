# PRD — Sistem Manajemen Data Tenaga Kerja (SIMANTAP)

**Versi:** 2.0 (Pembaruan Arsitektur: PHP / Laravel + MySQL)  
**Tanggal Pembaruan:** 15 September 2026  
**Sumber Data Acuan:** `TEMPLATE_TENAGA_KERJA_UPDATE_JULI_2026_SESUAI_DATABASE.xlsx`

---

## 1. Ringkasan Eksekutif

Aplikasi web internal untuk mengelola data tenaga kerja outsourcing/mitra (198+ record saat ini, tersebar di 5 Unit Layanan: ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, UP3 Ponorogo). Aplikasi menggantikan pengelolaan manual via Excel dengan sistem web terpusat yang mendukung:

- Autentikasi dan manajemen admin
- Kelola data tenaga kerja (CRUD lengkap)
- Kelola sertifikasi per tenaga kerja (multi-sertifikasi + upload berkas/foto sertifikat)
- Import & export data ke/dari Excel & CSV
- Dashboard ringkasan (distribusi tenaga kerja per Unit Layanan & statistik status kerja)
- Master data Unit Layanan dan Perusahaan mitra

> **Pembaruan Arsitektur (v2.0):**  
> Sistem telah diperbarui dari rancangan awal (Next.js + Firebase) menjadi **PHP (Laravel Framework) + MySQL (dikelola via phpMyAdmin)**. Seluruh penyimpanan berkas sertifikat menggunakan **Laravel Public Storage** lokal (`storage/app/public/sertifikasi`) tanpa dependensi ke layanan pihak ketiga (Cloudinary/Firebase).

---

## 2. Latar Belakang & Tujuan

Saat ini data tenaga kerja dikelola manual di Excel, rawan duplikasi, sulit dicari, dan tidak ada riwayat sertifikasi yang terpusat. Tujuan proyek:

1. **Sentralisasi Data:** Seluruh data tenaga kerja disimpan dalam basis data relasional MySQL (`simantap`) yang mudah diakses dan dikelola oleh admin secara lokal/intranet.
2. **Digitalisasi Bukti Sertifikasi:** Nomor dan berkas sertifikat terhubung langsung dengan profil tenaga kerja (one-to-many relationship).
3. **Kompatibilitas:** Menyediakan fitur import dan export Excel/CSV yang sesuai dengan format eksisting.
4. **Kemudahan Pemeliharaan:** Menggunakan stack standar industri PHP/Laravel yang mudah dijalankan di lingkungan server lokal (XAMPP/phpMyAdmin) maupun server intranet instansi.

---

## 3. Ruang Lingkup

### 3.1 In-Scope (Fitur Aktif)
- **Dashboard Ringkasan:** Statistik total tenaga kerja, distribusi per Unit Layanan, per Perusahaan, dan per Status Kontrak.
- **CRUD Tenaga Kerja:** Tambah, lihat detail, perbarui, dan hapus data tenaga kerja beserta filter pencarian cepat.
- **Manajemen Sertifikasi:** Tambah berkas sertifikat per tenaga kerja, preview dokumen/gambar, dan hapus berkas.
- **Master Data:** Kelola data Unit Layanan (ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, UP3 Ponorogo) dan Perusahaan mitra.
- **Pengaturan Admin:** Manajemen user admin dan hak akses.
- **Import & Export:** Export data ke CSV/Excel dan import data template tenaga kerja.

### 3.2 Out-of-Scope (Fase Lanjutan)
- Notifikasi otomatis kedaluwarsa sertifikat via WhatsApp/Email.
- Approval workflow berjenjang antar-bidang.
- Integrasi Single Sign-On (SSO) korporat.

---

## 4. Pengguna & Role

| Role | Deskripsi | Akses |
|---|---|---|
| **Super Admin** | Pengelola sistem utama | Akses penuh ke seluruh menu, kelola user admin, master data, dan data tenaga kerja |
| **Admin Unit / Staff** | Pengelola operasional | Input, ubah, export, dan verifikasi data tenaga kerja serta berkas sertifikasi |

---

## 5. Model Data & Skema Database (MySQL)

Database MySQL dikelola via phpMyAdmin dengan nama basis data: **`simantap`**.

### 5.1 Tabel `tenaga_kerjas`
Menyimpan data identitas dan status kepegawaian tenaga kerja.

| Field | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `nik` | VARCHAR(16) | Nomor Induk Kependudukan (Unik) |
| `nama` | VARCHAR(255) | Nama lengkap tenaga kerja |
| `tanggal_lahir` | DATE | Tanggal lahir |
| `jenis_kelamin` | ENUM('L', 'P') | Laki-laki / Perempuan |
| `jabatan` | VARCHAR(255) | Posisi / jabatan |
| `unit_layanan_id` | BIGINT (FK) | Relasi ke tabel `unit_layanans` |
| `perusahaan_id` | BIGINT (FK) | Relasi ke tabel `perusahaans` |
| `status_kontrak` | VARCHAR(50) | PKWT / PKWTT / Mitra |
| `nomor_kontrak` | VARCHAR(100) (Nullable) | Nomor kontrak / perjanjian kerja |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & pembaruan |

### 5.2 Tabel `sertifikasis`
Menyimpan sertifikasi yang dimiliki oleh masing-masing tenaga kerja (Relasi 1-to-Many).

| Field | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `tenaga_kerja_id` | BIGINT (FK) | Relasi ke `tenaga_kerjas.id` (Cascade delete) |
| `nomor_sertifikat` | VARCHAR(255) | Nomor registrasi sertifikat |
| `judul_sertifikasi` | VARCHAR(255) | Nama kompetensi / sertifikasi |
| `file_path` | VARCHAR(255) | Path penyimpanan berkas di `public/storage/sertifikasi` |
| `tanggal_terbit` | DATE (Nullable) | Tanggal terbit sertifikat |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & pembaruan |

### 5.3 Tabel `unit_layanans` (Master Data)
| Field | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `nama` | VARCHAR(255) | Nama unit (ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, UP3 Ponorogo) |
| `unit_induk` | VARCHAR(255) (Nullable) | Unit induk (UP3 Ponorogo) |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & pembaruan |

### 5.4 Tabel `perusahaans` (Master Data)
| Field | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `nama` | VARCHAR(255) | Nama vendor / mitra penyedia |
| `nomor_perjanjian` | VARCHAR(255) (Nullable) | Nomor kontrak kerja sama mitra |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & pembaruan |

### 5.5 Tabel `users` (Admin & Akses)
| Field | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT (PK, Auto Increment) | Primary Key |
| `name` | VARCHAR(255) | Nama admin |
| `email` | VARCHAR(255) (Unique) | Email untuk login |
| `password` | VARCHAR(255) | Hash password (Bcrypt) |
| `role` | VARCHAR(50) | `superadmin` / `admin` |
| `created_at`, `updated_at` | TIMESTAMP | Waktu pembuatan & pembaruan |

---

## 6. Arsitektur Teknis & Implementasi

```
[Browser Admin / Client]
           │
           ▼
[Web Server (Apache / Nginx / Artisan)]
           │
           ▼
[Laravel Application Framework (PHP 8.1+)]
  ├── Routing & Middleware (routes/web.php)
  ├── Controllers (App\Http\Controllers)
  ├── Eloquent ORM Models (App\Models)
  └── Views (Blade Templating + Tailwind CSS)
           │
     ┌─────┴────────────────┐
     ▼                      ▼
[MySQL Database]     [File Storage]
(Database: simantap) (storage/app/public)
(via phpMyAdmin)     (symlink ke public/storage)
```

- **Backend:** Laravel Framework (PHP 8.1+) dengan pola arsitektur MVC.
- **Frontend:** Laravel Blade Templating Engine dengan styling modern Tailwind CSS.
- **Database Engine:** MySQL / MariaDB via XAMPP / phpMyAdmin (`DB_DATABASE=simantap`).
- **File Storage:** Laravel Local Public Disk (`php artisan storage:link`).
- **Development Server:** `php artisan serve` (Port 8000).

---

## 7. Struktur Menu & Routing Aplikasi

| Route URL | Controller & Method | Deskripsi |
|---|---|---|
| `/` & `/dashboard` | `DashboardController@index` | Dashboard ringkasan, chart, dan statistik tenaga kerja |
| `/tenaga-kerja` | `TenagaKerjaController@index` | Daftar tabel tenaga kerja, pagination, search & filter |
| `/tenaga-kerja` (POST) | `TenagaKerjaController@store` | Simpan data tenaga kerja baru |
| `/tenaga-kerja/{id}` (PUT) | `TenagaKerjaController@update` | Update data tenaga kerja |
| `/tenaga-kerja/{id}` (DELETE) | `TenagaKerjaController@destroy` | Hapus data tenaga kerja beserta sertifikasinya |
| `/tenaga-kerja/{id}/sertifikasi` | `SertifikasiController@store` | Upload dan simpan sertifikasi baru |
| `/sertifikasi/{id}` (DELETE) | `SertifikasiController@destroy` | Hapus sertifikasi dan berkas gambar terkait |
| `/master-data/unit-layanan` | `MasterDataController@unitLayanan` | Master data unit layanan |
| `/master-data/perusahaan` | `MasterDataController@perusahaan` | Master data perusahaan vendor |
| `/pengaturan/admin` | `PengaturanAdminController@index` | Manajemen pengguna admin sistem |
| `/import-export` | `ImportExportController@index` | Halaman kelola import & export data |
| `/import-export/export` | `ImportExportController@exportCsv` | Download data tenaga kerja dalam format CSV/Excel |

---

## 8. Panduan Menjalankan Sistem Secara Lokal

1. **Prasyarat:**
   - PHP >= 8.1
   - Composer
   - MySQL / MariaDB (melalui XAMPP)
2. **Langkah Konfigurasi:**
   - Buka XAMPP Control Panel, jalankan service **Apache** dan **MySQL**.
   - Buka phpMyAdmin (`http://localhost/phpmyadmin`) dan pastikan database **`simantap`** telah dibuat.
   - Konfigurasi file `laravel/.env`:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=simantap
     DB_USERNAME=root
     DB_PASSWORD=
     ```
   - Jalankan migration: `php artisan migrate`
   - Buat symlink storage (jika belum): `php artisan storage:link`
   - Jalankan dev server: `php artisan serve`
   - Akses aplikasi di browser melalui `http://127.0.0.1:8000`.