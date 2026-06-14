'use client';

import { use, useState, useEffect, useCallback } from 'react';
import Link from 'next/link';
import { fetchStandings, fetchTopScorers } from '@/lib/api';
import StandingsTable from '@/components/competition/StandingsTable';
import TopScorers from '@/components/competition/TopScorers';

type CompTabType = 'standings' | 'scorers';

export default function CompetitionPage({
  params,
  searchParams,
}: {
  params: Promise<{ compId: string }>;
  searchParams: Promise<{ tab?: string }>;
}) {
  const { compId } = use(params);
  const { tab } = use(searchParams);
  const cid = parseInt(compId);

  const [activeTab, setActiveTab] = useState<CompTabType>((tab as CompTabType) || 'standings');
  const [standingsData, setStandingsData] = useState<any>(null);
  const [topScorersData, setTopScorersData] = useState<any>(null);

  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [compName, setCompName] = useState<string>('تفاصيل البطولة');

  // Load standings/metadata first
  const loadInitialData = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      // Fetch standings as it has both standings and basic metadata
      const res = await fetchStandings(cid);
      setStandingsData(res);
      
      const resolvedName = res.standings?.[0]?.displayName || 'البطولة';
      setCompName(resolvedName);
    } catch {
      // Standings might not be active, but let's try to get scorers if that tab was requested
      if (activeTab === 'scorers') {
        try {
          const res = await fetchTopScorers(cid);
          setTopScorersData(res);
          const resolvedName = res.stats?.athletesStats?.[0]?.competitionName || 'البطولة';
          setCompName(resolvedName);
        } catch {
          setError('تعذّر تحميل بيانات البطولة.');
        }
      } else {
        setError('جدول الترتيب غير متوفر لهذه البطولة حالياً.');
      }
    } finally {
      setLoading(false);
    }
  }, [cid, activeTab]);

  useEffect(() => {
    loadInitialData();
  }, [loadInitialData]);

  // Load scorers lazily
  useEffect(() => {
    if (activeTab === 'scorers' && !topScorersData) {
      const loadScorers = async () => {
        try {
          const res = await fetchTopScorers(cid);
          setTopScorersData(res);
        } catch (err) {
          console.error('Error fetching scorers:', err);
        }
      };
      loadScorers();
    }
  }, [activeTab, cid, topScorersData]);

  // Handle tab switch
  const handleTabSwitch = (newTab: CompTabType) => {
    setActiveTab(newTab);
    // Push state to URL without full refresh
    const url = new URL(window.location.href);
    url.searchParams.set('tab', newTab);
    window.history.pushState({}, '', url.toString());
  };

  if (loading) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-8 space-y-6">
        <div className="h-20 skeleton" />
        <div className="h-12 skeleton" />
        <div className="h-96 skeleton" />
      </div>
    );
  }

  return (
    <div className="pb-16 bg-[#0a0e17] min-h-screen">
      {/* Competition Header */}
      <div className="w-full bg-gradient-to-b from-[#111827] to-[#0a0e17] border-b border-[#1f2937] py-8 px-4 text-center">
        <div className="max-w-3xl mx-auto">
          <div className="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#1f2937]/35 border border-[#374151]/20 text-3xl mb-3 shadow-lg shadow-black/25">
            🏆
          </div>
          <h1 className="text-xl font-black text-[#f9fafb]">{compName}</h1>
          <p className="text-[10px] font-bold text-[#6b7280] mt-1.5">
            منصة Zinou TV الرياضية
          </p>
        </div>
      </div>

      {/* Tabs */}
      <div className="max-w-3xl mx-auto mt-6 px-4">
        <div className="bg-[#111827] border border-[#1f2937] rounded-2xl overflow-hidden shadow-lg shadow-black/15">
          <div className="flex bg-[#111827] border-b border-[#1f2937]">
            <button
              onClick={() => handleTabSwitch('standings')}
              className={`flex-1 text-center py-4 text-xs font-black transition-all border-b-2 flex items-center justify-center gap-1.5 ${
                activeTab === 'standings'
                  ? 'text-[#10b981] border-[#10b981] bg-[#1f2937]/30'
                  : 'text-[#9ca3af] border-transparent hover:text-white hover:bg-[#1f2937]/10'
              }`}
            >
              <span>📊</span>
              <span>الترتيب</span>
            </button>
            <button
              onClick={() => handleTabSwitch('scorers')}
              className={`flex-1 text-center py-4 text-xs font-black transition-all border-b-2 flex items-center justify-center gap-1.5 ${
                activeTab === 'scorers'
                  ? 'text-[#10b981] border-[#10b981] bg-[#1f2937]/30'
                  : 'text-[#9ca3af] border-transparent hover:text-white hover:bg-[#1f2937]/10'
              }`}
            >
              <span>🥇</span>
              <span>الهدافون</span>
            </button>
          </div>

          <div className="p-5">
            {error && activeTab === 'standings' ? (
              <div className="text-center py-12">
                <div className="text-4xl mb-4">📊</div>
                <p className="text-[#9ca3af] font-bold text-sm">{error}</p>
                <Link
                  href="/"
                  className="mt-6 inline-block px-5 py-2 rounded-xl bg-[#1f2937] border border-[#374151] text-xs font-bold text-white hover:bg-[#1f2937]/50"
                >
                  العودة للرئيسية
                </Link>
              </div>
            ) : activeTab === 'standings' ? (
              <StandingsTable standingsData={standingsData} />
            ) : (
              <TopScorers topScorersData={topScorersData} />
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
