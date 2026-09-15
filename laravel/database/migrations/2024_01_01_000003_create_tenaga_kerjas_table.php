<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenaga_kerjas', function (Blueprint $table) {
            $table->id();
            $table->string('no_urut')->nullable();
            $table->string('nomor_perjanjian')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('nama');
            $table->string('nik', 20)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('jenis_kelamin', 20)->default('LAKI');
            $table->text('alamat_domisili')->nullable();
            $table->string('kota_kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('jabatan_terakhir')->nullable();
            $table->string('fungsi_pekerjaan')->nullable();
            $table->string('unit')->nullable();
            $table->foreignId('unit_layanan_id')->nullable()->constrained('unit_layanans')->nullOnDelete();
            $table->string('nomor_bpjs_kesehatan')->nullable();
            $table->string('nomor_bpjs_ketenagakerjaan')->nullable();
            $table->string('nomor_dplk')->nullable();
            $table->string('bank_dplk')->nullable();
            $table->string('no_perjanjian_kerja')->nullable();
            $table->date('tanggal_masuk_kerja')->nullable();
            $table->string('status_tenaga_kerja', 20)->default('PKWTT'); // PKWT / PKWTT
            $table->string('skema_tenaga_kerja')->default('PEMBORONGAN');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenaga_kerjas');
    }
};
