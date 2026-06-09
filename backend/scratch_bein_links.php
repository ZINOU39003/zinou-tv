<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Channel;

$placeholder = '/RyutFaTedJmMo11nUWUtG1pNlpQcGcxK2dlazFJQWRSSVduZlFGRXYzNnM1R1hrczc1VW9TdHVuM009';

// Find channels that DO NOT have the placeholder
$channels = Channel::where('stream_url', '!=', $placeholder)
    ->with('servers')
    ->get();

echo "User updated channels:\n";
foreach ($channels as $channel) {
    echo "Channel: {$channel->name} (ID: {$channel->id})\n";
    echo "  Main URL: {$channel->stream_url}\n";
}
