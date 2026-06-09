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

$query = "Real Madrid";

$urls = [
    "competitors_query" => "https://webws.365scores.com/web/competitors/?appTypeId=5&langId=27&query=" . urlencode($query),
    "competitors_q" => "https://webws.365scores.com/web/competitors/?appTypeId=5&langId=27&q=" . urlencode($query),
    "competitors_search" => "https://webws.365scores.com/web/competitors/search/?appTypeId=5&langId=27&query=" . urlencode($query),
    "search_all" => "https://webws.365scores.com/web/search/?appTypeId=5&langId=27&query=" . urlencode($query),
    "search_q" => "https://webws.365scores.com/web/search/?appTypeId=5&langId=27&q=" . urlencode($query),
];

foreach ($urls as $name => $url) {
    try {
        $response = Http::withoutVerifying()->withHeaders($headers)->timeout(4)->get($url);
        echo "=== {$name} ===\nStatus: " . $response->status() . "\n";
        if ($response->successful()) {
            $json = $response->json();
            echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
            echo "Snippet: " . substr(json_encode($json, JSON_UNESCAPED_UNICODE), 0, 250) . "\n\n";
        } else {
            echo "Body: " . substr($response->body(), 0, 100) . "\n\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}
