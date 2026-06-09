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

$patterns = [
    "https://webws.365scores.com/web/competitors/squad/?appTypeId=5&langId=27&competitors={$compId}",
    "https://webws.365scores.com/web/competitors/squad/?appTypeId=5&langId=27&competitorId={$compId}",
    "https://webws.365scores.com/web/squad/?appTypeId=5&langId=27&competitors={$compId}",
    "https://webws.365scores.com/web/squad/?appTypeId=5&langId=27&competitorId={$compId}",
    "https://webws.365scores.com/web/competitors/roster/?appTypeId=5&langId=27&competitors={$compId}",
    "https://webws.365scores.com/web/competitor/roster/?appTypeId=5&langId=27&competitorId={$compId}",
    "https://webws.365scores.com/web/competitor/squad/?appTypeId=5&langId=27&competitorId={$compId}",
    "https://webws.365scores.com/web/competitors/members/?appTypeId=5&langId=27&competitors={$compId}",
    "https://webws.365scores.com/web/competitor/members/?appTypeId=5&langId=27&competitorId={$compId}",
    "https://webws.365scores.com/web/competitors/players/?appTypeId=5&langId=27&competitors={$compId}",
    "https://webws.365scores.com/web/competitor/players/?appTypeId=5&langId=27&competitorId={$compId}",
    "https://webws.365scores.com/web/athletes/?appTypeId=5&langId=27&competitors={$compId}",
    "https://webws.365scores.com/web/athletes/?appTypeId=5&langId=27&competitorId={$compId}",
];

foreach ($patterns as $url) {
    try {
        $response = Http::withoutVerifying()->withHeaders($headers)->timeout(3)->get($url);
        echo "URL: {$url}\nStatus: " . $response->status() . "\n";
        if ($response->successful()) {
            $json = $response->json();
            echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
            echo "Snippet: " . substr(json_encode($json, JSON_UNESCAPED_UNICODE), 0, 200) . "\n\n";
            break; // found it!
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}
