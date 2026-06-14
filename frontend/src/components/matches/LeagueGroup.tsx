'use client';

import { useState } from 'react';
import { Game, Competition } from '@/lib/types';
import Link from 'next/link';
import MatchCard from './MatchCard';

interface LeagueGroupProps {
  competition: Competition;
  games: Game[];
}

export default function LeagueGroup({ competition, games }: LeagueGroupProps) {
  const [collapsed, setCollapsed] = useState(false);

  return (
    <div className="bg-[#111827] border border-[#1f2937] rounded-2xl mb-4 overflow-hidden">
      {/* League Header */}
      <div
        className="flex items-center justify-between px-5 py-3.5 bg-[#1f2937]/50 cursor-pointer hover:bg-[#1f2937] transition-colors"
        onClick={() => setCollapsed(!collapsed)}
      >
        <div className="flex items-center gap-3">
          <div className={`w-7 h-7 rounded-full border border-[#374151] flex items-center justify-center text-[#9ca3af] text-xs transition-transform duration-300 ${collapsed ? '-rotate-90' : ''}`}>
            ▼
          </div>
          <h3 className="font-extrabold text-sm text-[#f9fafb]">{competition.name}</h3>
        </div>
        <div className="flex gap-2">
          <Link
            href={`/competition/${competition.id}?tab=scorers`}
            onClick={(e) => { e.stopPropagation(); }}
            className="text-[10px] font-bold text-[#9ca3af] border border-[#374151] px-3 py-1 rounded-full hover:bg-[#374151] hover:text-white transition-colors"
          >
            الهدافون
          </Link>
          <Link
            href={`/competition/${competition.id}?tab=standings`}
            onClick={(e) => { e.stopPropagation(); }}
            className="text-[10px] font-bold text-[#9ca3af] border border-[#374151] px-3 py-1 rounded-full hover:bg-[#374151] hover:text-white transition-colors"
          >
            الترتيب
          </Link>
        </div>
      </div>

      {/* Match List */}
      {!collapsed && (
        <div>
          {games.map((game) => (
            <MatchCard key={game.id} game={game} />
          ))}
        </div>
      )}
    </div>
  );
}
