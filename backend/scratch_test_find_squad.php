<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$headers = [
    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Accept' => 'application/json, text/plain, */*',
    'Origin' => 'https://www.365scores.com',
    'Referer' => 'https://www.365scores.com/',
];

// Let's get today's matches
$today = now()->format('d/m/Y');
$gamesUrl = "https://webws.365scores.com/web/games/allscores/?appTypeId=5&langId=27&timezoneName=Africa/Tunis&startDate={$today}&endDate={$today}&sports=1";

$res = Http::withoutVerifying()->withHeaders($headers)->get($gamesUrl);
$data = $res->json();

if (!empty($data['games'])) {
    foreach (array_slice($data['games'], 0, 5) as $game) {
        $homeId = $game['homeCompetitor']['id'] ?? null;
        $awayId = $game['awayCompetitor']['id'] ?? null;
        
        echo "Match: {$game['homeCompetitor']['name']} (ID: {$homeId}) vs {$game['awayCompetitor']['name']} (ID: {$awayId})\n";
        
        foreach ([$homeId, $awayId] as $cid) {
            if (!$cid) continue;
            
            $url = "https://webws.365scores.com/web/athletes/?appTypeId=5&langId=27&competitorId={$cid}";
            $response = Http::withoutVerifying()->withHeaders($headers)->get($url);
            
            if ($response->successful()) {
                $json = $response->json();
                echo "  Competitor ID {$cid} athletes keys: " . implode(', ', array_keys($json ?? [])) . "\n";
                if (!empty($json['athletes'])) {
                    echo "  FOUND SQUAD! Count: " . count($json['athletes']) . "\n";
                    echo "  First player name: " . ($json['athletes'][0]['name'] ?? '—') . "\n";
                    // Exit on first squad found to inspect
                    print_r(array_slice($json['athletes'], 0, 1));
                    exit;
                }
            } else {
                echo "  Failed for {$cid}. Status: " . $response->status() . "\n";
            }
        }
    }
} else {
    echo "No games found today.\n";
}
