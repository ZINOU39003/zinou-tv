'use client';

import { useState, useEffect, useCallback } from 'react';
import Link from 'next/link';
import { fetchNews } from '@/lib/api';
import { NewsItem, NewsResponse } from '@/lib/types';
import AdSense from '@/components/ads/AdSense';

function formatRelativeTime(dateStr: string): string {
  try {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    
    // Fallback if the publish date is in the future
    if (diffMs < 0) return 'الآن';

    const diffMins = Math.floor(diffMs / 60000);
    if (diffMins < 1) return 'الآن';
    if (diffMins < 60) return `منذ ${diffMins} دقيقة`;
    
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) {
      if (diffHours === 1) return 'منذ ساعة';
      if (diffHours === 2) return 'منذ ساعتين';
      if (diffHours <= 10) return `منذ ${diffHours} ساعات`;
      return `منذ ${diffHours} ساعة`;
    }
    
    const diffDays = Math.floor(diffHours / 24);
    if (diffDays === 1) return 'أمس';
    if (diffDays === 2) return 'منذ يومين';
    if (diffDays <= 10) return `منذ ${diffDays} أيام`;
    return `منذ ${diffDays} يوم`;
  } catch {
    return 'قبل فترة';
  }
}

export default function NewsPage() {
  const [data, setData] = useState<NewsResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const loadNews = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const result = await fetchNews();
      setData(result);
    } catch (err) {
      console.error(err);
      setError('تعذّر تحميل الأخبار الرياضية حالياً. تأكد من اتصالك بالسيرفر.');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadNews();
  }, [loadNews]);

  const newsItems = data?.news || [];
  const featuredArticle = newsItems[0];
  const otherArticles = newsItems.slice(1);

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between border-b border-[#1f2937] pb-6 mb-8 gap-4">
        <div>
          <h1 className="text-3xl font-black text-[#f9fafb] tracking-tight">
            آخر أخبار <span className="text-[#10b981]">كرة القدم</span>
          </h1>
          <p className="text-sm font-bold text-[#6b7280] mt-2">
            تغطية حية ومباشرة على مدار الساعة لآخر مستجدات الملاعب العالمية والمحلية
          </p>
        </div>
        <div>
          <button
            onClick={loadNews}
            className="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#1f2937] border border-[#374151] hover:border-[#10b981] hover:bg-[#111827] text-sm font-bold text-white transition-all"
            disabled={loading}
          >
            <span className={`${loading ? 'animate-spin' : ''}`}>🔄</span>
            تحديث الأخبار
          </button>
        </div>
      </div>

      {/* Main Content Area */}
      {loading ? (
        <div className="space-y-8">
          {/* Featured Loading */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 bg-[#111827] border border-[#1f2937] rounded-3xl overflow-hidden p-6">
            <div className="lg:col-span-2 h-[350px] skeleton rounded-2xl" />
            <div className="space-y-4 py-4">
              <div className="h-6 w-24 skeleton" />
              <div className="h-14 w-full skeleton" />
              <div className="h-6 w-32 skeleton" />
            </div>
          </div>
          {/* Grid Loading */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3].map((i) => (
              <div key={i} className="bg-[#111827] border border-[#1f2937] rounded-2xl overflow-hidden">
                <div className="h-48 skeleton" />
                <div className="p-5 space-y-4">
                  <div className="h-10 skeleton" />
                  <div className="h-5 w-24 skeleton" />
                </div>
              </div>
            ))}
          </div>
        </div>
      ) : error ? (
        <div className="text-center py-24 bg-[#111827] border border-[#1f2937] rounded-3xl">
          <div className="text-6xl mb-4">📰</div>
          <p className="text-[#ef4444] font-bold text-lg">{error}</p>
          <button
            onClick={loadNews}
            className="mt-6 px-8 py-3 rounded-xl bg-gradient-to-r from-[#10b981] to-[#059669] text-white font-extrabold text-sm hover:opacity-90 shadow-lg shadow-[#10b981]/30 transition-all"
          >
            إعادة المحاولة
          </button>
        </div>
      ) : newsItems.length === 0 ? (
        <div className="text-center py-24 bg-[#111827] border border-[#1f2937] rounded-3xl">
          <div className="text-6xl mb-4">📭</div>
          <p className="text-[#6b7280] font-bold text-lg">لا توجد أخبار متاحة حالياً.</p>
        </div>
      ) : (
        <div className="space-y-8">
          {/* Featured Article */}
          {featuredArticle && (
            <>
              <Link
                href={`/news/read?url=${encodeURIComponent(featuredArticle.url)}&id=${featuredArticle.id}&date=${encodeURIComponent(featuredArticle.publishDate)}`}
                className="group grid grid-cols-1 lg:grid-cols-3 gap-8 bg-[#111827]/60 hover:bg-[#111827] border border-[#1f2937] hover:border-[#10b981]/50 rounded-3xl overflow-hidden p-6 transition-all duration-300 cursor-pointer shadow-lg shadow-black/20"
              >
                <div className="lg:col-span-2 relative overflow-hidden rounded-2xl aspect-video lg:aspect-auto lg:h-[380px]">
                  <img
                    src={featuredArticle.image}
                    alt={featuredArticle.title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    onError={(e) => {
                      (e.target as HTMLImageElement).src = 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=600&auto=format&fit=crop';
                    }}
                  />
                  <div className="absolute top-4 right-4 bg-[#10b981] text-white text-xs font-black px-4 py-1.5 rounded-full shadow-lg">
                    خبر مميز
                  </div>
                </div>
                <div className="flex flex-col justify-between py-2">
                  <div className="space-y-4">
                    <div className="flex items-center gap-2 text-xs font-bold text-[#6b7280]">
                      <span>⚽ كرة القدم</span>
                      <span>•</span>
                      <span>{formatRelativeTime(featuredArticle.publishDate)}</span>
                    </div>
                    <h2 className="text-xl md:text-2xl font-black text-white leading-snug group-hover:text-[#10b981] transition-colors duration-200">
                      {featuredArticle.title}
                    </h2>
                  </div>
                  <div className="mt-6 lg:mt-0 flex items-center gap-2 text-[#10b981] font-black text-sm">
                    <span>اقرأ التفاصيل بالكامل</span>
                    <span className="transition-transform group-hover:translate-x-[-4px]">←</span>
                  </div>
                </div>
              </Link>
              
              {/* Ad below Featured Article */}
              <AdSense slot="1000000002" format="horizontal" />
            </>
          )}

          {/* Grid of Other Articles */}
          {otherArticles.length > 0 && (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {otherArticles.map((item, index) => (
                <div key={item.id} className="contents">
                  <Link
                    href={`/news/read?url=${encodeURIComponent(item.url)}&id=${item.id}&date=${encodeURIComponent(item.publishDate)}`}
                    className="group bg-[#111827]/60 hover:bg-[#111827] border border-[#1f2937] hover:border-[#10b981]/30 rounded-2xl overflow-hidden flex flex-col justify-between transition-all duration-300 cursor-pointer hover:-translate-y-1 shadow-md hover:shadow-lg shadow-black/10"
                  >
                    <div className="relative overflow-hidden aspect-video">
                      <img
                        src={item.image}
                        alt={item.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        onError={(e) => {
                          (e.target as HTMLImageElement).src = 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=600&auto=format&fit=crop';
                        }}
                      />
                    </div>
                    <div className="p-5 flex-1 flex flex-col justify-between">
                      <div className="space-y-3">
                        <div className="flex items-center gap-2 text-[10px] font-bold text-[#6b7280]">
                          <span>⚽ كرة القدم</span>
                          <span>•</span>
                          <span>{formatRelativeTime(item.publishDate)}</span>
                        </div>
                        <h3 className="font-extrabold text-sm text-[#f9fafb] leading-relaxed group-hover:text-[#10b981] transition-colors duration-200 line-clamp-3">
                          {item.title}
                        </h3>
                      </div>
                      <div className="mt-5 pt-3 border-t border-[#1f2937] flex items-center justify-between text-xs font-bold text-[#6b7280] group-hover:text-[#10b981] transition-colors">
                        <span>تفاصيل الخبر</span>
                        <span className="transition-transform group-hover:translate-x-[-3px]">←</span>
                      </div>
                    </div>
                  </Link>

                  {/* Interleaved Ad slots after 3rd, 9th, and 15th articles */}
                  {(index === 2 || index === 8 || index === 14) && (
                    <div className="bg-[#111827]/40 border border-dashed border-[#1f2937] rounded-2xl p-4 flex flex-col justify-center min-h-[300px]">
                      <AdSense slot={`100000000${3 + Math.floor(index / 6)}`} format="rectangle" />
                    </div>
                  )}
                </div>
              ))}
            </div>
          )}
        </div>
      )}
    </div>
  );
}
