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

$cid = 131; // Real Madrid

$tests = [
    "competitorId={$cid}&competitions=12", // Real Madrid is in La Liga (competition ID 12)
    "competitorId={$cid}&competitionId=12",
    "competitors={$cid}&competitions=12",
    "competitors={$cid}&competitionId=12",
    "competitorId={$cid}&season=2025",
    "competitorId={$cid}&season=2026",
    "competitors={$cid}&season=2026",
    "competitors={$cid}&withSquad=true",
    "competitors={$cid}&withSquad=1",
];

foreach ($tests as $t) {
    $url = "https://webws.365scores.com/web/athletes/?appTypeId=5&langId=27&{$t}";
    try {
        $response = Http::withoutVerifying()->withHeaders($headers)->timeout(4)->get($url);
        echo "Param: {$t}\nStatus: " . $response->status() . "\n";
        if ($response->successful()) {
            $json = $response->json();
            echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
            if (!empty($json['athletes'])) {
                echo "  FOUND athletes! Count: " . count($json['athletes']) . "\n";
                echo "  First player name: " . ($json['athletes'][0]['name'] ?? '—') . "\n";
                break;
            }
            echo "\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}
