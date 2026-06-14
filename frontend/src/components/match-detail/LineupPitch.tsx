'use client';

import { useState } from 'react';
import Link from 'next/link';
import { Game } from '@/lib/types';
import { PLAYER_IMG, DEF_LOGO } from '@/lib/utils';

interface LineupPitchProps {
  game: Game;
}

export default function LineupPitch({ game }: LineupPitchProps) {
  const [selectedTeam, setSelectedTeam] = useState<'home' | 'away'>('home');

  const team = selectedTeam === 'home' ? game.homeCompetitor : game.awayCompetitor;
  const otherTeam = selectedTeam === 'home' ? game.awayCompetitor : game.homeCompetitor;

  const formationStr = team.lineups?.formation || '4-3-3';
  const members = team.lineups?.members || [];
  const allMembers = game.members || [];

  // Filter starters (statusId === 1) and substitutes (statusId === 2)
  // statusId is sometimes 1 or status is 1
  const starters = members.filter((m) => m.statusId === 1 || (m as any).status === 1);
  const substitutes = members.filter((m) => m.statusId === 2 || (m as any).status === 2);

  if (starters.length === 0) {
    return (
      <div className="text-center py-12 text-sm font-bold text-[#6b7280]">
        👕 التشكيلة غير متوفرة لهذه المباراة حالياً.
      </div>
    );
  }

  // Parse formation (e.g. "4-3-3" -> [1, 4, 3, 3])
  const rowsCount = [1, ...formationStr.split('-').map(Number)];

  // Group players by rows to render from top down (attackers at top, GK at bottom)
  const groupedStartersByRow = (() => {
    const rows: any[][] = [];
    let playerIdx = starters.length - 1; // start from end to slice
    const reversedRowsCount = [...rowsCount].reverse();

    reversedRowsCount.forEach((count) => {
      const startIdx = Math.max(0, playerIdx - count + 1);
      const rowPlayers = starters.slice(startIdx, playerIdx + 1);
      rows.push(rowPlayers);
      playerIdx -= count;
    });

    return rows; // outer array has Attackers first, GK last
  })();

  return (
    <div className="space-y-6">
      {/* Team Switcher */}
      <div className="flex bg-[#111827] border border-[#1f2937] p-1.5 rounded-xl gap-2">
        <button
          onClick={() => setSelectedTeam('home')}
          className={`flex-1 py-2.5 rounded-lg text-xs font-black transition-colors ${
            selectedTeam === 'home'
              ? 'bg-[#10b981] text-white shadow-md shadow-[#10b981]/20'
              : 'text-[#9ca3af] hover:text-white'
          }`}
        >
          {game.homeCompetitor.name}
        </button>
        <button
          onClick={() => setSelectedTeam('away')}
          className={`flex-1 py-2.5 rounded-lg text-xs font-black transition-colors ${
            selectedTeam === 'away'
              ? 'bg-[#6366f1] text-white shadow-md shadow-[#6366f1]/20'
              : 'text-[#9ca3af] hover:text-white'
          }`}
        >
          {game.awayCompetitor.name}
        </button>
      </div>

      {/* The Pitch */}
      <div className="relative w-full bg-[#1e4620] border-2 border-white rounded-2xl p-4 min-h-[480px] flex flex-col justify-between overflow-hidden shadow-2xl shadow-black/40">
        {/* Grass Stripes Pattern */}
        <div className="absolute inset-0 flex flex-col pointer-events-none opacity-20">
          {[...Array(10)].map((_, i) => (
            <div
              key={i}
              className={`flex-1 w-full ${i % 2 === 0 ? 'bg-black' : 'bg-transparent'}`}
            />
          ))}
        </div>

        {/* Pitch markings */}
        <div className="absolute top-1/2 left-0 w-full h-[2px] bg-white/40 -translate-y-1/2 pointer-events-none" />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-28 h-28 border-2 border-white/40 rounded-full pointer-events-none" />
        
        {/* Penalty Area Top */}
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-16 border-2 border-t-0 border-white/40 pointer-events-none" />
        {/* Goal Area Top */}
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-6 border-2 border-t-0 border-white/40 pointer-events-none" />
        
        {/* Penalty Area Bottom */}
        <div className="absolute bottom-0 left-1/2 -translate-x-1/2 w-48 h-16 border-2 border-b-0 border-white/40 pointer-events-none" />
        {/* Goal Area Bottom */}
        <div className="absolute bottom-0 left-1/2 -translate-x-1/2 w-24 h-6 border-2 border-b-0 border-white/40 pointer-events-none" />

        {/* Formation Display */}
        <div className="absolute top-4 right-4 bg-black/40 text-white text-[10px] font-black px-2.5 py-1 rounded-full border border-white/20 z-10">
          {formationStr}
        </div>

        {/* Rows of Players */}
        <div className="relative z-10 w-full h-full flex flex-col justify-between min-h-[440px] py-2">
          {groupedStartersByRow.map((row, rowIdx) => (
            <div key={rowIdx} className="flex justify-around items-center w-full">
              {row.map((m) => {
                const pInfo = allMembers.find((x) => x.id === m.id);
                const athleteId = pInfo?.athleteId || (m as any).athleteId || m.id;
                const pImg = `${PLAYER_IMG}${athleteId}`;

                return (
                  <Link 
                    key={m.id} 
                    href={`/player/${athleteId}`}
                    className="flex flex-col items-center justify-center w-16 group/player cursor-pointer hover:scale-105 transition-transform"
                  >
                    <div className="relative">
                      <div className="w-11 h-11 rounded-full border-2 border-white bg-[#111827] shadow-lg shadow-black/35 overflow-hidden flex items-center justify-center group-hover/player:border-[#10b981] transition-colors">
                        <img
                          src={pImg}
                          alt={pInfo?.name || 'لاعب'}
                          className="w-full h-full object-cover"
                          onError={(e) => {
                            (e.target as HTMLImageElement).src = 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/default';
                          }}
                        />
                      </div>
                      {m.jerseyNumber && (
                        <span className="absolute -bottom-1 -right-1 bg-black text-[#10b981] border border-[#374151] rounded-full text-[9px] font-black w-4 h-4 flex items-center justify-center">
                          {m.jerseyNumber}
                        </span>
                      )}
                    </div>
                    <span className="text-[10px] font-extrabold text-white text-center mt-1.5 shadow-sm bg-black/60 px-2 py-0.5 rounded border border-white/10 max-w-[65px] truncate block group-hover/player:bg-[#10b981]/80 transition-colors" title={pInfo?.name || 'لاعب'}>
                      {pInfo?.name || 'لاعب'}
                    </span>
                  </Link>
                );
              })}
            </div>
          ))}
        </div>
      </div>

      {/* Substitutes List */}
      {substitutes.length > 0 && (
        <div className="bg-[#111827] border border-[#1f2937] rounded-2xl p-5">
          <h3 className="text-sm font-black text-[#f9fafb] mb-4 flex items-center gap-2">
            <span>🔄</span> البدلاء
          </h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {substitutes.map((sub) => {
              const pInfo = allMembers.find((x) => x.id === sub.id);
              const athleteId = pInfo?.athleteId || (sub as any).athleteId || sub.id;
              const pImg = `${PLAYER_IMG}${athleteId}`;
              const posName = sub.position?.name || 'لاعب';

              return (
                <Link
                  key={sub.id}
                  href={`/player/${athleteId}`}
                  className="flex items-center justify-between p-3 rounded-xl bg-[#1f2937]/30 border border-[#374151]/20 hover:border-[#10b981]/30 hover:bg-[#1f2937]/50 transition-all duration-200 cursor-pointer"
                >
                  <div className="flex items-center gap-3">
                    <img
                      src={pImg}
                      alt={pInfo?.name || 'لاعب'}
                      className="w-9 h-9 rounded-full object-cover border border-[#374151]"
                      onError={(e) => {
                        (e.target as HTMLImageElement).src = 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/default';
                      }}
                    />
                    <div>
                      <h4 className="text-xs font-extrabold text-[#f9fafb]">{pInfo?.name || 'لاعب'}</h4>
                      <span className="text-[10px] font-medium text-[#6b7280]">{posName}</span>
                    </div>
                  </div>
                  {sub.jerseyNumber && (
                    <span className="text-xs font-black text-[#10b981] bg-[#10b981]/5 border border-[#10b981]/25 px-2 py-0.5 rounded-md">
                      #{sub.jerseyNumber}
                    </span>
                  )}
                </Link>
              );
            })}
          </div>
        </div>
      )}
    </div>
  );
}
