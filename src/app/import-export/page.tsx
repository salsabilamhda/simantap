"use client";

import React, { useState } from "react";
import Link from "next/link";
import { ArrowLeft, UploadCloud, Download, FileSpreadsheet, AlertCircle, CheckCircle2 } from "lucide-react";

export default function ImportExportPage() {
  const [isExporting, setIsExporting] = useState(false);

  const handleExport = () => {
    setIsExporting(true);
    setTimeout(() => {
      setIsExporting(false);
      alert("Ekspor file Excel berhasil disiapkan sesuai format template TEMPLATE_TENAGA_KERJA_UPDATE_JULI_2026.xlsx");
    }, 1000);
  };

  return (
    <div className="max-w-4xl mx-auto space-y-8">
      
      {/* Header */}
      <div className="flex items-center gap-3">
        <Link
          href="/"
          className="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[#1E8C86] hover:border-[#2BA8A2] transition-colors"
        >
          <ArrowLeft className="w-5 h-5" />
        </Link>
        <div>
          <h1 className="text-2xl font-black text-[#1E8C86]">Impor & Ekspor Data Excel</h1>
          <p className="text-xs text-gray-500 font-medium">
            Pertukaran data langsung yang kompatibel dengan sheet template asli (.xlsx)
          </p>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {/* Card Import Excel */}
        <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom flex flex-col justify-between">
          <div>
            <div className="flex items-center gap-2.5 pb-4 mb-4 border-b-2 border-dashed border-[#2BA8A2]/20">
              <div className="w-9 h-9 rounded-xl bg-[#E8F6F5] flex items-center justify-center text-[#1E8C86]">
                <UploadCloud className="w-5 h-5" />
              </div>
              <div>
                <h3 className="font-extrabold text-[#1E8C86] text-lg">Unggah Berkas Excel</h3>
                <p className="text-xs text-gray-400 font-medium">Format: .xlsx (Sheet Duplikat)</p>
              </div>
            </div>

            <p className="text-xs text-gray-600 leading-relaxed mb-4">
              Unggah file spreadsheet untuk menambahkan atau memperbarui data secara massal. Sistem akan otomatis memvalidasi 16 digit NIK dan struktur kolom acuan.
            </p>

            <div className="border-2 border-dashed border-[#2BA8A2]/40 rounded-2xl p-6 text-center bg-[#EFF8F7]/50 hover:bg-[#EFF8F7] transition-colors cursor-pointer">
              <FileSpreadsheet className="w-10 h-10 text-[#2BA8A2] mx-auto mb-2" />
              <p className="text-xs font-bold text-gray-700">Tarik berkas .xlsx ke sini, atau klik untuk memilih</p>
              <span className="text-[10px] text-gray-400 mt-1 block">Maksimal 10MB</span>
            </div>
          </div>

          <div className="mt-6">
            <button
              onClick={() => alert("Fitur unggah berkas excel akan memproses data ke Firestore")}
              className="btn-sky w-full py-2.5 text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer"
            >
              <UploadCloud className="w-4 h-4" />
              Proses Impor
            </button>
          </div>
        </div>

        {/* Card Export Excel */}
        <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom flex flex-col justify-between">
          <div>
            <div className="flex items-center gap-2.5 pb-4 mb-4 border-b-2 border-dashed border-[#2BA8A2]/20">
              <div className="w-9 h-9 rounded-xl bg-[#FFF8E7] flex items-center justify-center text-[#C9A227]">
                <Download className="w-5 h-5" />
              </div>
              <div>
                <h3 className="font-extrabold text-[#1E8C86] text-lg">Unduh Data Excel</h3>
                <p className="text-xs text-gray-400 font-medium">Kompatibel 100% dengan Template Asli</p>
              </div>
            </div>

            <p className="text-xs text-gray-600 leading-relaxed mb-4">
              Ekspor seluruh data atau hasil filter saat ini ke dalam berkas Excel dengan struktur 30 kolom lengkap serta rekapitulasi pivot.
            </p>

            <div className="p-4 rounded-2xl bg-[#FFF8E7] border border-[#FFD23F]/30 space-y-2 text-xs">
              <div className="flex items-center gap-2 text-gray-700 font-semibold">
                <CheckCircle2 className="w-4 h-4 text-[#27AE60]" />
                <span>Format kolom No, NIK, Perjanjian, BPJS</span>
              </div>
              <div className="flex items-center gap-2 text-gray-700 font-semibold">
                <CheckCircle2 className="w-4 h-4 text-[#27AE60]" />
                <span>Otomatis gabungkan multi-sertifikasi</span>
              </div>
              <div className="flex items-center gap-2 text-gray-700 font-semibold">
                <CheckCircle2 className="w-4 h-4 text-[#27AE60]" />
                <span>198+ Record data tenaga kerja aktif</span>
              </div>
            </div>
          </div>

          <div className="mt-6">
            <button
              onClick={handleExport}
              disabled={isExporting}
              className="btn-gold-primary w-full py-2.5 text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer"
            >
              <Download className="w-4 h-4 text-[#2C3E50]" />
              {isExporting ? "Menyiapkan Berkas..." : "Unduh Excel (.xlsx)"}
            </button>
          </div>
        </div>

      </div>

    </div>
  );
}
