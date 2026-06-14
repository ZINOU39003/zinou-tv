'use client';

import Link from 'next/link';
import { Game } from '@/lib/types';
import { getMatchStatus, scoreDisplay, formatMatchTime, IMG_URL, DEF_LOGO } from '@/lib/utils';
import LiveBadge from '../matches/LiveBadge';

interface TeamFixturesProps {
  games: Game[];
  teamId: number;
}

export default function TeamFixtures({ games, teamId }: TeamFixturesProps) {
  if (!games || games.length === 0) {
    return (
      <div className="text-center py-12 text-[#9ca3af] font-bold text-sm">
        لا توجد مباريات مجدولة لهذا الفريق حالياً.
      </div>
    );
  }

  // Sort games: most recent or upcoming first
  const sortedGames = [...games].sort(
    (a, b) => new Date(b.startTime).getTime() - new Date(a.startTime).getTime()
  );

  return (
    <div className="space-y-4">
      {sortedGames.map((game) => {
        const home = game.homeCompetitor;
        const away = game.awayCompetitor;
        const status = getMatchStatus(game);
        
        // Parse date
        const matchDate = new Date(game.startTime).toLocaleDateString('ar-DZ', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
        });

        const isHome = home.id === teamId;

        return (
          <Link
            key={game.id}
            href={`/match/${game.id}`}
            className="flex items-center justify-between p-4 rounded-2xl bg-[#111827] border border-[#1f2937] hover:border-[#10b981]/35 hover:bg-[#1f2937]/35 transition-all duration-300 group shadow-md"
          >
            {/* Home Competitor */}
            <div className="flex-1 flex items-center justify-end gap-3 min-w-0">
              <span className={`text-xs font-bold truncate max-w-[120px] transition-colors ${
                isHome ? 'text-[#10b981] font-black' : 'text-[#f9fafb] group-hover:text-[#10b981]'
              }`}>
                {home.name}
              </span>
              <img
                src={`${IMG_URL}${home.id}`}
                alt={home.name}
                width={30}
                height={30}
                className="w-8 h-8 object-contain flex-shrink-0"
                onError={(e) => {
                  (e.target as HTMLImageElement).src = DEF_LOGO;
                }}
              />
            </div>

            {/* Score / Center Info */}
            <div className="w-[120px] flex flex-col items-center flex-shrink-0 mx-4">
              <span className="text-[9px] font-bold text-[#6b7280] mb-1 truncate max-w-[110px]">
                {game.competitionDisplayName || 'مباراة'}
              </span>

              {status.key === 'pre' ? (
                <div className="px-3 py-1 rounded-full bg-[#1f2937] text-white text-xs font-black border border-[#374151]/55">
                  {formatMatchTime(game.startTime)}
                </div>
              ) : (
                <div className={`flex items-center gap-2 px-3 py-1 rounded-full border text-sm font-black ${
                  status.key === 'live'
                    ? 'border-[#ef4444]/50 bg-[#ef4444]/10'
                    : 'border-[#1f2937] bg-[#1f2937]/50'
                }`}>
                  <span className="text-[#10b981]">{scoreDisplay(home.score)}</span>
                  <span className="text-[#6b7280] text-xs">-</span>
                  <span className="text-[#6366f1]">{scoreDisplay(away.score)}</span>
                </div>
              )}

              {status.key === 'live' ? (
                <div className="mt-1">
                  <LiveBadge text={status.text} />
                </div>
              ) : (
                <span className="text-[9px] font-bold text-[#6b7280] mt-1">{matchDate}</span>
              )}
            </div>

            {/* Away Competitor */}
            <div className="flex-1 flex items-center gap-3 min-w-0">
              <img
                src={`${IMG_URL}${away.id}`}
                alt={away.name}
                width={30}
                height={30}
                className="w-8 h-8 object-contain flex-shrink-0"
                onError={(e) => {
                  (e.target as HTMLImageElement).src = DEF_LOGO;
                }}
              />
              <span className={`text-xs font-bold truncate max-w-[120px] transition-colors ${
                !isHome ? 'text-[#10b981] font-black' : 'text-[#f9fafb] group-hover:text-[#6366f1]'
              }`}>
                {away.name}
              </span>
            </div>
          </Link>
        );
      })}
    </div>
  );
}
