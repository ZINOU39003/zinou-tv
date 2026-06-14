'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { fetchSearch } from '@/lib/api';
import { IMG_URL, DEF_LOGO } from '@/lib/utils';

export default function SearchPage() {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState<any>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // Debounced search query
  useEffect(() => {
    if (!query.trim()) {
      setResults(null);
      setLoading(false);
      return;
    }

    const delayDebounceFn = setTimeout(async () => {
      setLoading(true);
      setError(null);
      try {
        const res = await fetchSearch(query);
        setResults(res);
      } catch (err) {
        console.error('Error fetching search results:', err);
        setError('حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.');
      } finally {
        setLoading(false);
      }
    }, 400); // 400ms debounce delay

    return () => clearTimeout(delayDebounceFn);
  }, [query]);

  const competitions = results?.competitions || [];
  const competitors = results?.competitors || [];

  return (
    <div className="min-h-screen bg-[#0a0e17] pb-16 pt-6">
      <div className="max-w-3xl mx-auto px-4">
        {/* Search Header */}
        <div className="text-center mb-8">
          <h1 className="text-2xl font-black text-white">البحث السريع</h1>
          <p className="text-xs text-[#6b7280] font-bold mt-1.5">
            ابحث عن فرقك المفضلة، اللاعبين، أو الدوريات العالمية
          </p>
        </div>

        {/* Search Input Box */}
        <div className="relative mb-8">
          <input
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="اكتب اسم الفريق أو الدوري هنا..."
            className="w-full px-6 py-4 pr-12 rounded-2xl bg-[#111827] border border-[#1f2937] text-sm font-bold text-white placeholder-[#6b7280] focus:outline-none focus:border-[#10b981] focus:ring-2 focus:ring-[#10b981]/15 transition-all shadow-lg shadow-black/15"
          />
          <span className="absolute right-4 top-1/2 -translate-y-1/2 text-xl pointer-events-none">
            🔍
          </span>
          {loading && (
            <span className="absolute left-4 top-1/2 -translate-y-1/2 flex h-4 w-4">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#10b981] opacity-75"></span>
              <span className="relative inline-flex rounded-full h-4 w-4 bg-[#10b981]"></span>
            </span>
          )}
        </div>

        {error && (
          <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/35 text-center text-xs font-bold text-red-500 mb-6">
            {error}
          </div>
        )}

        {/* Results Container */}
        {!query.trim() && (
          <div className="text-center py-20 bg-[#111827]/40 border border-[#1f2937]/50 rounded-3xl p-6 backdrop-blur-sm">
            <span className="text-5xl block mb-4">⚽</span>
            <h2 className="text-sm font-black text-[#f9fafb]">ابدأ البحث الآن</h2>
            <p className="text-[10px] text-[#6b7280] font-bold mt-1.5 max-w-xs mx-auto">
              اكتب اسم أي بطولة أو نادٍ رياضي لمشاهدة جدول الترتيب، تفاصيل اللاعبين، والمباريات والنتائج مباشرة.
            </p>
          </div>
        )}

        {query.trim() && !loading && competitions.length === 0 && competitors.length === 0 && (
          <div className="text-center py-20 bg-[#111827]/40 border border-[#1f2937]/50 rounded-3xl p-6 backdrop-blur-sm">
            <span className="text-5xl block mb-4">🧐</span>
            <h2 className="text-sm font-black text-[#f9fafb]">لا توجد نتائج</h2>
            <p className="text-[10px] text-[#6b7280] font-bold mt-1.5">
              لم نجد أي دوري أو فريق يطابق "{query}". جرب استخدام كلمات مفتاحية أخرى.
            </p>
          </div>
        )}

        {/* Results List */}
        {query.trim() && (competitions.length > 0 || competitors.length > 0) && (
          <div className="space-y-6">
            {/* Competitions Results */}
            {competitions.length > 0 && (
              <div className="bg-[#111827] border border-[#1f2937] rounded-3xl p-5 shadow-lg shadow-black/15">
                <h3 className="text-xs font-black text-[#9ca3af] mb-4 border-r-3 border-[#10b981] pr-2">
                  🏆 البطولات والدوريات ({competitions.length})
                </h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                  {competitions.map((comp: any) => (
                    <Link
                      key={comp.id}
                      href={`/competition/${comp.id}`}
                      className="flex items-center gap-3 p-3.5 rounded-2xl bg-[#1f2937]/35 border border-[#374151]/20 hover:border-[#10b981]/30 hover:bg-[#1f2937]/70 transition-all duration-300 group"
                    >
                      <div className="w-10 h-10 rounded-xl bg-[#1f2937] border border-[#374151]/55 flex items-center justify-center text-lg shadow-sm">
                        🏆
                      </div>
                      <div className="min-w-0">
                        <h4 className="text-xs font-black text-[#f9fafb] group-hover:text-[#10b981] transition-colors truncate">
                          {comp.name}
                        </h4>
                        <span className="text-[9px] font-bold text-[#6b7280] mt-0.5 block">
                          بطولة كرة قدم
                        </span>
                      </div>
                    </Link>
                  ))}
                </div>
              </div>
            )}

            {/* Competitors (Teams) Results */}
            {competitors.length > 0 && (
              <div className="bg-[#111827] border border-[#1f2937] rounded-3xl p-5 shadow-lg shadow-black/15">
                <h3 className="text-xs font-black text-[#9ca3af] mb-4 border-r-3 border-[#6366f1] pr-2">
                  🛡️ الفرق والأندية ({competitors.length})
                </h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                  {competitors.map((team: any) => (
                    <Link
                      key={team.id}
                      href={`/team/${team.id}`}
                      className="flex items-center gap-3 p-3.5 rounded-2xl bg-[#1f2937]/35 border border-[#374151]/20 hover:border-[#6366f1]/30 hover:bg-[#1f2937]/70 transition-all duration-300 group"
                    >
                      <div className="w-10 h-10 rounded-xl bg-[#1f2937] border border-[#374151]/55 p-2 flex items-center justify-center shadow-sm">
                        <img
                          src={`${IMG_URL}${team.id}`}
                          alt={team.name}
                          className="w-full h-full object-contain filter drop-shadow-sm group-hover:scale-105 transition-transform duration-300"
                          onError={(e) => {
                            (e.target as HTMLImageElement).src = DEF_LOGO;
                          }}
                        />
                      </div>
                      <div className="min-w-0">
                        <h4 className="text-xs font-black text-[#f9fafb] group-hover:text-[#6366f1] transition-colors truncate">
                          {team.name}
                        </h4>
                        <span className="text-[9px] font-bold text-[#6b7280] mt-0.5 block">
                          نادي رياضي
                        </span>
                      </div>
                    </Link>
                  ))}
                </div>
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
