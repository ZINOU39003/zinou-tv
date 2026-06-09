<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

// Create an internal request for the stream proxy route
// We use channel ID 27483 (from our database list)
$channelId = 27483;
$uri = "/stream-proxy/{$channelId}/playlist_ha.mpd";

echo "Creating request to: {$uri}\n";
$request = Request::create($uri, 'GET');

// Handle the request using the router/kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);

echo "Response Status Code: " . $response->status() . "\n";
echo "Response Content-Type: " . $response->headers->get('Content-Type') . "\n";
echo "Response Headers:\n";
foreach ($response->headers->all() as $name => $values) {
    echo "  {$name}: " . implode(', ', $values) . "\n";
}

$body = $response->getContent();
echo "\nResponse Body Snippet (first 500 chars):\n";
echo substr($body, 0, 500) . "\n";

$kernel->terminate($request, $response);
