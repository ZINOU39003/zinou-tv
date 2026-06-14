'use client';

import { Game, GameEvent } from '@/lib/types';

interface EventsTimelineProps {
  game: Game;
}

export default function EventsTimeline({ game }: EventsTimelineProps) {
  const events = game.events || [];
  const members = game.members || [];
  const homeId = game.homeCompetitor.id;
  const awayId = game.awayCompetitor.id;

  if (events.length === 0) {
    return (
      <div className="text-center py-12 text-sm font-bold text-[#6b7280]">
        ⚽ لا توجد أحداث مسجلة في هذه المباراة حتى الآن.
      </div>
    );
  }

  // Sort events chronologically (newest first for timeline)
  const sortedEvents = [...events].sort((a, b) => b.gameTime - a.gameTime);

  const getEventIcon = (typeId: number) => {
    switch (typeId) {
      case 1: // Goal
        return '⚽';
      case 2: // Yellow Card
        return '🟨';
      case 3: // Red Card
        return '🟥';
      case 4: // Substitution
        return '🔄';
      default:
        return '⚡';
    }
  };

  const getEventText = (event: GameEvent, pName: string) => {
    const typeId = event.eventType?.id;
    if (typeId === 1) return `هدف لصالح ${pName}`;
    if (typeId === 2) return `بطاقة صفراء للاعب ${pName}`;
    if (typeId === 3) return `بطاقة حمراء للاعب ${pName}`;
    if (typeId === 4) return `تبديل: ${pName}`;
    return event.eventType?.name || 'حدث';
  };

  return (
    <div className="relative border-r border-[#1f2937] mr-4 md:mr-8 py-6 space-y-6">
      {sortedEvents.map((ev, index) => {
        const player = members.find((m) => m.id === ev.playerId);
        const pName = player ? player.name : 'لاعب';
        const isHome = ev.competitorId === homeId;
        const icon = getEventIcon(ev.eventType?.id ?? 0);

        return (
          <div key={index} className="relative flex items-center justify-between">
            {/* Timeline Dot & Icon */}
            <div className="absolute -right-[15px] w-[30px] h-[30px] rounded-full bg-[#111827] border-2 border-[#1f2937] flex items-center justify-center text-xs z-10 shadow-md">
              {icon}
            </div>

            {/* Event Detail Container */}
            <div className={`w-full pr-8 flex ${isHome ? 'justify-start text-right' : 'justify-end text-left'}`}>
              <div className="max-w-[85%] bg-[#111827] border border-[#1f2937] rounded-2xl p-4 shadow-lg shadow-black/10 hover:border-[#10b981]/30 transition-colors">
                <div className="flex items-center gap-2 mb-1">
                  <span className="text-sm font-black text-[#10b981]">{ev.gameTime}'</span>
                  <span className="text-xs font-medium text-[#6b7280]">
                    {isHome ? game.homeCompetitor.name : game.awayCompetitor.name}
                  </span>
                </div>
                <p className="text-sm font-bold text-[#f9fafb]">
                  {getEventText(ev, pName)}
                </p>
              </div>
            </div>
          </div>
        );
      })}
    </div>
  );
}
