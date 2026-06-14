'use client';

import { use, useState, useEffect, useCallback } from 'react';
import Link from 'next/link';
import { fetchMatchDetail, fetchMatchLineup, fetchMatchStats, fetchH2H } from '@/lib/api';
import { Game } from '@/lib/types';
import { getMatchStatus } from '@/lib/utils';
import MatchHeader from '@/components/match-detail/MatchHeader';
import MatchTabs, { TabType } from '@/components/match-detail/MatchTabs';
import EventsTimeline from '@/components/match-detail/EventsTimeline';
import LineupPitch from '@/components/match-detail/LineupPitch';
import StatsComparison from '@/components/match-detail/StatsComparison';
import H2HSection from '@/components/match-detail/H2HSection';
import AdSense from '@/components/ads/AdSense';

export default function MatchDetailPage({ params }: { params: Promise<{ gameId: string }> }) {
  const { gameId } = use(params);
  const gid = parseInt(gameId);

  const [game, setGame] = useState<Game | null>(null);
  const [lineupGame, setLineupGame] = useState<Game | null>(null);
  const [statsData, setStatsData] = useState<any>(null);
  const [h2hData, setH2hData] = useState<any>(null);

  const [activeTab, setActiveTab] = useState<TabType>('events');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Load basic match details
  const loadMatchDetails = useCallback(async () => {
    try {
      const res = await fetchMatchDetail(gid);
      setGame(res.game);
      setError(null);
    } catch {
      setError('تعذّر تحميل تفاصيل المباراة.');
    } finally {
      setLoading(false);
    }
  }, [gid]);

  useEffect(() => {
    loadMatchDetails();
  }, [loadMatchDetails]);

  // Auto-refresh for live matches
  useEffect(() => {
    if (!game) return;
    const status = getMatchStatus(game);
    if (status.key !== 'live') return;

    const interval = setInterval(() => {
      loadMatchDetails();
    }, 30000);

    return () => clearInterval(interval);
  }, [game, loadMatchDetails]);

  // Load tab-specific data lazily
  useEffect(() => {
    if (!game) return;

    const loadTabDetails = async () => {
      try {
        if (activeTab === 'lineup' && !lineupGame) {
          const res = await fetchMatchLineup(gid);
          setLineupGame(res.game);
        } else if (activeTab === 'stats' && !statsData) {
          const res = await fetchMatchStats(gid);
          setStatsData(res);
        } else if (activeTab === 'info' && !h2hData) {
          const res = await fetchH2H(gid).catch(() => null);
          setH2hData(res || { games: [] });
        }
      } catch (err) {
        console.error('Error loading tab details:', err);
      }
    };

    loadTabDetails();
  }, [activeTab, game, gid, lineupGame, statsData, h2hData]);

  if (loading) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-8 space-y-6">
        <div className="h-48 skeleton" />
        <div className="h-12 skeleton" />
        <div className="h-80 skeleton" />
      </div>
    );
  }

  if (error || !game) {
    return (
      <div className="text-center py-20">
        <div className="text-5xl mb-4">⚠️</div>
        <p className="text-[#ef4444] font-bold text-lg">{error || 'المباراة غير موجودة.'}</p>
        <Link
          href="/"
          className="mt-6 inline-block px-6 py-2.5 rounded-xl bg-[#10b981] text-white font-bold text-sm hover:bg-[#059669] transition-colors"
        >
          العودة للرئيسية
        </Link>
      </div>
    );
  }

  const status = getMatchStatus(game);
  const hasLineups = game.hasLineups || false;
  const hasStats = game.hasStats || false;

  return (
    <div className="pb-16 bg-[#0a0e17] min-h-screen">
      {/* Top Match Header */}
      <MatchHeader game={game} />

      {/* Tabs */}
      <div className="max-w-3xl mx-auto mt-6 px-4">
        {/* AdSense Match Banner */}
        <AdSense slot="1000000009" format="horizontal" className="mb-4 my-0" />

        <div className="bg-[#111827] border border-[#1f2937] rounded-2xl overflow-hidden shadow-lg shadow-black/15">
          <MatchTabs
            activeTab={activeTab}
            onSelectTab={setActiveTab}
            hasLineups={hasLineups}
            hasStats={hasStats}
          />

          <div className="p-5">
            {activeTab === 'events' && (
              <EventsTimeline game={game} />
            )}

            {activeTab === 'lineup' && lineupGame && (
              <LineupPitch game={lineupGame} />
            )}

            {activeTab === 'stats' && (
              <StatsComparison statsData={statsData} />
            )}

            {activeTab === 'info' && (
              <div className="space-y-6">
                {/* Stadium / Match Info Card */}
                <div className="bg-[#1f2937]/35 border border-[#374151]/20 p-5 rounded-2xl">
                  <h3 className="text-xs font-black text-[#9ca3af] mb-4">🏟️ معلومات الملعب والطاقم</h3>
                  <div className="space-y-3">
                    {game.venue && (
                      <div className="flex justify-between items-center text-xs font-bold">
                        <span className="text-[#6b7280]">الملعب:</span>
                        <span className="text-[#f9fafb]">{game.venue.name}</span>
                      </div>
                    )}
                    {game.officials && game.officials.length > 0 && (
                      <div className="flex justify-between items-center text-xs font-bold">
                        <span className="text-[#6b7280]">الحكم الرئيسي:</span>
                        <span className="text-[#f9fafb]">{game.officials[0].name}</span>
                      </div>
                    )}
                    <div className="flex justify-between items-center text-xs font-bold">
                      <span className="text-[#6b7280]">تاريخ المباراة:</span>
                      <span className="text-[#f9fafb]">
                        {new Date(game.startTime).toLocaleDateString('ar-DZ', {
                          weekday: 'long',
                          year: 'numeric',
                          month: 'long',
                          day: 'numeric',
                        })}
                      </span>
                    </div>
                  </div>
                </div>

                {/* H2H Section */}
                <H2HSection h2hData={h2hData} />
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
