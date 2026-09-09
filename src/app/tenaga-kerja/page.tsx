import React from "react";
import Link from "next/link";
import { ArrowLeft, PlusCircle } from "lucide-react";
import TenagaKerjaTable from "@/components/tenaga-kerja-table";

export default function TenagaKerjaListPage() {
  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <Link
            href="/"
            className="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[#1E8C86] hover:border-[#2BA8A2] transition-colors"
          >
            <ArrowLeft className="w-5 h-5" />
          </Link>
          <div>
            <h1 className="text-2xl font-black text-[#1E8C86]">Manajemen Tenaga Kerja</h1>
            <p className="text-xs text-gray-500 font-medium">
              Kelola, cari, dan perbarui data profil serta sertifikasi personil
            </p>
          </div>
        </div>

        <Link
          href="/tenaga-kerja/tambah"
          className="btn-gold-primary px-4 py-2.5 flex items-center gap-2 text-xs uppercase tracking-wider cursor-pointer"
        >
          <PlusCircle className="w-4 h-4 text-[#2C3E50]" />
          <span>Tambah Data</span>
        </Link>
      </div>

      <TenagaKerjaTable />
    </div>
  );
}
