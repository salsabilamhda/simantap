import React from "react";
import { UNIT_LAYANAN_DATA } from "@/lib/sample-data";
import { Building2, Award } from "lucide-react";

export default function UnitDistribution() {
  const total = UNIT_LAYANAN_DATA.reduce((acc, curr) => acc + curr.count, 0);

  return (
    <div className="bg-white rounded-3xl p-6 border border-[#2BA8A2]/15 shadow-card-custom">
      <div className="flex items-center justify-between pb-4 mb-5 border-b-2 border-dashed border-[#2BA8A2]/20">
        <div className="flex items-center gap-2.5">
          <div className="w-9 h-9 rounded-xl bg-[#E8F6F5] flex items-center justify-center text-[#1E8C86]">
            <Building2 className="w-5 h-5" />
          </div>
          <div>
            <h3 className="font-extrabold text-[#1E8C86] text-lg">Distribusi Unit Layanan</h3>
            <p className="text-xs text-gray-400 font-medium">Replikasi Pivot Sheet1 Acuan Database</p>
          </div>
        </div>

        <span className="px-3 py-1 rounded-full text-xs font-black bg-[#FFF8E7] text-[#1E8C86] border border-[#2BA8A2]/30">
          Total: {total} Personil
        </span>
      </div>

      <div className="space-y-4">
        {UNIT_LAYANAN_DATA.map((unit, index) => {
          const percentage = Math.round((unit.count / total) * 100);

          return (
            <div key={unit.kode} className="group">
              <div className="flex items-center justify-between mb-1.5">
                <div className="flex items-center gap-2">
                  <span
                    className="w-3 h-3 rounded-full"
                    style={{ backgroundColor: unit.colorHex }}
                  />
                  <span className="text-xs sm:text-sm font-bold text-gray-800">
                    {unit.nama}
                  </span>
                  {index === 0 && (
                    <span className="flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-[#FFF8E7] text-[#C9A227] border border-[#FFD23F]">
                      <Award className="w-3 h-3" /> Terbanyak
                    </span>
                  )}
                </div>

                <div className="flex items-center gap-2">
                  <span className="text-xs text-gray-400">
                    {unit.pkwttCount} PKWTT / {unit.pkwtCount} PKWT
                  </span>
                  <span className="text-xs font-black text-gray-900 min-w-10 text-right">
                    {unit.count} ({percentage}%)
                  </span>
                </div>
              </div>

              {/* Progress bar with animated width and gradient fill */}
              <div className="w-full h-3.5 bg-[#EFF8F7] rounded-full overflow-hidden p-0.5 border border-gray-100">
                <div
                  className="h-full rounded-full transition-all duration-700 ease-out"
                  style={{
                    width: `${percentage}%`,
                    backgroundColor: unit.colorHex,
                    boxShadow: `0 2px 8px ${unit.colorHex}55`,
                  }}
                />
              </div>
            </div>
          );
        })}
      </div>

      <div className="mt-6 pt-4 border-t border-dashed border-gray-100 flex flex-wrap items-center justify-between text-xs text-gray-500 gap-2">
        <span className="font-semibold text-gray-600">
          5 Unit Operasional: 4 Unit Layanan Pelanggan (ULP) & 1 Unit Pelaksana Pelayanan (UP3)
        </span>
        <span className="text-[11px] text-[#2BA8A2] font-bold">100% Terverifikasi</span>
      </div>
    </div>
  );
}
