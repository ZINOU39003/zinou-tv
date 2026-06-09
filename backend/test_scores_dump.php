<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

$uri = "/api/scores/today";
$request = Request::create($uri, 'GET');
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);

$body = $response->getContent();
$data = json_decode($body, true);
if (isset($data['games'])) {
    $slice = array_slice($data['games'], 0, 5);
    file_put_contents('response.json', json_encode($slice, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "Saved to response.json\n";
} else {
    echo "No games key\n";
}
