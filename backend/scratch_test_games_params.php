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
$today = now()->format('d/m/Y');

$tests = [
    "competitors={$cid}",
    "competitorIds={$cid}",
    "competitorId={$cid}",
];

foreach ($tests as $t) {
    // Let's test with a wide range, e.g. 01/01/2026 to 31/12/2026
    $url = "https://webws.365scores.com/web/games/?appTypeId=5&langId=27&{$t}&startDate=01/01/2026&endDate=31/12/2026";
    try {
        $response = Http::withoutVerifying()->withHeaders($headers)->timeout(4)->get($url);
        echo "Param: {$t}\nStatus: " . $response->status() . "\n";
        if ($response->successful()) {
            $json = $response->json();
            echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
            if (isset($json['games'])) {
                echo "  FOUND games! Count: " . count($json['games']) . "\n";
            }
            if (isset($json['summary']) && !empty($json['summary'])) {
                echo "  FOUND summary keys: " . implode(', ', array_keys($json['summary'])) . "\n";
            }
            echo "\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}
