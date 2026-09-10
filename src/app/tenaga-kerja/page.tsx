import React, { Suspense } from "react";
import TenagaKerjaTable from "@/components/tenaga-kerja-table";

export const metadata = {
  title: "Data Tenaga Kerja — SIMANTAP UP3 Ponorogo",
  description:
    "Daftar lengkap data personil tenaga kerja outsourcing dan sertifikasi di lingkungan kerja ULP Balong, Pacitan, Ponorogo, Trenggalek, dan UP3 Ponorogo.",
};

function TableSkeleton() {
  return (
    <div className="space-y-6 animate-pulse">
      <div className="h-24 bg-white rounded-3xl border border-gray-100 p-6" />
      <div className="h-96 bg-white rounded-3xl border border-gray-100 p-6" />
    </div>
  );
}

export default function TenagaKerjaListPage() {
  return (
    <div className="space-y-6">
      <Suspense fallback={<TableSkeleton />}>
        <TenagaKerjaTable />
      </Suspense>
    </div>
  );
}
