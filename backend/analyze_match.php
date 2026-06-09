<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$url = 'https://webws.365scores.com/web/game/?appTypeId=5&langId=27&gameId=4121405'; // Use a valid gameId from today
$response = Illuminate\Support\Facades\Http::withoutVerifying()
    ->withHeaders([
        'User-Agent' => 'Mozilla/5.0',
    ])
    ->get($url);

$data = $response->json();
$game = $data['game'] ?? null;
if ($game) {
    echo "TV Networks: " . json_encode($game['tvNetworks'] ?? [], JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "No game\n";
}

