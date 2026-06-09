<?php
$playerId = 53744; // Mbappe
$paths = ['Athletes', 'Competitors', 'Players'];
foreach ($paths as $path) {
    $url = "https://imagecache.365scores.com/image/upload/f_png,w_120,h_120,c_limit/v5/$path/$playerId";
    $headers = get_headers($url);
    echo "$path: " . $headers[0] . "\n";
}
