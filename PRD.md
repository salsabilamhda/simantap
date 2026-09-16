# Product Requirements Document (PRD)

## SIMANTAP

**Versi:** 1.0  
**Tanggal pembaruan:** 16 September 2026  
**Status:** MVP berjalan / siap dikembangkan  
**Platform:** Web internal, PHP Native, MySQL

## 1. Ringkasan Produk

SIMANTAP adalah aplikasi internal untuk mengelola data tenaga kerja outsourcing/mitra pada wilayah kerja UP3 dan Unit Layanan. Aplikasi memusatkan data personil, unit layanan, perusahaan mitra, sertifikasi, impor-ekspor data, dan akun admin dalam satu sistem.

Produk ditujukan untuk menggantikan pengelolaan spreadsheet yang tersebar dengan data terstruktur yang dapat dicari, difilter, dipantau, dan diekspor kembali untuk kebutuhan operasional.

## 2. Masalah yang Diselesaikan

- Data tenaga kerja sulit dicari ketika tersimpan di banyak file atau format.
- Informasi status PKWTT/PKWT, unit layanan, jabatan, dan data jaminan perlu dikelola dalam satu sumber data.
- Bukti sertifikasi perlu dikaitkan langsung dengan tenaga kerja terkait.
- Admin membutuhkan ringkasan jumlah personil per status dan unit layanan.
- Migrasi data dari Excel harus tetap tersedia tanpa ketergantungan Composer atau PhpSpreadsheet.

## 3. Tujuan dan Indikator Keberhasilan

### Tujuan

1. Menyediakan satu sumber data tenaga kerja yang terstruktur dan mudah dikelola.
2. Mempercepat pencarian dan pemantauan data personil per unit layanan.
3. Menjaga keterkaitan data sertifikat dengan tenaga kerja.
4. Mendukung pertukaran data dengan format Excel/CSV.
5. Membatasi akses pengelolaan data kepada pengguna admin.

### Indikator keberhasilan

- Admin dapat menambah, melihat, mengubah, dan menghapus data tenaga kerja.
- Admin dapat menemukan data berdasarkan nama, NIK, atau jabatan serta memfilter unit dan status.
- Dashboard menampilkan total personil, status PKWTT/PKWT, dan distribusi unit.
- Data sertifikasi dapat disimpan beserta bukti file dan periode berlaku.
- File Excel dapat dipreview sebelum import dan data tenaga kerja dapat diekspor ke CSV.
- Seluruh aksi perubahan data menggunakan autentikasi session dan perlindungan CSRF.

## 4. Pengguna dan Hak Akses

| Peran | Kebutuhan | Hak akses saat ini |
|---|---|---|
| Super Admin | Mengelola seluruh data dan akun | Kelola tenaga kerja, sertifikasi, unit, perusahaan, dan akun admin |
| Admin Operasional | Memutakhirkan data operasional harian | Mengelola data operasional sesuai akses aplikasi |

Catatan: pembatasan hak akses per modul masih perlu diperketat sebelum penggunaan produksi multi-peran.

## 5. Ruang Lingkup MVP

### 5.1 Dashboard

- Menampilkan total tenaga kerja.
- Menampilkan jumlah dan persentase PKWTT serta PKWT.
- Menampilkan jumlah unit layanan.
- Menampilkan distribusi personil per unit, termasuk rincian PKWTT/PKWT.
- Menampilkan lima data tenaga kerja terbaru beserta jumlah sertifikasi.
- Menyediakan pintasan tambah tenaga kerja dan impor/ekspor.

### 5.2 Manajemen tenaga kerja

Admin dapat:

- menambah data baru;
- melihat detail data;
- mengubah data;
- menghapus data;
- mencari berdasarkan nama, NIK, atau jabatan;
- memfilter berdasarkan unit layanan dan status tenaga kerja;
- menavigasi data dengan pagination.

Data utama mencakup identitas, kontak, alamat, pendidikan, jabatan/fungsi, perusahaan, unit, status dan skema kerja, nomor perjanjian, tanggal masuk, BPJS, DPLK, serta metadata pencatatan.

### 5.3 Sertifikasi

- Satu tenaga kerja dapat memiliki banyak sertifikasi.
- Sertifikasi menyimpan judul, nomor, tanggal terbit, dan tanggal kadaluarsa.
- Admin dapat mengunggah bukti sertifikat JPG/PNG dengan batas ukuran 5 MB.
- Sertifikasi dapat dihapus dari tenaga kerja terkait.
- Sertifikasi otomatis terhapus ketika data tenaga kerja induknya dihapus.

### 5.4 Master data

#### Unit layanan

- Menambah unit dengan kode, nama, unit induk, dan warna aksen.
- Menampilkan jumlah tenaga kerja per unit.
- Menggunakan unit sebagai referensi pada data tenaga kerja.

#### Perusahaan mitra

- Menambah nama perusahaan dan nomor perjanjian/kontrak.
- Menampilkan daftar perusahaan mitra aktif.

### 5.5 Impor dan ekspor

- Mengunduh template import tenaga kerja.
- Membaca file `.xlsx` atau `.xls` di browser menggunakan SheetJS.
- Memvalidasi keberadaan seluruh kolom template wajib.
- Menampilkan preview maksimal 20 baris sebelum import.
- Mengirim data yang telah disetujui ke endpoint import PHP.
- Mengekspor data tenaga kerja ke CSV yang kompatibel dengan Excel dan Google Sheets.
- Mempertahankan leading zero pada NIK dan nomor jaminan saat ekspor.

### 5.6 Administrasi akun

- Login menggunakan session.
- Password disimpan dalam bentuk hash.
- Menambah akun admin dengan nama, email, password, role, dan status.
- Mendukung role `admin` dan `superadmin` pada data pengguna.

## 6. Kebutuhan Fungsional

| ID | Kebutuhan | Prioritas | Status |
|---|---|---|---|
| FR-01 | Admin dapat login dan mengakses aplikasi melalui session | Must | Berjalan |
| FR-02 | Sistem menyediakan dashboard ringkasan operasional | Must | Berjalan |
| FR-03 | Admin dapat CRUD data tenaga kerja | Must | Berjalan |
| FR-04 | Sistem menyediakan pencarian, filter, dan pagination | Must | Berjalan |
| FR-05 | Admin dapat mengelola sertifikasi dan bukti file | Must | Berjalan |
| FR-06 | Admin dapat mengelola unit layanan dan perusahaan mitra | Must | Berjalan |
| FR-07 | Sistem menyediakan import Excel dengan template dan preview | Must | Berjalan |
| FR-08 | Sistem menyediakan ekspor CSV | Must | Berjalan |
| FR-09 | Super Admin dapat menambah akun admin | Should | Berjalan |
| FR-10 | Sistem mencatat audit trail perubahan data | Should | Belum tersedia |
| FR-11 | Sistem memberikan peringatan sertifikat yang akan kadaluarsa | Should | Belum tersedia |
| FR-12 | Sistem menyediakan backup dan pemulihan database terjadwal | Should | Belum tersedia |
| FR-13 | Hak akses dibatasi secara granular per role | Should | Perlu penguatan |

## 7. Kebutuhan Nonfungsional

- **Keamanan:** gunakan PDO prepared statement, password hash, session authentication, CSRF token, validasi input, dan validasi tipe/ukuran file upload.
- **Kinerja:** daftar tenaga kerja menggunakan pagination; pencarian dan filter memanfaatkan indeks database yang tersedia.
- **Konsistensi:** relasi tenaga kerja-unit dan tenaga kerja-sertifikasi menjaga integritas referensi.
- **Kompatibilitas:** berjalan pada PHP native dengan MySQL/MariaDB di Laragon atau XAMPP; UI harus dapat dipakai pada desktop dan perangkat mobile.
- **Pemeliharaan:** konfigurasi database dipisahkan dari halaman; struktur action dan include dipertahankan modular.
- **Data:** backup database dan folder `assets/sertifikat` harus dilakukan sebelum deployment atau migrasi.

## 8. Model Data Inti

- `users`: akun, role, status, dan kredensial admin.
- `unit_layanans`: kode, nama, unit induk, dan warna unit.
- `perusahaans`: nama perusahaan dan nomor perjanjian.
- `tenaga_kerjas`: data utama personil dan referensi unit layanan.
- `sertifikasis`: sertifikasi personil dan lokasi bukti file.

Relasi utama:

- `unit_layanans` 1 ke banyak `tenaga_kerjas`.
- `tenaga_kerjas` 1 ke banyak `sertifikasis`.
- Penghapusan tenaga kerja menghapus sertifikasi terkait secara cascade.

## 9. Alur Utama

### Pencatatan tenaga kerja

1. Admin membuka menu Data Tenaga Kerja.
2. Admin mengisi data personil dan memilih unit layanan.
3. Sistem memvalidasi dan menyimpan data.
4. Data muncul di tabel dan ikut dihitung pada dashboard.

### Import dari Excel

1. Admin mengunduh template.
2. Admin memilih file `.xlsx` atau `.xls`.
3. Sistem membaca dan memvalidasi header.
4. Sistem menampilkan preview.
5. Admin menjalankan proses import.
6. Sistem menyimpan baris yang valid dan menampilkan hasil proses.

### Pengelolaan sertifikat

1. Admin membuka sertifikasi dari baris tenaga kerja.
2. Admin memasukkan judul, nomor, tanggal, dan bukti file.
3. Sistem menyimpan metadata dan file pada folder upload.
4. Sertifikasi tampil pada detail tenaga kerja.

## 10. Batasan dan Asumsi

- Aplikasi digunakan untuk lingkungan internal, bukan multi-tenant.
- Database utama adalah MySQL/MariaDB dan file sertifikat disimpan lokal.
- Import saat ini berfokus pada data tenaga kerja; import sertifikat berbasis file belum menjadi bagian dari batch import.
- Seed akun admin pada SQL hanya untuk instalasi/pengujian awal dan wajib diganti sebelum produksi.
- Notifikasi, audit trail, dan backup otomatis belum termasuk MVP berjalan.

## 11. Prioritas Rilis Berikutnya

1. Memperketat role-based access control dan melindungi route admin.
2. Menambahkan audit trail untuk tambah, ubah, hapus, import, dan pengelolaan sertifikat.
3. Menambahkan indikator sertifikat mendekati kadaluarsa dan sudah kadaluarsa.
4. Menambahkan validasi duplikasi NIK serta hasil import per baris.
5. Menambahkan backup database dan file upload yang terjadwal.
6. Mengganti kredensial seed dan menambahkan kebijakan password produksi.

## 12. Kriteria Penerimaan MVP

- Admin yang valid dapat masuk dan pengguna tanpa session tidak dapat mengakses halaman operasional.
- CRUD tenaga kerja berhasil menyimpan data yang dapat dicari dan difilter.
- Dashboard memperbarui jumlah berdasarkan data database.
- Setiap sertifikasi tersimpan pada tenaga kerja yang tepat dan file dapat dibuka.
- Import hanya dapat diproses setelah header wajib tersedia dan preview ditampilkan.
- Ekspor menghasilkan file CSV dengan kolom operasional dan karakter Indonesia tetap terbaca.
- Data master unit dan perusahaan dapat digunakan pada alur pengelolaan tenaga kerja.
