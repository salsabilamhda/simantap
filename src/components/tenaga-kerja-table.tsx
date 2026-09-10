"use client";

import React, { useMemo, useState, useEffect } from "react";
import { useSearchParams } from "next/navigation";
import { INITIAL_TENAGA_KERJA } from "@/lib/sample-data";
import { TenagaKerja, StatusTenagaKerja, SkemaTenagaKerja, JenisKelamin } from "@/types/tenaga-kerja";
import {
  Search,
  Filter,
  PlusCircle,
  Eye,
  Pencil,
  Trash2,
  X,
  Download,
  Award,
  Building,
  Phone,
  User,
  Shield,
  Briefcase,
  ChevronLeft,
  ChevronRight,
  CheckCircle2,
  Star,
  FileSpreadsheet,
} from "lucide-react";

interface TenagaKerjaTableProps {
  initialOpenAdd?: boolean;
}

export default function TenagaKerjaTable({ initialOpenAdd = false }: TenagaKerjaTableProps) {
  const searchParams = useSearchParams();
  const [tableData, setTableData] = useState<TenagaKerja[]>(INITIAL_TENAGA_KERJA);
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedUnit, setSelectedUnit] = useState("ALL");
  const [selectedStatus, setSelectedStatus] = useState("ALL");
  
  // Pagination
  const [currentPage, setCurrentPage] = useState(1);
  const pageSize = 8;

  // Modals state
  const [isAddModalOpen, setIsAddModalOpen] = useState(initialOpenAdd);
  const [selectedDetail, setSelectedDetail] = useState<TenagaKerja | null>(null);
  const [editingWorker, setEditingWorker] = useState<TenagaKerja | null>(null);
  const [toastMessage, setToastMessage] = useState<string | null>(null);

  // Auto-open add modal if query parameter ?tambah=true is present
  useEffect(() => {
    if (searchParams?.get("tambah") === "true") {
      setIsAddModalOpen(true);
    }
  }, [searchParams]);

  // Form State for Add Worker
  const defaultFormData: Omit<TenagaKerja, "id"> = {
    noUrut: "",
    nomorPerjanjian: "1211,Pj/DAN,00,07/F04000000/2024",
    namaPerusahaan: "PT ANUGERAH PUTRA PERMANA",
    nama: "",
    nik: "",
    tempatLahir: "",
    tanggalLahir: "",
    usia: "",
    pendidikanTerakhir: "SMK",
    jurusan: "",
    noTelepon: "",
    email: "",
    jenisKelamin: "LAKI",
    alamatDomisili: "",
    kotaKabupaten: "PONOROGO",
    provinsi: "JAWA TIMUR",
    jabatanTerakhir: "",
    fungsiPekerjaan: "Pelayanan Operasional",
    unit: "ULP PONOROGO",
    unitLayanan: "ULP PONOROGO",
    nomorBpjsKesehatan: "",
    nomorBpjsKetenagakerjaan: "",
    nomorDplk: "",
    bankDplk: "BNI",
    noPerjanjianKerja: "009/APP/PON/YTK-MDN/2024",
    tanggalMasukKerja: "2024-01-01",
    statusTenagaKerja: "PKWTT",
    skemaTenagaKerja: "PEMBORONGAN",
    sertifikasiList: [],
  };

  const [formData, setFormData] = useState<Omit<TenagaKerja, "id">>(defaultFormData);

  // Filtered data
  const filteredData = useMemo(() => {
    return tableData.filter((item) => {
      const q = searchQuery.toLowerCase();
      const matchSearch =
        !searchQuery ||
        item.nama.toLowerCase().includes(q) ||
        item.nik.includes(q) ||
        item.jabatanTerakhir.toLowerCase().includes(q) ||
        item.unitLayanan.toLowerCase().includes(q) ||
        item.namaPerusahaan.toLowerCase().includes(q);

      const matchUnit =
        selectedUnit === "ALL" ||
        item.unitLayanan.toUpperCase().includes(selectedUnit.toUpperCase());

      const matchStatus =
        selectedStatus === "ALL" || item.statusTenagaKerja === selectedStatus;

      return matchSearch && matchUnit && matchStatus;
    });
  }, [searchQuery, selectedUnit, selectedStatus, tableData]);

  // Paginated items
  const totalPages = Math.ceil(filteredData.length / pageSize) || 1;
  const paginatedData = useMemo(() => {
    const startIndex = (currentPage - 1) * pageSize;
    return filteredData.slice(startIndex, startIndex + pageSize);
  }, [filteredData, currentPage, pageSize]);

  // Reset to page 1 on filter change
  useEffect(() => {
    setCurrentPage(1);
  }, [searchQuery, selectedUnit, selectedStatus]);

  const showToast = (msg: string) => {
    setToastMessage(msg);
    setTimeout(() => {
      setToastMessage(null);
    }, 3500);
  };

  // Add Worker Handler
  const handleAddSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.nama.trim() || !formData.nik.trim()) {
      alert("Mohon lengkapi Nama dan NIK tenaga kerja!");
      return;
    }

    const newWorker: TenagaKerja = {
      ...formData,
      id: `tk-${Date.now()}`,
      noUrut: String(tableData.length + 1).padStart(2, "0"),
      createdAt: new Date().toISOString(),
    };

    setTableData([newWorker, ...tableData]);
    setFormData(defaultFormData);
    setIsAddModalOpen(false);
    showToast(`Data tenaga kerja "${newWorker.nama}" berhasil ditambahkan!`);
  };

  // Edit Worker Handler
  const handleEditSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!editingWorker) return;

    setTableData((prev) =>
      prev.map((item) =>
        item.id === editingWorker.id
          ? { ...item, ...editingWorker, updatedAt: new Date().toISOString() }
          : item
      )
    );

    showToast(`Perubahan data "${editingWorker.nama}" berhasil disimpan!`);
    setEditingWorker(null);
  };

  // Delete Worker Handler
  const handleDeleteWorker = (item: TenagaKerja) => {
    if (confirm(`Apakah Anda yakin ingin menghapus data tenaga kerja "${item.nama}"?`)) {
      setTableData((prev) => prev.filter((tk) => tk.id !== item.id));
      showToast(`Data "${item.nama}" telah dihapus.`);
    }
  };

  // Export CSV Handler
  const handleExportCSV = () => {
    const headers = [
      "No Urut",
      "Nama Tenaga Kerja",
      "NIK",
      "Usia",
      "Pendidikan Terakhir",
      "Jurusan",
      "No Telepon",
      "Email",
      "Jenis Kelamin",
      "Unit Layanan",
      "Jabatan Terakhir",
      "Nama Perusahaan",
      "Status Tenaga Kerja",
      "Skema Tenaga Kerja",
      "No BPJS Kesehatan",
      "No BPJS Ketenagakerjaan",
    ];

    const rows = filteredData.map((item, idx) => [
      idx + 1,
      `"${item.nama}"`,
      `"${item.nik}"`,
      `"${item.usia || "-"}"`,
      `"${item.pendidikanTerakhir}"`,
      `"${item.jurusan || "-"}"`,
      `"${item.noTelepon}"`,
      `"${item.email || "-"}"`,
      `"${item.jenisKelamin}"`,
      `"${item.unitLayanan}"`,
      `"${item.jabatanTerakhir}"`,
      `"${item.namaPerusahaan}"`,
      `"${item.statusTenagaKerja}"`,
      `"${item.skemaTenagaKerja}"`,
      `"${item.nomorBpjsKesehatan || "-"}"`,
      `"${item.nomorBpjsKetenagakerjaan || "-"}"`,
    ]);

    const csvContent =
      "data:text/csv;charset=utf-8," +
      [headers.join(","), ...rows.map((e) => e.join(","))].join("\n");

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute(
      "download",
      `data_tenaga_kerja_${new Date().toISOString().slice(0, 10)}.csv`
    );
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast("File CSV data tenaga kerja berhasil diunduh.");
  };

  return (
    <div className="space-y-6">
      {/* Toast Notification */}
      {toastMessage && (
        <div className="fixed top-6 right-6 z-50 flex items-center gap-2 bg-[#1E8C86] text-white px-4 py-3 rounded-2xl shadow-xl border border-white/20 animate-fade-in text-xs font-bold">
          <CheckCircle2 className="w-4 h-4 text-[#FFD23F]" />
          <span>{toastMessage}</span>
        </div>
      )}

      {/* Top Banner Card (Matching user screenshot header) */}
      <div className="bg-white rounded-3xl p-5 sm:p-6 border border-[#2BA8A2]/15 shadow-card-custom flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
          <h2 className="text-xl sm:text-2xl font-black text-[#2C3E50] tracking-tight">
            Data Tenaga Kerja & Mitra Outsourcing
          </h2>
          <p className="text-xs text-gray-400 font-medium mt-1">
            Sistem Informasi Pengelolaan Tenaga Kerja dan Sertifikasi Wilayah UP3 Ponorogo
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-2.5">
          {/* Blue Stat Pill (like "Total: 0 Ulasan" in screenshot) */}
          <div className="px-3.5 py-1.5 rounded-full bg-[#E0F2FE] text-[#0284C7] text-xs font-black border border-[#BAE6FD] shadow-2xs">
            Total: {tableData.length} Tenaga Kerja
          </div>

          {/* Yellow Rating Pill (like "⭐ 0.0 / 5" in screenshot) */}
          <div className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#FFF8E7] text-[#C9A227] text-xs font-black border border-[#FFD23F] shadow-2xs">
            <Star className="w-3.5 h-3.5 fill-[#FFD23F] text-[#C9A227]" />
            <span>5 Unit Layanan</span>
          </div>

          {/* Export CSV Button (like "Export CSV" in screenshot top bar) */}
          <button
            onClick={handleExportCSV}
            className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#27AE60] hover:bg-[#219653] text-white text-xs font-bold transition-all shadow-xs cursor-pointer"
            title="Unduh data dalam format CSV"
          >
            <Download className="w-3.5 h-3.5" />
            <span>Export CSV</span>
          </button>
        </div>
      </div>

      {/* Main Table Card (Matching Screenshot Main Card) */}
      <div className="bg-white rounded-3xl p-5 sm:p-6 border border-[#2BA8A2]/15 shadow-card-custom space-y-5">
        
        {/* Title Inside Card */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <h3 className="text-lg font-black text-[#2C3E50]">
            Data Tenaga Kerja & Personil Lapangan
          </h3>
          <span className="text-xs text-gray-400 font-semibold">
            Menampilkan {filteredData.length} dari total {tableData.length} data
          </span>
        </div>

        {/* Filter & Action Bar (Matching Screenshot Search + Filters + Unified Tambah Data) */}
        <div className="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
          
          {/* Search Box */}
          <div className="relative flex-1 min-w-[260px]">
            <Search className="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              placeholder="Cari berdasarkan nama tenaga kerja, NIK, atau divisi..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full bg-[#F8FAFC] border border-gray-200 rounded-2xl pl-10 pr-4 py-2.5 text-xs font-medium text-gray-700 placeholder:text-gray-400 focus:outline-none focus:border-[#2BA8A2] focus:bg-white transition-all"
            />
            {searchQuery && (
              <button
                onClick={() => setSearchQuery("")}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5"
              >
                <X className="w-3.5 h-3.5" />
              </button>
            )}
          </div>

          {/* Filters & Unified Add Data Button */}
          <div className="flex flex-wrap items-center gap-2.5">
            
            {/* Filter Unit Layanan (like "Semua Kategori Mitra" dropdown) */}
            <div className="relative">
              <select
                value={selectedUnit}
                onChange={(e) => setSelectedUnit(e.target.value)}
                className="bg-[#F8FAFC] border border-gray-200 rounded-2xl pl-3 pr-8 py-2.5 text-xs font-semibold text-gray-700 appearance-none cursor-pointer focus:outline-none focus:border-[#2BA8A2] transition-all"
              >
                <option value="ALL">Semua Unit Layanan</option>
                <option value="TRENGGALEK">ULP Trenggalek</option>
                <option value="PACITAN">ULP Pacitan</option>
                <option value="BALONG">ULP Balong</option>
                <option value="PONOROGO">ULP Ponorogo</option>
                <option value="UP3">UP3 Ponorogo</option>
              </select>
              <Filter className="w-3.5 h-3.5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
            </div>

            {/* Filter Status PKWTT / PKWT */}
            <div className="relative">
              <select
                value={selectedStatus}
                onChange={(e) => setSelectedStatus(e.target.value)}
                className="bg-[#F8FAFC] border border-gray-200 rounded-2xl pl-3 pr-8 py-2.5 text-xs font-semibold text-gray-700 appearance-none cursor-pointer focus:outline-none focus:border-[#2BA8A2] transition-all"
              >
                <option value="ALL">Semua Status</option>
                <option value="PKWTT">PKWTT (Tetap)</option>
                <option value="PKWT">PKWT (Kontrak)</option>
              </select>
              <Filter className="w-3.5 h-3.5 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
            </div>

            {/* UNIFIED TAMBAH DATA BUTTON (Integrated into this menu!) */}
            <button
              onClick={() => setIsAddModalOpen(true)}
              className="inline-flex items-center gap-2 bg-[#1E8C86] hover:bg-[#17756f] text-white px-4 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-teal-glow cursor-pointer"
            >
              <PlusCircle className="w-4 h-4 text-[#FFD23F]" />
              <span>Tambah Tenaga Kerja</span>
            </button>

          </div>
        </div>

        {/* Clean Data Table (Faithful to Screenshot) */}
        <div className="overflow-x-auto rounded-2xl border border-gray-100">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="bg-gray-50/70 border-b border-gray-200 text-[11px] font-black uppercase text-gray-400 tracking-wider">
                <th className="py-3.5 px-4 text-center w-12">NO</th>
                <th className="py-3.5 px-4">NAMA TENAGA KERJA</th>
                <th className="py-3.5 px-4">UNIT LAYANAN</th>
                <th className="py-3.5 px-4">JABATAN & PERUSAHAAN</th>
                <th className="py-3.5 px-4">STATUS</th>
                <th className="py-3.5 px-4">SKEMA</th>
                <th className="py-3.5 px-4">SERTIFIKASI</th>
                <th className="py-3.5 px-4 text-center">AKSI</th>
              </tr>
            </thead>

            <tbody className="divide-y divide-gray-100 text-xs">
              {paginatedData.map((item, index) => {
                const rowNo = (currentPage - 1) * pageSize + index + 1;
                const hasCertificates = item.sertifikasiList && item.sertifikasiList.length > 0;

                return (
                  <tr
                    key={item.id}
                    className="hover:bg-[#EFF8F7]/50 transition-colors group"
                  >
                    {/* NO */}
                    <td className="py-4 px-4 text-center font-bold text-gray-400 text-xs">
                      {rowNo}
                    </td>

                    {/* NAMA TENAGA KERJA */}
                    <td className="py-4 px-4">
                      <div className="font-extrabold text-gray-800 text-xs sm:text-sm">
                        {item.nama}
                      </div>
                      <div className="text-[11px] text-gray-400 flex items-center gap-1.5 mt-0.5">
                        <span className="font-mono text-gray-500 font-semibold">{item.nik}</span>
                        {item.noTelepon && (
                          <>
                            <span>•</span>
                            <span className="flex items-center gap-0.5 text-gray-400">
                              <Phone className="w-2.5 h-2.5" />
                              {item.noTelepon}
                            </span>
                          </>
                        )}
                      </div>
                    </td>

                    {/* UNIT LAYANAN (Pill Green Badge like "Mitra Resmi" in screenshot) */}
                    <td className="py-4 px-4 whitespace-nowrap">
                      <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-[#E8F8F0] text-[#1E8C86] border border-[#2BA8A2]/20 shadow-2xs">
                        <span className="w-1.5 h-1.5 rounded-full bg-[#1E8C86]" />
                        {item.unitLayanan}
                      </span>
                    </td>

                    {/* JABATAN & PERUSAHAAN */}
                    <td className="py-4 px-4">
                      <div className="font-bold text-gray-800 text-xs">
                        {item.jabatanTerakhir}
                      </div>
                      <div className="text-[11px] text-gray-400 mt-0.5 truncate max-w-[200px]">
                        {item.namaPerusahaan}
                      </div>
                    </td>

                    {/* STATUS (PKWTT / PKWT) */}
                    <td className="py-4 px-4 whitespace-nowrap">
                      <span
                        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black ${
                          item.statusTenagaKerja === "PKWTT"
                            ? "bg-[#27AE60]/15 text-[#27AE60] border border-[#27AE60]/30"
                            : "bg-[#EF6C4A]/15 text-[#EF6C4A] border border-[#EF6C4A]/30"
                        }`}
                      >
                        {item.statusTenagaKerja}
                      </span>
                    </td>

                    {/* SKEMA */}
                    <td className="py-4 px-4 whitespace-nowrap">
                      <span className="text-[11px] text-gray-600 font-semibold uppercase">
                        {item.skemaTenagaKerja || "-"}
                      </span>
                    </td>

                    {/* SERTIFIKASI */}
                    <td className="py-4 px-4 whitespace-nowrap">
                      {hasCertificates ? (
                        <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-[#FFF8E7] text-[#C9A227] border border-[#FFD23F] shadow-2xs">
                          <Award className="w-3 h-3 text-[#C9A227]" />
                          <span>{item.sertifikasiList?.length} Sertifikat</span>
                        </span>
                      ) : (
                        <span className="text-[11px] text-gray-400 italic">
                          Belum ada
                        </span>
                      )}
                    </td>

                    {/* AKSI (Pill Button like "Lihat Ulasan" in screenshot) */}
                    <td className="py-4 px-4 text-center whitespace-nowrap">
                      <div className="inline-flex items-center justify-center gap-1.5">
                        
                        {/* Detail Pill Button (Matching "Lihat Ulasan" in screenshot) */}
                        <button
                          type="button"
                          onClick={() => setSelectedDetail(item)}
                          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold text-[#0284C7] bg-[#E0F2FE]/70 hover:bg-[#BAE6FD] border border-[#38BDF8]/40 transition-all cursor-pointer shadow-2xs"
                          title="Lihat Detail Profil"
                        >
                          <Eye className="w-3.5 h-3.5 text-[#0284C7]" />
                          <span>Lihat Detail</span>
                        </button>

                        {/* Edit Button */}
                        <button
                          type="button"
                          onClick={() => setEditingWorker(item)}
                          className="p-1.5 rounded-full text-gray-400 hover:text-[#1E8C86] hover:bg-[#EFF8F7] transition-colors cursor-pointer"
                          title="Edit Data"
                        >
                          <Pencil className="w-3.5 h-3.5" />
                        </button>

                        {/* Delete Button */}
                        <button
                          type="button"
                          onClick={() => handleDeleteWorker(item)}
                          className="p-1.5 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                          title="Hapus Data"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>

                      </div>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>

          {/* Empty state */}
          {filteredData.length === 0 && (
            <div className="py-14 text-center text-gray-400 bg-gray-50/40">
              <p className="text-sm font-semibold">
                Tidak ada data tenaga kerja yang sesuai dengan filter pencarian.
              </p>
              <button
                onClick={() => {
                  setSearchQuery("");
                  setSelectedUnit("ALL");
                  setSelectedStatus("ALL");
                }}
                className="mt-3 text-xs text-[#1E8C86] font-bold hover:underline"
              >
                Reset Semua Filter
              </button>
            </div>
          )}
        </div>

        {/* Table Footer (Matching Screenshot Bottom Row) */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2 text-xs">
          
          {/* Left: Total Perusahaan/Tenaga Kerja (like "Total: 883 Perusahaan" in screenshot) */}
          <div className="font-semibold text-gray-500">
            Total: <strong className="text-gray-800">{filteredData.length}</strong> Tenaga Kerja
          </div>

          {/* Center: Pagination Controls */}
          {totalPages > 1 && (
            <div className="flex items-center gap-1.5">
              <button
                type="button"
                disabled={currentPage === 1}
                onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
                className="p-1.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                aria-label="Halaman Sebelumnya"
              >
                <ChevronLeft className="w-4 h-4" />
              </button>

              {Array.from({ length: totalPages }, (_, i) => i + 1).map((pageNum) => (
                <button
                  key={pageNum}
                  onClick={() => setCurrentPage(pageNum)}
                  className={`w-8 h-8 rounded-xl font-bold text-xs transition-all ${
                    currentPage === pageNum
                      ? "bg-[#1E8C86] text-white shadow-teal-glow"
                      : "border border-gray-200 text-gray-600 hover:bg-gray-50"
                  }`}
                >
                  {pageNum}
                </button>
              ))}

              <button
                type="button"
                disabled={currentPage === totalPages}
                onClick={() => setCurrentPage((p) => Math.min(totalPages, p + 1))}
                className="p-1.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                aria-label="Halaman Selanjutnya"
              >
                <ChevronRight className="w-4 h-4" />
              </button>
            </div>
          )}

          {/* Right: Copyright/Institution Info (like "JTI Politeknik Negeri Malang © 2026") */}
          <div className="text-gray-400 font-medium text-[11px]">
            SIMANTAP PLN UP3 Ponorogo &copy; {new Date().getFullYear()}
          </div>

        </div>

      </div>

      {/* ========================================================================= */}
      {/* INTEGRATED MODAL 1: TAMBAH TENAGA KERJA (UNIFIED DI MENU INI)              */}
      {/* ========================================================================= */}
      {isAddModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
          <div className="w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-[#2BA8A2]/20 my-auto animate-in fade-in zoom-in-95">
            
            {/* Modal Header */}
            <div className="flex items-start justify-between gap-4 pb-4 border-b border-gray-100">
              <div>
                <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E8F6F5] text-[#1E8C86] text-[11px] font-extrabold mb-1">
                  <PlusCircle className="w-3.5 h-3.5" />
                  <span>Form Tambah Data Personil Terpadu</span>
                </div>
                <h4 className="text-xl sm:text-2xl font-black text-[#1E8C86]">
                  Tambah Tenaga Kerja Baru
                </h4>
                <p className="text-xs text-gray-400 mt-0.5">
                  Lengkapi data personil tenaga kerja sesuai kolom acuan database PLN UP3 Ponorogo
                </p>
              </div>

              <button
                type="button"
                onClick={() => setIsAddModalOpen(false)}
                className="w-9 h-9 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 flex items-center justify-center transition-colors"
                aria-label="Tutup form"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Modal Form */}
            <form onSubmit={handleAddSubmit} className="space-y-6 mt-6">
              
              {/* Section 1: Data Pribadi & Kontak */}
              <div className="space-y-3">
                <div className="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#1E8C86] pb-1 border-b border-gray-100">
                  <User className="w-4 h-4 text-[#2BA8A2]" />
                  <span>1. Data Pribadi & Kontak</span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Nama Lengkap *</label>
                    <input
                      type="text"
                      required
                      placeholder="Contoh: SUGENG"
                      value={formData.nama}
                      onChange={(e) => setFormData({ ...formData, nama: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-bold text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">NIK (16 Digit) *</label>
                    <input
                      type="text"
                      required
                      maxLength={16}
                      placeholder="350201..."
                      value={formData.nik}
                      onChange={(e) => setFormData({ ...formData, nik: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-mono text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Tempat Lahir</label>
                    <input
                      type="text"
                      placeholder="Contoh: PONOROGO"
                      value={formData.tempatLahir}
                      onChange={(e) => setFormData({ ...formData, tempatLahir: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Tanggal Lahir</label>
                    <input
                      type="date"
                      value={formData.tanggalLahir}
                      onChange={(e) => setFormData({ ...formData, tanggalLahir: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Jenis Kelamin</label>
                    <select
                      value={formData.jenisKelamin}
                      onChange={(e) => setFormData({ ...formData, jenisKelamin: e.target.value as JenisKelamin })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-semibold text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    >
                      <option value="LAKI">Laki-Laki</option>
                      <option value="PEREMPUAN">Perempuan</option>
                    </select>
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Pendidikan Terakhir</label>
                    <select
                      value={formData.pendidikanTerakhir}
                      onChange={(e) => setFormData({ ...formData, pendidikanTerakhir: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-semibold text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    >
                      <option value="SMA">SMA</option>
                      <option value="SMK">SMK</option>
                      <option value="D3">D3</option>
                      <option value="S1">S1</option>
                      <option value="S2">S2</option>
                    </select>
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Jurusan Pendidikan</label>
                    <input
                      type="text"
                      placeholder="Contoh: TEKNIK LISTRIK"
                      value={formData.jurusan}
                      onChange={(e) => setFormData({ ...formData, jurusan: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">No. WhatsApp / Telepon</label>
                    <input
                      type="text"
                      placeholder="0813-XXXX-XXXX"
                      value={formData.noTelepon}
                      onChange={(e) => setFormData({ ...formData, noTelepon: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div className="md:col-span-2">
                    <label className="block font-bold text-gray-700 mb-1">Alamat Domisili</label>
                    <input
                      type="text"
                      placeholder="Jl. / Dusun RT/RW, Desa, Kecamatan"
                      value={formData.alamatDomisili}
                      onChange={(e) => setFormData({ ...formData, alamatDomisili: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>
                </div>
              </div>

              {/* Section 2: Penempatan Kerja */}
              <div className="space-y-3">
                <div className="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#1E8C86] pb-1 border-b border-gray-100">
                  <Building className="w-4 h-4 text-[#2BA8A2]" />
                  <span>2. Penempatan & Status Kontrak</span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Unit Layanan *</label>
                    <select
                      value={formData.unitLayanan}
                      onChange={(e) => setFormData({ ...formData, unitLayanan: e.target.value, unit: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-bold text-[#1E8C86] focus:outline-none focus:border-[#2BA8A2]"
                    >
                      <option value="ULP TRENGGALEK">ULP TRENGGALEK</option>
                      <option value="ULP PACITAN">ULP PACITAN</option>
                      <option value="ULP BALONG">ULP BALONG</option>
                      <option value="ULP PONOROGO">ULP PONOROGO</option>
                      <option value="UP3 PONOROGO">UP3 PONOROGO</option>
                    </select>
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Jabatan Terakhir *</label>
                    <input
                      type="text"
                      required
                      placeholder="Contoh: PETUGAS PELAYANAN TEKNIK (YANTEK)"
                      value={formData.jabatanTerakhir}
                      onChange={(e) => setFormData({ ...formData, jabatanTerakhir: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-bold text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Status Tenaga Kerja</label>
                    <select
                      value={formData.statusTenagaKerja}
                      onChange={(e) => setFormData({ ...formData, statusTenagaKerja: e.target.value as StatusTenagaKerja })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-semibold text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    >
                      <option value="PKWTT">PKWTT (Tetap)</option>
                      <option value="PKWT">PKWT (Kontrak Tertentu)</option>
                    </select>
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Skema Tenaga Kerja</label>
                    <select
                      value={formData.skemaTenagaKerja}
                      onChange={(e) => setFormData({ ...formData, skemaTenagaKerja: e.target.value as SkemaTenagaKerja })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-semibold text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    >
                      <option value="PEMBORONGAN">PEMBORONGAN</option>
                      <option value="VOLUME BASED">VOLUME BASED</option>
                    </select>
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Nama Perusahaan Mitra</label>
                    <input
                      type="text"
                      value={formData.namaPerusahaan}
                      onChange={(e) => setFormData({ ...formData, namaPerusahaan: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Nomor Perjanjian Kerja</label>
                    <input
                      type="text"
                      value={formData.nomorPerjanjian}
                      onChange={(e) => setFormData({ ...formData, nomorPerjanjian: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-mono text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>
                </div>
              </div>

              {/* Section 3: Jaminan Sosial */}
              <div className="space-y-3">
                <div className="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#1E8C86] pb-1 border-b border-gray-100">
                  <Shield className="w-4 h-4 text-[#2BA8A2]" />
                  <span>3. BPJS & Jaminan Sosial</span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                  <div>
                    <label className="block font-bold text-gray-700 mb-1">No. BPJS Kesehatan</label>
                    <input
                      type="text"
                      placeholder="0001..."
                      value={formData.nomorBpjsKesehatan}
                      onChange={(e) => setFormData({ ...formData, nomorBpjsKesehatan: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-mono text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">No. BPJS Ketenagakerjaan</label>
                    <input
                      type="text"
                      placeholder="1904..."
                      value={formData.nomorBpjsKetenagakerjaan}
                      onChange={(e) => setFormData({ ...formData, nomorBpjsKetenagakerjaan: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-mono text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Nomor DPLK</label>
                    <input
                      type="text"
                      placeholder="8103..."
                      value={formData.nomorDplk}
                      onChange={(e) => setFormData({ ...formData, nomorDplk: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-mono text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-gray-700 mb-1">Bank Pengelola DPLK</label>
                    <input
                      type="text"
                      value={formData.bankDplk}
                      onChange={(e) => setFormData({ ...formData, bankDplk: e.target.value })}
                      className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 font-semibold text-gray-800 focus:outline-none focus:border-[#2BA8A2]"
                    />
                  </div>
                </div>
              </div>

              {/* Form Buttons */}
              <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button
                  type="button"
                  onClick={() => setIsAddModalOpen(false)}
                  className="px-5 py-2.5 rounded-2xl border border-gray-200 text-gray-600 hover:bg-gray-100 font-bold text-xs transition-colors cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="px-6 py-2.5 rounded-2xl bg-[#1E8C86] hover:bg-[#17756f] text-white font-bold text-xs flex items-center gap-2 shadow-teal-glow transition-all cursor-pointer"
                >
                  <CheckCircle2 className="w-4 h-4 text-[#FFD23F]" />
                  <span>Simpan Data Tenaga Kerja</span>
                </button>
              </div>

            </form>

          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* INTEGRATED MODAL 2: LIHAT DETAIL PROFIL TENAGA KERJA                      */}
      {/* ========================================================================= */}
      {selectedDetail && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
          <div className="w-full max-w-3xl max-h-[85vh] overflow-y-auto rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-[#2BA8A2]/15 my-auto animate-in fade-in zoom-in-95">
            
            {/* Detail Header */}
            <div className="flex items-start justify-between gap-4 pb-4 border-b border-gray-200">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#2BA8A2] to-[#1E8C86] text-white font-black text-lg flex items-center justify-center shadow-teal-glow">
                  {selectedDetail.nama.charAt(0)}
                </div>
                <div>
                  <p className="text-[11px] uppercase tracking-[0.2em] text-gray-400 font-bold">
                    Profil Tenaga Kerja
                  </p>
                  <h4 className="text-xl sm:text-2xl font-black text-[#1E8C86] mt-0.5">
                    {selectedDetail.nama}
                  </h4>
                </div>
              </div>

              <button
                type="button"
                onClick={() => setSelectedDetail(null)}
                className="w-9 h-9 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center justify-center transition-colors"
                aria-label="Tutup detail"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Detail Content */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5 text-xs">
              
              {/* Card 1: Data Pribadi */}
              <div className="rounded-2xl bg-[#F8FBFB] p-4 border border-[#2BA8A2]/15 space-y-2">
                <p className="text-[11px] uppercase tracking-wider text-[#1E8C86] font-black mb-2 flex items-center gap-1.5">
                  <User className="w-3.5 h-3.5" />
                  <span>Data Pribadi & Kontak</span>
                </p>
                <div><span className="font-bold text-gray-500">NIK:</span> <span className="font-mono font-bold text-gray-800">{selectedDetail.nik}</span></div>
                <div><span className="font-bold text-gray-500">Tempat/Tanggal Lahir:</span> {selectedDetail.tempatLahir || "-"}, {selectedDetail.tanggalLahir || "-"}</div>
                <div><span className="font-bold text-gray-500">Usia:</span> {selectedDetail.usia || "-"}</div>
                <div><span className="font-bold text-gray-500">Jenis Kelamin:</span> {selectedDetail.jenisKelamin}</div>
                <div><span className="font-bold text-gray-500">Pendidikan:</span> {selectedDetail.pendidikanTerakhir} - {selectedDetail.jurusan || "-"}</div>
                <div><span className="font-bold text-gray-500">No. WhatsApp/Telepon:</span> {selectedDetail.noTelepon}</div>
                <div><span className="font-bold text-gray-500">Email:</span> {selectedDetail.email || "-"}</div>
              </div>

              {/* Card 2: Penempatan */}
              <div className="rounded-2xl bg-[#F8FBFB] p-4 border border-[#2BA8A2]/15 space-y-2">
                <p className="text-[11px] uppercase tracking-wider text-[#1E8C86] font-black mb-2 flex items-center gap-1.5">
                  <Building className="w-3.5 h-3.5" />
                  <span>Penempatan & Kontrak</span>
                </p>
                <div>
                  <span className="font-bold text-gray-500">Unit Layanan:</span>{" "}
                  <span className="font-extrabold text-[#1E8C86] bg-[#E8F8F0] px-2 py-0.5 rounded-md">
                    {selectedDetail.unitLayanan}
                  </span>
                </div>
                <div><span className="font-bold text-gray-500">Jabatan:</span> <span className="font-bold text-gray-800">{selectedDetail.jabatanTerakhir}</span></div>
                <div><span className="font-bold text-gray-500">Perusahaan Mitra:</span> {selectedDetail.namaPerusahaan}</div>
                <div>
                  <span className="font-bold text-gray-500">Status:</span>{" "}
                  <span className={`px-2 py-0.5 rounded-full text-[10px] font-black ${
                    selectedDetail.statusTenagaKerja === "PKWTT" ? "bg-[#27AE60]/15 text-[#27AE60]" : "bg-[#EF6C4A]/15 text-[#EF6C4A]"
                  }`}>
                    {selectedDetail.statusTenagaKerja}
                  </span>
                </div>
                <div><span className="font-bold text-gray-500">Skema:</span> {selectedDetail.skemaTenagaKerja}</div>
                <div><span className="font-bold text-gray-500">No. Perjanjian:</span> <span className="font-mono text-[10px]">{selectedDetail.nomorPerjanjian || "-"}</span></div>
              </div>

              {/* Card 3: Jaminan Sosial */}
              <div className="rounded-2xl bg-[#F8FBFB] p-4 border border-[#2BA8A2]/15 md:col-span-2">
                <p className="text-[11px] uppercase tracking-wider text-[#1E8C86] font-black mb-2 flex items-center gap-1.5">
                  <Shield className="w-3.5 h-3.5" />
                  <span>Alamat Domisili & Jaminan Sosial</span>
                </p>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-2 text-gray-700">
                  <div><span className="font-bold text-gray-500">Alamat:</span> {selectedDetail.alamatDomisili || "-"}</div>
                  <div><span className="font-bold text-gray-500">Kota / Kab:</span> {selectedDetail.kotaKabupaten || "-"}</div>
                  <div><span className="font-bold text-gray-500">BPJS Kesehatan:</span> <span className="font-mono">{selectedDetail.nomorBpjsKesehatan || "-"}</span></div>
                  <div><span className="font-bold text-gray-500">BPJS Ketenagakerjaan:</span> <span className="font-mono">{selectedDetail.nomorBpjsKetenagakerjaan || "-"}</span></div>
                  <div><span className="font-bold text-gray-500">DPLK:</span> <span className="font-mono">{selectedDetail.nomorDplk || "-"}</span> ({selectedDetail.bankDplk || "-"})</div>
                </div>
              </div>

              {/* Card 4: Sertifikasi List */}
              <div className="rounded-2xl bg-[#FFF8E7]/40 p-4 border border-[#FFD23F]/40 md:col-span-2">
                <p className="text-[11px] uppercase tracking-wider text-[#C9A227] font-black mb-2 flex items-center gap-1.5">
                  <Award className="w-3.5 h-3.5 text-[#C9A227]" />
                  <span>Daftar Sertifikasi Kompetensi</span>
                </p>
                {selectedDetail.sertifikasiList && selectedDetail.sertifikasiList.length > 0 ? (
                  <div className="space-y-2">
                    {selectedDetail.sertifikasiList.map((cert) => (
                      <div key={cert.id} className="p-2.5 rounded-xl bg-white border border-[#FFD23F]/40 flex items-center justify-between">
                        <div>
                          <div className="font-bold text-gray-800">{cert.judulSertifikasi}</div>
                          <div className="text-[10px] text-gray-400 font-mono">No. {cert.nomorSertifikat}</div>
                        </div>
                        <span className="text-[10px] px-2 py-0.5 rounded-full bg-[#E8F8F0] text-[#1E8C86] font-bold">
                          Terverifikasi
                        </span>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-gray-400 italic">Belum ada data sertifikasi terdaftar untuk personil ini.</p>
                )}
              </div>

            </div>

            {/* Modal Actions */}
            <div className="flex items-center justify-between pt-6 border-t border-gray-100 mt-5">
              <button
                type="button"
                onClick={() => {
                  const toEdit = selectedDetail;
                  setSelectedDetail(null);
                  setEditingWorker(toEdit);
                }}
                className="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-[#E8F6F5] text-[#1E8C86] hover:bg-[#2BA8A2] hover:text-white text-xs font-bold transition-all cursor-pointer"
              >
                <Pencil className="w-3.5 h-3.5" />
                <span>Edit Data Personil Ini</span>
              </button>

              <button
                type="button"
                onClick={() => setSelectedDetail(null)}
                className="px-5 py-2 rounded-2xl bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold transition-colors cursor-pointer"
              >
                Tutup
              </button>
            </div>

          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* INTEGRATED MODAL 3: EDIT DATA TENAGA KERJA                                */}
      {/* ========================================================================= */}
      {editingWorker && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto">
          <div className="w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-[#2BA8A2]/20 my-auto animate-in fade-in zoom-in-95">
            
            <div className="flex items-start justify-between gap-4 pb-4 border-b border-gray-100">
              <div>
                <h4 className="text-xl font-black text-[#1E8C86]">
                  Edit Data: {editingWorker.nama}
                </h4>
                <p className="text-xs text-gray-400 mt-0.5">
                  Perbarui informasi data tenaga kerja secara langsung
                </p>
              </div>

              <button
                type="button"
                onClick={() => setEditingWorker(null)}
                className="w-9 h-9 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 flex items-center justify-center"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleEditSubmit} className="space-y-4 mt-5 text-xs">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold text-gray-700 mb-1">Nama Lengkap</label>
                  <input
                    type="text"
                    value={editingWorker.nama}
                    onChange={(e) => setEditingWorker({ ...editingWorker, nama: e.target.value })}
                    className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3 py-2 font-bold text-gray-800"
                  />
                </div>

                <div>
                  <label className="block font-bold text-gray-700 mb-1">NIK</label>
                  <input
                    type="text"
                    value={editingWorker.nik}
                    onChange={(e) => setEditingWorker({ ...editingWorker, nik: e.target.value })}
                    className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3 py-2 font-mono"
                  />
                </div>

                <div>
                  <label className="block font-bold text-gray-700 mb-1">Unit Layanan</label>
                  <select
                    value={editingWorker.unitLayanan}
                    onChange={(e) => setEditingWorker({ ...editingWorker, unitLayanan: e.target.value, unit: e.target.value })}
                    className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3 py-2 font-bold text-[#1E8C86]"
                  >
                    <option value="ULP TRENGGALEK">ULP TRENGGALEK</option>
                    <option value="ULP PACITAN">ULP PACITAN</option>
                    <option value="ULP BALONG">ULP BALONG</option>
                    <option value="ULP PONOROGO">ULP PONOROGO</option>
                    <option value="UP3 PONOROGO">UP3 PONOROGO</option>
                  </select>
                </div>

                <div>
                  <label className="block font-bold text-gray-700 mb-1">Jabatan Terakhir</label>
                  <input
                    type="text"
                    value={editingWorker.jabatanTerakhir}
                    onChange={(e) => setEditingWorker({ ...editingWorker, jabatanTerakhir: e.target.value })}
                    className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3 py-2 font-bold text-gray-800"
                  />
                </div>

                <div>
                  <label className="block font-bold text-gray-700 mb-1">Status Tenaga Kerja</label>
                  <select
                    value={editingWorker.statusTenagaKerja}
                    onChange={(e) => setEditingWorker({ ...editingWorker, statusTenagaKerja: e.target.value as StatusTenagaKerja })}
                    className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3 py-2 font-semibold"
                  >
                    <option value="PKWTT">PKWTT</option>
                    <option value="PKWT">PKWT</option>
                  </select>
                </div>

                <div>
                  <label className="block font-bold text-gray-700 mb-1">Skema</label>
                  <select
                    value={editingWorker.skemaTenagaKerja}
                    onChange={(e) => setEditingWorker({ ...editingWorker, skemaTenagaKerja: e.target.value })}
                    className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3 py-2 font-semibold"
                  >
                    <option value="PEMBORONGAN">PEMBORONGAN</option>
                    <option value="VOLUME BASED">VOLUME BASED</option>
                  </select>
                </div>

                <div>
                  <label className="block font-bold text-gray-700 mb-1">No. WhatsApp / Telepon</label>
                  <input
                    type="text"
                    value={editingWorker.noTelepon}
                    onChange={(e) => setEditingWorker({ ...editingWorker, noTelepon: e.target.value })}
                    className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3 py-2"
                  />
                </div>

                <div>
                  <label className="block font-bold text-gray-700 mb-1">Perusahaan Mitra</label>
                  <input
                    type="text"
                    value={editingWorker.namaPerusahaan}
                    onChange={(e) => setEditingWorker({ ...editingWorker, namaPerusahaan: e.target.value })}
                    className="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3 py-2"
                  />
                </div>
              </div>

              <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button
                  type="button"
                  onClick={() => setEditingWorker(null)}
                  className="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-100"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-xl bg-[#1E8C86] hover:bg-[#17756f] text-white font-bold shadow-teal-glow"
                >
                  Simpan Perubahan
                </button>
              </div>
            </form>

          </div>
        </div>
      )}

    </div>
  );
}
