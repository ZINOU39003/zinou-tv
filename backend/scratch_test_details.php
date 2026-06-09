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
$url = "https://webws.365scores.com/web/games/allscores/?appTypeId=5&langId=27&timezoneName=Africa/Tunis&startDate=01/01/2026&endDate=31/12/2026&sports=1&competitors={$compId}";

$response = Http::withoutVerifying()->withHeaders($headers)->get($url);
$data = $response->json();

echo "Status: " . $response->status() . "\n";
echo "Keys: " . implode(', ', array_keys($data ?? [])) . "\n";
if (isset($data['games'])) {
    echo "Games count: " . count($data['games']) . "\n";
    echo "First game snippet:\n";
    print_r(array_slice($data['games'], 0, 1));
} else {
    echo "No games key found.\n";
}
