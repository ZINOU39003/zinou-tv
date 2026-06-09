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

$compId = 131; // Real Madrid

$url = "https://webws.365scores.com/web/athletes/?appTypeId=5&langId=27&competitors={$compId}";
echo "Testing with competitorId={$compId}...\n";

try {
    $response = Http::withoutVerifying()->withHeaders($headers)->timeout(8)->get($url);
    echo "Status: " . $response->status() . "\n";
    if ($response->successful()) {
        $json = $response->json();
        echo "Keys: " . implode(', ', array_keys($json ?? [])) . "\n";
        if (!empty($json['athletes'])) {
            echo "  FOUND athletes! Count: " . count($json['athletes']) . "\n";
            echo "  First player:\n";
            print_r(array_slice($json['athletes'], 0, 2));
        } else {
            echo "  Athletes array is empty.\n";
        }
    } else {
        echo "Body: " . substr($response->body(), 0, 200) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
