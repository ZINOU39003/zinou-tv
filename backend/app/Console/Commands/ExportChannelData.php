<?php

namespace App\Console\Commands;

use App\Services\ChannelDataSyncService;
use Illuminate\Console\Command;

class ExportChannelData extends Command
{
    protected $signature = 'channels:export {path=storage/app/channel-data-export.json}';

    protected $description = 'Export categories, packages and channels to JSON';

    public function handle(ChannelDataSyncService $sync): int
    {
        $path = $this->argument('path');
        $fullPath = base_path($path);
        $data = $sync->export();

        if (! is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info('Exported:');
        $this->line('  Categories: '.count($data['categories']));
        $this->line('  Packages: '.count($data['packages']));
        $this->line('  Channels: '.count($data['channels']));
        $this->info("Saved to: {$fullPath}");

        return self::SUCCESS;
    }
}
