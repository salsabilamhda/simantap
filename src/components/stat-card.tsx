import React from "react";
import { LucideIcon } from "lucide-react";

interface StatCardProps {
  title: string;
  value: string | number;
  subtitle: string;
  icon: LucideIcon;
  variant?: "teal" | "gold" | "coral" | "sky";
  badgeText?: string;
}

export default function StatCard({
  title,
  value,
  subtitle,
  icon: Icon,
  variant = "teal",
  badgeText,
}: StatCardProps) {
  const variantStyles = {
    teal: {
      borderLeft: "border-l-[#2BA8A2]",
      glow: "shadow-teal-glow",
      iconBg: "bg-[#E8F6F5] text-[#1E8C86]",
      badge: "bg-[#E8F6F5] text-[#1E8C86]",
    },
    gold: {
      borderLeft: "border-l-[#FFD23F]",
      glow: "shadow-accent-glow",
      iconBg: "bg-[#FFF8E7] text-[#C9A227]",
      badge: "bg-[#FFF8E7] text-[#C9A227]",
    },
    coral: {
      borderLeft: "border-l-[#EF6C4A]",
      glow: "shadow-coral-glow",
      iconBg: "bg-[#FDF1EE] text-[#EF6C4A]",
      badge: "bg-[#FDF1EE] text-[#EF6C4A]",
    },
    sky: {
      borderLeft: "border-l-[#5DADE2]",
      glow: "shadow-sky-glow",
      iconBg: "bg-[#EFF8FD] text-[#3A7DC9]",
      badge: "bg-[#EFF8FD] text-[#3A7DC9]",
    },
  }[variant];

  return (
    <div
      className={`relative bg-white rounded-2xl p-5 border border-gray-100 border-l-[6px] ${variantStyles.borderLeft} shadow-card-custom transition-all hover:-translate-y-1`}
    >
      <div className="flex items-start justify-between">
        <div>
          <span className="text-xs font-bold uppercase tracking-wider text-gray-400">
            {title}
          </span>
          <div className="text-3xl font-black text-[#2C3E50] mt-1.5 tracking-tight">
            {value}
          </div>
        </div>

        <div className={`w-12 h-12 rounded-2xl flex items-center justify-center ${variantStyles.iconBg} shadow-xs`}>
          <Icon className="w-6 h-6" />
        </div>
      </div>

      <div className="mt-4 flex items-center justify-between pt-3 border-t border-dashed border-gray-100">
        <span className="text-xs text-gray-500 font-medium">{subtitle}</span>
        {badgeText && (
          <span className={`px-2.5 py-0.5 rounded-full text-[11px] font-extrabold ${variantStyles.badge}`}>
            {badgeText}
          </span>
        )}
      </div>
    </div>
  );
}
