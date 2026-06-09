<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Channel;
use App\Enums\ChannelQuality;
use App\Enums\StreamType;
use App\Services\EncryptionService;
use Illuminate\Database\Seeder;

class SampleChannelSeeder extends Seeder
{
    public function run(): void
    {
        $encryptionService = resolve(EncryptionService::class);

        $sports = Category::where('slug', 'sports')->first();
        $movies = Category::where('slug', 'movies')->first();
        $news = Category::where('slug', 'news')->first();

        // Sample channels
        $channels = [
            // Sports
            [
                'name' => 'World Cup 2026: USA vs Mexico',
                'name_ar' => 'كأس العالم 2026: أمريكا ضد المكسيك',
                'category_id' => $sports->id,
                'logo_url' => 'https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/bein1.png',
                'stream_url' => $encryptionService->encrypt('https://demo.unified-streaming.com/k8s/live/stable/sintel.smil/.m3u8'),
                'stream_type' => StreamType::M3U8,
                'quality' => ChannelQuality::HD,
                'backup_url' => $encryptionService->encrypt('http://sample.vodobox.com/planete_libre/planete_libre.m3u8'),
                'sort_order' => 1,
            ],
            [
                'name' => 'World Cup 2026: Canada vs France',
                'name_ar' => 'كأس العالم 2026: كندا ضد فرنسا',
                'category_id' => $sports->id,
                'logo_url' => 'https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/bein2.png',
                'stream_url' => $encryptionService->encrypt('http://playertest.longtailvideo.com/adaptive/bipbop/bipbop.m3u8'),
                'stream_type' => StreamType::M3U8,
                'quality' => ChannelQuality::HD,
                'backup_url' => null,
                'sort_order' => 2,
            ],
            
            // Movies
            [
                'name' => 'MBC Action FHD',
                'name_ar' => 'إم بي سي أكشن FHD',
                'category_id' => $movies->id,
                'logo_url' => 'https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/mbc_action.png',
                'stream_url' => $encryptionService->encrypt('https://bitdash-a.akamaihd.net/content/sintel/hls/playlist.m3u8'),
                'stream_type' => StreamType::M3U8,
                'quality' => ChannelQuality::FHD,
                'backup_url' => null,
                'sort_order' => 1,
            ],

            // News
            [
                'name' => 'Al Jazeera News',
                'name_ar' => 'الجزيرة الإخبارية',
                'category_id' => $news->id,
                'logo_url' => 'https://raw.githubusercontent.com/souhailzou/sport-iptv-logos/main/aljazeera.png',
                'stream_url' => $encryptionService->encrypt('http://amg-aljazeera-01-ch01-jazeera-arabic-live.fastly.amagi.tv/playlist.m3u8'),
                'stream_type' => StreamType::M3U8,
                'quality' => ChannelQuality::SD,
                'backup_url' => null,
                'sort_order' => 1,
            ]
        ];

        foreach ($channels as $channel) {
            Channel::firstOrCreate(['name' => $channel['name']], $channel);
        }
    }
}
