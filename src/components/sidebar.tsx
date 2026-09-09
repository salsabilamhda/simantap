"use client";

import React, { useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  LayoutDashboard,
  Users,
  UserPlus,
  FileSpreadsheet,
  Building2,
  Briefcase,
  ShieldCheck,
  LogOut,
  Menu,
  X,
  Sparkles,
} from "lucide-react";

interface NavItem {
  name: string;
  href: string;
  icon: React.ComponentType<{ className?: string }>;
  badge?: string;
}

interface NavSection {
  title: string;
  items: NavItem[];
}

export default function Sidebar() {
  const pathname = usePathname();
  const [isOpen, setIsOpen] = useState(false);

  const menuSections: NavSection[] = [
    {
      title: "Menu Utama",
      items: [
        {
          name: "Dashboard",
          href: "/",
          icon: LayoutDashboard,
        },
        {
          name: "Data Tenaga Kerja",
          href: "/tenaga-kerja",
          icon: Users,
          badge: "198",
        },
        {
          name: "Tambah Personil",
          href: "/tenaga-kerja/tambah",
          icon: UserPlus,
        },
        {
          name: "Impor & Ekspor Excel",
          href: "/import-export",
          icon: FileSpreadsheet,
        },
      ],
    },
    {
      title: "Master Data",
      items: [
        {
          name: "Unit Layanan",
          href: "/master-data/unit-layanan",
          icon: Building2,
          badge: "5",
        },
        {
          name: "Perusahaan Mitra",
          href: "/master-data/perusahaan",
          icon: Briefcase,
        },
      ],
    },
    {
      title: "Sistem & Akses",
      items: [
        {
          name: "Kelola Admin",
          href: "/pengaturan/admin",
          icon: ShieldCheck,
        },
      ],
    },
  ];

  const toggleSidebar = () => setIsOpen(!isOpen);

  return (
    <>
      {/* Mobile Topbar for opening sidebar */}
      <div className="md:hidden sticky top-0 z-50 flex items-center justify-between px-4 py-3 bg-white/95 backdrop-blur-md border-b border-[#2BA8A2]/15 shadow-sm">
        <Link href="/" className="flex items-center gap-2.5">
          <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-[#2BA8A2] to-[#1E8C86] shadow-teal-glow text-white font-black text-lg flex items-center justify-center">
            S
          </div>
          <span className="text-xl font-black tracking-tight text-[#1E8C86]">
            SIMANTAP
          </span>
        </Link>

        <button
          onClick={toggleSidebar}
          aria-label="Buka Menu"
          className="p-2 rounded-xl bg-[#EFF8F7] text-[#1E8C86] hover:bg-[#E8F6F5] transition-colors cursor-pointer"
        >
          {isOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
        </button>
      </div>

      {/* Backdrop overlay for mobile */}
      {isOpen && (
        <div
          onClick={() => setIsOpen(false)}
          className="fixed inset-0 z-40 bg-black/30 backdrop-blur-xs md:hidden"
        />
      )}

      {/* Sidebar Container */}
      <aside
        className={`fixed top-0 bottom-0 left-0 z-50 w-72 bg-white border-r border-[#2BA8A2]/15 flex flex-col justify-between transition-transform duration-300 ease-in-out md:translate-x-0 ${
          isOpen ? "translate-x-0" : "-translate-x-full"
        }`}
      >
        {/* Top: Branding Logo & Subtitle */}
        <div className="p-5 border-b border-[#2BA8A2]/10">
          <div className="flex items-center justify-between">
            <Link href="/" className="flex items-center gap-3 group">
              <div className="relative flex items-center justify-center w-11 h-11 rounded-2xl bg-gradient-to-br from-[#2BA8A2] to-[#1E8C86] shadow-teal-glow text-white font-black text-xl tracking-wider transition-transform group-hover:scale-105">
                S
                <span className="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-[#FFD23F] border-2 border-white shadow-accent-glow" />
              </div>

              <div className="flex flex-col">
                <div className="flex items-center gap-1.5">
                  <span className="text-xl font-black tracking-tight text-[#1E8C86]">
                    SIMANTAP
                  </span>
                  <span className="px-1.5 py-0.5 text-[10px] font-extrabold bg-[#FFF8E7] text-[#C9A227] border border-[#FFD23F] rounded shadow-2xs">
                    v1.0
                  </span>
                </div>
                <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                  Data Tenaga Kerja
                </span>
              </div>
            </Link>

            {/* Close button for mobile */}
            <button
              onClick={() => setIsOpen(false)}
              className="md:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Ribbon Tagline */}
          <div className="mt-3.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#FFF8E7] border border-[#FFD23F]/50 text-[#1E8C86] text-[11px] font-extrabold w-full">
            <Sparkles className="w-3.5 h-3.5 text-[#C9A227] shrink-0" />
            <span className="truncate">Unit ULP & UP3 Ponorogo</span>
          </div>
        </div>

        {/* Middle: Scrollable Menu Sections */}
        <div className="flex-1 overflow-y-auto px-4 py-4 space-y-6">
          {menuSections.map((section) => (
            <div key={section.title}>
              <div className="px-3 mb-2 text-[10px] font-black uppercase tracking-wider text-gray-400">
                {section.title}
              </div>

              <div className="space-y-1">
                {section.items.map((item) => {
                  const Icon = item.icon;
                  const isActive =
                    pathname === item.href ||
                    (item.href !== "/" && pathname.startsWith(item.href));

                  return (
                    <Link
                      key={item.name}
                      href={item.href}
                      onClick={() => setIsOpen(false)}
                      className={`flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all ${
                        isActive
                          ? "bg-gradient-to-r from-[#2BA8A2] to-[#1E8C86] text-white shadow-teal-glow translate-x-1"
                          : "text-gray-600 hover:text-[#1E8C86] hover:bg-[#EFF8F7]"
                      }`}
                    >
                      <div className="flex items-center gap-3">
                        <Icon
                          className={`w-4 h-4 ${
                            isActive ? "text-white" : "text-[#2BA8A2]"
                          }`}
                        />
                        <span>{item.name}</span>
                      </div>

                      {item.badge && (
                        <span
                          className={`px-2 py-0.5 rounded-full text-[10px] font-black ${
                            isActive
                              ? "bg-white/25 text-white"
                              : "bg-[#FFF8E7] text-[#C9A227] border border-[#FFD23F]"
                          }`}
                        >
                          {item.badge}
                        </span>
                      )}
                    </Link>
                  );
                })}
              </div>
            </div>
          ))}
        </div>

        {/* Bottom: Admin User Profile Card & Logout */}
        <div className="p-4 border-t border-[#2BA8A2]/10 bg-[#EFF8F7]/50">
          <div className="flex items-center justify-between p-2.5 rounded-2xl bg-white border border-[#2BA8A2]/15 shadow-2xs">
            <div className="flex items-center gap-2.5 overflow-hidden">
              <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#2A9D8F] to-[#5BAD6F] text-white flex items-center justify-center font-black text-xs shrink-0 shadow-xs">
                SA
              </div>
              <div className="flex flex-col truncate">
                <span className="text-xs font-bold text-gray-800 truncate">
                  Super Admin
                </span>
                <span className="text-[10px] text-gray-400 truncate">
                  admin@simantap.id
                </span>
              </div>
            </div>

            <button
              onClick={() => alert("Sesi admin aktif")}
              title="Keluar Sesi"
              className="p-1.5 rounded-xl text-gray-400 hover:text-[#EF6C4A] hover:bg-[#FDF1EE] transition-colors cursor-pointer"
            >
              <LogOut className="w-4 h-4" />
            </button>
          </div>
        </div>

      </aside>
    </>
  );
}
