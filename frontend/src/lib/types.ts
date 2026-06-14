export interface Competition {
  id: number;
  name: string;
  nameForURL?: string;
  color?: string;
  sportId?: number;
  countryId?: number;
  isDomesticLeague?: boolean;
}

export interface Competitor {
  id: number;
  name: string;
  score?: number;
  isQualified?: boolean;
  toQualify?: boolean;
  lineups?: {
    formation?: string;
    members?: LineupMember[];
  };
}

export interface LineupMember {
  id: number;
  jerseyNumber?: number;
  statusId?: number; // 1=starter, 2=substitute
  position?: { id: number; name: string };
}

export interface TvNetwork {
  id: number;
  name: string;
}

export interface GameEvent {
  eventType?: { id: number; name: string };
  playerId?: number;
  competitorId?: number;
  gameTime: number;
  stageId?: number;
  order?: number;
}

export interface Member {
  id: number;
  name: string;
  competitorId?: number;
  athleteId?: number;
  nationality?: { id: number; name: string };
}

export interface Game {
  id: number;
  competitionId: number;
  competitionDisplayName?: string;
  statusId: number;
  statusText?: string;
  gameTime?: number;
  startTime: string;
  homeCompetitor: Competitor;
  awayCompetitor: Competitor;
  tvNetworks?: TvNetwork[];
  hasLineups?: boolean;
  hasStats?: boolean;
  venue?: { id: number; name: string };
  officials?: { name: string; roleId?: number }[];
  events?: GameEvent[];
  members?: Member[];
}

export interface ScoresResponse {
  games: Game[];
  competitions: Competition[];
}

export type MatchStatus = 'pre' | 'live' | 'done';

export interface MatchStatusInfo {
  key: MatchStatus;
  text: string;
}

export interface NewsItem {
  id: number;
  publishDate: string;
  sourceId: number;
  title: string;
  image: string;
  url: string;
  isMagazine?: boolean;
}

export interface NewsResponse {
  news: NewsItem[];
  newsSources?: any[];
}

export interface ScrapedArticle {
  title: string;
  image: string;
  paragraphs: string[];
  url: string;
}


