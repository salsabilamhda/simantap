<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenaga_kerja_id')->constrained('tenaga_kerjas')->cascadeOnDelete();
            $table->string('nomor_sertifikat')->nullable();
            $table->string('judul_sertifikasi');
            $table->text('gambar_sertifikat_url')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikasis');
    }
};
