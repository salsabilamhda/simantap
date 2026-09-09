export type StatusTenagaKerja = "PKWT" | "PKWTT";
export type SkemaTenagaKerja = "PEMBORONGAN" | "VOLUME BASED" | string;
export type JenisKelamin = "LAKI" | "PEREMPUAN";

export interface Sertifikasi {
  id: string;
  nomorSertifikat: string;
  judulSertifikasi: string;
  gambarSertifikatUrl?: string;
  cloudinaryPublicId?: string;
  tanggalTerbit?: string;
  tanggalKadaluarsa?: string;
  createdAt?: string;
}

export interface TenagaKerja {
  id: string;
  noUrut?: string | number;
  nomorPerjanjian: string;
  namaPerusahaan: string;
  nama: string;
  nik: string;
  tempatLahir: string;
  tanggalLahir: string; // YYYY-MM-DD or string
  usia?: string;
  pendidikanTerakhir: string;
  jurusan: string;
  noTelepon: string;
  email: string;
  jenisKelamin: JenisKelamin;
  alamatDomisili: string;
  kotaKabupaten: string;
  provinsi: string;
  jabatanTerakhir: string;
  fungsiPekerjaan?: string;
  unit: string;
  unitLayanan: string;
  nomorBpjsKesehatan: string;
  nomorBpjsKetenagakerjaan: string;
  nomorDplk: string;
  bankDplk: string;
  noPerjanjianKerja: string;
  tanggalMasukKerja: string;
  statusTenagaKerja: StatusTenagaKerja;
  skemaTenagaKerja: SkemaTenagaKerja;
  sertifikasiList?: Sertifikasi[];
  createdAt?: string;
  updatedAt?: string;
}

export interface UnitLayananSummary {
  nama: string;
  kode: string;
  count: number;
  colorHex: string;
  pkwtCount: number;
  pkwttCount: number;
}

export interface AdminUser {
  uid: string;
  nama: string;
  email: string;
  role: "superadmin" | "admin";
  avatarUrl?: string;
  createdAt?: string;
}
