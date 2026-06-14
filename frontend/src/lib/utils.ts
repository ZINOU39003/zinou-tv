import { Game, MatchStatusInfo } from './types';

export function getMatchStatus(game: Game): MatchStatusInfo {
  const stId = game.statusId;
  let txt = game.statusText || 'لم تبدأ';
  let key: 'pre' | 'live' | 'done' = 'pre';

  if ([2, 130].includes(stId) || txt.includes('مباشر') || txt.includes('الشوط')) {
    key = 'live';
    txt = (game.gameTime && game.gameTime > 0) ? Math.floor(game.gameTime) + "'" : 'مباشر';
  } else if ([9, 31].includes(stId) || txt.includes('استراحة')) {
    key = 'live';
    txt = 'استراحة';
  } else if ([3, 4, 5, 6, 7, 12, 13, 22, 35, 36].includes(stId) || txt.includes('انتهت')) {
    key = 'done';
    txt = 'انتهت';
  }
  return { key, text: txt };
}

export function scoreDisplay(sc?: number): string {
  return (sc == null || sc === -1) ? '-' : String(sc);
}

export function formatMatchTime(startTime: string): string {
  const dt = new Date(startTime);
  return dt.toLocaleTimeString('ar-DZ', { hour: '2-digit', minute: '2-digit', hour12: false });
}

export function formatDate(offset: number): { display: string; api: string } {
  const d = new Date();
  d.setDate(d.getDate() + offset);
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const year = d.getFullYear();
  return {
    display: `${day}/${month}/${year}`,
    api: `${day}-${month}-${year}`,
  };
}

export const IMG_URL = 'https://imagecache.365scores.com/image/upload/f_png,w_60,h_60,c_limit/v2/competitors/';
export const IMG_URL_LARGE = 'https://imagecache.365scores.com/image/upload/f_png,w_120,h_120,c_limit/v2/competitors/';
export const PLAYER_IMG = 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/';
export const DEF_LOGO = 'https://a.espncdn.com/combiner/i?img=/i/teamlogos/soccer/500/default-team-logo-500.png';

// World Cup competition ID
export const WORLD_CUP_ID = 5930;
