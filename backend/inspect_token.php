<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$channel = App\Models\Channel::find(27483);
if (!$channel) {
    echo "Channel not found\n";
    exit;
}

$enc = resolve(App\Services\EncryptionService::class);
$decrypted = $enc->decrypt($channel->stream_url);
echo "DECRYPTED URL:\n" . $decrypted . "\n";
