<?php

namespace App\Console\Commands;

use App\Services\ChannelDataSyncService;
use Illuminate\Console\Command;

class RestoreRenderLegacy extends Command
{
    protected $signature = 'channels:restore-legacy {--merge-local : Also merge all local export channels without deleting}';

    protected $description = 'Restore Render legacy networks (World Cup 2026 + arab CHANNEL) and relink channels';

    public function handle(ChannelDataSyncService $sync): int
    {
        $this->info('Restoring Render legacy network structure...');
        $stats = $sync->restoreRenderLegacyStructure();

        $this->line("  Networks/packages: {$stats['categories']} categories, {$stats['packages']} packages");
        $this->line("  Relinked World Cup channels: {$stats['relinked_world_cup_channels']}");

        if ($this->option('merge-local')) {
            $path = base_path('database/data/channel-data-export.json');
            if (file_exists($path)) {
                $this->info('Merging local channel export (no delete)...');
                $merge = $sync->import(json_decode(file_get_contents($path), true), false);
                $this->line("  Merged channels: {$merge['channels']}, skipped: {$merge['skipped']}");
            }
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
