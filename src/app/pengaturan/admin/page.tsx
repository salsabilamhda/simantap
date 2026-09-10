"use client";

import React from "react";
import Link from "next/link";
import { ArrowLeft, UserPlus, Mail } from "lucide-react";

export default function PengaturanAdminPage() {
  const admins = [
    {
      nama: "Super Admin",
      email: "admin@simantap.id",
      role: "Super Admin",
      status: "Aktif",
      terdaftar: "9 September 2026",
    },
    {
      nama: "Admin HR ULP Ponorogo",
      email: "admin.ponorogo@simantap.id",
      role: "Admin Operasional",
      status: "Aktif",
      terdaftar: "9 September 2026",
    },
  ];

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <Link
            href="/"
            className="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[#1E8C86] hover:border-[#2BA8A2] transition-colors"
          >
            <ArrowLeft className="w-5 h-5" />
          </Link>
          <div>
            <h1 className="text-2xl font-black text-[#1E8C86]">Kelola Akun Admin</h1>
            <p className="text-xs text-gray-500 font-medium">
              Manajemen pengguna berwenang untuk akses Firebase Authentication
            </p>
          </div>
        </div>

        <button
          onClick={() => alert("Fitur tambah akun admin baru (Firebase Auth)")}
          className="btn-gold-primary px-4 py-2.5 flex items-center gap-2 text-xs uppercase tracking-wider cursor-pointer"
        >
          <UserPlus className="w-4 h-4 text-[#2C3E50]" />
          <span>Tambah Admin</span>
        </button>
      </div>

      <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom">
        <div className="divide-y divide-gray-100">
          {admins.map((admin) => (
            <div
              key={admin.email}
              className="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
            >
              <div className="flex items-center gap-3">
                <div className="w-11 h-11 rounded-2xl bg-[#E8F6F5] text-[#1E8C86] flex items-center justify-center font-black text-sm shadow-xs">
                  {admin.nama.slice(0, 2).toUpperCase()}
                </div>
                <div>
                  <div className="font-extrabold text-gray-800 text-sm">{admin.nama}</div>
                  <div className="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                    <Mail className="w-3.5 h-3.5" />
                    <span>{admin.email}</span>
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-3">
                <span className="px-3 py-1 rounded-full text-xs font-black bg-[#FFF8E7] text-[#C9A227] border border-[#FFD23F]">
                  {admin.role}
                </span>
                <span className="px-3 py-1 rounded-full text-xs font-bold bg-[#27AE60]/10 text-[#27AE60]">
                  {admin.status}
                </span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
