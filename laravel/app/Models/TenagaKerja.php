<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenagaKerja extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['usia'];

    public function unitLayanan()
    {
        return $this->belongsTo(UnitLayanan::class, 'unit_layanan_id');
    }

    public function sertifikasis()
    {
        return $this->hasMany(Sertifikasi::class, 'tenaga_kerja_id');
    }

    public function getUsiaAttribute()
    {
        if (!$this->tanggal_lahir) {
            return '-';
        }
        try {
            $dob = new \DateTime($this->tanggal_lahir);
            $now = new \DateTime();
            return $now->diff($dob)->y . ' Tahun';
        } catch (\Exception $e) {
            return '-';
        }
    }
}
