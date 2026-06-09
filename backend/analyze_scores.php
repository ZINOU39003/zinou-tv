<?php
$data = json_decode(file_get_contents('scores.json'), true);
if (!$data) {
    echo "No JSON data\n";
    exit;
}

$games = $data['games'] ?? [];
echo "Total games: " . count($games) . "\n";
foreach ($games as $g) {
    if (isset($g['tvNetworks']) || isset($g['broadcasters'])) {
        echo "Match " . $g['homeCompetitor']['name'] . " vs " . $g['awayCompetitor']['name'] . "\n";
        echo "TV Networks: " . json_encode($g['tvNetworks'] ?? []) . "\n";
        echo "Broadcasters: " . json_encode($g['broadcasters'] ?? []) . "\n";
    }
}
