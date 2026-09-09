import type { Metadata } from "next";
import { Plus_Jakarta_Sans } from "next/font/google";
import Sidebar from "@/components/sidebar";
import "./globals.css";

const plusJakarta = Plus_Jakarta_Sans({
  subsets: ["latin"],
  weight: ["400", "500", "600", "700", "800"],
  variable: "--font-sans",
});

export const metadata: Metadata = {
  title: "SIMANTAP — Sistem Manajemen Data Tenaga Kerja",
  description:
    "Aplikasi internal pengelolaan data tenaga kerja outsourcing/mitra dan sertifikasi di lingkungan kerja ULP Balong, Pacitan, Ponorogo, Trenggalek, dan UP3 Ponorogo.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="id" className={`${plusJakarta.variable} h-full antialiased`}>
      <body className="min-h-full bg-[#EFF8F7] text-[#2C3E50] antialiased">
        {/* Left Sidebar Navigation */}
        <Sidebar />

        {/* Main Content Area (Offset for desktop sidebar) */}
        <div className="md:pl-72 flex flex-col min-h-screen">
          
          {/* Top Desktop Bar */}
          <header className="hidden md:flex items-center justify-between px-8 py-4 bg-white/70 backdrop-blur-md border-b border-[#2BA8A2]/15 sticky top-0 z-30">
            <div className="flex items-center gap-2">
              <span className="text-xs font-black text-[#1E8C86] uppercase tracking-wider">
                Wilayah Kerja UP3 Ponorogo
              </span>
              <span className="text-gray-300">•</span>
              <span className="text-xs text-gray-500 font-semibold">
                Sistem Database Tenaga Kerja Mitra
              </span>
            </div>

            <div className="flex items-center gap-3">
              <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#E8F6F5] text-[#1E8C86] border border-[#2BA8A2]/20">
                <span className="w-2 h-2 rounded-full bg-[#27AE60] animate-pulse" />
                Database Real-Time
              </span>
            </div>
          </header>

          {/* Page Content */}
          <main className="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            {children}
          </main>

          {/* Footer */}
          <footer className="bg-white border-t border-[#2BA8A2]/15 py-6 px-8 mt-auto">
            <div className="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 font-medium gap-2 text-center sm:text-left">
              <p>
                &copy; {new Date().getFullYear()} <strong className="text-[#1E8C86]">SIMANTAP</strong> — Sistem Manajemen Data Tenaga Kerja.
              </p>
              <p className="text-[11px] text-gray-400">
                ULP Balong • ULP Pacitan • ULP Ponorogo • ULP Trenggalek • UP3 Ponorogo
              </p>
            </div>
          </footer>

        </div>
      </body>
    </html>
  );
}
