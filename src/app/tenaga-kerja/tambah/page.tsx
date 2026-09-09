"use client";

import React, { useState } from "react";
import Link from "next/link";
import { ArrowLeft, Save, PlusCircle, Building, User, FileText, Shield } from "lucide-react";

export default function TambahTenagaKerjaPage() {
  const [formData, setFormData] = useState({
    nama: "",
    nik: "",
    tempatLahir: "",
    tanggalLahir: "",
    pendidikanTerakhir: "SMK",
    jurusan: "",
    noTelepon: "",
    email: "",
    jenisKelamin: "LAKI",
    alamatDomisili: "",
    kotaKabupaten: "PONOROGO",
    provinsi: "JAWA TIMUR",
    jabatanTerakhir: "",
    fungsiPekerjaan: "",
    unitLayanan: "ULP PONOROGO",
    namaPerusahaan: "PT ANUGERAH PUTRA PERMANA",
    nomorPerjanjian: "1211,Pj/DAN,00,07/F04000000/2024",
    nomorBpjsKesehatan: "",
    nomorBpjsKetenagakerjaan: "",
    nomorDplk: "",
    bankDplk: "BNI",
    statusTenagaKerja: "PKWTT",
    skemaTenagaKerja: "PEMBORONGAN",
  });

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
  ) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    alert("Data berhasil disimpan secara lokal (Simulasi Form Tambah Data)");
  };

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      
      {/* Header */}
      <div className="flex items-center gap-3">
        <Link
          href="/"
          className="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[#1E8C86] hover:border-[#2BA8A2] transition-colors"
        >
          <ArrowLeft className="w-5 h-5" />
        </Link>
        <div>
          <h1 className="text-2xl font-black text-[#1E8C86]">Tambah Tenaga Kerja Baru</h1>
          <p className="text-xs text-gray-500 font-medium">
            Masukkan data tenaga kerja sesuai dengan kolom template acuan database
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        
        {/* Section 1: Data Pribadi */}
        <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom space-y-4">
          <div className="section-title">
            <User className="w-5 h-5 text-[#2BA8A2]" />
            <span>1. Data Pribadi & Kontak</span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
              <label className="block font-bold text-gray-700 mb-1">Nama Lengkap *</label>
              <input
                type="text"
                name="nama"
                required
                value={formData.nama}
                onChange={handleChange}
                placeholder="Contoh: SUGENG"
                className="input-cream w-full px-3 py-2"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">NIK (16 Digit) *</label>
              <input
                type="text"
                name="nik"
                maxLength={16}
                required
                value={formData.nik}
                onChange={handleChange}
                placeholder="350201..."
                className="input-cream w-full px-3 py-2 font-mono"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Tempat Lahir</label>
              <input
                type="text"
                name="tempatLahir"
                value={formData.tempatLahir}
                onChange={handleChange}
                placeholder="PONOROGO"
                className="input-cream w-full px-3 py-2"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Tanggal Lahir</label>
              <input
                type="date"
                name="tanggalLahir"
                value={formData.tanggalLahir}
                onChange={handleChange}
                className="input-cream w-full px-3 py-2"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Jenis Kelamin</label>
              <select
                name="jenisKelamin"
                value={formData.jenisKelamin}
                onChange={handleChange}
                className="input-cream w-full px-3 py-2 font-semibold cursor-pointer"
              >
                <option value="LAKI">Laki-Laki</option>
                <option value="PEREMPUAN">Perempuan</option>
              </select>
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Pendidikan Terakhir</label>
              <select
                name="pendidikanTerakhir"
                value={formData.pendidikanTerakhir}
                onChange={handleChange}
                className="input-cream w-full px-3 py-2 font-semibold cursor-pointer"
              >
                <option value="SMA">SMA</option>
                <option value="SMK">SMK</option>
                <option value="D3">D3</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
              </select>
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">No. Telepon / WhatsApp</label>
              <input
                type="text"
                name="noTelepon"
                value={formData.noTelepon}
                onChange={handleChange}
                placeholder="0813-XXXX-XXXX"
                className="input-cream w-full px-3 py-2"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Email</label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                placeholder="nama@gmail.com"
                className="input-cream w-full px-3 py-2"
              />
            </div>
          </div>
        </div>

        {/* Section 2: Penempatan & Perusahaan */}
        <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom space-y-4">
          <div className="section-title">
            <Building className="w-5 h-5 text-[#2BA8A2]" />
            <span>2. Penempatan & Status Kontrak</span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
              <label className="block font-bold text-gray-700 mb-1">Unit Layanan *</label>
              <select
                name="unitLayanan"
                value={formData.unitLayanan}
                onChange={handleChange}
                className="input-cream w-full px-3 py-2 font-semibold cursor-pointer"
              >
                <option value="ULP TRENGGALEK">ULP Trenggalek</option>
                <option value="ULP PACITAN">ULP Pacitan</option>
                <option value="ULP BALONG">ULP Balong</option>
                <option value="ULP PONOROGO">ULP Ponorogo</option>
                <option value="UP3 PONOROGO">UP3 Ponorogo</option>
              </select>
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Jabatan Terakhir *</label>
              <input
                type="text"
                name="jabatanTerakhir"
                required
                value={formData.jabatanTerakhir}
                onChange={handleChange}
                placeholder="Contoh: PETUGAS YANTEK"
                className="input-cream w-full px-3 py-2"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Status Tenaga Kerja</label>
              <select
                name="statusTenagaKerja"
                value={formData.statusTenagaKerja}
                onChange={handleChange}
                className="input-cream w-full px-3 py-2 font-semibold cursor-pointer"
              >
                <option value="PKWTT">PKWTT (Tetap)</option>
                <option value="PKWT">PKWT (Kontrak Tertentu)</option>
              </select>
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Skema Tenaga Kerja</label>
              <select
                name="skemaTenagaKerja"
                value={formData.skemaTenagaKerja}
                onChange={handleChange}
                className="input-cream w-full px-3 py-2 font-semibold cursor-pointer"
              >
                <option value="PEMBORONGAN">Pemborongan</option>
                <option value="VOLUME BASED">Volume Based</option>
              </select>
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Nama Perusahaan Mitra</label>
              <input
                type="text"
                name="namaPerusahaan"
                value={formData.namaPerusahaan}
                onChange={handleChange}
                className="input-cream w-full px-3 py-2"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Nomor Perjanjian</label>
              <input
                type="text"
                name="nomorPerjanjian"
                value={formData.nomorPerjanjian}
                onChange={handleChange}
                className="input-cream w-full px-3 py-2 font-mono"
              />
            </div>
          </div>
        </div>

        {/* Section 3: Jaminan Sosial */}
        <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom space-y-4">
          <div className="section-title">
            <Shield className="w-5 h-5 text-[#2BA8A2]" />
            <span>3. BPJS & Jaminan Hari Tua</span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
              <label className="block font-bold text-gray-700 mb-1">No. BPJS Kesehatan</label>
              <input
                type="text"
                name="nomorBpjsKesehatan"
                value={formData.nomorBpjsKesehatan}
                onChange={handleChange}
                placeholder="0001..."
                className="input-cream w-full px-3 py-2 font-mono"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">No. BPJS Ketenagakerjaan</label>
              <input
                type="text"
                name="nomorBpjsKetenagakerjaan"
                value={formData.nomorBpjsKetenagakerjaan}
                onChange={handleChange}
                placeholder="1904..."
                className="input-cream w-full px-3 py-2 font-mono"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Nomor DPLK</label>
              <input
                type="text"
                name="nomorDplk"
                value={formData.nomorDplk}
                onChange={handleChange}
                placeholder="8103..."
                className="input-cream w-full px-3 py-2 font-mono"
              />
            </div>

            <div>
              <label className="block font-bold text-gray-700 mb-1">Bank DPLK</label>
              <input
                type="text"
                name="bankDplk"
                value={formData.bankDplk}
                onChange={handleChange}
                className="input-cream w-full px-3 py-2"
              />
            </div>
          </div>
        </div>

        {/* Submit Actions */}
        <div className="flex items-center justify-end gap-3 pt-2">
          <Link
            href="/"
            className="px-5 py-2.5 rounded-full text-xs font-bold text-gray-600 hover:bg-gray-100 transition-colors"
          >
            Batal
          </Link>

          <button
            type="submit"
            className="btn-gold-primary px-6 py-2.5 flex items-center gap-2 text-xs uppercase tracking-wider cursor-pointer"
          >
            <Save className="w-4 h-4 text-[#2C3E50]" />
            Simpan Data
          </button>
        </div>

      </form>

    </div>
  );
}
