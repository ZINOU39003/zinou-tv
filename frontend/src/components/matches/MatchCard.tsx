'use client';

import Link from 'next/link';
import { Game } from '@/lib/types';
import { getMatchStatus, scoreDisplay, formatMatchTime, IMG_URL, DEF_LOGO } from '@/lib/utils';
import LiveBadge from './LiveBadge';

interface MatchCardProps {
  game: Game;
}

export default function MatchCard({ game }: MatchCardProps) {
  const home = game.homeCompetitor;
  const away = game.awayCompetitor;
  const status = getMatchStatus(game);
  const tv = game.tvNetworks?.[0]?.name || '';

  return (
    <Link
      href={`/match/${game.id}`}
      className="flex items-center justify-between px-5 py-4 border-b border-[#1f2937] last:border-b-0 hover:bg-[#1f2937]/50 transition-colors cursor-pointer group"
    >
      {/* Home Team */}
      <div className="flex-1 flex items-center justify-end gap-3">
        <span className="text-sm font-bold text-[#f9fafb] group-hover:text-[#10b981] transition-colors truncate max-w-[120px]">
          {home.name}
        </span>
        <img
          src={`${IMG_URL}${home.id}`}
          alt={home.name}
          width={36}
          height={36}
          className="w-9 h-9 object-contain"
          onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
        />
      </div>

      {/* Score / Time */}
      <div className="w-[130px] flex flex-col items-center flex-shrink-0 mx-2">
        {status.key === 'pre' ? (
          <>
            <div className="px-4 py-1 rounded-full bg-gradient-to-r from-[#10b981] to-[#059669] text-white text-sm font-black">
              {formatMatchTime(game.startTime)}
            </div>
            <span className="text-[10px] text-[#6b7280] mt-1">لم تنطلق بعد</span>
          </>
        ) : (
          <>
            <div className={`flex items-center gap-2 px-4 py-1 rounded-full border text-base font-black ${
              status.key === 'live' 
                ? 'border-[#ef4444]/50 bg-[#ef4444]/10' 
                : 'border-[#1f2937] bg-[#1f2937]/50'
            }`}>
              <span className="text-[#10b981]">{scoreDisplay(home.score)}</span>
              <span className="text-[#6b7280] text-xs">-</span>
              <span className="text-[#6366f1]">{scoreDisplay(away.score)}</span>
            </div>
            {status.key === 'live' ? (
              <LiveBadge text={status.text} />
            ) : (
              <span className="text-[10px] text-[#6b7280] mt-1">{status.text}</span>
            )}
          </>
        )}
        {tv && (
          <span className="text-[9px] text-[#6366f1] mt-1 truncate max-w-[120px]">📺 {tv}</span>
        )}
      </div>

      {/* Away Team */}
      <div className="flex-1 flex items-center gap-3">
        <img
          src={`${IMG_URL}${away.id}`}
          alt={away.name}
          width={36}
          height={36}
          className="w-9 h-9 object-contain"
          onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
        />
        <span className="text-sm font-bold text-[#f9fafb] group-hover:text-[#6366f1] transition-colors truncate max-w-[120px]">
          {away.name}
        </span>
      </div>
    </Link>
  );
}
