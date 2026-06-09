<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Tournament;

class FetchStandingsCommand extends Command
{
    protected $signature = 'app:fetch-standings';
    protected $description = 'Fetch standings from ESPN API';

    // List of major leagues to fetch
    protected $leagues = [
        'eng.1' => 'Premier League',
        'esp.1' => 'La Liga',
        'ger.1' => 'Bundesliga',
        'ita.1' => 'Serie A',
        'fra.1' => 'Ligue 1',
        'uefa.champions' => 'Champions League',
        'uefa.europa' => 'Europa League',
        'fifa.world' => 'World Cup',
    ];

    public function handle()
    {
        $this->info("Fetching standings from ESPN API...");

        foreach ($this->leagues as $leagueCode => $leagueName) {
            $this->info("Fetching {$leagueName}...");
            $url = "https://site.api.espn.com/apis/site/v2/sports/soccer/{$leagueCode}/standings";
            
            $response = Http::withoutVerifying()->get($url);

            if (!$response->successful()) {
                $this->error("Failed to fetch {$leagueName}. Status: " . $response->status());
                continue;
            }

            $data = $response->json();
            
            $standings = [];
            if (isset($data['children'])) {
                $standings = $data['children'];
            }

            // Find tournament
            $tournament = Tournament::where('name', $leagueName)->first();
            
            if ($tournament) {
                $tournament->update([
                    'standings' => $standings
                ]);
                $this->info("Saved standings for {$leagueName}");
            } else {
                $this->warn("Tournament {$leagueName} not found in DB.");
            }
        }

        $this->info('All standings fetched successfully!');
    }
}
