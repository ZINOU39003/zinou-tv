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

// Let's test a few endpoints with Al Nassr (ID: 6185 or 5529 or similar? let's use a popular competitor ID from 365scores like 6185 or Real Madrid 12)
$compId = 12; // Real Madrid

$endpoints = [
    'competitors_multi' => "https://webws.365scores.com/web/competitors/?appTypeId=5&langId=27&competitors={$compId}",
    'competitor_details' => "https://webws.365scores.com/web/competitor/?appTypeId=5&langId=27&competitorId={$compId}",
    'competitor_games' => "https://webws.365scores.com/web/competitors/games/?appTypeId=5&langId=27&competitors={$compId}",
    'competitor_squad' => "https://webws.365scores.com/web/competitors/squad/?appTypeId=5&langId=27&competitors={$compId}",
    'competitor_squad_single' => "https://webws.365scores.com/web/competitor/squad/?appTypeId=5&langId=27&competitorId={$compId}",
    'athlete_details' => "https://webws.365scores.com/web/athlete/?appTypeId=5&langId=27&athleteId=12345", // dummy athlete
    'athletes_multi' => "https://webws.365scores.com/web/athletes/?appTypeId=5&langId=27&athletes=12345",
];

foreach ($endpoints as $name => $url) {
    try {
        $response = Http::withoutVerifying()->withHeaders($headers)->timeout(5)->get($url);
        echo "=== {$name} ===\nStatus: " . $response->status() . "\n";
        if ($response->successful()) {
            $json = $response->json();
            echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
            // Print first 200 chars of json string
            echo "Snippet: " . substr(json_encode($json, JSON_UNESCAPED_UNICODE), 0, 300) . "\n\n";
        } else {
            echo "Body: " . substr($response->body(), 0, 100) . "\n\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}
