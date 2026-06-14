'use client';

import { use, useState, useEffect, useCallback } from 'react';
import Link from 'next/link';
import { fetchPlayerDetail } from '@/lib/api';
import { PLAYER_IMG, IMG_URL, DEF_LOGO } from '@/lib/utils';

export default function PlayerPage({
  params,
}: {
  params: Promise<{ playerId: string }>;
}) {
  const { playerId } = use(params);
  const aid = parseInt(playerId);

  const [player, setPlayer] = useState<any>(null);
  const [club, setClub] = useState<any>(null);
  const [nationalTeam, setNationalTeam] = useState<any>(null);
  
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const loadPlayerData = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const res = await fetchPlayerDetail(aid);
      const athlete = res.athletes?.[0];
      if (!athlete) {
        throw new Error('اللاعب غير موجود');
      }

      setPlayer(athlete);

      // Find club team from competitors
      const competitors = res.competitors || [];
      const userClub = competitors.find((c: any) => c.id === athlete.clubId);
      if (userClub) {
        setClub(userClub);
      }

      // Find national team if available
      const userNat = competitors.find((c: any) => c.id === athlete.nationalTeamId);
      if (userNat) {
        setNationalTeam(userNat);
      }

    } catch (err: any) {
      console.error('Error loading player data:', err);
      setError('تعذّر تحميل بيانات اللاعب.');
    } finally {
      setLoading(false);
    }
  }, [aid]);

  useEffect(() => {
    loadPlayerData();
  }, [loadPlayerData]);

  if (loading) {
    return (
      <div className="max-w-2xl mx-auto px-4 py-12 space-y-6">
        <div className="h-48 skeleton rounded-3xl" />
        <div className="h-32 skeleton rounded-2xl" />
      </div>
    );
  }

  if (error || !player) {
    return (
      <div className="max-w-md mx-auto px-4 py-16 text-center">
        <div className="text-5xl mb-4">👤</div>
        <p className="text-sm font-bold text-[#9ca3af]">{error || 'تعذّر العثور على اللاعب.'}</p>
        <Link
          href="/"
          className="mt-6 inline-block px-6 py-2.5 rounded-xl bg-[#1f2937] border border-[#374151] text-xs font-black text-white hover:bg-[#1f2937]/50 transition-colors"
        >
          العودة للرئيسية
        </Link>
      </div>
    );
  }

  const pImg = `${PLAYER_IMG}${player.id}`;
  const teamColor = club?.color || '#10b981';

  return (
    <div className="pb-16 bg-[#0a0e17] min-h-screen">
      {/* Player Header Card */}
      <div className="relative w-full bg-gradient-to-b from-[#111827] to-[#0a0e17] border-b border-[#1f2937] py-12 px-4">
        {/* Glow backdrop */}
        <div 
          className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 rounded-full blur-[100px] opacity-10 pointer-events-none"
          style={{ backgroundColor: teamColor }}
        />

        <div className="relative max-w-2xl mx-auto flex flex-col items-center text-center z-10">
          {/* Back Button */}
          <div className="w-full flex justify-end mb-4">
            <button 
              onClick={() => window.history.back()}
              className="px-4 py-2 rounded-xl bg-[#1f2937]/50 border border-[#374151]/30 text-xs font-bold text-[#9ca3af] hover:text-white transition-colors cursor-pointer"
            >
              ◀ العودة
            </button>
          </div>

          {/* Player Picture */}
          <div className="relative mb-5 group">
            <div className="absolute inset-0 rounded-full blur-md opacity-20 group-hover:opacity-35 transition-opacity" style={{ backgroundColor: teamColor }} />
            <div className="relative w-28 h-28 md:w-32 md:h-32 rounded-full border-3 border-white bg-[#1f2937]/50 overflow-hidden shadow-xl shadow-black/35 flex items-center justify-center">
              <img
                src={pImg}
                alt={player.name}
                className="w-full h-full object-cover"
                onError={(e) => {
                  (e.target as HTMLImageElement).src = 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/default';
                }}
              />
            </div>
          </div>

          {/* Player Info */}
          <h1 className="text-xl md:text-2xl font-black text-[#f9fafb]">{player.name}</h1>
          <p className="text-xs font-bold text-[#10b981] mt-1">
            {player.position?.name || 'لاعب كرة قدم'}
          </p>

          {player.jerseyNumber && (
            <span className="mt-3 px-3 py-1 rounded-md bg-[#1f2937] border border-[#374151] text-xs font-black text-[#9ca3af]">
              الرقم #{player.jerseyNumber}
            </span>
          )}
        </div>
      </div>

      {/* Profile Details */}
      <div className="max-w-2xl mx-auto mt-6 px-4 space-y-6">
        {/* Personal details card */}
        <div className="bg-[#111827] border border-[#1f2937] rounded-3xl p-6 shadow-lg shadow-black/15">
          <h3 className="text-xs font-black text-[#9ca3af] mb-4 border-r-3 border-[#10b981] pr-2">
            👤 المعلومات الشخصية
          </h3>
          <div className="grid grid-cols-2 gap-4 text-right">
            {player.age && (
              <div className="p-3.5 rounded-xl bg-[#1f2937]/35 border border-[#374151]/20">
                <span className="text-[10px] text-[#6b7280] font-bold block mb-1">العمر</span>
                <span className="text-xs font-black text-white">{player.age} عاماً</span>
              </div>
            )}
            
            {player.nationalityName && (
              <div className="p-3.5 rounded-xl bg-[#1f2937]/35 border border-[#374151]/20">
                <span className="text-[10px] text-[#6b7280] font-bold block mb-1">الجنسية</span>
                <span className="text-xs font-black text-white">🌍 {player.nationalityName}</span>
              </div>
            )}

            {player.status && (
              <div className="p-3.5 rounded-xl bg-[#1f2937]/35 border border-[#374151]/20">
                <span className="text-[10px] text-[#6b7280] font-bold block mb-1">الحالة</span>
                <span className="text-xs font-black text-white">{player.status}</span>
              </div>
            )}

            {player.gender && (
              <div className="p-3.5 rounded-xl bg-[#1f2937]/35 border border-[#374151]/20">
                <span className="text-[10px] text-[#6b7280] font-bold block mb-1">الجنس</span>
                <span className="text-xs font-black text-white">
                  {player.gender === 'Male' || player.gender === 'male' ? 'ذكر' : 'أنثى'}
                </span>
              </div>
            )}
          </div>
        </div>

        {/* Club / National Team Card */}
        {(club || nationalTeam) && (
          <div className="bg-[#111827] border border-[#1f2937] rounded-3xl p-6 shadow-lg shadow-black/15">
            <h3 className="text-xs font-black text-[#9ca3af] mb-4 border-r-3 border-[#6366f1] pr-2">
              🛡️ الفريق والمنتخب الوطني
            </h3>
            <div className="space-y-4">
              {/* Club info */}
              {club && (
                <Link
                  href={`/team/${club.id}`}
                  className="flex items-center justify-between p-4 rounded-2xl bg-[#1f2937]/35 border border-[#374151]/20 hover:border-[#10b981]/35 hover:bg-[#1f2937]/75 transition-all duration-300 group"
                >
                  <div className="flex items-center gap-3">
                    <img
                      src={`${IMG_URL}${club.id}`}
                      alt={club.name}
                      className="w-10 h-10 object-contain"
                      onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
                    />
                    <div>
                      <h4 className="text-xs font-black text-white group-hover:text-[#10b981] transition-colors">
                        {club.name}
                      </h4>
                      <span className="text-[9px] font-bold text-[#6b7280] mt-0.5 block">النادي الحالي</span>
                    </div>
                  </div>
                  <span className="text-xs text-[#9ca3af] group-hover:text-white transition-colors">عرض الصفحة ◀</span>
                </Link>
              )}

              {/* National team info */}
              {nationalTeam && (
                <Link
                  href={`/team/${nationalTeam.id}`}
                  className="flex items-center justify-between p-4 rounded-2xl bg-[#1f2937]/35 border border-[#374151]/20 hover:border-[#6366f1]/35 hover:bg-[#1f2937]/75 transition-all duration-300 group"
                >
                  <div className="flex items-center gap-3">
                    <img
                      src={`${IMG_URL}${nationalTeam.id}`}
                      alt={nationalTeam.name}
                      className="w-10 h-10 object-contain"
                      onError={(e) => { (e.target as HTMLImageElement).src = DEF_LOGO; }}
                    />
                    <div>
                      <h4 className="text-xs font-black text-white group-hover:text-[#6366f1] transition-colors">
                        {nationalTeam.name}
                      </h4>
                      <span className="text-[9px] font-bold text-[#6b7280] mt-0.5 block">المنتخب الوطني</span>
                    </div>
                  </div>
                  <span className="text-xs text-[#9ca3af] group-hover:text-white transition-colors">عرض الصفحة ◀</span>
                </Link>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
