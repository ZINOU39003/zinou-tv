'use client';

import { use, useState, useEffect, useCallback } from 'react';
import Link from 'next/link';
import { fetchCompetitorDetail, fetchCompetitorGames, fetchCompetitorSquad, fetchStandings } from '@/lib/api';
import TeamHeader from '@/components/team/TeamHeader';
import SquadList from '@/components/team/SquadList';
import TeamFixtures from '@/components/team/TeamFixtures';
import StandingsTable from '@/components/competition/StandingsTable';

type TeamTabType = 'fixtures' | 'squad' | 'standings';

export default function TeamPage({
  params,
  searchParams,
}: {
  params: Promise<{ teamId: string }>;
  searchParams: Promise<{ tab?: string }>;
}) {
  const { teamId } = use(params);
  const { tab } = use(searchParams);
  const tid = parseInt(teamId);

  const [activeTab, setActiveTab] = useState<TeamTabType>((tab as TeamTabType) || 'fixtures');
  const [competitor, setCompetitor] = useState<any>(null);
  const [games, setGames] = useState<any[]>([]);
  const [squadData, setSquadData] = useState<any>(null);
  const [standingsData, setStandingsData] = useState<any>(null);

  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const loadData = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      // Fetch details and games concurrently
      const [detailRes, gamesRes] = await Promise.all([
        fetchCompetitorDetail(tid),
        fetchCompetitorGames(tid),
      ]);

      const comp = detailRes.competitors?.[0];
      if (!comp) {
        throw new Error('الفريق غير موجود');
      }

      setCompetitor(comp);
      setGames(gamesRes.games || []);
    } catch (err: any) {
      console.error('Error loading team details:', err);
      setError('تعذّر تحميل بيانات الفريق.');
    } finally {
      setLoading(false);
    }
  }, [tid]);

  // Load initial data on mount
  useEffect(() => {
    loadData();
  }, [loadData]);

  // Load squad data lazily when squad tab is opened
  useEffect(() => {
    if (activeTab === 'squad' && !squadData) {
      const loadSquad = async () => {
        try {
          const squadRes = await fetchCompetitorSquad(tid);
          setSquadData(squadRes);
        } catch (err) {
          console.error('Error loading squad:', err);
        }
      };
      loadSquad();
    }
  }, [activeTab, tid, squadData]);

  // Load standings data lazily when standings tab is opened
  useEffect(() => {
    if (activeTab === 'standings' && !standingsData && competitor?.mainCompetitionId) {
      const loadStandings = async () => {
        try {
          const standingsRes = await fetchStandings(competitor.mainCompetitionId);
          setStandingsData(standingsRes);
        } catch (err) {
          console.error('Error loading standings:', err);
        }
      };
      loadStandings();
    }
  }, [activeTab, competitor, standingsData, tid]);

  const handleTabSwitch = (newTab: TeamTabType) => {
    setActiveTab(newTab);
    const url = new URL(window.location.href);
    url.searchParams.set('tab', newTab);
    window.history.pushState({}, '', url.toString());
  };

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-8 space-y-6">
        <div className="h-40 skeleton rounded-3xl" />
        <div className="h-12 skeleton rounded-xl" />
        <div className="space-y-4">
          <div className="h-20 skeleton rounded-2xl" />
          <div className="h-20 skeleton rounded-2xl" />
          <div className="h-20 skeleton rounded-2xl" />
        </div>
      </div>
    );
  }

  if (error || !competitor) {
    return (
      <div className="max-w-md mx-auto px-4 py-16 text-center">
        <div className="text-5xl mb-4">🛡️</div>
        <p className="text-sm font-bold text-[#9ca3af]">{error || 'تعذّر العثور على الفريق.'}</p>
        <Link
          href="/"
          className="mt-6 inline-block px-6 py-2.5 rounded-xl bg-[#1f2937] border border-[#374151] text-xs font-black text-white hover:bg-[#1f2937]/50 transition-colors"
        >
          العودة للرئيسية
        </Link>
      </div>
    );
  }

  return (
    <div className="pb-16 bg-[#0a0e17] min-h-screen">
      <TeamHeader competitor={competitor} />

      <div className="max-w-4xl mx-auto mt-6 px-4">
        {/* Navigation Tabs */}
        <div className="bg-[#111827] border border-[#1f2937] rounded-2xl overflow-hidden shadow-lg shadow-black/15">
          <div className="flex bg-[#111827] border-b border-[#1f2937]">
            <button
              onClick={() => handleTabSwitch('fixtures')}
              className={`flex-1 text-center py-4 text-xs font-black transition-all border-b-2 flex items-center justify-center gap-1.5 ${
                activeTab === 'fixtures'
                  ? 'text-[#10b981] border-[#10b981] bg-[#1f2937]/30'
                  : 'text-[#9ca3af] border-transparent hover:text-white hover:bg-[#1f2937]/10'
              }`}
            >
              <span>📅</span>
              <span>المباريات والنتائج</span>
            </button>
            <button
              onClick={() => handleTabSwitch('squad')}
              className={`flex-1 text-center py-4 text-xs font-black transition-all border-b-2 flex items-center justify-center gap-1.5 ${
                activeTab === 'squad'
                  ? 'text-[#10b981] border-[#10b981] bg-[#1f2937]/30'
                  : 'text-[#9ca3af] border-transparent hover:text-white hover:bg-[#1f2937]/10'
              }`}
            >
              <span>👕</span>
              <span>قائمة اللاعبين</span>
            </button>
            {competitor.mainCompetitionId && (
              <button
                onClick={() => handleTabSwitch('standings')}
                className={`flex-1 text-center py-4 text-xs font-black transition-all border-b-2 flex items-center justify-center gap-1.5 ${
                  activeTab === 'standings'
                    ? 'text-[#10b981] border-[#10b981] bg-[#1f2937]/30'
                    : 'text-[#9ca3af] border-transparent hover:text-white hover:bg-[#1f2937]/10'
                }`}
              >
                <span>📊</span>
                <span>جدول الترتيب</span>
              </button>
            )}
          </div>

          {/* Tab Content */}
          <div className="p-5">
            {activeTab === 'fixtures' ? (
              <TeamFixtures games={games} teamId={tid} />
            ) : activeTab === 'squad' ? (
              <SquadList squadData={squadData} />
            ) : (
              <StandingsTable standingsData={standingsData} />
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
