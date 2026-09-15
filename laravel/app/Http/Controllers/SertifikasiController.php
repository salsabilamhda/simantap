<?php

namespace App\Http\Controllers;

use App\Models\TenagaKerja;
use App\Models\Sertifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SertifikasiController extends Controller
{
    public function store(Request $request, $tenagaKerjaId)
    {
        $tk = TenagaKerja::findOrFail($tenagaKerjaId);

        $validated = $request->validate([
            'nomor_sertifikat' => 'nullable|string|max:255',
            'judul_sertifikasi' => 'required|string|max:255',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_kadaluarsa' => 'nullable|date',
            'gambar_sertifikat' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('gambar_sertifikat')) {
            $path = $request->file('gambar_sertifikat')->store('sertifikat', 'public');
            $filePath = Storage::url($path);
        }

        Sertifikasi::create([
            'tenaga_kerja_id' => $tk->id,
            'nomor_sertifikat' => $validated['nomor_sertifikat'] ?? null,
            'judul_sertifikasi' => $validated['judul_sertifikasi'],
            'gambar_sertifikat_url' => $filePath,
            'tanggal_terbit' => $validated['tanggal_terbit'] ?? null,
            'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'] ?? null,
        ]);

        return redirect()->back()->with('success', "Sertifikasi berhasil ditambahkan untuk {$tk->nama}!");
    }

    public function destroy($id)
    {
        $cert = Sertifikasi::findOrFail($id);
        $cert->delete();

        return redirect()->back()->with('success', 'Sertifikasi berhasil dihapus.');
    }
}
