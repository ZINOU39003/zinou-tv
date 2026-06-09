<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Channel;

$placeholder = '/RyutFaTedJmMo11nUWUtG1pNlpQcGcxK2dlazFJQWRSSVduZlFGRXYzNnM1R1hrczc1VW9TdHVuM009';

// Define base names
$baseNames = [
    'BEIN SPORTS 1' => '/^BEIN SPORTS 1(\s|$)(?!XTRA)/i',
    'BEIN SPORTS 2' => '/^BEIN SPORTS 2(\s|$)(?!XTRA)/i',
    'BEIN SPORTS 3' => '/^BEIN SPORTS 3(\s|$)(?!XTRA)/i',
    'BEIN SPORTS 4' => '/^BEIN SPORTS 4(\s|$)(?!XTRA)/i',
    'BEIN SPORTS 5' => '/^BEIN SPORTS 5(\s|$)(?!XTRA)/i',
    'BEIN SPORTS 6' => '/^BEIN SPORTS 6(\s|$)(?!XTRA)/i',
    'BEIN SPORTS 7' => '/^BEIN SPORTS 7(\s|$)(?!XTRA)/i',
    'BEIN SPORTS 8' => '/^BEIN SPORTS 8(\s|$)(?!XTRA)/i',
    'BEIN SPORTS 9' => '/^BEIN SPORTS 9(\s|$)(?!XTRA)/i',
    'beIN Sports Max 1' => '/^beIN Sports Max 1(\s|$)/i',
    'beIN Sports Max 2' => '/^beIN Sports Max 2(\s|$)/i',
    'beIN Sports Max 3' => '/^beIN Sports Max 3(\s|$)/i',
    'beIN Sports Max 4' => '/^beIN Sports Max 4(\s|$)/i',
    'beIN Sports Max 5' => '/^beIN Sports Max 5(\s|$)/i',
    'beIN Sports Max 6' => '/^beIN Sports Max 6(\s|$)/i',
];

$allBein = Channel::where('name', 'like', '%beIN%')
    ->orWhere('name_ar', 'like', '%beIN%')
    ->get();

$updatedCount = 0;
$deletedCount = 0;

// First pass: Distribute links
foreach ($baseNames as $baseName => $regex) {
    $group = [];
    $validUrl = null;

    foreach ($allBein as $ch) {
        if (preg_match($regex, $ch->name)) {
            $group[] = $ch;
            if ($ch->stream_url !== $placeholder && !empty($ch->stream_url)) {
                $validUrl = $ch->stream_url;
            }
        }
    }

    if ($validUrl) {
        foreach ($group as $ch) {
            if ($ch->stream_url === $placeholder) {
                $ch->stream_url = $validUrl;
                $ch->save();
                $updatedCount++;
            }
        }
    }
}

// Reload channels to check for any that still have placeholder or empty URL
$allBeinAfterUpdate = Channel::where('name', 'like', '%beIN%')
    ->orWhere('name_ar', 'like', '%beIN%')
    ->get();

// Second pass: Delete empty channels
foreach ($allBeinAfterUpdate as $ch) {
    if ($ch->stream_url === $placeholder || empty($ch->stream_url)) {
        $ch->delete();
        $deletedCount++;
    }
}

echo "Successfully distributed $updatedCount links.\n";
echo "Successfully deleted $deletedCount BEIN channels that had no valid links.\n";
