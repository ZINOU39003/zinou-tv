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

$compId = 12; // Burnley

$params = [
    "withSquad=true",
    "withSquads=true",
    "withPlayers=true",
    "withMembers=true",
    "withRoster=true",
    "showSquad=true",
    "showPlayers=true",
    "withAthletes=true",
];

foreach ($params as $p) {
    $url = "https://webws.365scores.com/web/competitors/?appTypeId=5&langId=27&competitors={$compId}&{$p}";
    try {
        $response = Http::withoutVerifying()->withHeaders($headers)->get($url);
        if ($response->successful()) {
            $json = $response->json();
            echo "Param: {$p}\n";
            echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
            // Check if competitor object has more keys or athletes key exists
            if (isset($json['competitors'][0])) {
                echo "Competitor keys: " . implode(', ', array_keys($json['competitors'][0])) . "\n";
            }
            if (isset($json['athletes'])) {
                echo "  FOUND athletes in response!\n";
            }
            echo "\n";
        }
    } catch (\Exception $e) {
        echo "Error for {$p}: " . $e->getMessage() . "\n\n";
    }
}
