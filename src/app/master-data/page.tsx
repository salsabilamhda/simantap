import React from "react";
import Link from "next/link";
import { ArrowLeft, Building2, Briefcase, PlusCircle, CheckCircle2 } from "lucide-react";
import { UNIT_LAYANAN_DATA } from "@/lib/sample-data";

export default function MasterDataPage() {
  return (
    <div className="max-w-4xl mx-auto space-y-8">
      
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <Link
            href="/"
            className="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[#1E8C86] hover:border-[#2BA8A2] transition-colors"
          >
            <ArrowLeft className="w-5 h-5" />
          </Link>
          <div>
            <h1 className="text-2xl font-black text-[#1E8C86]">Master Data</h1>
            <p className="text-xs text-gray-500 font-medium">
              Pengaturan unit layanan pelanggan dan perusahaan penyedia jasa mitra
            </p>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {/* Unit Layanan Master */}
        <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom">
          <div className="flex items-center justify-between pb-4 mb-4 border-b-2 border-dashed border-[#2BA8A2]/20">
            <div className="flex items-center gap-2">
              <div className="w-8 h-8 rounded-xl bg-[#E8F6F5] flex items-center justify-center text-[#1E8C86]">
                <Building2 className="w-4 h-4" />
              </div>
              <h3 className="font-extrabold text-[#1E8C86] text-base">Unit Layanan</h3>
            </div>
            <span className="text-xs font-bold text-gray-400">5 Terdaftar</span>
          </div>

          <div className="space-y-2.5">
            {UNIT_LAYANAN_DATA.map((u) => (
              <div
                key={u.kode}
                className="flex items-center justify-between p-3 rounded-2xl bg-[#EFF8F7] border border-[#2BA8A2]/10 text-xs"
              >
                <div className="flex items-center gap-2">
                  <span
                    className="w-2.5 h-2.5 rounded-full"
                    style={{ backgroundColor: u.colorHex }}
                  />
                  <span className="font-extrabold text-gray-800">{u.nama}</span>
                </div>
                <span className="font-bold text-[#1E8C86]">{u.count} Personil</span>
              </div>
            ))}
          </div>
        </div>

        {/* Perusahaan Mitra Master */}
        <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom">
          <div className="flex items-center justify-between pb-4 mb-4 border-b-2 border-dashed border-[#2BA8A2]/20">
            <div className="flex items-center gap-2">
              <div className="w-8 h-8 rounded-xl bg-[#FFF8E7] flex items-center justify-center text-[#C9A227]">
                <Briefcase className="w-4 h-4" />
              </div>
              <h3 className="font-extrabold text-[#1E8C86] text-base">Perusahaan Mitra</h3>
            </div>
            <span className="text-xs font-bold text-gray-400">1 Aktif</span>
          </div>

          <div className="p-4 rounded-2xl bg-[#FFF8E7] border border-[#FFD23F]/30 space-y-2 text-xs">
            <div className="font-black text-gray-800 text-sm">
              PT ANUGERAH PUTRA PERMANA
            </div>
            <div className="text-gray-500 font-medium">
              No. Perjanjian: <span className="font-mono text-gray-700">1211,Pj/DAN,00,07/F04000000/2024</span>
            </div>
            <div className="pt-2 flex items-center gap-2 text-[11px] text-[#27AE60] font-bold">
              <CheckCircle2 className="w-3.5 h-3.5" />
              Mitra Kerja Sama Aktif
            </div>
          </div>
        </div>

      </div>

    </div>
  );
}
