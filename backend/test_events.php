<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$playerId = 27; // random player id
$url = "https://webws.365scores.com/web/player/?langId=24&playerId=$playerId&appTypeId=5";
$response = Illuminate\Support\Facades\Http::withoutVerifying()
    ->withHeaders([
        'User-Agent' => 'Mozilla/5.0',
    ])
    ->get($url);

echo json_encode(array_keys($response->json() ?? []), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if (isset($response->json()['player'])) {
    echo "\nHas player!";
}
