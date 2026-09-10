import { redirect } from "next/navigation";

export default function TambahTenagaKerjaPage() {
  // Tambah data sekarang disatukan di menu Data Tenaga Kerja
  redirect("/tenaga-kerja?tambah=true");
}
