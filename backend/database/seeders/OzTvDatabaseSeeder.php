<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Channel;
use App\Services\EncryptionService;
use App\Enums\StreamType;
use App\Enums\ChannelQuality;
use Illuminate\Support\Str;

class OzTvDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Disable foreign keys and truncate existing tables to clean database
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Channel::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $encryptionService = resolve(EncryptionService::class);
        $placeholderUrl = $encryptionService->encrypt('http://placeholder.com/');

        // 2. Parse BeIN Sports channels from assets/bein_sports.m3u
        $m3uPath = base_path('../scratch/oztv_extracted/assets/bein_sports.m3u');
        
        if (!file_exists($m3uPath)) {
            $this->command->error("bein_sports.m3u file not found in scratch folder!");
            return;
        }

        $lines = file($m3uPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $currentChannel = null;
        $categoriesMap = [];

        foreach ($lines as $line) {
            $line = trim($line);
            
            if (str_starts_with($line, '#EXTINF')) {
                // Parse logo URL
                $logoUrl = null;
                if (preg_match('/tvg-logo="([^"]+)"/', $line, $logoMatches)) {
                    $logoUrl = $logoMatches[1];
                }
                
                // Parse group-title and channel name
                $groupTitle = 'BEIN SPORTS';
                $channelName = 'BeIN Sports';
                
                $groupStart = strpos($line, 'group-title="');
                if ($groupStart !== false) {
                    $groupStart += strlen('group-title="');
                    $commaPos = strpos($line, ',', $groupStart);
                    if ($commaPos !== false) {
                        $groupTitle = substr($line, $groupStart, $commaPos - $groupStart);
                        $channelName = substr($line, $commaPos + 1);
                    } else {
                        $groupTitle = substr($line, $groupStart);
                        $channelName = $groupTitle;
                    }
                }
                
                // Remove unicode vertical bars and labels
                $groupTitle = trim(str_replace(['┃AR┃', '┃', 'AR'], '', $groupTitle));
                $channelName = trim(str_replace(['┃AR┃', '┃', 'AR'], '', $channelName));
                
                if (empty($groupTitle)) {
                    $groupTitle = 'BEIN SPORTS';
                }

                $currentChannel = [
                    'name' => $channelName,
                    'logo_url' => $logoUrl,
                    'group' => $groupTitle
                ];
            } elseif (str_starts_with($line, 'http') && $currentChannel) {
                // Determine Category
                $groupName = $currentChannel['group'];
                if (!isset($categoriesMap[$groupName])) {
                    $category = Category::create([
                        'name' => $groupName,
                        'name_ar' => $groupName,
                        'slug' => Str::slug($groupName),
                        'is_active' => true,
                        'type' => 'content_type',
                        'sort_order' => count($categoriesMap) + 1
                    ]);
                    $categoriesMap[$groupName] = $category;
                }
                
                $category = $categoriesMap[$groupName];
                
                // Check if this is the channel we want to make our test channel (e.g. BeIN Sports 7)
                $isTestChannel = str_contains(strtoupper($currentChannel['name']), 'BEIN SPORTS 7');
                $streamUrlToSave = $placeholderUrl;
                $drmHeadersToSave = null;
                
                if ($isTestChannel) {
                    $streamUrlToSave = $encryptionService->encrypt('http://het131c.ycn-redirect.com/live/33523510/index.m3u8');
                    $drmHeadersToSave = json_encode([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',
                        'Referer' => 'https://x.com'
                    ]);
                }

                Channel::create([
                    'name' => $currentChannel['name'],
                    'name_ar' => $currentChannel['name'],
                    'category_id' => $category->id,
                    'logo_url' => $currentChannel['logo_url'],
                    'stream_url' => $streamUrlToSave,
                    'stream_type' => StreamType::M3U8,
                    'quality' => ChannelQuality::HD,
                    'drm_headers' => $drmHeadersToSave,
                    'is_active' => true,
                    'sort_order' => 0
                ]);
                
                $currentChannel = null;
            }
        }

        // 3. Add other standard Yacine TV / OzTV Categories
        $otherCategories = [
            [
                'name' => 'قنوات SSC الرياضية',
                'name_ar' => 'قنوات SSC الرياضية',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/af/Saudi_Sports_Company_logo_20210721.png',
                'channels' => [
                    'SSC 1 HD', 'SSC 2 HD', 'SSC 3 HD', 'SSC 4 HD', 'SSC 5 HD', 'SSC EXTRA 1 HD', 'SSC EXTRA 2 HD'
                ]
            ],
            [
                'name' => 'قنوات SHAHID VIP',
                'name_ar' => 'قنوات SHAHID VIP',
                'logo' => 'https://www.arabnews.com/sites/default/files/styles/n_670_395/public/shahid_vip.png?itok=6ghFza7t',
                'channels' => [
                    'Shahid Cinema', 'Shahid Drama', 'Shahid Masr', 'Shahid Khaleeji'
                ]
            ],
            [
                'name' => 'قنوات MBC ترفيهية',
                'name_ar' => 'قنوات MBC ترفيهية',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/MBC_Group_logo.svg/320px-MBC_Group_logo.svg.png',
                'channels' => [
                    'MBC 1', 'MBC 2', 'MBC 3', 'MBC 4', 'MBC Action', 'MBC Max', 'MBC Drama', 'MBC Bollywood'
                ]
            ],
            [
                'name' => 'قنوات الكأس الرياضية',
                'name_ar' => 'قنوات الكأس الرياضية',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/23/Al_Kass_logo.svg/320px-Al_Kass_logo.svg.png',
                'channels' => [
                    'AlKass One HD', 'AlKass Two HD', 'AlKass Three HD', 'AlKass Four HD', 'AlKass Five HD'
                ]
            ],
            [
                'name' => 'قنوات أبو ظبي الرياضية',
                'name_ar' => 'قنوات أبو ظبي الرياضية',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Abu_Dhabi_Media_logo.svg/320px-Abu_Dhabi_Media_logo.svg.png',
                'channels' => [
                    'AD Sports 1 HD', 'AD Sports 2 HD', 'AD Sports Premium 1', 'AD Sports Premium 2'
                ]
            ],
            [
                'name' => 'قنوات الأطفال',
                'name_ar' => 'قنوات الأطفال',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Globe_icon.svg/320px-Globe_icon.svg.png',
                'channels' => [
                    'Spacetoon', 'CN Arabic', 'Majid TV', 'MBC 3 Kids', 'Rotana Kids'
                ]
            ],
            [
                'name' => 'باقة نتفليكس',
                'name_ar' => 'باقة نتفليكس',
                'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Netflix_2015_logo.svg/320px-Netflix_2015_logo.svg.png',
                'channels' => [
                    'Netflix Action VIP', 'Netflix Comedy VIP', 'Netflix Horror VIP', 'Netflix Documentaries'
                ]
            ],
        ];

        foreach ($otherCategories as $catData) {
            $category = Category::create([
                'name' => $catData['name'],
                'name_ar' => $catData['name_ar'],
                'slug' => Str::slug($catData['name']),
                'icon' => $catData['logo'],
                'is_active' => true,
                'type' => 'content_type',
                'sort_order' => count($categoriesMap) + 1
            ]);

            foreach ($catData['channels'] as $index => $chanName) {
                Channel::create([
                    'name' => $chanName,
                    'name_ar' => $chanName,
                    'category_id' => $category->id,
                    'logo_url' => $catData['logo'],
                    'stream_url' => $placeholderUrl,
                    'stream_type' => StreamType::M3U8,
                    'quality' => ChannelQuality::HD,
                    'is_active' => true,
                    'sort_order' => $index
                ]);
            }
        }
    }
}
