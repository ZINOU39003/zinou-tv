import { ScoresResponse, Game, NewsResponse, ScrapedArticle } from './types';

const BASE = '/api/scores';

export async function fetchMatchesByDate(dateApi: string): Promise<ScoresResponse> {
  const res = await fetch(`${BASE}/date/${dateApi}`);
  if (!res.ok) throw new Error('Failed to fetch matches');
  return res.json();
}

export async function fetchMatchDetail(gameId: number): Promise<{ game: Game }> {
  const res = await fetch(`${BASE}/match/${gameId}`);
  if (!res.ok) throw new Error('Failed to fetch match detail');
  return res.json();
}

export async function fetchMatchLineup(gameId: number): Promise<{ game: Game }> {
  const res = await fetch(`${BASE}/lineup/${gameId}`);
  if (!res.ok) throw new Error('Failed to fetch lineup');
  return res.json();
}

export async function fetchMatchStats(gameId: number): Promise<{ game: Game }> {
  const res = await fetch(`${BASE}/stats/${gameId}`);
  if (!res.ok) throw new Error('Failed to fetch stats');
  return res.json();
}

export async function fetchStandings(competitionId: number): Promise<any> {
  const res = await fetch(`${BASE}/standings/${competitionId}`);
  if (!res.ok) throw new Error('Failed to fetch standings');
  return res.json();
}

export async function fetchTopScorers(competitionId: number): Promise<any> {
  const res = await fetch(`${BASE}/topscorers/${competitionId}`);
  if (!res.ok) throw new Error('Failed to fetch top scorers');
  return res.json();
}

export async function fetchH2H(gameId: number): Promise<any> {
  const res = await fetch(`${BASE}/h2h/${gameId}`);
  if (!res.ok) throw new Error('Failed to fetch h2h');
  return res.json();
}

export async function fetchCompetitorDetail(competitorId: number): Promise<any> {
  const res = await fetch(`${BASE}/competitor/${competitorId}`);
  if (!res.ok) throw new Error('Failed to fetch competitor');
  return res.json();
}

export async function fetchCompetitorGames(competitorId: number): Promise<any> {
  const res = await fetch(`${BASE}/competitor/games/${competitorId}`);
  if (!res.ok) throw new Error('Failed to fetch competitor games');
  return res.json();
}

export async function fetchCompetitorSquad(competitorId: number): Promise<any> {
  const res = await fetch(`${BASE}/competitor/${competitorId}/squad`);
  if (!res.ok) throw new Error('Failed to fetch competitor squad');
  return res.json();
}

export async function fetchSearch(query: string): Promise<any> {
  const res = await fetch(`${BASE}/search?q=${encodeURIComponent(query)}`);
  if (!res.ok) throw new Error('Failed to fetch search results');
  return res.json();
}

export async function fetchPlayerDetail(athleteId: number): Promise<any> {
  const res = await fetch(`${BASE}/player/${athleteId}`);
  if (!res.ok) throw new Error('Failed to fetch player details');
  return res.json();
}

export async function fetchNews(): Promise<NewsResponse> {
  const res = await fetch(`${BASE}/news`);
  if (!res.ok) throw new Error('Failed to fetch news');
  return res.json();
}

export async function fetchNewsArticle(url: string, id?: string): Promise<ScrapedArticle> {
  const query = id ? `id=${id}` : `url=${encodeURIComponent(url)}`;
  const res = await fetch(`${BASE}/news/article?${query}`);
  if (!res.ok) throw new Error('Failed to fetch news article');
  return res.json();
}


