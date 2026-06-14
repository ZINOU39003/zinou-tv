'use client';

import Link from 'next/link';
import { PLAYER_IMG } from '@/lib/utils';

interface SquadListProps {
  squadData: any;
}

export default function SquadList({ squadData }: SquadListProps) {
  if (!squadData || !squadData.members) {
    return (
      <div className="text-center py-12 text-[#9ca3af] font-bold text-sm">
        لا توجد تشكيلة متوفرة لهذا الفريق حالياً.
      </div>
    );
  }

  const { members, competitor } = squadData;

  // Merge lineup members (which contain position) with athlete details (which contain name)
  const mappedMembers = competitor?.lineups?.members?.map((lm: any) => {
    const athleteInfo = members.find((m: any) => m.id === lm.id);
    return {
      ...lm,
      ...athleteInfo,
    };
  }) || [];

  // Group players by position
  const groups = {
    goalkeepers: [] as any[],
    defenders: [] as any[],
    midfielders: [] as any[],
    forwards: [] as any[],
    others: [] as any[],
  };

  mappedMembers.forEach((m: any) => {
    const posName = (m.position?.shortName || m.position?.name || '').toLowerCase();
    const posAr = m.position?.name || '';
    
    if (posName.includes('goalkeeper') || posAr.includes('حارس')) {
      groups.goalkeepers.push(m);
    } else if (posName.includes('defender') || posAr.includes('مدافع')) {
      groups.defenders.push(m);
    } else if (posName.includes('midfielder') || posAr.includes('وسط')) {
      groups.midfielders.push(m);
    } else if (posName.includes('forward') || posName.includes('winger') || posName.includes('striker') || posAr.includes('مهاجم')) {
      groups.forwards.push(m);
    } else {
      groups.others.push(m);
    }
  });

  const sections = [
    { title: '🧤 حراس المرمى', list: groups.goalkeepers },
    { title: '🛡️ المدافعون', list: groups.defenders },
    { title: '⚙️ لاعبو الوسط', list: groups.midfielders },
    { title: '⚽ المهاجمون', list: groups.forwards },
    { title: '🏃 لاعبون آخرون', list: groups.others },
  ].filter((s) => s.list.length > 0);

  if (sections.length === 0) {
    return (
      <div className="text-center py-12 text-[#9ca3af] font-bold text-sm">
        لا توجد تشكيلة متوفرة لهذا الفريق حالياً.
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {sections.map((sec) => (
        <div key={sec.title} className="space-y-4">
          <h3 className="text-sm font-black text-[#f9fafb] border-r-4 border-[#10b981] pr-3">
            {sec.title}
          </h3>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {sec.list.map((player) => {
              const athleteId = player.athleteId || player.id;
              const pImg = `${PLAYER_IMG}${athleteId}`;
              const displayPosition = player.position?.name || 'لاعب';
              const rating = player.ranking ? parseFloat(player.ranking).toFixed(1) : null;

              return (
                <Link
                  key={player.id}
                  href={`/player/${athleteId}`}
                  className="flex items-center justify-between p-4 rounded-2xl bg-[#111827] border border-[#1f2937] hover:border-[#10b981]/35 hover:bg-[#1f2937]/35 transition-all duration-300 group shadow-md cursor-pointer"
                >
                  <div className="flex items-center gap-4">
                    {/* Player Image */}
                    <div className="relative w-12 h-12 rounded-full overflow-hidden border border-[#374151] bg-[#1f2937]/50 flex-shrink-0">
                      <img
                        src={pImg}
                        alt={player.name || 'لاعب'}
                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                        onError={(e) => {
                          (e.target as HTMLImageElement).src = 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/default';
                        }}
                      />
                    </div>

                    <div>
                      <h4 className="text-xs font-black text-[#f9fafb] group-hover:text-[#10b981] transition-colors">
                        {player.name || 'لاعب'}
                      </h4>
                      <p className="text-[10px] font-bold text-[#6b7280] mt-0.5">
                        {displayPosition}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3">
                    {/* Player Rating if available */}
                    {rating && (
                      <span className="text-[10px] font-black text-white px-2 py-0.5 rounded bg-[#10b981]/15 border border-[#10b981]/25">
                        ⭐ {rating}
                      </span>
                    )}

                    {/* Jersey Number */}
                    {player.jerseyNumber && (
                      <span className="text-xs font-black text-[#9ca3af] bg-[#1f2937] px-2.5 py-1 rounded-lg border border-[#374151]/55">
                        #{player.jerseyNumber}
                      </span>
                    )}
                  </div>
                </Link>
              );
            })}
          </div>
        </div>
      ))}
    </div>
  );
}
