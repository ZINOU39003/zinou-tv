<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Fetch from a range of dates to find major competitions (CL, PL, La Liga, WC etc.)
// Use a date in the future when these competitions will be active
// Also try the catalog endpoint
$url = 'https://webws.365scores.com/web/competitors/?appTypeId=5&langId=27&sports=1&competitions=572,573,574,5,576,7,8,9,6,4';
$response = Illuminate\Support\Facades\Http::withoutVerifying()
    ->withHeaders(['User-Agent' => 'Mozilla/5.0', 'Origin' => 'https://www.365scores.com'])
    ->get($url);
echo "Competitors endpoint: " . $response->status() . "\n";

// Try the catalog endpoint  
$url2 = 'https://webws.365scores.com/web/catalog/?appTypeId=5&langId=27&sports=1';
$r2 = Illuminate\Support\Facades\Http::withoutVerifying()
    ->withHeaders(['User-Agent' => 'Mozilla/5.0', 'Origin' => 'https://www.365scores.com'])
    ->get($url2);
echo "Catalog endpoint: " . $r2->status() . "\n";
$d2 = $r2->json();
echo "Keys: " . implode(', ', array_keys($d2 ?? [])) . "\n";
if (isset($d2['competitions'])) {
    // Filter to most popular (rank < 200)
    $top = array_filter($d2['competitions'], fn($c) => ($c['popularityRank'] ?? 9999) < 500);
    usort($top, fn($a,$b) => $a['popularityRank'] - $b['popularityRank']);
    foreach ($top as $c) {
        echo "  ID:{$c['id']} Rank:{$c['popularityRank']} Name:{$c['name']}\n";
    }
}
