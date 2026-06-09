<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Fetch today's games to extract competition IDs
$url = 'https://webws.365scores.com/web/games/allscores/?appTypeId=5&langId=27&timezoneName=Africa/Tunis&startDate=08/06/2026&endDate=08/06/2026&sports=1';
$response = Illuminate\Support\Facades\Http::withoutVerifying()
    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'Origin' => 'https://www.365scores.com'])
    ->get($url);

$data = $response->json();
$comps = $data['competitions'] ?? [];
// Sort by popularityRank
usort($comps, fn($a, $b) => ($a['popularityRank'] ?? 9999999) - ($b['popularityRank'] ?? 9999999));

echo "TOP COMPETITIONS TODAY:\n";
echo str_pad("ID", 8) . str_pad("Rank", 12) . str_pad("Name", 45) . "Country\n";
echo str_repeat("-", 80) . "\n";
foreach ($comps as $c) {
    echo str_pad($c['id'], 8) . str_pad($c['popularityRank'] ?? 'N/A', 12) . str_pad($c['name'] ?? 'N/A', 45) . ($c['countryId'] ?? '') . "\n";
}
