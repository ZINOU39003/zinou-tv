<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Search for specific competition IDs across multiple days to find major competitions
$urls = [
    // Try different dates to find Champions League, Premier League etc.
    'https://webws.365scores.com/web/competitions/?appTypeId=5&langId=27&sports=1&topCount=50',
    'https://webws.365scores.com/web/competitions/popular/?appTypeId=5&langId=27&sports=1',
];

foreach ($urls as $url) {
    echo "URL: $url\n";
    $response = Illuminate\Support\Facades\Http::withoutVerifying()
        ->withHeaders(['User-Agent' => 'Mozilla/5.0', 'Origin' => 'https://www.365scores.com'])
        ->get($url);
    echo "Status: " . $response->status() . "\n";
    $data = $response->json();
    echo "Keys: " . implode(', ', array_keys($data ?? [])) . "\n\n";
    if (isset($data['competitions'])) {
        foreach (array_slice($data['competitions'], 0, 30) as $c) {
            echo "  ID:{$c['id']} Rank:" . ($c['popularityRank'] ?? 'N/A') . " Name:{$c['name']}\n";
        }
    }
    echo "---\n";
}
