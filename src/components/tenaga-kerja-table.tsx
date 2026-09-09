"use client";

import React, { useState, useMemo } from "react";
import { TenagaKerja } from "@/types/tenaga-kerja";
import { INITIAL_TENAGA_KERJA, PLAYER_AVATAR_COLORS } from "@/lib/sample-data";
import { Search, Filter, Eye, Award, Phone, Building } from "lucide-react";

export default function TenagaKerjaTable() {
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedUnit, setSelectedUnit] = useState("ALL");
  const [selectedStatus, setSelectedStatus] = useState("ALL");

  const filteredData = useMemo(() => {
    return INITIAL_TENAGA_KERJA.filter((item) => {
      const matchSearch =
        item.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
        item.nik.includes(searchQuery) ||
        item.jabatanTerakhir.toLowerCase().includes(searchQuery.toLowerCase());

      const matchUnit =
        selectedUnit === "ALL" ||
        item.unitLayanan.toUpperCase().includes(selectedUnit.toUpperCase());

      const matchStatus =
        selectedStatus === "ALL" || item.statusTenagaKerja === selectedStatus;

      return matchSearch && matchUnit && matchStatus;
    });
  }, [searchQuery, selectedUnit, selectedStatus]);

  return (
    <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom">
      
      {/* Header and Filter Controls */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b-2 border-dashed border-[#2BA8A2]/20">
        <div>
          <h3 className="text-lg font-black text-[#1E8C86] flex items-center gap-2">
            <span>Data Tenaga Kerja Terdaftar</span>
            <span className="text-xs px-2.5 py-0.5 rounded-full bg-[#E8F6F5] text-[#1E8C86] font-extrabold">
              {filteredData.length} Ditampilkan
            </span>
          </h3>
          <p className="text-xs text-gray-400 font-medium mt-0.5">
            Daftar tenaga kerja aktif di unit ULP & UP3 Ponorogo
          </p>
        </div>

        {/* Filter Bar */}
        <div className="flex flex-wrap items-center gap-2.5">
          {/* Search Input using cream surface & sky glow */}
          <div className="relative min-w-[240px]">
            <Search className="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              placeholder="Cari Nama / NIK / Jabatan..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="input-cream pl-9 pr-4 py-2 text-xs font-semibold w-full focus:outline-none"
            />
          </div>

          {/* Unit Filter */}
          <div className="relative">
            <select
              value={selectedUnit}
              onChange={(e) => setSelectedUnit(e.target.value)}
              className="input-cream pl-3 pr-8 py-2 text-xs font-semibold appearance-none cursor-pointer"
            >
              <option value="ALL">Semua Unit</option>
              <option value="TRENGGALEK">ULP Trenggalek</option>
              <option value="PACITAN">ULP Pacitan</option>
              <option value="BALONG">ULP Balong</option>
              <option value="PONOROGO">ULP Ponorogo</option>
              <option value="UP3">UP3 Ponorogo</option>
            </select>
            <Filter className="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
          </div>

          {/* Status Filter */}
          <div className="relative">
            <select
              value={selectedStatus}
              onChange={(e) => setSelectedStatus(e.target.value)}
              className="input-cream pl-3 pr-8 py-2 text-xs font-semibold appearance-none cursor-pointer"
            >
              <option value="ALL">Semua Status</option>
              <option value="PKWTT">PKWTT</option>
              <option value="PKWT">PKWT</option>
            </select>
            <Filter className="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
          </div>
        </div>
      </div>

      {/* Table Content */}
      <div className="overflow-x-auto mt-4">
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b border-gray-100 text-[11px] font-black uppercase text-gray-400 tracking-wider">
              <th className="py-3 px-4">Tenaga Kerja</th>
              <th className="py-3 px-4">NIK & Usia</th>
              <th className="py-3 px-4">Unit Layanan</th>
              <th className="py-3 px-4">Jabatan</th>
              <th className="py-3 px-4">Status & Skema</th>
              <th className="py-3 px-4">Sertifikasi</th>
              <th className="py-3 px-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100 text-xs">
            {filteredData.map((item, index) => {
              const avatarColor = PLAYER_AVATAR_COLORS[index % PLAYER_AVATAR_COLORS.length];
              const initials = item.nama
                .split(" ")
                .slice(0, 2)
                .map((n) => n[0])
                .join("");

              return (
                <tr key={item.id} className="hover:bg-[#EFF8F7]/60 transition-colors group">
                  
                  {/* Nama & Avatar with colored pool */}
                  <td className="py-3.5 px-4">
                    <div className="flex items-center gap-3">
                      <div
                        className="w-10 h-10 rounded-full flex items-center justify-center text-white font-extrabold text-xs shadow-xs"
                        style={{ backgroundColor: avatarColor }}
                      >
                        {initials}
                      </div>
                      <div>
                        <div className="font-extrabold text-gray-800 text-sm">
                          {item.nama}
                        </div>
                        <div className="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                          <Phone className="w-3 h-3 text-gray-400" />
                          <span>{item.noTelepon}</span>
                        </div>
                      </div>
                    </div>
                  </td>

                  {/* NIK & Usia */}
                  <td className="py-3.5 px-4">
                    <div className="font-mono font-bold text-gray-700">{item.nik}</div>
                    <div className="text-[11px] text-gray-400">{item.usia || "-"}</div>
                  </td>

                  {/* Unit Layanan */}
                  <td className="py-3.5 px-4">
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#E8F6F5] text-[#1E8C86]">
                      <Building className="w-3 h-3 text-[#2BA8A2]" />
                      {item.unitLayanan}
                    </span>
                  </td>

                  {/* Jabatan */}
                  <td className="py-3.5 px-4">
                    <div className="font-bold text-gray-800">{item.jabatanTerakhir}</div>
                    <div className="text-[11px] text-gray-400">{item.namaPerusahaan}</div>
                  </td>

                  {/* Status & Skema */}
                  <td className="py-3.5 px-4">
                    <div className="flex flex-col gap-1 items-start">
                      <span
                        className={`px-2 py-0.5 rounded-full text-[10px] font-black ${
                          item.statusTenagaKerja === "PKWTT"
                            ? "bg-[#27AE60]/15 text-[#27AE60]"
                            : "bg-[#EF6C4A]/15 text-[#EF6C4A]"
                        }`}
                      >
                        {item.statusTenagaKerja}
                      </span>
                      <span className="text-[10px] text-gray-400 uppercase font-semibold">
                        {item.skemaTenagaKerja}
                      </span>
                    </div>
                  </td>

                  {/* Sertifikasi */}
                  <td className="py-3.5 px-4">
                    {item.sertifikasiList && item.sertifikasiList.length > 0 ? (
                      <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-[#FFF8E7] text-[#C9A227] border border-[#FFD23F] shadow-xs">
                        <Award className="w-3.5 h-3.5" />
                        {item.sertifikasiList.length} Sertifikat
                      </span>
                    ) : (
                      <span className="text-[11px] text-gray-400 italic">Belum ada</span>
                    )}
                  </td>

                  {/* Aksi */}
                  <td className="py-3.5 px-4 text-center">
                    <button
                      className="btn-teal-outline px-3 py-1.5 text-[11px] inline-flex items-center gap-1 cursor-pointer"
                      title="Lihat Detail Profil"
                    >
                      <Eye className="w-3.5 h-3.5" />
                      Detail
                    </button>
                  </td>

                </tr>
              );
            })}
          </tbody>
        </table>

        {filteredData.length === 0 && (
          <div className="py-12 text-center text-gray-400">
            <p className="text-sm font-semibold">Tidak ada data yang sesuai filter pencarian.</p>
          </div>
        )}
      </div>

    </div>
  );
}
