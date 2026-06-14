'use client';

import Link from 'next/link';
import { Game } from '@/lib/types';
import { getMatchStatus, scoreDisplay, formatMatchTime, IMG_URL, DEF_LOGO } from '@/lib/utils';

interface MatchHeaderProps {
  game: Game;
}

export default function MatchHeader({ game }: MatchHeaderProps) {
  const home = game.homeCompetitor;
  const away = game.awayCompetitor;
  const status = getMatchStatus(game);
  const tv = game.tvNetworks?.[0]?.name || '';

  return (
    <div className="w-full bg-gradient-to-b from-[#111827] to-[#0a0e17] border-b border-[#1f2937] py-8 px-4 text-center">
      <div className="max-w-3xl mx-auto flex items-center justify-between">
        {/* Home Team */}
        <Link href={`/team/${home.id}`} className="flex-1 flex flex-col items-center gap-2 group cursor-pointer">
          <div className="w-20 h-20 rounded-2xl bg-[#1f2937]/30 border border-[#374151]/30 p-3 flex items-center justify-center shadow-lg shadow-black/20 group-hover:border-[#10b981]/50 transition-colors">
            <img
              src={`${IMG_URL}${home.id}`}
              alt={home.name}
              className="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
              onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
            />
          </div>
          <h2 className="text-base font-black text-[#f9fafb] group-hover:text-[#10b981] transition-colors truncate max-w-[140px]" title={home.name}>
            {home.name}
          </h2>
        </Link>

        {/* Center - Score / Time */}
        <div className="flex flex-col items-center px-4">
          <span className="text-xs font-extrabold text-[#10b981] bg-[#10b981]/10 px-4 py-1 rounded-full mb-3 border border-[#10b981]/20">
            {game.competitionDisplayName}
          </span>
          
          <div className="flex items-center gap-6">
            {status.key === 'pre' ? (
              <div className="text-3xl font-black text-[#f9fafb] tracking-wider">
                {formatMatchTime(game.startTime)}
              </div>
            ) : (
              <div className="flex items-center gap-4 text-4xl font-black">
                <span className="text-[#10b981]">{scoreDisplay(home.score)}</span>
                <span className="text-[#374151] text-2xl">-</span>
                <span className="text-[#6366f1]">{scoreDisplay(away.score)}</span>
              </div>
            )}
          </div>

          <div className="mt-3 flex flex-col items-center gap-1">
            {status.key === 'live' ? (
              <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#ef4444]/20 text-[#ef4444] border border-[#ef4444]/30">
                <span className="w-2 h-2 rounded-full bg-[#ef4444] animate-live-pulse" />
                {status.text}
              </span>
            ) : (
              <span className="text-xs font-bold text-[#9ca3af] bg-[#1f2937] px-3 py-1 rounded-full border border-[#374151]/30">
                {status.text}
              </span>
            )}
            {tv && (
              <span className="text-[10px] font-bold text-[#6366f1] bg-[#6366f1]/10 px-2.5 py-0.5 rounded-full border border-[#6366f1]/20 mt-1">
                📺 {tv}
              </span>
            )}
          </div>
        </div>

        {/* Away Team */}
        <Link href={`/team/${away.id}`} className="flex-1 flex flex-col items-center gap-2 group cursor-pointer">
          <div className="w-20 h-20 rounded-2xl bg-[#1f2937]/30 border border-[#374151]/30 p-3 flex items-center justify-center shadow-lg shadow-black/20 group-hover:border-[#6366f1]/50 transition-colors">
            <img
              src={`${IMG_URL}${away.id}`}
              alt={away.name}
              className="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
              onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
            />
          </div>
          <h2 className="text-base font-black text-[#f9fafb] group-hover:text-[#6366f1] transition-colors truncate max-w-[140px]" title={away.name}>
            {away.name}
          </h2>
        </Link>
      </div>
    </div>
  );
}
