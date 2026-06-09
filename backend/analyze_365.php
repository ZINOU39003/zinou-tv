<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$url = 'https://webws.365scores.com/web/games/allscores/?appTypeId=5&langId=27&timezoneName=Africa/Tunis&startDate=08/06/2026&endDate=08/06/2026&sports=1';
$response = Illuminate\Support\Facades\Http::withoutVerifying()
    ->withHeaders([
        'User-Agent' => 'Mozilla/5.0',
    ])
    ->get($url);

$data = $response->json();
if (!isset($data['games'])) { echo "Failed\n"; exit; }

// Check first game with TV networks
$tvGame = null;
foreach ($data['games'] as $g) {
    if (!empty($g['tvNetworks'])) {
        $tvGame = $g;
        break;
    }
}
if ($tvGame) {
    echo "TV Game: " . $tvGame['homeCompetitor']['name'] . "\n";
    echo "TV Networks: " . json_encode($tvGame['tvNetworks'], JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "No TV Networks found in games.\n";
}

// How are commentators stored?
$commGame = null;
foreach ($data['games'] as $g) {
    if (!empty($g['tvNetworks']) && isset($g['tvNetworks'][0]['commentator'])) {
        echo "Commentator: " . $g['tvNetworks'][0]['commentator'] . "\n";
    }
}

// Competitions format
$c = $data['competitions'][0];
echo "Competition format: " . json_encode($c, JSON_UNESCAPED_UNICODE) . "\n";

