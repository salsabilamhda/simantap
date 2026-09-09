"use client";

import React from "react";
import Link from "next/link";
import { ArrowLeft, Building2, PlusCircle, Users } from "lucide-react";
import { UNIT_LAYANAN_DATA } from "@/lib/sample-data";

export default function MasterUnitLayananPage() {
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
            <h1 className="text-2xl font-black text-[#1E8C86]">Master Unit Layanan</h1>
            <p className="text-xs text-gray-500 font-medium">
              Daftar 5 Unit Layanan Pelanggan (ULP) dan Unit Pelaksana Pelayanan (UP3)
            </p>
          </div>
        </div>

        <button
          onClick={() => alert("Fitur tambah master unit layanan")}
          className="btn-gold-primary px-4 py-2.5 flex items-center gap-2 text-xs uppercase tracking-wider cursor-pointer"
        >
          <PlusCircle className="w-4 h-4 text-[#2C3E50]" />
          <span>Tambah Unit</span>
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        {UNIT_LAYANAN_DATA.map((unit) => (
          <div
            key={unit.kode}
            className="card-simantap p-6 border-l-[6px] transition-all hover:-translate-y-1"
            style={{ borderLeftColor: unit.colorHex }}
          >
            <div className="flex items-start justify-between mb-4">
              <div
                className="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-xs font-black text-lg"
                style={{ backgroundColor: unit.colorHex }}
              >
                <Building2 className="w-6 h-6" />
              </div>
              <span className="px-3 py-1 rounded-full text-xs font-black bg-[#EFF8F7] text-[#1E8C86]">
                Kode: {unit.kode}
              </span>
            </div>

            <h3 className="text-lg font-black text-gray-800">{unit.nama}</h3>
            <p className="text-xs text-gray-400 font-medium mt-0.5">Induk: UP3 Ponorogo</p>

            <div className="mt-5 pt-4 border-t border-dashed border-gray-100 flex items-center justify-between">
              <div className="flex items-center gap-1.5 text-xs text-gray-500 font-bold">
                <Users className="w-4 h-4 text-[#2BA8A2]" />
                <span>Total Personil</span>
              </div>
              <span className="text-base font-black text-[#1E8C86]">{unit.count} Orang</span>
            </div>

            <div className="mt-2 text-[11px] text-gray-400 flex items-center justify-between">
              <span>PKWTT: {unit.pkwttCount}</span>
              <span>PKWT: {unit.pkwtCount}</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
