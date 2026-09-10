import { Suspense } from "react";
import StatCard from "@/components/stat-card";
import TenagaKerjaTable from "@/components/tenaga-kerja-table";
import UnitDistribution from "@/components/unit-distribution";
import { STATS_SUMMARY } from "@/lib/sample-data";
import {
    Briefcase,
    Building2,
    FileSpreadsheet,
    PlusCircle,
    ShieldCheck,
    Sparkles,
    UserCheck,
    Users
} from "lucide-react";
import Link from "next/link";

export default function DashboardPage() {
  return (
    <div className="w-full max-w-full space-y-8 overflow-hidden min-w-0">
      
      {/* Hero Welcome Banner */}
      <div className="relative overflow-hidden bg-gradient-to-r from-white via-[#E8F6F5] to-white rounded-3xl p-6 sm:p-8 border border-[#2BA8A2]/20 shadow-card-custom">
        <div className="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
          <div className="max-w-2xl">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FFF8E7] border border-[#FFD23F] text-[#C9A227] text-xs font-black mb-3 shadow-xs">
              <Sparkles className="w-3.5 h-3.5" />
              <span>Sistem Manajemen Data Tenaga Kerja Terintegrasi</span>
            </div>
            
            <h1 className="text-2xl sm:text-4xl font-black text-[#1E8C86] tracking-tight leading-tight">
              Selamat Datang di SIMANTAP
            </h1>
            
            <p className="mt-2 text-sm text-gray-600 font-medium leading-relaxed">
              Pusat kendali dan monitoring data tenaga kerja outsourcing/mitra di wilayah kerja 5 Unit Layanan (ULP Balong, ULP Pacitan, ULP Ponorogo, ULP Trenggalek, dan UP3 Ponorogo).
            </p>
          </div>

          {/* Quick Action Buttons */}
          <div className="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <Link
              href="/tenaga-kerja?tambah=true"
              className="btn-gold-primary px-5 py-3 text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer flex-1 md:flex-initial"
            >
              <PlusCircle className="w-4 h-4 text-[#2C3E50]" />
              Tambah Tenaga Kerja
            </Link>

            <Link
              href="/import-export"
              className="btn-teal-outline px-5 py-3 text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer flex-1 md:flex-initial"
            >
              <FileSpreadsheet className="w-4 h-4" />
              Impor / Ekspor
            </Link>
          </div>
        </div>

        {/* Decorative Background Accents */}
        <div className="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-[#2BA8A2]/10 blur-2xl pointer-events-none" />
        <div className="absolute -bottom-10 -left-10 w-40 h-40 rounded-full bg-[#FFD23F]/15 blur-2xl pointer-events-none" />
      </div>

      {/* KPI Cards Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <StatCard
          title="Total Tenaga Kerja"
          value={STATS_SUMMARY.totalTenagaKerja}
          subtitle="Tercatat di seluruh unit"
          icon={Users}
          variant="teal"
          badgeText="100% Aktif"
        />

        <StatCard
          title="Status PKWTT"
          value={STATS_SUMMARY.totalPkwtt}
          subtitle="Perjanjian Kerja Tetap"
          icon={ShieldCheck}
          variant="gold"
          badgeText="91.9% Mayoritas"
        />

        <StatCard
          title="Status PKWT"
          value={STATS_SUMMARY.totalPkwt}
          subtitle="Perjanjian Waktu Tertentu"
          icon={Briefcase}
          variant="coral"
          badgeText="8.1% Kontrak"
        />

        <StatCard
          title="Unit Layanan"
          value={STATS_SUMMARY.unitCount}
          subtitle="ULP & UP3 Ponorogo"
          icon={Building2}
          variant="sky"
          badgeText="5 Lokasi"
        />
      </div>

      {/* Middle Section: Distribution & Summary Card */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 min-w-0">
        
        {/* Unit Breakdown (2 Columns) */}
        <div className="lg:col-span-2 min-w-0 overflow-hidden">
          <UnitDistribution />
        </div>

        {/* Mitra & Demografi Card (1 Column) */}
        <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom flex flex-col justify-between min-w-0 overflow-hidden">
          <div>
            <div className="flex items-center gap-2.5 pb-4 mb-4 border-b-2 border-dashed border-[#2BA8A2]/20">
              <div className="w-9 h-9 rounded-xl bg-[#FFF8E7] flex items-center justify-center text-[#C9A227]">
                <UserCheck className="w-5 h-5" />
              </div>
              <div>
                <h3 className="font-extrabold text-[#1E8C86] text-lg">Ringkasan Mitra</h3>
                <p className="text-xs text-gray-400 font-medium">Informasi Penyedia & Demografi</p>
              </div>
            </div>

            <div className="space-y-4 text-xs">
              <div className="p-3.5 rounded-2xl bg-[#EFF8F7] border border-[#2BA8A2]/10 min-w-0 overflow-hidden">
                <span className="text-gray-400 font-bold uppercase text-[10px] tracking-wider block">
                  Perusahaan Penyedia Jasa
                </span>
                <span className="font-extrabold text-sm text-gray-800 mt-0.5 block break-words leading-relaxed">
                  {STATS_SUMMARY.perusahaanUtama}
                </span>
                <span className="text-[11px] text-[#2BA8A2] font-semibold mt-1 inline-block break-words leading-relaxed max-w-full">
                  No. Kontrak: 1211,Pj/DAN,00,07/F04000000/2024
                </span>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div className="p-3 rounded-2xl bg-[#FFF8E7] border border-[#FFD23F]/30 text-center">
                  <span className="text-[10px] font-bold text-gray-400 uppercase">Laki-Laki</span>
                  <div className="text-xl font-black text-gray-800 mt-0.5">
                    {STATS_SUMMARY.totalLaki}
                  </div>
                  <span className="text-[10px] text-gray-500 font-semibold">98.5% Total</span>
                </div>

                <div className="p-3 rounded-2xl bg-[#FDF1EE] border border-[#EF6C4A]/30 text-center">
                  <span className="text-[10px] font-bold text-gray-400 uppercase">Perempuan</span>
                  <div className="text-xl font-black text-[#EF6C4A] mt-0.5">
                    {STATS_SUMMARY.totalPerempuan}
                  </div>
                  <span className="text-[10px] text-gray-500 font-semibold">1.5% Total</span>
                </div>
              </div>

              <div className="p-3 rounded-2xl bg-white border border-gray-200">
                <div className="flex items-center justify-between text-gray-600 mb-1">
                  <span className="font-bold">Skema Mayoritas</span>
                  <span className="font-bold text-[#1E8C86]">Pemborongan</span>
                </div>
                <div className="text-[11px] text-gray-400">
                  Pelayanan Teknik (Yantek), Pemeliharaan, dan Penertiban Pemakaian Tenaga Listrik (P2TL)
                </div>
              </div>
            </div>
          </div>

          <div className="mt-5 pt-4 border-t border-dashed border-gray-100 flex items-center justify-between text-xs">
            <span className="text-gray-400 font-medium">Database: Firestore</span>
            <span className="text-gray-400 font-medium">Berkas: Cloudinary</span>
          </div>
        </div>

      </div>

      {/* Tenaga Kerja Table Section */}
      <div>
        <Suspense fallback={<div className="h-96 bg-white rounded-3xl animate-pulse" />}>
          <TenagaKerjaTable />
        </Suspense>
      </div>

    </div>
  );
}
