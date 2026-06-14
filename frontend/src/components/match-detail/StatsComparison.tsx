'use client';

interface StatItem {
  name: string;
  homeValue: string;
  awayValue: string;
  type?: number;
}

interface StatGroup {
  title: string;
  stats: StatItem[];
}

interface StatsComparisonProps {
  statsData: { stats: StatGroup[] } | null;
}

export default function StatsComparison({ statsData }: StatsComparisonProps) {
  if (!statsData || !statsData.stats || statsData.stats.length === 0) {
    return (
      <div className="text-center py-12 text-sm font-bold text-[#6b7280]">
        📊 الإحصائيات التفصيلية غير متوفرة لهذه المباراة حالياً.
      </div>
    );
  }

  // Parse percentages for progress bars
  const getPcts = (home: string, away: string) => {
    // Strip everything except numbers (handles "55%" or "544 (88%)" etc.)
    const hMatch = home.match(/^(\d+)/);
    const aMatch = away.match(/^(\d+)/);
    const hNum = hMatch ? parseFloat(hMatch[1]) : 0;
    const aNum = aMatch ? parseFloat(aMatch[1]) : 0;

    if (hNum === 0 && aNum === 0) {
      return { h: 50, a: 50 };
    }
    const total = hNum + aNum;
    return {
      h: (hNum / total) * 100,
      a: (aNum / total) * 100,
    };
  };

  return (
    <div className="space-y-6">
      {statsData.stats.map((group, gIdx) => (
        <div key={gIdx} className="bg-[#111827] border border-[#1f2937] rounded-2xl p-6 shadow-lg shadow-black/10">
          <h3 className="text-sm font-black text-[#f9fafb] mb-6 flex items-center gap-2">
            <span>📈</span> {group.title}
          </h3>

          <div className="space-y-5">
            {group.stats.map((stat, sIdx) => {
              const { h: homePct, a: awayPct } = getPcts(stat.homeValue, stat.awayValue);

              return (
                <div key={sIdx} className="space-y-1.5">
                  {/* Label Row */}
                  <div className="flex items-center justify-between text-xs font-black">
                    <span className="text-[#10b981]">{stat.homeValue}</span>
                    <span className="text-[#9ca3af]">{stat.name}</span>
                    <span className="text-[#6366f1]">{stat.awayValue}</span>
                  </div>

                  {/* Visual Bar Split */}
                  <div className="w-full h-2 rounded-full bg-[#1f2937] overflow-hidden flex flex-row-reverse">
                    {/* Home Team Bar (RTL right-to-left, so home is right) */}
                    <div
                      style={{ width: `${homePct}%` }}
                      className="bg-gradient-to-l from-[#10b981] to-[#059669] h-full"
                    />
                    {/* Away Team Bar */}
                    <div
                      style={{ width: `${awayPct}%` }}
                      className="bg-gradient-to-r from-[#6366f1] to-[#4f46e5] h-full"
                    />
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      ))}
    </div>
  );
}
