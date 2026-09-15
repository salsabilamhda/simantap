<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sertifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenaga_kerja_id',
        'nomor_sertifikat',
        'judul_sertifikasi',
        'gambar_sertifikat_url',
        'tanggal_terbit',
        'tanggal_kadaluarsa',
    ];

    public function tenagaKerja()
    {
        return $this->belongsTo(TenagaKerja::class, 'tenaga_kerja_id');
    }
}
