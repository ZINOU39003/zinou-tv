'use client';

import Link from 'next/link';
import { IMG_URL, DEF_LOGO } from '@/lib/utils';

interface StandingsRow {
  position: number;
  points: number;
  play: number;
  won: number;
  draw: number;
  lost: number;
  goalsFor: number;
  goalsAgainst: number;
  goalsDiff: number;
  competitor: {
    id: number;
    name: string;
  };
}

interface StandingsTableProps {
  standingsData: { standings?: { rows?: StandingsRow[] }[] } | null;
}

export default function StandingsTable({ standingsData }: StandingsTableProps) {
  const rows = standingsData?.standings?.[0]?.rows || [];

  if (rows.length === 0) {
    return (
      <div className="text-center py-12 text-sm font-bold text-[#6b7280]">
        📊 جدول الترتيب غير متوفر لهذه البطولة حالياً.
      </div>
    );
  }

  return (
    <div className="bg-[#111827] border border-[#1f2937] rounded-2xl overflow-hidden shadow-lg shadow-black/10">
      <div className="overflow-x-auto">
        <table className="w-full text-right text-xs border-collapse">
          <thead>
            <tr className="bg-[#1f2937]/40 text-[#9ca3af] border-b border-[#1f2937] font-black">
              <th className="py-4 px-4 text-center w-12">#</th>
              <th className="py-4 px-3 text-right">الفريق</th>
              <th className="py-4 px-3 text-center w-12">لعب</th>
              <th className="py-4 px-2 text-center w-10">فاز</th>
              <th className="py-4 px-2 text-center w-10">تعادل</th>
              <th className="py-4 px-2 text-center w-10">خسر</th>
              <th className="py-4 px-3 text-center w-16">له/عليه</th>
              <th className="py-4 px-3 text-center w-14">الفارق</th>
              <th className="py-4 px-4 text-center w-16 text-[#10b981]">النقاط</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-[#1f2937]">
            {rows.map((row) => {
              const comp = row.competitor;
              const isTop = row.position <= 4;
              const isBottom = row.position >= rows.length - 3;

              return (
                <tr
                  key={comp.id}
                  className="hover:bg-[#1f2937]/25 transition-colors font-bold text-[#f9fafb]"
                >
                  <td className="py-3.5 px-4 text-center">
                    <span
                      className={`inline-flex items-center justify-center w-6 h-6 rounded-md text-[11px] font-black ${
                        isTop
                          ? 'bg-[#10b981]/15 text-[#10b981]'
                          : isBottom
                          ? 'bg-[#ef4444]/15 text-[#ef4444]'
                          : 'bg-[#1f2937] text-[#9ca3af]'
                      }`}
                    >
                      {row.position}
                    </span>
                  </td>
                  <td className="py-3.5 px-3">
                    <Link href={`/team/${comp.id}`} className="flex items-center gap-3 group cursor-pointer">
                      <img
                        src={`${IMG_URL}${comp.id}`}
                        alt={comp.name}
                        width={24}
                        height={24}
                        className="w-6 h-6 object-contain group-hover:scale-105 transition-transform"
                        onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
                      />
                      <span className="text-xs font-black text-[#f9fafb] group-hover:text-[#10b981] transition-colors truncate max-w-[150px]">
                        {comp.name}
                      </span>
                    </Link>
                  </td>
                  <td className="py-3.5 px-3 text-center text-[#9ca3af]">{row.play}</td>
                  <td className="py-3.5 px-2 text-center text-[#9ca3af]">{row.won}</td>
                  <td className="py-3.5 px-2 text-center text-[#9ca3af]">{row.draw}</td>
                  <td className="py-3.5 px-2 text-center text-[#9ca3af]">{row.lost}</td>
                  <td className="py-3.5 px-3 text-center text-[#6b7280]">
                    {row.goalsFor}:{row.goalsAgainst}
                  </td>
                  <td className={`py-3.5 px-3 text-center font-black ${
                    row.goalsDiff > 0 ? 'text-[#10b981]' : row.goalsDiff < 0 ? 'text-[#ef4444]' : 'text-[#6b7280]'
                  }`}>
                    {row.goalsDiff > 0 ? `+${row.goalsDiff}` : row.goalsDiff}
                  </td>
                  <td className="py-3.5 px-4 text-center font-black text-[13px] text-[#10b981] bg-[#10b981]/5">
                    {row.points}
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
