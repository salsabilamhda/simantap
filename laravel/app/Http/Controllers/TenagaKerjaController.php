<?php

namespace App\Http\Controllers;

use App\Models\TenagaKerja;
use App\Models\UnitLayanan;
use App\Models\Perusahaan;
use App\Models\Sertifikasi;
use Illuminate\Http\Request;

class TenagaKerjaController extends Controller
{
    public function index(Request $request)
    {
        $query = TenagaKerja::with(['unitLayanan', 'sertifikasis']);

        // Search query (nama, NIK)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('jabatan_terakhir', 'like', "%{$search}%");
            });
        }

        // Unit Layanan filter
        if ($request->filled('unit') && $request->unit !== 'ALL') {
            $query->where('unit_layanan_id', $request->unit);
        }

        // Status Tenaga Kerja filter
        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status_tenaga_kerja', $request->status);
        }

        $tenagaKerjaList = $query->orderBy('id', 'asc')->paginate(10)->withQueryString();

        $unitLayanans = UnitLayanan::all();
        $perusahaans = Perusahaan::all();

        return view('tenaga-kerja.index', compact('tenagaKerjaList', 'unitLayanans', 'perusahaans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'nomor_perjanjian' => 'nullable|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'jurusan' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'jenis_kelamin' => 'required|in:LAKI,PEREMPUAN',
            'alamat_domisili' => 'nullable|string',
            'kota_kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'jabatan_terakhir' => 'nullable|string|max:255',
            'fungsi_pekerjaan' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'unit_layanan_id' => 'nullable|exists:unit_layanans,id',
            'nomor_bpjs_kesehatan' => 'nullable|string|max:100',
            'nomor_bpjs_ketenagakerjaan' => 'nullable|string|max:100',
            'nomor_dplk' => 'nullable|string|max:100',
            'bank_dplk' => 'nullable|string|max:100',
            'no_perjanjian_kerja' => 'nullable|string|max:255',
            'tanggal_masuk_kerja' => 'nullable|date',
            'status_tenaga_kerja' => 'required|in:PKWT,PKWTT',
            'skema_tenaga_kerja' => 'nullable|string|max:100',
        ]);

        $tk = TenagaKerja::create($validated);

        return redirect()->route('tenaga-kerja.index')
            ->with('success', "Tenaga kerja {$tk->nama} berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $tk = TenagaKerja::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'nomor_perjanjian' => 'nullable|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'jurusan' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'jenis_kelamin' => 'required|in:LAKI,PEREMPUAN',
            'alamat_domisili' => 'nullable|string',
            'kota_kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'jabatan_terakhir' => 'nullable|string|max:255',
            'fungsi_pekerjaan' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'unit_layanan_id' => 'nullable|exists:unit_layanans,id',
            'nomor_bpjs_kesehatan' => 'nullable|string|max:100',
            'nomor_bpjs_ketenagakerjaan' => 'nullable|string|max:100',
            'nomor_dplk' => 'nullable|string|max:100',
            'bank_dplk' => 'nullable|string|max:100',
            'no_perjanjian_kerja' => 'nullable|string|max:255',
            'tanggal_masuk_kerja' => 'nullable|date',
            'status_tenaga_kerja' => 'required|in:PKWT,PKWTT',
            'skema_tenaga_kerja' => 'nullable|string|max:100',
        ]);

        $tk->update($validated);

        return redirect()->back()->with('success', "Data {$tk->nama} berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $tk = TenagaKerja::findOrFail($id);
        $name = $tk->nama;
        $tk->delete();

        return redirect()->route('tenaga-kerja.index')
            ->with('success', "Data tenaga kerja {$name} berhasil dihapus.");
    }
}
