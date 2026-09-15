<?php

namespace App\Http\Controllers;

use App\Models\TenagaKerja;
use App\Models\UnitLayanan;
use Illuminate\Http\Request;

class ImportExportController extends Controller
{
    public function index()
    {
        $totalRecords = TenagaKerja::count();
        $unitCounts = UnitLayanan::withCount('tenagaKerjas')->get();

        return view('import-export', compact('totalRecords', 'unitCounts'));
    }

    public function exportCsv()
    {
        $fileName = 'DATA_TENAGA_KERJA_SIMANTAP_' . date('Ymd_His') . '.csv';
        $tenagaKerja = TenagaKerja::with(['unitLayanan', 'sertifikasis'])->orderBy('id', 'asc')->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'NO',
            'NOMOR PERJANJIAN',
            'NAMA PERUSAHAAN',
            'NAMA',
            'NIK',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'USIA',
            'PENDIDIKAN TERAKHIR',
            'JURUSAN',
            'NO TELEPON',
            'EMAIL',
            'JENIS KELAMIN',
            'ALAMAT DOMISILI',
            'KOTA KABUPATEN',
            'PROVINSI',
            'JABATAN TERAKHIR',
            'FUNGSI PEKERJAAN',
            'UNIT',
            'UNIT LAYANAN',
            'NOMOR BPJS KESEHATAN',
            'NOMOR BPJS KETENAGAKERJAAN',
            'NOMOR DPLK',
            'BANK DPLK',
            'NO PERJANJIAN KERJA',
            'TANGGAL MASUK KERJA',
            'STATUS TENAGA KERJA',
            'SKEMA TENAGA KERJA',
            'JUMLAH SERTIFIKASI',
        ];

        $callback = function() use ($tenagaKerja, $columns) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            $i = 1;
            foreach ($tenagaKerja as $row) {
                fputcsv($file, [
                    $row->no_urut ?: $i,
                    $row->nomor_perjanjian,
                    $row->nama_perusahaan,
                    $row->nama,
                    "'" . $row->nik, // protect leading zeros
                    $row->tempat_lahir,
                    $row->tanggal_lahir,
                    $row->usia,
                    $row->pendidikan_terakhir,
                    $row->jurusan,
                    $row->no_telepon,
                    $row->email,
                    $row->jenis_kelamin,
                    $row->alamat_domisili,
                    $row->kota_kabupaten,
                    $row->provinsi,
                    $row->jabatan_terakhir,
                    $row->fungsi_pekerjaan,
                    $row->unit,
                    $row->unitLayanan ? $row->unitLayanan->nama : $row->unit,
                    "'" . $row->nomor_bpjs_kesehatan,
                    "'" . $row->nomor_bpjs_ketenagakerjaan,
                    "'" . $row->nomor_dplk,
                    $row->bank_dplk,
                    $row->no_perjanjian_kerja,
                    $row->tanggal_masuk_kerja,
                    $row->status_tenaga_kerja,
                    $row->skema_tenaga_kerja,
                    $row->sertifikasis->count(),
                ]);
                $i++;
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
