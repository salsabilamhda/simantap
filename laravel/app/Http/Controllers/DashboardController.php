<?php

namespace App\Http\Controllers;

use App\Models\TenagaKerja;
use App\Models\UnitLayanan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenagaKerja = TenagaKerja::count();
        $totalPkwtt = TenagaKerja::where('status_tenaga_kerja', 'PKWTT')->count();
        $totalPkwt = TenagaKerja::where('status_tenaga_kerja', 'PKWT')->count();
        $unitCount = UnitLayanan::count();

        $units = UnitLayanan::withCount([
            'tenagaKerjas as count',
            'tenagaKerjas as pkwt_count' => function ($q) {
                $q->where('status_tenaga_kerja', 'PKWT');
            },
            'tenagaKerjas as pkwtt_count' => function ($q) {
                $q->where('status_tenaga_kerja', 'PKWTT');
            }
        ])->get();

        $recentWorkers = TenagaKerja::with('unitLayanan', 'sertifikasis')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalTenagaKerja',
            'totalPkwtt',
            'totalPkwt',
            'unitCount',
            'units',
            'recentWorkers'
        ));
    }
}
