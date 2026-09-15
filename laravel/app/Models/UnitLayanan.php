<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitLayanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'unit_induk',
        'color_hex',
    ];

    public function tenagaKerjas()
    {
        return $this->hasMany(TenagaKerja::class, 'unit_layanan_id');
    }
}
