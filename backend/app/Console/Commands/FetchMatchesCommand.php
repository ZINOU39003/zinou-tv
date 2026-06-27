<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SportMatch;
use App\Models\Tournament;

class FetchMatchesCommand extends Command
{
    protected $signature = 'app:fetch-matches {date?}';
    protected $description = 'Fetch matches from ESPN API';

    // List of major leagues to fetch
    protected $leagues = [
        'eng.1' => 'Premier League',
        'esp.1' => 'La Liga',
        'ger.1' => 'Bundesliga',
        'ita.1' => 'Serie A',
        'fra.1' => 'Ligue 1',
        'uefa.champions' => 'Champions League',
        'uefa.europa' => 'Europa League',
        'fifa.world' => 'World Cup',
        'fifa.friendly' => 'International Friendlies',
        'caf.wcq' => 'African World Cup Qualification',
        'afc.wcq' => 'Asian World Cup Qualification',
        'conmebol.wcq' => 'South American World Cup Qualification',
        'concacaf.wcq' => 'CONCACAF World Cup Qualification',
        'uefa.euro' => 'European Championship',
        'caf.nations' => 'Africa Cup of Nations',
    ];

    public function handle()
    {
        $date = $this->argument('date') ?: date('Ymd');
        $this->info("Fetching matches for {$date} from ESPN API...");

        foreach ($this->leagues as $leagueCode => $leagueName) {
            $this->info("Fetching {$leagueName}...");
            $url = "https://site.api.espn.com/apis/site/v2/sports/soccer/{$leagueCode}/scoreboard?dates={$date}";
            
            $response = Http::withoutVerifying()->get($url);

            if (!$response->successful()) {
                $this->error("Failed to fetch {$leagueName}. Status: " . $response->status());
                continue;
            }

            $data = $response->json();
            if (!isset($data['events'])) {
                continue;
            }

            // Auto-create the tournament
            $tournament = Tournament::firstOrCreate(
                ['name' => $leagueName],
                [
                    'name_ar' => $leagueName,
                    'is_active' => true,
                    'sort_order' => 1,
                ]
            );

            foreach ($data['events'] as $event) {
                $matchId = $event['id'];
                
                $competition = $event['competitions'][0];
                $competitors = $competition['competitors'];
                
                $homeTeam = null;
                $awayTeam = null;
                
                foreach ($competitors as $competitor) {
                    if ($competitor['homeAway'] === 'home') {
                        $homeTeam = $competitor;
                    } else {
                        $awayTeam = $competitor;
                    }
                }

                if (!$homeTeam || !$awayTeam) continue;

                $homeName = $homeTeam['team']['name'];
                $awayName = $awayTeam['team']['name'];
                $homeScore = (int) ($homeTeam['score'] ?? 0);
                $awayScore = (int) ($awayTeam['score'] ?? 0);
                
                $statusStr = $event['status']['type']['state'] ?? 'pre'; // pre, in, post
                $isLive = ($statusStr === 'in');
                
                $matchTime = new \DateTime($event['date']);
                $timeFormatted = $matchTime->format('H:i');
                $matchDateFormatted = $matchTime->format('Y-m-d');

                // Fetch deep match details (stats, lineups)
                $summaryUrl = "https://site.api.espn.com/apis/site/v2/sports/soccer/{$leagueCode}/summary?event={$matchId}";
                $summaryResponse = Http::withoutVerifying()->get($summaryUrl);
                
                $stats = [];
                $lineups = [];
                
                if ($summaryResponse->successful()) {
                    $summaryData = $summaryResponse->json();
                    
                    // Extract stats if available
                    if (isset($summaryData['boxscore']['teams'])) {
                        $stats = $summaryData['boxscore']['teams'];
                    }
                    
                    // Extract lineups/rosters if available
                    if (isset($summaryData['rosters'])) {
                        $lineups = $summaryData['rosters'];
                    }
                }

                // Match details
                $matchDetails = [
                    'home_logo' => $homeTeam['team']['logo'] ?? null,
                    'away_logo' => $awayTeam['team']['logo'] ?? null,
                    'status_name' => $event['status']['type']['shortDetail'] ?? '',
                    'stats' => $stats,
                    'lineups' => $lineups,
                ];

                // Check existing match state
                $existingMatch = SportMatch::where('external_id', (string) $matchId)->first();
                $notificationsState = $existingMatch && $existingMatch->notifications_state
                    ? json_decode($existingMatch->notifications_state, true)
                    : [
                        'timers' => ['30m' => false, '15m' => false, '5m' => false],
                        'started' => false, 'ended' => false, 'home_score' => 0, 'away_score' => 0, 
                        'lineups' => false, 'sent_events' => [], 'halftime' => false
                      ];

                $oneSignal = app(\App\Services\OneSignalService::class);
                
                // Determine if it's a big match
                $bigLeagues = ['uefa.champions', 'fifa.world', 'uefa.euro', 'caf.nations', 'esp.1', 'eng.1', 'ita.1'];
                $isBigMatch = in_array($leagueCode, $bigLeagues) || 
                              str_contains(strtolower($homeName), 'real madrid') || str_contains(strtolower($awayName), 'real madrid') ||
                              str_contains(strtolower($homeName), 'barcelona') || str_contains(strtolower($awayName), 'barcelona') ||
                              str_contains(strtolower($homeName), 'manchester');

                // Only send notifications if it's today's match to avoid spamming past matches
                $isToday = ($date === date('Ymd'));
                
                if ($isToday) {
                    $now = new \DateTime();
                    $diffInMinutes = ($matchTime->getTimestamp() - $now->getTimestamp()) / 60;
                    
                    // 1. Timers
                    if ($diffInMinutes > 0 && $diffInMinutes <= 35) {
                        $timers = ['30m' => 30, '15m' => 15, '5m' => 5];
                        foreach ($timers as $key => $mins) {
                            if ($diffInMinutes <= $mins && empty($notificationsState['timers'][$key])) {
                                $timeMsg = $mins === 5 ? "المباراة ستبدأ الآن" : "بعد {$mins} دقيقة";
                                $oneSignal->sendToAll(
                                    "🔔 {$timeMsg}",
                                    "{$homeName} 🆚 {$awayName}\nلا تنس متابعة المباراة!",
                                    [],
                                    ['large_icon' => $homeTeam['team']['logo'] ?? null]
                                );
                                $notificationsState['timers'][$key] = true;
                                break;
                            }
                        }
                    }

                    // Event: Match Started
                    if ($isLive && empty($notificationsState['started'])) {
                        $oneSignal->sendToAll(
                            "🟢 بدأت المباراة",
                            "{$homeName} × {$awayName}",
                            [],
                            ['large_icon' => $homeTeam['team']['logo'] ?? null]
                        );
                        $notificationsState['started'] = true;
                    }

                    // Events from ESPN keyEvents
                    $keyEvents = $summaryData['keyEvents'] ?? [];
                    if (!empty($keyEvents)) {
                        foreach ($keyEvents as $ev) {
                            $evId = (string) $ev['id'];
                            if (!isset($notificationsState['sent_events']) || !is_array($notificationsState['sent_events'])) {
                                $notificationsState['sent_events'] = [];
                            }
                            if (in_array($evId, $notificationsState['sent_events'])) continue;
                            
                            $type = $ev['type']['type'] ?? '';
                            $text = $ev['shortText'] ?? $ev['text'] ?? '';
                            $clock = $ev['clock']['displayValue'] ?? '';
                            $participant = $ev['participants'][0]['athlete']['displayName'] ?? '';
                            
                            $shouldSend = false;
                            $title = "";
                            $message = "";
                            
                            if ($type === 'goal') {
                                $title = "⚽ جوووول!";
                                $message = "{$homeName} {$homeScore} - {$awayScore} {$awayName}\n" . ($participant ? "سجل {$participant} في الدقيقة {$clock}." : $text);
                                $shouldSend = true;
                            } elseif ($type === 'redCard') {
                                $title = "🟥 بطاقة حمراء";
                                $message = "في مباراة {$homeName} ضد {$awayName}\n" . ($participant ? "للاعب {$participant} في الدقيقة {$clock}." : $text);
                                $shouldSend = true;
                            } elseif ($type === 'halftime' && empty($notificationsState['halftime'])) {
                                $title = "⏸ انتهى الشوط الأول";
                                $message = "{$homeName} {$homeScore} - {$awayScore} {$awayName}";
                                $shouldSend = true;
                                $notificationsState['halftime'] = true;
                            } elseif ($isBigMatch && $type === 'substitution') {
                                $title = "🔄 تبديل لاعب";
                                $message = $text;
                                $shouldSend = true;
                            }
                            
                            if ($shouldSend) {
                                $oneSignal->sendToAll($title, $message, [], ['large_icon' => $homeTeam['team']['logo'] ?? null]);
                                $notificationsState['sent_events'][] = $evId;
                            }
                        }
                    } else {
                        // Fallback to score change logic if no keyEvents provided
                        if ($isLive && $homeScore > ($notificationsState['home_score'] ?? 0)) {
                            $oneSignal->sendToAll("⚽ جوووول!", "{$homeName} يسجل! النتيجة الآن: {$homeName} {$homeScore} - {$awayScore} {$awayName}", [], ['large_icon' => $homeTeam['team']['logo'] ?? null]);
                        }
                        if ($isLive && $awayScore > ($notificationsState['away_score'] ?? 0)) {
                            $oneSignal->sendToAll("⚽ جوووول!", "{$awayName} يسجل! النتيجة الآن: {$homeName} {$homeScore} - {$awayScore} {$awayName}", [], ['large_icon' => $homeTeam['team']['logo'] ?? null]);
                        }
                    }
                    
                    $notificationsState['home_score'] = $homeScore;
                    $notificationsState['away_score'] = $awayScore;

                    // Event: Match Ended
                    if ($statusStr === 'post' && empty($notificationsState['ended']) && !empty($notificationsState['started'])) {
                        $oneSignal->sendToAll(
                            "✅ انتهت المباراة",
                            "{$homeName} {$homeScore} - {$awayScore} {$awayName}",
                            [],
                            ['large_icon' => $homeTeam['team']['logo'] ?? null]
                        );
                        $notificationsState['ended'] = true;
                    }
                }

                SportMatch::updateOrCreate(
                    ['external_id' => (string) $matchId],
                    [
                        'tournament_id' => $tournament->id,
                        'team_one_name' => $homeName,
                        'team_one_name_ar' => $homeName,
                        'team_two_name' => $awayName,
                        'team_two_name_ar' => $awayName,
                        'team_one_score' => $homeScore,
                        'team_two_score' => $awayScore,
                        'match_time' => $timeFormatted,
                        'match_date' => $matchDateFormatted,
                        'is_live' => $isLive,
                        'is_world_cup' => ($leagueCode === 'fifa.world'),
                        'is_active' => true,
                        'match_details' => $matchDetails,
                        'notifications_state' => json_encode($notificationsState),
                    ]
                );

                $this->info("Saved match: {$homeName} vs {$awayName} ({$homeScore}-{$awayScore})");
            }
        }

        $this->info('All matches fetched successfully!');
    }
}
