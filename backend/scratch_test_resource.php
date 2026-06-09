<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Channel;
use App\Http\Resources\ChannelResource;
use Illuminate\Http\Request;

// Create dummy request
$request = Request::create('/api/channels/27483', 'GET');

$channel = Channel::find(27483);
if (!$channel) {
    echo "Channel 27483 not found\n";
    exit;
}

$resource = new ChannelResource($channel);
$data = $resource->toArray($request);

echo "Channel ID: " . $data['id'] . "\n";
echo "Name: " . $data['name'] . "\n";
echo "Original Encrypted stream_url in DB: " . $channel->stream_url . "\n";
echo "Resource returned stream_url: " . $data['stream_url'] . "\n";
echo "Resource backup_url: " . $data['backup_url'] . "\n";
echo "Resource servers count: " . count($data['servers']) . "\n";
if (count($data['servers']) > 0) {
    echo "First server stream_url: " . $data['servers'][0]['stream_url'] . "\n";
}
