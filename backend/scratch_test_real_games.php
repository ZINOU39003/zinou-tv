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
$url = "https://webws.365scores.com/web/games/?appTypeId=5&langId=27&competitors={$cid}&sports=1";

$response = Http::withoutVerifying()->withHeaders($headers)->get($url);
$json = $response->json();

if (!empty($json['games'])) {
    echo "Games count: " . count($json['games']) . "\n";
    echo "First game snippet:\n";
    print_r($json['games'][0]);
}
