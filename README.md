# SIMANTAP

SIMANTAP adalah aplikasi manajemen data tenaga kerja berbasis PHP native dan MySQL untuk mendukung kebutuhan operasional data personil mitra/outsourcing di lingkungan kerja UP3 dan Unit Layanan.

Aplikasi ini digunakan untuk mengelola data tenaga kerja, master data unit layanan, perusahaan mitra, sertifikat, serta akses admin dalam satu sistem yang sederhana, cepat, dan mudah dikelola di lingkungan Laragon/XAMPP.

---

## Ringkasan proyek

SIMANTAP dirancang untuk membantu admin mengelola informasi tenaga kerja secara terpusat, termasuk:

- data personal dan kepegawaian
- status kerja PKWTT / PKWT
- unit layanan dan perusahaan mitra
- nomor BPJS, DPLK, dan nomor perjanjian kerja
- riwayat sertifikat dan upload bukti sertifikat
- dashboard ringkasan data
- ekspor data ke format CSV/Excel-style

Aplikasi ini tidak menggunakan framework modern seperti Next.js atau Firebase; seluruh project berjalan di PHP native dan database MySQL.

---

## Fitur utama

### 1. Dashboard
- menampilkan total tenaga kerja
- ringkasan status kerja PKWTT dan PKWT
- distribusi tenaga kerja per unit layanan
- data terbaru yang tersimpan di database

### 2. Manajemen tenaga kerja
- tambah data tenaga kerja
- edit dan hapus data
- pencarian berdasarkan nama, NIK, atau jabatan
- filter berdasarkan unit dan status kerja
- pagination untuk daftar data

### 3. Master data
- master data unit layanan
- master data perusahaan mitra
- konsistensi data untuk dropdown dan referensi pada form

### 4. Sertifikat tenaga kerja
- setiap tenaga kerja dapat memiliki beberapa sertifikat
- simpan nomor sertifikat, judul, tanggal terbit, dan tanggal kadaluarsa
- unggah file bukti sertifikat ke folder upload lokal
- data sertifikat terkait langsung dengan tenaga kerja tertentu

### 5. Impor dan ekspor data
- fitur ekspor data tenaga kerja dalam format CSV/Excel-style
- data dapat didownload sesuai kebutuhan operasional
- file template struktur umum dibuat sesuai kebutuhan database internal
- import data tenaga kerja dari `.xlsx` atau `.xls` memakai SheetJS di browser
- preview data sebelum dikirim sebagai JSON ke endpoint PHP native
- proses import tidak membutuhkan Composer, PhpSpreadsheet, atau folder `vendor`

### 6. Akses admin
- pengelolaan pengguna admin melalui panel admin
- sistem login berbasis session dan password hash
- akses dipisahkan sesuai kebutuhan aplikasi

---

## Teknologi yang digunakan

- PHP native
- MySQL
- PDO untuk koneksi database
- HTML, CSS, dan JavaScript
- Bootstrap/Tailwind-like styling via custom CSS classes
- Laragon sebagai lingkungan local development

---

## Struktur folder utama

```text
simantap/
├─ actions/                 # proses simpan, update, hapus data
├─ includes/                # layout header/sidebar/footer dan form reusable
├─ sql/                     # file backup/schema MySQL
├─ uploads/                 # folder file sertifikat
├─ config.php               # konfigurasi database dan base URL
├─ db.php                   # helper koneksi PDO dan query database
├─ dashboard.php            # halaman dashboard utama
├─ index.php                # redirect ke dashboard
├─ tenaga-kerja.php         # halaman data tenaga kerja (termasuk modal import Excel & tombol ekspor)
├─ master-data-unit.php     # master unit layanan
├─ master-data-perusahaan.php # master perusahaan mitra
├─ pengaturan-admin.php     # pengaturan admin
├─ export.php               # endpoint unduh data tenaga kerja (CSV/Excel)
├─ README.md                # dokumentasi proyek
└─ .
```

---

## Struktur database utama

Database yang digunakan adalah `simantap` dan memiliki tabel utama berikut:

- `unit_layanans` : data unit layanan
- `perusahaans` : data perusahaan mitra
- `tenaga_kerjas` : data utama tenaga kerja
- `sertifikasis` : riwayat sertifikat per tenaga kerja
- `users` : akun admin

File schema lengkap ada di:

- [sql/simantap.sql](sql/simantap.sql)

---

## Konfigurasi default

Konfigurasi database bawaan dibuat di [config.php](config.php):

```text
Host     : 127.0.0.1
Port     : 3306
Database : simantap
Username : root
Password : (kosong)
```

Jika penggunaan database Anda berbeda, sesuaikan nilai di [config.php](config.php) terlebih dahulu.

---

## Cara menjalankan di Laragon

### 1. Siapkan folder proyek

Letakkan project di folder berikut:

```text
C:\laragon\www\simantap
```

### 2. Jalankan Apache dan MySQL

- buka Laragon
- klik Start All

### 3. Import database

1. buka http://localhost/phpmyadmin
2. buat database `simantap` jika belum ada
3. import file [sql/simantap.sql](sql/simantap.sql)
4. tunggu sampai proses import selesai

### 4. Akses aplikasi

Buka URL berikut di browser:

```text
http://localhost/simantap
```

Aplikasi akan otomatis mengarahkan ke halaman dashboard setelah dibuka.

---

## Login default

Data admin bawaan sudah disediakan di file SQL dan database, yaitu:

- Username: `123456`
- Password: `123456`
- Email alternatif: `admin@simantap.id`

> Anda dapat login menggunakan Username (`123456`) maupun Email (`admin@simantap.id`) dengan password `123456`. Tersedia pula tombol praktis *Isi Otomatis Kredensial 123456* pada halaman login.

---

## Catatan penting

- Folder upload sertifikat berada di [assets/sertifikat](assets/sertifikat)
- Semua file foto atau bukti sertifikat disimpan secara lokal di project ini
- Aplikasi ini dibuat untuk kebutuhan internal dan bukan aplikasi multi-tenant penuh
- Untuk kebutuhan produksi, perlu ditambahkan validasi lanjutan, manajemen role lebih ketat, dan backup database otomatis

---

## Lisensi

Proyek ini dibuat untuk kebutuhan internal pengelolaan data tenaga kerja dan operasional unit kerja yang terkait.

---

## Status proyek

Project ini sudah memiliki:

- dashboard utama
- CRUD tenaga kerja
- master data unit dan perusahaan
- data sertifikat
- fitur ekspor data
- autentikasi admin dasar

Dengan demikian, README ini sudah sesuai dengan kondisi nyata aplikasi yang ada di workspace ini.