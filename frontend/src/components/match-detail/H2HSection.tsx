'use client';

import { Game } from '@/lib/types';
import { scoreDisplay, formatMatchTime, IMG_URL, DEF_LOGO } from '@/lib/utils';

interface H2HMatch {
  id: number;
  startTime: string;
  competitionDisplayName?: string;
  homeCompetitor: {
    id: number;
    name: string;
    score: number;
  };
  awayCompetitor: {
    id: number;
    name: string;
    score: number;
  };
}

interface H2HSectionProps {
  h2hData: { games?: H2HMatch[] } | null;
}

export default function H2HSection({ h2hData }: H2HSectionProps) {
  const games = h2hData?.games || [];

  if (games.length === 0) {
    return (
      <div className="text-center py-12 text-sm font-bold text-[#6b7280]">
        🤝 لا توجد مواجهات سابقة مسجلة بين الفريقين.
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <h3 className="text-sm font-black text-[#f9fafb] mb-4 flex items-center gap-2 px-1">
        <span>🤝</span> تاريخ المواجهات المباشرة
      </h3>

      <div className="bg-[#111827] border border-[#1f2937] rounded-2xl overflow-hidden divide-y divide-[#1f2937]">
        {games.map((game) => {
          const home = game.homeCompetitor;
          const away = game.awayCompetitor;
          const date = new Date(game.startTime).toLocaleDateString('ar-DZ', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
          });

          return (
            <div
              key={game.id}
              className="flex items-center justify-between p-4 hover:bg-[#1f2937]/20 transition-colors"
            >
              {/* Home Team */}
              <div className="flex-1 flex items-center justify-end gap-2.5">
                <span className="text-xs font-bold text-[#f9fafb] truncate max-w-[110px]">
                  {home.name}
                </span>
                <img
                  src={`${IMG_URL}${home.id}`}
                  alt={home.name}
                  width={24}
                  height={24}
                  className="w-6 h-6 object-contain"
                  onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
                />
              </div>

              {/* Score & Competition info */}
              <div className="flex flex-col items-center justify-center w-[120px] mx-2">
                <div className="flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-[#1f2937] border border-[#374151]/30 font-black text-xs text-[#f9fafb]">
                  <span className="text-[#10b981]">{scoreDisplay(home.score)}</span>
                  <span className="text-[#6b7280]">-</span>
                  <span className="text-[#6366f1]">{scoreDisplay(away.score)}</span>
                </div>
                <span className="text-[9px] font-bold text-[#6b7280] mt-1.5 truncate max-w-[100px]">
                  {game.competitionDisplayName || 'مباراة ودية'}
                </span>
                <span className="text-[8px] text-[#374151] mt-0.5">{date}</span>
              </div>

              {/* Away Team */}
              <div className="flex-1 flex items-center gap-2.5">
                <img
                  src={`${IMG_URL}${away.id}`}
                  alt={away.name}
                  width={24}
                  height={24}
                  className="w-6 h-6 object-contain"
                  onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
                />
                <span className="text-xs font-bold text-[#f9fafb] truncate max-w-[110px]">
                  {away.name}
                </span>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
