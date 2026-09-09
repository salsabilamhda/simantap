"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { Users, FileSpreadsheet, Layers, PlusCircle, ShieldCheck } from "lucide-react";

export default function Navbar() {
  const pathname = usePathname();

  const navLinks = [
    { name: "Dashboard", href: "/", icon: Layers },
    { name: "Data Tenaga Kerja", href: "/tenaga-kerja", icon: Users },
    { name: "Import & Export", href: "/import-export", icon: FileSpreadsheet },
    { name: "Master Data", href: "/master-data", icon: Layers },
  ];

  return (
    <header className="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-[#2BA8A2]/15 shadow-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-20">
          
          {/* Logo & Brand matching SIMANTAP design tokens */}
          <Link href="/" className="flex items-center gap-3 group">
            <div className="relative flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br from-[#2BA8A2] to-[#1E8C86] shadow-teal-glow text-white font-black text-2xl tracking-wider transition-transform group-hover:scale-105 group-hover:rotate-1">
              S
              <span className="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-[#FFD23F] border-2 border-white shadow-accent-glow" />
            </div>

            <div className="flex flex-col">
              <div className="flex items-center gap-2">
                <span className="text-2xl font-black tracking-tight text-[#1E8C86]">
                  SIMANTAP
                </span>
                <span className="hidden sm:inline-block px-2 py-0.5 text-xs font-extrabold bg-[#FFF8E7] text-[#1E8C86] border border-[#2BA8A2]/40 rounded-md shadow-xs">
                  v1.0
                </span>
              </div>
              <span className="text-[11px] font-semibold text-[#2C3E50]/70 uppercase tracking-wider">
                Sistem Manajemen Tenaga Kerja
              </span>
            </div>
          </Link>

          {/* Navigation Links */}
          <nav className="hidden md:flex items-center gap-1.5 bg-[#EFF8F7] p-1.5 rounded-full border border-[#2BA8A2]/20">
            {navLinks.map((link) => {
              const Icon = link.icon;
              const isActive = pathname === link.href;
              return (
                <Link
                  key={link.name}
                  href={link.href}
                  className={`flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold transition-all ${
                    isActive
                      ? "bg-white text-[#1E8C86] shadow-teal-glow"
                      : "text-[#2C3E50]/80 hover:text-[#1E8C86] hover:bg-white/60"
                  }`}
                >
                  <Icon className={`w-4 h-4 ${isActive ? "text-[#2BA8A2]" : "text-gray-400"}`} />
                  {link.name}
                </Link>
              );
            })}
          </nav>

          {/* CTA & User Profile */}
          <div className="flex items-center gap-3">
            <Link
              href="/tenaga-kerja/tambah"
              className="btn-gold-primary px-4 py-2.5 flex items-center gap-2 text-xs uppercase tracking-wider cursor-pointer"
            >
              <PlusCircle className="w-4 h-4 text-[#2C3E50]" />
              <span className="hidden sm:inline">Tambah Data</span>
            </Link>

            {/* Admin Avatar Badge */}
            <div className="flex items-center gap-2 pl-2 border-l border-gray-200">
              <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-[#2A9D8F] to-[#5BAD6F] text-white flex items-center justify-center font-bold text-sm shadow-md ring-2 ring-white">
                SA
              </div>
              <div className="hidden lg:flex flex-col text-left">
                <span className="text-xs font-bold text-gray-800 leading-none">Super Admin</span>
                <span className="text-[10px] text-[#2BA8A2] font-semibold flex items-center gap-0.5 mt-0.5">
                  <ShieldCheck className="w-3 h-3" /> Aktif
                </span>
              </div>
            </div>

          </div>

        </div>
      </div>
    </header>
  );
}
