<?php

namespace App\Console\Commands;

use App\Services\ChannelDataSyncService;
use Illuminate\Console\Command;

class ImportChannelData extends Command
{
    protected $signature = 'channels:import {path=database/data/channel-data-export.json} {--replace : Delete existing data before import}';

    protected $description = 'Import categories, packages and channels from JSON export';

    public function handle(ChannelDataSyncService $sync): int
    {
        $path = base_path($this->argument('path'));

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($path), true);
        if (! is_array($data)) {
            $this->error('Invalid JSON file.');

            return self::FAILURE;
        }

        $stats = $sync->import($data, (bool) $this->option('replace'));

        $this->info('Import complete:');
        $this->line("  Categories: {$stats['categories']}");
        $this->line("  Packages: {$stats['packages']}");
        $this->line("  Channels: {$stats['channels']}");
        $this->line("  Skipped: {$stats['skipped']}");

        return self::SUCCESS;
    }
}
