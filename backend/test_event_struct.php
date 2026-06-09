<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$url = 'https://webws.365scores.com/web/game/?appTypeId=5&langId=27&gameId=4121405'; // Example game
$response = Illuminate\Support\Facades\Http::withoutVerifying()
    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
    ->get($url);

$data = $response->json();
$game = $data['game'] ?? null;
if ($game) {
    if (isset($game['events'][0])) {
        echo "First event: " . json_encode($game['events'][0], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n";
    }
    if (isset($game['members'][0])) {
        echo "First member: " . json_encode($game['members'][0], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n";
    }
}
