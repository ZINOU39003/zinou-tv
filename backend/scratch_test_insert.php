<?php

use App\Models\Channel;
use App\Services\EncryptionService;
use App\Enums\StreamType;
use App\Enums\ChannelQuality;

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$encryptionService = resolve(EncryptionService::class);
$streamUrl = 'http://het131c.ycn-redirect.com/live/33523510/index.m3u8';
$encryptedUrl = $encryptionService->encrypt($streamUrl);

$channel = Channel::create([
    'name' => 'OzTV Test Channel',
    'name_ar' => 'قناة اختبار OzTV',
    'category_id' => 335, // بي ان سبورت ➤ LOCAL 8K
    'logo_url' => 'https://via.placeholder.com/150',
    'stream_url' => $encryptedUrl,
    'stream_type' => StreamType::M3U8,
    'quality' => ChannelQuality::HD,
    'drm_headers' => json_encode([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',
        'Referer' => 'https://x.com'
    ]),
    'is_active' => true,
    'sort_order' => 1
]);

echo "Channel created successfully! ID: " . $channel->id . "\n";
