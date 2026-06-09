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

$cid = 12; // Burnley (Burnley has hasSquad = 1)
$url = "https://webws.365scores.com/web/athletes/?appTypeId=5&langId=27&competitors={$cid}&sports=1";

echo "Fetching {$url}...\n";
$start = microtime(true);
try {
    $response = Http::withoutVerifying()->withHeaders($headers)->timeout(12)->get($url);
    $end = microtime(true);
    echo "Status: " . $response->status() . " (took " . round($end - $start, 2) . "s)\n";
    if ($response->successful()) {
        $json = $response->json();
        echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
        if (!empty($json['athletes'])) {
            echo "FOUND athletes! Count: " . count($json['athletes']) . "\n";
            echo "First player name: " . ($json['athletes'][0]['name'] ?? '—') . "\n";
        } else {
            echo "No athletes in array.\n";
        }
    } else {
        echo "Body: " . substr($response->body(), 0, 200) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
