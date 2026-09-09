"use client";

import { INITIAL_TENAGA_KERJA, PLAYER_AVATAR_COLORS } from "@/lib/sample-data";
import { TenagaKerja } from "@/types/tenaga-kerja";
import { Award, Building, Eye, Filter, PencilLine, Phone, Save, Search, X } from "lucide-react";
import { useMemo, useState } from "react";

export default function TenagaKerjaTable() {
  const [tableData, setTableData] = useState<TenagaKerja[]>(INITIAL_TENAGA_KERJA);
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedUnit, setSelectedUnit] = useState("ALL");
  const [selectedStatus, setSelectedStatus] = useState("ALL");
  const [editingId, setEditingId] = useState<string | null>(null);
  const [draft, setDraft] = useState<TenagaKerja | null>(null);
  const [selectedDetail, setSelectedDetail] = useState<TenagaKerja | null>(null);

  const filteredData = useMemo(() => {
    return tableData.filter((item) => {
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
  }, [searchQuery, selectedUnit, selectedStatus, tableData]);

  const handleEdit = (item: TenagaKerja) => {
    setEditingId(item.id);
    setDraft({ ...item });
  };

  const handleDraftChange = <K extends keyof TenagaKerja>(field: K, value: TenagaKerja[K]) => {
    setDraft((prev) => (prev ? { ...prev, [field]: value } : prev));
  };

  const handleSave = () => {
    if (!draft || !editingId) return;

    setTableData((prev) =>
      prev.map((item) =>
        item.id === editingId
          ? {
              ...item,
              ...draft,
              updatedAt: new Date().toISOString(),
            }
          : item
      )
    );

    setEditingId(null);
    setDraft(null);
  };

  const handleCancel = () => {
    setEditingId(null);
    setDraft(null);
  };

  return (
    <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom">
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

        <div className="flex flex-wrap items-center gap-2.5">
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
              const isEditing = editingId === item.id;

              return (
                <tr key={item.id} className="hover:bg-[#EFF8F7]/60 transition-colors group">
                  <td className="py-3.5 px-4">
                    {isEditing && draft ? (
                      <div className="space-y-2 min-w-[180px]">
                        <input
                          value={draft.nama}
                          onChange={(e) => handleDraftChange("nama", e.target.value)}
                          className="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm font-bold text-gray-800"
                        />
                        <input
                          value={draft.noTelepon}
                          onChange={(e) => handleDraftChange("noTelepon", e.target.value)}
                          className="w-full border border-gray-200 rounded-lg px-2 py-1 text-[11px] text-gray-500"
                        />
                      </div>
                    ) : (
                      <div className="flex items-center gap-3">
                        <div
                          className="w-10 h-10 rounded-full flex items-center justify-center text-white font-extrabold text-xs shadow-xs"
                          style={{ backgroundColor: avatarColor }}
                        >
                          {initials}
                        </div>
                        <div>
                          <div className="font-extrabold text-gray-800 text-sm">{item.nama}</div>
                          <div className="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                            <Phone className="w-3 h-3 text-gray-400" />
                            <span>{item.noTelepon}</span>
                          </div>
                        </div>
                      </div>
                    )}
                  </td>

                  <td className="py-3.5 px-4">
                    {isEditing && draft ? (
                      <div className="space-y-2 min-w-[130px]">
                        <input
                          value={draft.nik}
                          onChange={(e) => handleDraftChange("nik", e.target.value)}
                          className="w-full border border-gray-200 rounded-lg px-2 py-1 font-mono text-gray-700 font-bold"
                        />
                        <input
                          value={draft.usia || ""}
                          onChange={(e) => handleDraftChange("usia", e.target.value)}
                          className="w-full border border-gray-200 rounded-lg px-2 py-1 text-[11px] text-gray-500"
                        />
                      </div>
                    ) : (
                      <>
                        <div className="font-mono font-bold text-gray-700">{item.nik}</div>
                        <div className="text-[11px] text-gray-400">{item.usia || "-"}</div>
                      </>
                    )}
                  </td>

                  <td className="py-3.5 px-4">
                    {isEditing && draft ? (
                      <input
                        value={draft.unitLayanan}
                        onChange={(e) => handleDraftChange("unitLayanan", e.target.value)}
                        className="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-[11px] font-bold text-[#1E8C86] bg-[#E8F6F5]"
                      />
                    ) : (
                      <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#E8F6F5] text-[#1E8C86]">
                        <Building className="w-3 h-3 text-[#2BA8A2]" />
                        {item.unitLayanan}
                      </span>
                    )}
                  </td>

                  <td className="py-3.5 px-4">
                    {isEditing && draft ? (
                      <div className="space-y-2 min-w-[150px]">
                        <input
                          value={draft.jabatanTerakhir}
                          onChange={(e) => handleDraftChange("jabatanTerakhir", e.target.value)}
                          className="w-full border border-gray-200 rounded-lg px-2 py-1.5 font-bold text-gray-800"
                        />
                        <input
                          value={draft.namaPerusahaan}
                          onChange={(e) => handleDraftChange("namaPerusahaan", e.target.value)}
                          className="w-full border border-gray-200 rounded-lg px-2 py-1 text-[11px] text-gray-500"
                        />
                      </div>
                    ) : (
                      <>
                        <div className="font-bold text-gray-800">{item.jabatanTerakhir}</div>
                        <div className="text-[11px] text-gray-400">{item.namaPerusahaan}</div>
                      </>
                    )}
                  </td>

                  <td className="py-3.5 px-4">
                    {isEditing && draft ? (
                      <div className="space-y-2 min-w-[120px]">
                        <select
                          value={draft.statusTenagaKerja}
                          onChange={(e) => handleDraftChange("statusTenagaKerja", e.target.value as TenagaKerja["statusTenagaKerja"])}
                          className="w-full border border-gray-200 rounded-lg px-2 py-1 text-[10px] font-black"
                        >
                          <option value="PKWTT">PKWTT</option>
                          <option value="PKWT">PKWT</option>
                        </select>
                        <input
                          value={draft.skemaTenagaKerja}
                          onChange={(e) => handleDraftChange("skemaTenagaKerja", e.target.value)}
                          className="w-full border border-gray-200 rounded-lg px-2 py-1 text-[10px] text-gray-500 uppercase"
                        />
                      </div>
                    ) : (
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
                    )}
                  </td>

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

                  <td className="py-3.5 px-4 text-center">
                    {isEditing ? (
                      <div className="flex items-center justify-center gap-2">
                        <button
                          type="button"
                          onClick={handleSave}
                          className="inline-flex items-center gap-1 bg-[#1E8C86] text-white px-2 py-1.5 rounded-lg text-[10px] font-bold hover:bg-[#17756f]"
                        >
                          <Save className="w-3 h-3" />
                          Simpan
                        </button>
                        <button
                          type="button"
                          onClick={handleCancel}
                          className="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-2 py-1.5 rounded-lg text-[10px] font-bold hover:bg-gray-200"
                        >
                          <X className="w-3 h-3" />
                          Batal
                        </button>
                      </div>
                    ) : (
                      <div className="flex items-center justify-center gap-2">
                        <button
                          type="button"
                          onClick={() => handleEdit(item)}
                          className="btn-teal-outline px-2.5 py-1.5 text-[10px] inline-flex items-center gap-1 cursor-pointer"
                        >
                          <PencilLine className="w-3 h-3" />
                          Edit
                        </button>
                        <button
                          type="button"
                          onClick={() => setSelectedDetail(item)}
                          className="btn-teal-outline px-2.5 py-1.5 text-[10px] inline-flex items-center gap-1 cursor-pointer"
                          title="Lihat Detail Profil"
                        >
                          <Eye className="w-3 h-3" />
                          Detail
                        </button>
                      </div>
                    )}
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

      {selectedDetail && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
          <div className="w-full max-w-3xl max-h-[85vh] overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl border border-[#2BA8A2]/15">
            <div className="flex items-start justify-between gap-4 pb-4 border-b border-gray-200">
              <div>
                <p className="text-[11px] uppercase tracking-[0.2em] text-gray-400 font-bold">Profil Tenaga Kerja</p>
                <h4 className="text-2xl font-black text-[#1E8C86] mt-1">{selectedDetail.nama}</h4>
              </div>
              <button
                type="button"
                onClick={() => setSelectedDetail(null)}
                className="w-9 h-9 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center justify-center"
                aria-label="Tutup detail"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5 text-sm">
              <div className="rounded-2xl bg-[#F8FBFB] p-4 border border-[#2BA8A2]/10">
                <p className="text-[11px] uppercase tracking-[0.18em] text-gray-400 font-bold mb-2">Data Pribadi</p>
                <div className="space-y-2 text-gray-700">
                  <div><span className="font-bold text-gray-500">NIK:</span> {selectedDetail.nik}</div>
                  <div><span className="font-bold text-gray-500">Tempat/Tanggal Lahir:</span> {selectedDetail.tempatLahir || "-"}, {selectedDetail.tanggalLahir || "-"}</div>
                  <div><span className="font-bold text-gray-500">Usia:</span> {selectedDetail.usia || "-"}</div>
                  <div><span className="font-bold text-gray-500">Jenis Kelamin:</span> {selectedDetail.jenisKelamin}</div>
                  <div><span className="font-bold text-gray-500">Pendidikan:</span> {selectedDetail.pendidikanTerakhir} - {selectedDetail.jurusan || "-"}</div>
                  <div><span className="font-bold text-gray-500">Telepon:</span> {selectedDetail.noTelepon}</div>
                  <div><span className="font-bold text-gray-500">Email:</span> {selectedDetail.email || "-"}</div>
                </div>
              </div>

              <div className="rounded-2xl bg-[#F8FBFB] p-4 border border-[#2BA8A2]/10">
                <p className="text-[11px] uppercase tracking-[0.18em] text-gray-400 font-bold mb-2">Penempatan</p>
                <div className="space-y-2 text-gray-700">
                  <div><span className="font-bold text-gray-500">Unit:</span> {selectedDetail.unitLayanan}</div>
                  <div><span className="font-bold text-gray-500">Jabatan:</span> {selectedDetail.jabatanTerakhir}</div>
                  <div><span className="font-bold text-gray-500">Perusahaan:</span> {selectedDetail.namaPerusahaan}</div>
                  <div><span className="font-bold text-gray-500">Status:</span> {selectedDetail.statusTenagaKerja}</div>
                  <div><span className="font-bold text-gray-500">Skema:</span> {selectedDetail.skemaTenagaKerja}</div>
                  <div><span className="font-bold text-gray-500">Perjanjian:</span> {selectedDetail.nomorPerjanjian || "-"}</div>
                </div>
              </div>

              <div className="rounded-2xl bg-[#F8FBFB] p-4 border border-[#2BA8A2]/10 md:col-span-2">
                <p className="text-[11px] uppercase tracking-[0.18em] text-gray-400 font-bold mb-2">Alamat & Jaminan</p>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-3 text-gray-700">
                  <div><span className="font-bold text-gray-500">Alamat:</span> {selectedDetail.alamatDomisili || "-"}</div>
                  <div><span className="font-bold text-gray-500">Kota/Kab:</span> {selectedDetail.kotaKabupaten || "-"}</div>
                  <div><span className="font-bold text-gray-500">Provinsi:</span> {selectedDetail.provinsi || "-"}</div>
                  <div><span className="font-bold text-gray-500">BPJS Kesehatan:</span> {selectedDetail.nomorBpjsKesehatan || "-"}</div>
                  <div><span className="font-bold text-gray-500">BPJS Ketenagakerjaan:</span> {selectedDetail.nomorBpjsKetenagakerjaan || "-"}</div>
                  <div><span className="font-bold text-gray-500">DPLK:</span> {selectedDetail.nomorDplk || "-"} / {selectedDetail.bankDplk || "-"}</div>
                </div>
              </div>
            </div>

            <div className="flex justify-end pt-6">
              <button
                type="button"
                onClick={() => setSelectedDetail(null)}
                className="btn-teal-outline px-4 py-2.5 text-xs font-bold"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
