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

// Let's search for "Real Madrid" in English and Arabic
$queries = ["Real Madrid", "النصر", "الأهلي"];

foreach ($queries as $q) {
    $url = "https://webws.365scores.com/web/competitions/?appTypeId=5&langId=27&query=" . urlencode($q);
    $response = Http::withoutVerifying()->withHeaders($headers)->get($url);
    if ($response->successful()) {
        $json = $response->json();
        echo "Query: {$q}\n";
        echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
        if (!empty($json['competitors'])) {
            foreach (array_slice($json['competitors'], 0, 3) as $comp) {
                echo "  Team: {$comp['name']} (ID: {$comp['id']}) - Type: " . ($comp['type'] ?? '—') . "\n";
            }
        }
        echo "\n";
    }
}
