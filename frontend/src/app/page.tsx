'use client';

import { useState, useEffect, useCallback } from 'react';
import { fetchMatchesByDate } from '@/lib/api';
import { Game, Competition, ScoresResponse } from '@/lib/types';
import { getMatchStatus, formatDate, WORLD_CUP_ID } from '@/lib/utils';
import DaySelector from '@/components/matches/DaySelector';
import LeagueGroup from '@/components/matches/LeagueGroup';
import AdSense from '@/components/ads/AdSense';

export default function HomePage() {
  const [dayOffset, setDayOffset] = useState(0);
  const [data, setData] = useState<ScoresResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [searchTerm, setSearchTerm] = useState('');

  const loadMatches = useCallback(async (offset: number) => {
    setLoading(true);
    setError(null);
    try {
      const { api } = formatDate(offset);
      const result = await fetchMatchesByDate(api);
      setData(result);
    } catch {
      setError('تعذّر جلب البيانات. تأكد من تشغيل السيرفر.');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadMatches(dayOffset);
  }, [dayOffset, loadMatches]);

  // Auto-refresh every 30 seconds
  useEffect(() => {
    const interval = setInterval(() => {
      loadMatches(dayOffset);
    }, 30000);
    return () => clearInterval(interval);
  }, [dayOffset, loadMatches]);

  const handleDaySelect = (offset: number) => {
    setDayOffset(offset);
  };

  // Group games by competition
  const groupedGames = (() => {
    if (!data?.games) return [];

    let games = [...data.games];

    // Sort: live first, then upcoming, then finished
    games.sort((a, b) => {
      const sa = getMatchStatus(a).key;
      const sb = getMatchStatus(b).key;
      const w = { live: 1, pre: 2, done: 3 };
      if (w[sa] !== w[sb]) return w[sa] - w[sb];
      return new Date(a.startTime).getTime() - new Date(b.startTime).getTime();
    });

    // Filter by search
    if (searchTerm) {
      games = games.filter(
        (g) =>
          g.homeCompetitor.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
          g.awayCompetitor.name.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }

    // Group by competition
    const grouped: Record<number, { competition: Competition; games: Game[] }> = {};
    games.forEach((g) => {
      if (!grouped[g.competitionId]) {
        const comp = data.competitions?.find((c) => c.id === g.competitionId) || {
          id: g.competitionId,
          name: g.competitionDisplayName || 'بطولة أخرى',
        };
        grouped[g.competitionId] = { competition: comp, games: [] };
      }
      grouped[g.competitionId].games.push(g);
    });

    // Sort groups: World Cup first
    return Object.values(grouped).sort((a, b) => {
      const isWcA = a.competition.id === WORLD_CUP_ID || a.competition.name.includes('كأس العالم');
      const isWcB = b.competition.id === WORLD_CUP_ID || b.competition.name.includes('كأس العالم');
      if (isWcA && !isWcB) return -1;
      if (!isWcA && isWcB) return 1;
      // Then by number of live matches
      const liveA = a.games.filter((g) => getMatchStatus(g).key === 'live').length;
      const liveB = b.games.filter((g) => getMatchStatus(g).key === 'live').length;
      return liveB - liveA;
    });
  })();

  const { display } = formatDate(dayOffset);
  const totalGames = data?.games?.length || 0;
  const liveGames = data?.games?.filter((g) => getMatchStatus(g).key === 'live').length || 0;

  return (
    <div className="max-w-3xl mx-auto px-4 py-6">
      {/* Header */}
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="text-2xl font-black text-[#f9fafb]">
            Zinou <span className="text-[#10b981]">TV</span>
            <span className="text-sm font-bold text-[#6b7280] mr-3">- {display}</span>
          </h1>
        </div>
        <div className="flex items-center gap-3">
          {liveGames > 0 && (
            <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#ef4444]/10 border border-[#ef4444]/30">
              <span className="w-2 h-2 rounded-full bg-[#ef4444] animate-live-pulse" />
              <span className="text-xs font-bold text-[#ef4444]">{liveGames} مباشر</span>
            </div>
          )}
          <div className="text-xs font-bold text-[#6b7280] bg-[#1f2937] px-3 py-1.5 rounded-full">
            {totalGames} مباراة
          </div>
        </div>
      </div>

      {/* Top Ad Banner */}
      <AdSense slot="1000000000" format="horizontal" />

      {/* Day Selector */}
      <DaySelector currentOffset={dayOffset} onSelect={handleDaySelect} />

      {/* Search Bar */}
      <div className="flex gap-3 mb-6">
        <div className="flex-1 relative">
          <input
            type="text"
            placeholder="بحث عن مباراة..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full bg-[#111827] border border-[#1f2937] text-[#f9fafb] placeholder-[#6b7280] rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-[#10b981] transition-colors"
          />
          <svg
            className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[#6b7280]"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
        </div>
        <button className="bg-[#111827] border border-[#1f2937] text-[#9ca3af] px-4 rounded-xl text-sm font-bold hover:bg-[#1f2937] transition-colors">
          كل الدوريات
        </button>
      </div>

      {/* Match List */}
      {loading ? (
        <div className="space-y-4">
          {[1, 2, 3].map((i) => (
            <div key={i} className="bg-[#111827] border border-[#1f2937] rounded-2xl overflow-hidden">
              <div className="h-12 skeleton" />
              <div className="p-4 space-y-3">
                <div className="h-14 skeleton" />
                <div className="h-14 skeleton" />
              </div>
            </div>
          ))}
        </div>
      ) : error ? (
        <div className="text-center py-20">
          <div className="text-5xl mb-4">⚠️</div>
          <p className="text-[#ef4444] font-bold text-lg">{error}</p>
          <button
            onClick={() => loadMatches(dayOffset)}
            className="mt-4 px-6 py-2 rounded-xl bg-[#10b981] text-white font-bold text-sm hover:bg-[#059669] transition-colors"
          >
            إعادة المحاولة
          </button>
        </div>
      ) : groupedGames.length === 0 ? (
        <div className="text-center py-20">
          <div className="text-5xl mb-4">⚽</div>
          <p className="text-[#6b7280] font-bold text-lg">
            {searchTerm ? 'لا توجد نتائج للبحث' : 'لا توجد مباريات في هذا اليوم'}
          </p>
        </div>
      ) : (
        <div className="space-y-4">
          {groupedGames.map((group, idx) => (
            <div key={group.competition.id}>
              <LeagueGroup
                competition={group.competition}
                games={group.games}
              />
              {idx === 1 && <AdSense slot="1000000001" />}
            </div>
          ))}
        </div>
      )}

      {/* Auto-refresh indicator */}
      {!loading && data && (
        <div className="text-center mt-8 mb-4">
          <span className="text-[10px] text-[#374151] font-medium">
            يتم التحديث تلقائياً كل 30 ثانية • Zinou TV
          </span>
        </div>
      )}
    </div>
  );
}
