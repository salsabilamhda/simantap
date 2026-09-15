<?php

namespace App\Http\Controllers;

use App\Models\UnitLayanan;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function unitLayanan()
    {
        $units = UnitLayanan::withCount('tenagaKerjas')->get();
        return view('master-data.unit-layanan', compact('units'));
    }

    public function storeUnitLayanan(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|unique:unit_layanans,kode|max:50',
            'nama' => 'required|string|max:100',
            'unit_induk' => 'required|string|max:100',
            'color_hex' => 'required|string|max:20',
        ]);

        UnitLayanan::create($validated);

        return redirect()->back()->with('success', "Unit Layanan {$validated['nama']} berhasil ditambahkan!");
    }

    public function perusahaan()
    {
        $companies = Perusahaan::all();
        return view('master-data.perusahaan', compact('companies'));
    }

    public function storePerusahaan(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_perjanjian' => 'nullable|string|max:255',
        ]);

        Perusahaan::create($validated);

        return redirect()->back()->with('success', "Perusahaan {$validated['nama']} berhasil ditambahkan!");
    }
}
