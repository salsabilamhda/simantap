"use client";

import React from "react";
import Link from "next/link";
import { ArrowLeft, Briefcase, PlusCircle, CheckCircle2 } from "lucide-react";

export default function MasterPerusahaanPage() {
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
            <h1 className="text-2xl font-black text-[#1E8C86]">Master Perusahaan Mitra</h1>
            <p className="text-xs text-gray-500 font-medium">
              Daftar badan usaha penyedia tenaga kerja outsourcing/mitra
            </p>
          </div>
        </div>

        <button
          onClick={() => alert("Fitur tambah master perusahaan")}
          className="btn-gold-primary px-4 py-2.5 flex items-center gap-2 text-xs uppercase tracking-wider cursor-pointer"
        >
          <PlusCircle className="w-4 h-4 text-[#2C3E50]" />
          <span>Tambah Perusahaan</span>
        </button>
      </div>

      <div className="max-w-2xl">
        <div className="card-simantap p-6 border-l-[#FFD23F]">
          <div className="flex items-start justify-between">
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 rounded-2xl bg-[#FFF8E7] flex items-center justify-center text-[#C9A227] shadow-xs">
                <Briefcase className="w-6 h-6" />
              </div>
              <div>
                <h3 className="text-lg font-black text-gray-800">
                  PT ANUGERAH PUTRA PERMANA
                </h3>
                <span className="inline-flex items-center gap-1 text-[11px] font-bold text-[#27AE60] mt-0.5">
                  <CheckCircle2 className="w-3.5 h-3.5" /> Mitra Kerja Sama Aktif
                </span>
              </div>
            </div>

            <span className="px-3 py-1 rounded-full text-xs font-black bg-[#E8F6F5] text-[#1E8C86]">
              198 Personil
            </span>
          </div>

          <div className="mt-6 pt-4 border-t border-dashed border-gray-100 space-y-2 text-xs text-gray-600">
            <div className="flex items-center justify-between">
              <span className="font-semibold text-gray-400">Nomor Perjanjian Kerja Sama</span>
              <span className="font-mono font-bold text-gray-800">
                1211,Pj/DAN,00,07/F04000000/2024
              </span>
            </div>
            <div className="flex items-center justify-between">
              <span className="font-semibold text-gray-400">Bidang Layanan</span>
              <span className="font-bold text-gray-800">
                Pelayanan Teknik (Yantek) & Pemeliharaan Jaringan
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
