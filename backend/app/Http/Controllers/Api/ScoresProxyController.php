<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScoresProxyController extends Controller
{
    private const BASE_URL = 'https://webws.365scores.com/web';
    private const LANG_ID  = 27;   // Arabic
    private const APP_TYPE = 5;
    private const TIMEZONE = 'Africa/Tunis';
    private const CACHE_SHORT  = 15;   // seconds – live match data
    private const CACHE_MEDIUM = 60;   // seconds – match detail
    private const CACHE_LONG   = 300;  // seconds – lineups / standings

    private function headers(): array
    {
        return [
            'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept'          => 'application/json, text/plain, */*',
            'Accept-Language' => 'ar,en;q=0.9',
            'Origin'          => 'https://www.365scores.com',
            'Referer'         => 'https://www.365scores.com/',
        ];
    }

    private function timezone(Request $request = null): string
    {
        $tz = $request?->header('X-Timezone');
        if ($tz && in_array($tz, timezone_identifiers_list(), true)) {
            return $tz;
        }
        return self::TIMEZONE;
    }

    private function fetch(string $url, array $params, int $cacheSec): JsonResponse
    {
        $cacheKey = 'scores_' . md5($url . serialize($params));

        $data = Cache::remember($cacheKey, $cacheSec, function () use ($url, $params) {
            try {
                $response = Http::withoutVerifying()
                    ->withHeaders($this->headers())
                    ->timeout(10)
                    ->get($url, $params);

                if ($response->successful()) {
                    return $response->json();
                }
                return null;
            } catch (\Throwable $e) {
                Log::warning('365scores proxy error: ' . $e->getMessage());
                return null;
            }
        });

        if ($data === null) {
            Cache::forget($cacheKey);
            return response()->json(['error' => 'Failed to fetch data from scores provider'], 502);
        }

        return response()->json($data);
    }

    /**
     * Competition IDs for important leagues and tournaments.
     * Found by querying the 365scores API catalog.
     *
     * International:
     *   570  = مباريات ودية دولية (International Friendlies - senior)
     *   5930 = كأس العالم FIFA 2026
     *   613  = تصفيات كأس العالم - أمريكا الجنوبية (WC Qualifiers CONMEBOL)
     *   569  = تصفيات كأس العالم - أوروبا (WC Qualifiers UEFA)
     *   572  = دوري أبطال أوروبا
     *   573  = الدوري الأوروبي (UEFA Europa League)
     *   6196 = كأس آسيا (Asia Cup)
     *   5096 = كأس العالم للأندية (Club World Cup)
     *   564  = كأس أمم أفريقيا (AFCON)
     *   574  = اليويفا كونفرنس ليج
     *   575  = تصفيات اليورو (UEFA Euro Qualifiers)
     *   576  = بطولة أمم أوروبا (UEFA Nations League)
     *   578  = كوبا أمريكا
     *   565  = تصفيات كأس العالم - أفريقيا
     *
     * Top Club Leagues:
     *   5     = الدوري الإنجليزي الممتاز (Premier League)
     *   12    = لا ليغا (La Liga)
     *   11    = الدوري الألماني (Bundesliga)
     *   9     = الدوري الإيطالي (Serie A)
     *   8     = الدوري الفرنسي (Ligue 1)
     *   7     = الدوري الهولندي (Eredivisie)
     *   4     = الدوري البرتغالي (Primeira Liga)
     *   6     = الدوري التركي (Süper Lig)
     *   82    = الدوري السعودي (Saudi Pro League)
     *   584   = الدوري المصري
     *   171   = الدوري الجزائري
     *   406   = الدوري التونسي
     *   557   = الدوري المغربي
     *   371   = الدوري الأمريكي (MLS)
     *   83    = الدوري الأرجنتيني
     *   84    = الدوري البرازيلي
     */
    private const IMPORTANT_COMP_IDS = [
        // === INTERNATIONAL NATIONAL TEAMS ===
        570,  // مباريات ودية دولية (Friendlies)
        5930, // كأس العالم FIFA 2026
        613,  // تصفيات كأس العالم - أمريكا الجنوبية
        569,  // تصفيات كأس العالم - أوروبا
        565,  // تصفيات كأس العالم - أفريقيا
        564,  // كأس أمم أفريقيا (AFCON)
        6196, // كأس آسيا
        576,  // دوري أمم أوروبا
        578,  // كوبا أمريكا
        575,  // تصفيات اليورو
        571,  // مباريات ودية دولية تحت 21
        // === UEFA CLUB COMPETITIONS ===
        572,  // دوري أبطال أوروبا
        573,  // الدوري الأوروبي
        574,  // UEFA Conference League
        5096, // كأس العالم للأندية
        // === TOP 5 EUROPEAN LEAGUES ===
        5,    // Premier League
        12,   // La Liga
        11,   // Bundesliga
        9,    // Serie A
        8,    // Ligue 1
        // === OTHER MAJOR LEAGUES ===
        7,    // Eredivisie
        4,    // Primeira Liga
        6,    // Süper Lig
        82,   // Saudi Pro League
        371,  // MLS
        83,   // Argentine Primera División
        84,   // Brasileirão Série A
        // === ARAB LEAGUES ===
        584,  // الدوري المصري
        171,  // الدوري الجزائري
        406,  // الدوري التونسي
        557,  // الدوري المغربي
    ];

    /**
     * GET /api/scores/today
     * GET /api/scores/date/{date}  — date format: DD-MM-YYYY
     */
    public function byDate(Request $request, string $date = null): JsonResponse
    {
        if ($date === null) {
            $date = now()->format('d/m/Y');
        } else {
            $date = str_replace('-', '/', $date);
        }

        $competitionIds = $request->query('competition_id')
            ? (string) $request->query('competition_id')
            : implode(',', self::IMPORTANT_COMP_IDS);

        return $this->fetch(self::BASE_URL . '/games/allscores/', [
            'langId'         => self::LANG_ID,
            'startDate'      => $date,
            'endDate'        => $date,
            'sports'         => 1,
            'appTypeId'      => self::APP_TYPE,
            'timezoneName'   => $this->timezone($request),
            'competitionIds' => $competitionIds,
        ], self::CACHE_SHORT);
    }

    /**
     * GET /api/scores/worldcup
     * GET /api/scores/worldcup/{date}  — DD-MM-YYYY optional
     */
    public function worldCup(Request $request, string $date = null): JsonResponse
    {
        if ($date === null) {
            $date = now()->format('d/m/Y');
        } else {
            $date = str_replace('-', '/', $date);
        }

        $response = $this->fetch(self::BASE_URL . '/games/allscores/', [
            'langId'         => self::LANG_ID,
            'startDate'      => $date,
            'endDate'        => $date,
            'sports'         => 1,
            'appTypeId'      => self::APP_TYPE,
            'timezoneName'   => $this->timezone($request),
            'competitionIds' => '5930',
        ], self::CACHE_SHORT);

        $payload = json_decode($response->getContent(), true);
        if (is_array($payload) && isset($payload['games'])) {
            $payload['games'] = array_values(array_filter($payload['games'], function ($game) {
                $compId = $game['competitionId'] ?? null;
                $name = $game['competitionDisplayName'] ?? '';
                return $compId == 5930
                    || str_contains($name, 'كأس العالم')
                    || stripos($name, 'World Cup') !== false;
            }));
            return response()->json($payload);
        }

        return $response;
    }


    /**
     * GET /api/scores/match/{gameId}
     * Full match detail: events (goals, cards, subs), score, status
     */
    public function matchDetail(int $gameId): JsonResponse
    {
        return $this->fetch(self::BASE_URL . '/game/', [
            'gameId'    => $gameId,
            'langId'    => self::LANG_ID,
            'appTypeId' => self::APP_TYPE,
        ], self::CACHE_MEDIUM);
    }

    /**
     * GET /api/scores/stats/{gameId}
     * Match statistics: possession, shots, corners, fouls…
     */
    public function matchStats(int $gameId): JsonResponse
    {
        return $this->fetch(self::BASE_URL . '/game/stats/', [
            'gameId'    => $gameId,
            'langId'    => self::LANG_ID,
            'appTypeId' => self::APP_TYPE,
        ], self::CACHE_MEDIUM);
    }

    /**
     * GET /api/scores/lineup/{gameId}
     * Starting XI, substitutes, formation
     */
    public function matchLineup(int $gameId): JsonResponse
    {
        return $this->fetch(self::BASE_URL . '/game/lineup/', [
            'gameId'    => $gameId,
            'langId'    => self::LANG_ID,
            'appTypeId' => self::APP_TYPE,
        ], self::CACHE_LONG);
    }

    /**
     * GET /api/scores/standings/{competitionId}
     * League table / standings
     */
    public function standings(Request $request, int $competitionId): JsonResponse
    {
        return $this->fetch(self::BASE_URL . '/competition/standings/', [
            'competitionId' => $competitionId,
            'langId'        => self::LANG_ID,
            'appTypeId'     => self::APP_TYPE,
            'timezoneName'  => $this->timezone($request),
        ], self::CACHE_LONG);
    }

    /**
     * GET /api/scores/competitor/{competitorId}/squad
     * Squad from the competitor's most recent match with lineups available.
     */
    public function competitorSquad(Request $request, int $competitorId): JsonResponse
    {
        $cacheKey = 'scores_squad_' . $competitorId;

        $data = Cache::remember($cacheKey, self::CACHE_LONG, function () use ($competitorId, $request) {
            try {
                $gamesResponse = Http::withoutVerifying()
                    ->withHeaders($this->headers())
                    ->timeout(10)
                    ->get(self::BASE_URL . '/games/', [
                        'competitors'  => $competitorId,
                        'sports'       => 1,
                        'langId'       => self::LANG_ID,
                        'appTypeId'    => self::APP_TYPE,
                        'timezoneName' => $this->timezone($request),
                    ]);

                if (!$gamesResponse->successful()) {
                    return null;
                }

                $games = $gamesResponse->json('games') ?? [];
                usort($games, fn ($a, $b) => strcmp($b['startTime'] ?? '', $a['startTime'] ?? ''));

                foreach ($games as $game) {
                    if (!($game['hasLineups'] ?? false)) {
                        continue;
                    }

                    $lineupResponse = Http::withoutVerifying()
                        ->withHeaders($this->headers())
                        ->timeout(10)
                        ->get(self::BASE_URL . '/game/lineup/', [
                            'gameId'    => $game['id'],
                            'langId'    => self::LANG_ID,
                            'appTypeId' => self::APP_TYPE,
                        ]);

                    if (!$lineupResponse->successful()) {
                        continue;
                    }

                    $lineupGame = $lineupResponse->json('game');
                    if (!$lineupGame) {
                        continue;
                    }

                    $members = $lineupGame['members'] ?? [];
                    $squad = array_values(array_filter($members, fn ($m) => ($m['competitorId'] ?? null) == $competitorId));

                    return [
                        'competitorId' => $competitorId,
                        'sourceGameId' => $game['id'],
                        'members'      => $squad,
                        'competitor'   => ($lineupGame['homeCompetitor']['id'] ?? null) == $competitorId
                            ? ($lineupGame['homeCompetitor'] ?? null)
                            : ($lineupGame['awayCompetitor'] ?? null),
                    ];
                }

                return ['competitorId' => $competitorId, 'members' => [], 'competitor' => null];
            } catch (\Throwable $e) {
                Log::warning('365scores squad error: ' . $e->getMessage());
                return null;
            }
        });

        if ($data === null) {
            Cache::forget($cacheKey);
            return response()->json(['error' => 'Failed to fetch squad'], 502);
        }

        return response()->json($data);
    }

    /**
     * GET /api/scores/search?q=world+cup
     * Search competitions / teams
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        return $this->fetch(self::BASE_URL . '/competitions/', [
            'langId'    => self::LANG_ID,
            'appTypeId' => self::APP_TYPE,
            'query'     => $query,
        ], self::CACHE_LONG);
    }

    /**
     * GET /api/scores/head2head/{gameId}
     * Head-to-head history between the two teams
     */
    public function headToHead(int $gameId): JsonResponse
    {
        return $this->fetch(self::BASE_URL . '/game/head2head/', [
            'gameId'    => $gameId,
            'langId'    => self::LANG_ID,
            'appTypeId' => self::APP_TYPE,
        ], self::CACHE_LONG);
    }

    /**
     * GET /api/scores/competitor/{competitorId}
     * Get competitor profile details
     */
    public function competitorDetail(int $competitorId): JsonResponse
    {
        return $this->fetch(self::BASE_URL . '/competitors/', [
            'competitors' => $competitorId,
            'langId'      => self::LANG_ID,
            'appTypeId'   => self::APP_TYPE,
        ], self::CACHE_LONG);
    }

    /**
     * GET /api/scores/competitor/games/{competitorId}
     * Get competitor fixtures and results
     */
    public function competitorGames(int $competitorId): JsonResponse
    {
        return $this->fetch(self::BASE_URL . '/games/', [
            'competitors' => $competitorId,
            'sports'      => 1, // Only Football
            'langId'      => self::LANG_ID,
            'appTypeId'   => self::APP_TYPE,
        ], self::CACHE_MEDIUM);
    }
}
