<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

$uri = "/api/scores/today";
echo "Testing {$uri}\n";
$request = Request::create($uri, 'GET');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);

echo "Status Code: " . $response->status() . "\n";
$body = $response->getContent();
$data = json_decode($body, true);
if (isset($data['games'])) {
    echo "Games found: " . count($data['games']) . "\n";
} else {
    echo "No games key found.\n";
    echo "Snippet: " . substr($body, 0, 300) . "\n";
}
