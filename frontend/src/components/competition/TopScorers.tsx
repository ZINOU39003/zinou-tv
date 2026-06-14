'use client';

import { useState } from 'react';
import Link from 'next/link';
import { PLAYER_IMG, IMG_URL, DEF_LOGO } from '@/lib/utils';

interface ScorerRow {
  position: number;
  secondaryStatName?: string;
  entity: {
    id: number;
    name: string;
    positionName?: string;
    competitorId?: number;
  };
  stats: {
    value: string;
  }[];
}

interface StatCategory {
  id: number;
  name: string;
  rows?: ScorerRow[];
}

interface TopScorersProps {
  topScorersData: { stats?: { athletesStats?: StatCategory[] } } | null;
}

export default function TopScorers({ topScorersData }: TopScorersProps) {
  const categories = topScorersData?.stats?.athletesStats || [];
  const [activeCategoryId, setActiveCategoryId] = useState<number>(categories[0]?.id || 1);

  if (categories.length === 0) {
    return (
      <div className="text-center py-12 text-sm font-bold text-[#6b7280]">
        🥇 قائمة الهدافين غير متوفرة حالياً لهذه البطولة.
      </div>
    );
  }

  const activeCategory = categories.find((c) => c.id === activeCategoryId) || categories[0];
  const rows = activeCategory?.rows || [];

  return (
    <div className="space-y-6">
      {/* Category selector */}
      <div className="flex bg-[#111827] border border-[#1f2937] p-1.5 rounded-xl gap-2">
        {categories.map((cat) => (
          <button
            key={cat.id}
            onClick={() => setActiveCategoryId(cat.id)}
            className={`flex-1 py-2.5 rounded-lg text-xs font-black transition-colors ${
              activeCategoryId === cat.id
                ? 'bg-[#10b981] text-white shadow-md shadow-[#10b981]/20'
                : 'text-[#9ca3af] hover:text-white'
            }`}
          >
            {cat.name}
          </button>
        ))}
      </div>

      {/* Scorers List */}
      {rows.length === 0 ? (
        <div className="text-center py-12 text-sm font-bold text-[#6b7280]">
          لا توجد بيانات مسجلة في هذه الفئة.
        </div>
      ) : (
        <div className="bg-[#111827] border border-[#1f2937] rounded-2xl overflow-hidden divide-y divide-[#1f2937] shadow-lg shadow-black/10">
          {rows.map((row, index) => {
            const player = row.entity;
            const rank = index + 1;
            const value = row.stats?.[0]?.value || '0';
            const pImg = `${PLAYER_IMG}${player.id}`;
            const teamLogo = player.competitorId ? `${IMG_URL}${player.competitorId}` : null;

            return (
              <div
                key={player.id}
                className="flex items-center justify-between p-4 hover:bg-[#1f2937]/20 transition-colors"
              >
                {/* Stats Value */}
                <div className="flex flex-col items-center justify-center w-16 bg-[#10b981]/5 border border-[#10b981]/20 px-3 py-1.5 rounded-xl">
                  <span className="text-lg font-black text-[#10b981]">{value}</span>
                  <span className="text-[9px] text-[#6b7280] font-bold">
                    {activeCategory.name}
                  </span>
                </div>

                {/* Player details */}
                <div className="flex-1 flex items-center justify-end gap-3 mr-4">
                  <div className="text-right">
                    <Link href={`/player/${player.id}`} className="hover:text-[#10b981] transition-colors block">
                      <h4 className="text-xs font-black text-[#f9fafb] hover:text-[#10b981]">{player.name}</h4>
                    </Link>
                    <div className="flex items-center gap-1.5 justify-end mt-0.5">
                      {player.positionName && (
                        <span className="text-[9px] font-bold text-[#6b7280]">
                          {player.positionName}
                        </span>
                      )}
                      {teamLogo && player.competitorId && (
                        <Link href={`/team/${player.competitorId}`} className="hover:scale-105 transition-transform">
                          <img
                            src={teamLogo}
                            alt="Team Logo"
                            className="w-3.5 h-3.5 object-contain"
                            onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
                          />
                        </Link>
                      )}
                    </div>
                    {row.secondaryStatName && (
                      <span className="text-[8px] font-medium text-[#374151] mt-0.5 block">
                        {row.secondaryStatName}
                      </span>
                    )}
                  </div>
                  <Link href={`/player/${player.id}`} className="w-11 h-11 rounded-full border border-[#1f2937] bg-[#1f2937]/30 overflow-hidden shadow-inner flex items-center justify-center hover:border-[#10b981] transition-colors">
                    <img
                      src={pImg}
                      alt={player.name}
                      className="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                      onError={(e) => {
                        (e.target as HTMLImageElement).src = 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/default';
                      }}
                    />
                  </Link>
                </div>

                {/* Rank */}
                <div className="w-8 text-center text-sm font-black text-[#9ca3af] italic">
                  #{rank}
                </div>
              </div>
            );
          })}
        </div>
      )}
    </div>
  );
}
