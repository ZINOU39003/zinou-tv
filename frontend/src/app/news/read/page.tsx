'use client';

import { useState, useEffect, useCallback, Suspense } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { fetchNewsArticle } from '@/lib/api';
import { ScrapedArticle } from '@/lib/types';
import AdSense from '@/components/ads/AdSense';

function formatRelativeTime(dateStr: string): string {
  try {
    const date = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
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

function ReadArticleContent() {
  const searchParams = useSearchParams();
  const router = useRouter();
  const url = searchParams.get('url') || '';
  const id = searchParams.get('id') || '';
  const dateStr = searchParams.get('date') || '';

  const [article, setArticle] = useState<ScrapedArticle | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const loadArticleContent = useCallback(async () => {
    if (!id && !url) {
      setError('رابط أو معرّف الخبر غير موجود أو غير صالح.');
      setLoading(false);
      return;
    }

    setLoading(true);
    setError(null);
    try {
      const data = await fetchNewsArticle(url, id);
      setArticle(data);
    } catch (err) {
      console.error(err);
      setError('تعذّر تحميل محتوى الخبر من المصدر حالياً.');
    } finally {
      setLoading(false);
    }
  }, [id, url]);

  useEffect(() => {
    loadArticleContent();
  }, [loadArticleContent]);

  if (error) {
    return (
      <div className="text-center py-20 bg-[#111827] border border-[#1f2937] rounded-3xl">
        <div className="text-5xl mb-4">⚠️</div>
        <p className="text-[#ef4444] font-bold text-lg">{error}</p>
        <div className="mt-6 flex justify-center gap-4">
          <Link
            href="/news"
            className="px-6 py-2.5 rounded-xl bg-[#1f2937] text-white font-bold text-sm border border-[#374151] hover:bg-[#111827]"
          >
            العودة للأخبار
          </Link>
          <button
            onClick={loadArticleContent}
            className="px-6 py-2.5 rounded-xl bg-[#10b981] text-white font-bold text-sm hover:bg-[#059669]"
          >
            إعادة المحاولة
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-3xl mx-auto">
      {/* Back Button */}
      <div className="mb-6">
        <button
          onClick={() => router.back()}
          className="group flex items-center gap-2 text-[#9ca3af] hover:text-white font-bold text-sm transition-colors"
        >
          <span>→</span>
          <span>العودة للأخبار</span>
        </button>
      </div>

      {loading ? (
        <div className="space-y-6">
          <div className="h-6 w-32 skeleton" />
          <div className="h-16 w-full skeleton" />
          <div className="h-[350px] w-full skeleton rounded-3xl" />
          <div className="space-y-4 pt-4">
            <div className="h-4 w-full skeleton" />
            <div className="h-4 w-full skeleton" />
            <div className="h-4 w-[90%] skeleton" />
            <div className="h-4 w-[95%] skeleton" />
            <div className="h-4 w-[80%] skeleton" />
          </div>
        </div>
      ) : article ? (
        <article className="space-y-8">
          {/* Metadata */}
          <div className="flex items-center gap-3 text-xs font-bold text-[#6b7280]">
            <span className="px-3 py-1 rounded-full bg-[#1f2937] text-[#10b981]">⚽ كرة القدم</span>
            {dateStr && (
              <>
                <span>•</span>
                <span>{formatRelativeTime(dateStr)}</span>
              </>
            )}
          </div>

          {/* Title */}
          <h1 className="text-2xl md:text-4xl font-black text-white leading-snug">
            {article.title}
          </h1>

          {/* Image */}
          {article.image && (
            <div className="relative overflow-hidden rounded-3xl border border-[#1f2937] shadow-xl aspect-video w-full">
              <img
                src={article.image}
                alt={article.title}
                className="w-full h-full object-cover"
                onError={(e) => {
                  (e.target as HTMLImageElement).src = 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=600&auto=format&fit=crop';
                }}
              />
            </div>
          )}

          {/* Ad below Image */}
          <AdSense slot="1000000005" format="horizontal" />

          {/* Body Paragraphs */}
          <div className="space-y-6 text-[#e5e7eb] text-base md:text-lg leading-relaxed font-semibold">
            {article.paragraphs.length > 0 ? (
              article.paragraphs.map((p, idx) => (
                <div key={idx} className="contents">
                  <p className="text-[#f9fafb]/90">
                    {p}
                  </p>
                  {idx === 2 && <AdSense slot="1000000006" format="fluid" />}
                  {idx === 5 && <AdSense slot="1000000007" format="auto" />}
                </div>
              ))
            ) : (
              <p className="text-[#6b7280] italic text-center py-6">
                تعذّر العثور على محتوى مقروء في هذه الصفحة.
              </p>
            )}
          </div>

          {/* Bottom Ad */}
          <AdSense slot="1000000008" format="horizontal" />

          {/* Source footer */}
          <div className="pt-8 border-t border-[#1f2937] text-xs font-bold text-[#6b7280] flex flex-col sm:flex-row justify-between items-center gap-3">
            <span>تم استخلاص هذا المقال تلقائياً بنظام قارئ الأخبار الذكي.</span>
            <a
              href={article.url}
              target="_blank"
              rel="noopener noreferrer"
              className="text-[#10b981] hover:underline flex items-center gap-1"
            >
              <span>مشاهدة المصدر الأصلي</span>
              <span>🔗</span>
            </a>
          </div>
        </article>
      ) : null}
    </div>
  );
}

export default function ReadArticlePage() {
  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      <Suspense
        fallback={
          <div className="max-w-3xl mx-auto space-y-6">
            <div className="h-6 w-32 skeleton" />
            <div className="h-16 w-full skeleton" />
            <div className="h-[350px] w-full skeleton rounded-3xl" />
          </div>
        }
      >
        <ReadArticleContent />
      </Suspense>
    </div>
  );
}
