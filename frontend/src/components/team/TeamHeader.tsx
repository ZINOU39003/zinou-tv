'use client';

import { IMG_URL_LARGE, DEF_LOGO } from '@/lib/utils';

interface TeamHeaderProps {
  competitor: any;
}

export default function TeamHeader({ competitor }: TeamHeaderProps) {
  if (!competitor) return null;

  const logoUrl = `${IMG_URL_LARGE}${competitor.id}`;
  const teamColor = competitor.color || '#10b981';

  return (
    <div className="relative w-full bg-gradient-to-b from-[#111827] to-[#0a0e17] border-b border-[#1f2937] py-10 px-4">
      {/* Decorative colored glow in the background */}
      <div 
        className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 rounded-full blur-[100px] opacity-10 pointer-events-none"
        style={{ backgroundColor: teamColor }}
      />

      <div className="relative max-w-4xl mx-auto flex flex-col items-center text-center z-10">
        {/* Team Logo Container */}
        <div className="relative group mb-5">
          <div className="absolute inset-0 rounded-3xl blur-md opacity-25 group-hover:opacity-40 transition-opacity" style={{ backgroundColor: teamColor }} />
          <div className="relative w-24 h-24 md:w-28 md:h-28 rounded-3xl bg-[#1f2937]/50 border border-[#374151]/30 p-4 flex items-center justify-center shadow-xl shadow-black/30 backdrop-blur-md">
            <img
              src={logoUrl}
              alt={competitor.name}
              className="w-full h-full object-contain filter drop-shadow-md"
              onError={(e) => {
                (e.target as HTMLImageElement).src = DEF_LOGO;
              }}
            />
          </div>
        </div>

        {/* Team Info */}
        <h1 className="text-2xl md:text-3xl font-black text-[#f9fafb] tracking-tight">
          {competitor.name}
        </h1>
        
        {competitor.symbolicName && (
          <p className="text-xs font-bold text-[#6b7280] mt-1">
            {competitor.symbolicName}
          </p>
        )}

        <div className="flex flex-wrap items-center justify-center gap-2 mt-4 text-[10px] md:text-xs font-bold text-[#9ca3af]">
          {competitor.competitions?.slice(0, 3).map((comp: any) => (
            <span
              key={comp.id}
              className="px-3 py-1 rounded-full bg-[#1f2937]/60 border border-[#374151]/35 backdrop-blur-sm"
            >
              🏆 {comp.name}
            </span>
          ))}
        </div>
      </div>
    </div>
  );
}
