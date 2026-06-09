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

$cid = 5083; // Jordan
$date = "08/06/2026"; // Today

$url = "https://webws.365scores.com/web/games/allscores/?appTypeId=5&langId=27&timezoneName=Africa/Tunis&startDate={$date}&endDate={$date}&sports=1&competitors={$cid}";
echo "Querying {$url}...\n";

try {
    $response = Http::withoutVerifying()->withHeaders($headers)->get($url);
    echo "Status: " . $response->status() . "\n";
    if ($response->successful()) {
        $json = $response->json();
        echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
        if (isset($json['games'])) {
            echo "  FOUND games! Count: " . count($json['games']) . "\n";
            print_r(array_slice($json['games'], 0, 1));
        } else {
            echo "  No games key in response.\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
