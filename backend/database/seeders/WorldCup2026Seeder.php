<?php

namespace Database\Seeders;

use App\Models\SportMatch;
use App\Models\Tournament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorldCup2026Seeder extends Seeder
{
    public function run(): void
    {
        // Find or create World Cup 2026 tournament
        $tournament = Tournament::firstOrCreate(
            ['name' => 'FIFA World Cup 2026'],
            [
                'name_ar'    => 'كأس العالم FIFA 2026',
                'logo_url'   => 'https://upload.wikimedia.org/wikipedia/en/thumb/8/84/2026_FIFA_World_Cup.svg/200px-2026_FIFA_World_Cup.svg.png',
                'is_active'  => true,
                'sort_order' => 1,
            ]
        );

        // Delete existing World Cup matches to avoid duplicates
        SportMatch::where('tournament_id', $tournament->id)
                  ->where('is_world_cup', true)
                  ->delete();

        $flagBase = 'https://flagcdn.com/w80/';

        // All 64 group stage matches from the official schedule
        $matches = [

            // ====== 6/11 الخميس ======
            ['date' => '2026-06-11', 'time' => '22:00', 'team1_ar' => 'المكسيك',       'team1_en' => 'Mexico',               'flag1' => 'mx',
             'team2_ar' => 'جنوب أفريقيا', 'team2_en' => 'South Africa',          'flag2' => 'za'],

            // ====== 6/12 الجمعة ======
            ['date' => '2026-06-12', 'time' => '05:00', 'team1_ar' => 'التشيك',       'team1_en' => 'Czech Republic',       'flag1' => 'cz',
             'team2_ar' => 'كوريا الجنوبية', 'team2_en' => 'South Korea',          'flag2' => 'kr'],
            ['date' => '2026-06-12', 'time' => '22:00', 'team1_ar' => 'البوسنة والهرسك','team1_en' => 'Bosnia & Herzegovina','flag1' => 'ba',
             'team2_ar' => 'كندا',          'team2_en' => 'Canada',               'flag2' => 'ca'],

            // ====== 6/13 السبت ======
            ['date' => '2026-06-13', 'time' => '04:00', 'team1_ar' => 'باراغواي',     'team1_en' => 'Paraguay',            'flag1' => 'py',
             'team2_ar' => 'أمريكا',        'team2_en' => 'USA',                  'flag2' => 'us'],
            ['date' => '2026-06-13', 'time' => '22:00', 'team1_ar' => 'قطر',          'team1_en' => 'Qatar',               'flag1' => 'qa',
             'team2_ar' => 'سويسرا',        'team2_en' => 'Switzerland',          'flag2' => 'ch'],

            // ====== 6/14 الأحد ======
            ['date' => '2026-06-14', 'time' => '01:00', 'team1_ar' => 'المغرب',       'team1_en' => 'Morocco',             'flag1' => 'ma',
             'team2_ar' => 'البرازيل',      'team2_en' => 'Brazil',               'flag2' => 'br'],
            ['date' => '2026-06-14', 'time' => '04:00', 'team1_ar' => 'إسكتلندا',     'team1_en' => 'Scotland',            'flag1' => 'gb-sct',
             'team2_ar' => 'هايتي',         'team2_en' => 'Haiti',                'flag2' => 'ht'],
            ['date' => '2026-06-14', 'time' => '07:00', 'team1_ar' => 'تركيا',        'team1_en' => 'Turkey',              'flag1' => 'tr',
             'team2_ar' => 'أستراليا',      'team2_en' => 'Australia',            'flag2' => 'au'],
            ['date' => '2026-06-14', 'time' => '20:00', 'team1_ar' => 'ألمانيا',      'team1_en' => 'Germany',             'flag1' => 'de',
             'team2_ar' => 'كوراساو',       'team2_en' => 'Curaçao',             'flag2' => 'cw'],
            ['date' => '2026-06-14', 'time' => '23:00', 'team1_ar' => 'اليابان',      'team1_en' => 'Japan',               'flag1' => 'jp',
             'team2_ar' => 'هولندا',        'team2_en' => 'Netherlands',          'flag2' => 'nl'],

            // ====== 6/15 الاثنين ======
            ['date' => '2026-06-15', 'time' => '02:00', 'team1_ar' => 'كوت ديفوار',  'team1_en' => 'Ivory Coast',         'flag1' => 'ci',
             'team2_ar' => 'الإكوادور',     'team2_en' => 'Ecuador',              'flag2' => 'ec'],
            ['date' => '2026-06-15', 'time' => '05:00', 'team1_ar' => 'تونس',         'team1_en' => 'Tunisia',             'flag1' => 'tn',
             'team2_ar' => 'السويد',        'team2_en' => 'Sweden',               'flag2' => 'se'],
            ['date' => '2026-06-15', 'time' => '19:00', 'team1_ar' => 'إسبانيا',      'team1_en' => 'Spain',               'flag1' => 'es',
             'team2_ar' => 'الرأس الأخضر', 'team2_en' => 'Cape Verde',           'flag2' => 'cv'],
            ['date' => '2026-06-15', 'time' => '22:00', 'team1_ar' => 'بلجيكا',       'team1_en' => 'Belgium',             'flag1' => 'be',
             'team2_ar' => 'مصر',           'team2_en' => 'Egypt',                'flag2' => 'eg'],

            // ====== 6/16 الثلاثاء ======
            ['date' => '2026-06-16', 'time' => '01:00', 'team1_ar' => 'أوروغواي',     'team1_en' => 'Uruguay',             'flag1' => 'uy',
             'team2_ar' => 'السعودية',      'team2_en' => 'Saudi Arabia',         'flag2' => 'sa'],
            ['date' => '2026-06-16', 'time' => '04:00', 'team1_ar' => 'إيران',        'team1_en' => 'Iran',                'flag1' => 'ir',
             'team2_ar' => 'نيوزيلندا',     'team2_en' => 'New Zealand',          'flag2' => 'nz'],
            ['date' => '2026-06-16', 'time' => '22:00', 'team1_ar' => 'فرنسا',        'team1_en' => 'France',              'flag1' => 'fr',
             'team2_ar' => 'السنغال',       'team2_en' => 'Senegal',              'flag2' => 'sn'],

            // ====== 6/17 الأربعاء ======
            ['date' => '2026-06-17', 'time' => '01:00', 'team1_ar' => 'النرويج',      'team1_en' => 'Norway',              'flag1' => 'no',
             'team2_ar' => 'العراق',        'team2_en' => 'Iraq',                 'flag2' => 'iq'],
            ['date' => '2026-06-17', 'time' => '04:00', 'team1_ar' => 'الجزائر',      'team1_en' => 'Algeria',             'flag1' => 'dz',
             'team2_ar' => 'الأرجنتين',    'team2_en' => 'Argentina',            'flag2' => 'ar'],
            ['date' => '2026-06-17', 'time' => '07:00', 'team1_ar' => 'الأردن',       'team1_en' => 'Jordan',              'flag1' => 'jo',
             'team2_ar' => 'النمسا',        'team2_en' => 'Austria',              'flag2' => 'at'],
            ['date' => '2026-06-17', 'time' => '20:00', 'team1_ar' => 'البرتغال',     'team1_en' => 'Portugal',            'flag1' => 'pt',
             'team2_ar' => 'الكونغو',       'team2_en' => 'Congo',                'flag2' => 'cg'],
            ['date' => '2026-06-17', 'time' => '23:00', 'team1_ar' => 'إنجلترا',      'team1_en' => 'England',             'flag1' => 'gb-eng',
             'team2_ar' => 'كرواتيا',       'team2_en' => 'Croatia',              'flag2' => 'hr'],

            // ====== 6/18 الخميس ======
            ['date' => '2026-06-18', 'time' => '02:00', 'team1_ar' => 'بنما',         'team1_en' => 'Panama',              'flag1' => 'pa',
             'team2_ar' => 'غانا',          'team2_en' => 'Ghana',                'flag2' => 'gh'],
            ['date' => '2026-06-18', 'time' => '05:00', 'team1_ar' => 'كولومبيا',     'team1_en' => 'Colombia',            'flag1' => 'co',
             'team2_ar' => 'أوزبكستان',    'team2_en' => 'Uzbekistan',           'flag2' => 'uz'],
            ['date' => '2026-06-18', 'time' => '19:00', 'team1_ar' => 'جنوب أفريقيا','team1_en' => 'South Africa',        'flag1' => 'za',
             'team2_ar' => 'التشيك',        'team2_en' => 'Czech Republic',       'flag2' => 'cz'],
            ['date' => '2026-06-18', 'time' => '22:00', 'team1_ar' => 'البوسنة والهرسك','team1_en' => 'Bosnia & Herzegovina','flag1' => 'ba',
             'team2_ar' => 'سويسرا',        'team2_en' => 'Switzerland',          'flag2' => 'ch'],

            // ====== 6/19 الجمعة ======
            ['date' => '2026-06-19', 'time' => '01:00', 'team1_ar' => 'قطر',          'team1_en' => 'Qatar',               'flag1' => 'qa',
             'team2_ar' => 'كندا',          'team2_en' => 'Canada',               'flag2' => 'ca'],
            ['date' => '2026-06-19', 'time' => '04:00', 'team1_ar' => 'المكسيك',      'team1_en' => 'Mexico',              'flag1' => 'mx',
             'team2_ar' => 'كوريا الجنوبية','team2_en' => 'South Korea',          'flag2' => 'kr'],
            ['date' => '2026-06-19', 'time' => '22:00', 'team1_ar' => 'أمريكا',       'team1_en' => 'USA',                 'flag1' => 'us',
             'team2_ar' => 'أستراليا',      'team2_en' => 'Australia',            'flag2' => 'au'],

            // ====== 6/20 السبت ======
            ['date' => '2026-06-20', 'time' => '01:00', 'team1_ar' => 'المغرب',       'team1_en' => 'Morocco',             'flag1' => 'ma',
             'team2_ar' => 'إسكتلندا',      'team2_en' => 'Scotland',             'flag2' => 'gb-sct'],
            ['date' => '2026-06-20', 'time' => '03:30', 'team1_ar' => 'البرازيل',     'team1_en' => 'Brazil',              'flag1' => 'br',
             'team2_ar' => 'هايتي',         'team2_en' => 'Haiti',                'flag2' => 'ht'],
            ['date' => '2026-06-20', 'time' => '06:00', 'team1_ar' => 'باراغواي',     'team1_en' => 'Paraguay',            'flag1' => 'py',
             'team2_ar' => 'تركيا',         'team2_en' => 'Turkey',               'flag2' => 'tr'],
            ['date' => '2026-06-20', 'time' => '20:00', 'team1_ar' => 'السويد',       'team1_en' => 'Sweden',              'flag1' => 'se',
             'team2_ar' => 'هولندا',        'team2_en' => 'Netherlands',          'flag2' => 'nl'],
            ['date' => '2026-06-20', 'time' => '23:00', 'team1_ar' => 'ألمانيا',      'team1_en' => 'Germany',             'flag1' => 'de',
             'team2_ar' => 'كوت ديفوار',   'team2_en' => 'Ivory Coast',          'flag2' => 'ci'],

            // ====== 6/21 الأحد ======
            ['date' => '2026-06-21', 'time' => '03:00', 'team1_ar' => 'الإكوادور',    'team1_en' => 'Ecuador',             'flag1' => 'ec',
             'team2_ar' => 'كوراساو',       'team2_en' => 'Curaçao',             'flag2' => 'cw'],
            ['date' => '2026-06-21', 'time' => '07:00', 'team1_ar' => 'اليابان',      'team1_en' => 'Japan',               'flag1' => 'jp',
             'team2_ar' => 'تونس',          'team2_en' => 'Tunisia',              'flag2' => 'tn'],
            ['date' => '2026-06-21', 'time' => '19:00', 'team1_ar' => 'السعودية',     'team1_en' => 'Saudi Arabia',        'flag1' => 'sa',
             'team2_ar' => 'إسبانيا',       'team2_en' => 'Spain',                'flag2' => 'es'],
            ['date' => '2026-06-21', 'time' => '22:00', 'team1_ar' => 'إيران',        'team1_en' => 'Iran',                'flag1' => 'ir',
             'team2_ar' => 'بلجيكا',        'team2_en' => 'Belgium',              'flag2' => 'be'],

            // ====== 6/22 الاثنين ======
            ['date' => '2026-06-22', 'time' => '01:00', 'team1_ar' => 'الرأس الأخضر','team1_en' => 'Cape Verde',          'flag1' => 'cv',
             'team2_ar' => 'أوروغواي',      'team2_en' => 'Uruguay',              'flag2' => 'uy'],
            ['date' => '2026-06-22', 'time' => '04:00', 'team1_ar' => 'نيوزيلندا',    'team1_en' => 'New Zealand',         'flag1' => 'nz',
             'team2_ar' => 'مصر',           'team2_en' => 'Egypt',                'flag2' => 'eg'],
            ['date' => '2026-06-22', 'time' => '20:00', 'team1_ar' => 'النمسا',       'team1_en' => 'Austria',             'flag1' => 'at',
             'team2_ar' => 'الأرجنتين',    'team2_en' => 'Argentina',            'flag2' => 'ar'],

            // ====== 6/23 الثلاثاء ======
            ['date' => '2026-06-23', 'time' => '00:00', 'team1_ar' => 'العراق',       'team1_en' => 'Iraq',                'flag1' => 'iq',
             'team2_ar' => 'فرنسا',         'team2_en' => 'France',               'flag2' => 'fr'],
            ['date' => '2026-06-23', 'time' => '03:00', 'team1_ar' => 'السنغال',      'team1_en' => 'Senegal',             'flag1' => 'sn',
             'team2_ar' => 'النرويج',       'team2_en' => 'Norway',               'flag2' => 'no'],
            ['date' => '2026-06-23', 'time' => '06:00', 'team1_ar' => 'الجزائر',      'team1_en' => 'Algeria',             'flag1' => 'dz',
             'team2_ar' => 'الأردن',        'team2_en' => 'Jordan',               'flag2' => 'jo'],
            ['date' => '2026-06-23', 'time' => '20:00', 'team1_ar' => 'أوزبكستان',   'team1_en' => 'Uzbekistan',          'flag1' => 'uz',
             'team2_ar' => 'البرتغال',      'team2_en' => 'Portugal',             'flag2' => 'pt'],
            ['date' => '2026-06-23', 'time' => '23:00', 'team1_ar' => 'غانا',         'team1_en' => 'Ghana',               'flag1' => 'gh',
             'team2_ar' => 'إنجلترا',       'team2_en' => 'England',              'flag2' => 'gb-eng'],

            // ====== 6/24 الأربعاء ======
            ['date' => '2026-06-24', 'time' => '02:00', 'team1_ar' => 'كرواتيا',      'team1_en' => 'Croatia',             'flag1' => 'hr',
             'team2_ar' => 'بنما',          'team2_en' => 'Panama',               'flag2' => 'pa'],
            ['date' => '2026-06-24', 'time' => '05:00', 'team1_ar' => 'كولومبيا',     'team1_en' => 'Colombia',            'flag1' => 'co',
             'team2_ar' => 'الكونغو',       'team2_en' => 'Congo',                'flag2' => 'cg'],
            ['date' => '2026-06-24', 'time' => '22:00', 'team1_ar' => 'كندا',         'team1_en' => 'Canada',              'flag1' => 'ca',
             'team2_ar' => 'سويسرا',        'team2_en' => 'Switzerland',          'flag2' => 'ch'],
            ['date' => '2026-06-24', 'time' => '22:00', 'team1_ar' => 'قطر',          'team1_en' => 'Qatar',               'flag1' => 'qa',
             'team2_ar' => 'البوسنة والهرسك','team2_en' => 'Bosnia & Herzegovina','flag2' => 'ba'],

            // ====== 6/25 الخميس ======
            ['date' => '2026-06-25', 'time' => '01:00', 'team1_ar' => 'البرازيل',     'team1_en' => 'Brazil',              'flag1' => 'br',
             'team2_ar' => 'إسكتلندا',      'team2_en' => 'Scotland',             'flag2' => 'gb-sct'],
            ['date' => '2026-06-25', 'time' => '01:00', 'team1_ar' => 'هايتي',        'team1_en' => 'Haiti',               'flag1' => 'ht',
             'team2_ar' => 'المغرب',        'team2_en' => 'Morocco',              'flag2' => 'ma'],
            ['date' => '2026-06-25', 'time' => '04:00', 'team1_ar' => 'المكسيك',      'team1_en' => 'Mexico',              'flag1' => 'mx',
             'team2_ar' => 'التشيك',        'team2_en' => 'Czech Republic',       'flag2' => 'cz'],
            ['date' => '2026-06-25', 'time' => '04:00', 'team1_ar' => 'كوريا الجنوبية','team1_en' => 'South Korea',        'flag1' => 'kr',
             'team2_ar' => 'جنوب أفريقيا', 'team2_en' => 'South Africa',         'flag2' => 'za'],
            ['date' => '2026-06-25', 'time' => '23:00', 'team1_ar' => 'كوراساو',      'team1_en' => 'Curaçao',            'flag1' => 'cw',
             'team2_ar' => 'الإكوادور',    'team2_en' => 'Ecuador',              'flag2' => 'ec'],

            // ====== 6/26 الجمعة ======
            ['date' => '2026-06-26', 'time' => '02:00', 'team1_ar' => 'السويد',       'team1_en' => 'Sweden',              'flag1' => 'se',
             'team2_ar' => 'اليابان',       'team2_en' => 'Japan',                'flag2' => 'jp'],
            ['date' => '2026-06-26', 'time' => '02:00', 'team1_ar' => 'هولندا',       'team1_en' => 'Netherlands',         'flag1' => 'nl',
             'team2_ar' => 'تونس',          'team2_en' => 'Tunisia',              'flag2' => 'tn'],
            ['date' => '2026-06-26', 'time' => '05:00', 'team1_ar' => 'أمريكا',       'team1_en' => 'USA',                 'flag1' => 'us',
             'team2_ar' => 'تركيا',         'team2_en' => 'Turkey',               'flag2' => 'tr'],
            ['date' => '2026-06-26', 'time' => '05:00', 'team1_ar' => 'أستراليا',     'team1_en' => 'Australia',           'flag1' => 'au',
             'team2_ar' => 'باراغواي',      'team2_en' => 'Paraguay',             'flag2' => 'py'],
            ['date' => '2026-06-26', 'time' => '22:00', 'team1_ar' => 'فرنسا',        'team1_en' => 'France',              'flag1' => 'fr',
             'team2_ar' => 'النرويج',       'team2_en' => 'Norway',               'flag2' => 'no'],
            ['date' => '2026-06-26', 'time' => '22:00', 'team1_ar' => 'العراق',       'team1_en' => 'Iraq',                'flag1' => 'iq',
             'team2_ar' => 'السنغال',       'team2_en' => 'Senegal',              'flag2' => 'sn'],

            // ====== 6/27 السبت ======
            ['date' => '2026-06-27', 'time' => '03:00', 'team1_ar' => 'السعودية',     'team1_en' => 'Saudi Arabia',        'flag1' => 'sa',
             'team2_ar' => 'الرأس الأخضر', 'team2_en' => 'Cape Verde',           'flag2' => 'cv'],
            ['date' => '2026-06-27', 'time' => '03:00', 'team1_ar' => 'إسبانيا',      'team1_en' => 'Spain',               'flag1' => 'es',
             'team2_ar' => 'أوروغواي',      'team2_en' => 'Uruguay',              'flag2' => 'uy'],
            ['date' => '2026-06-27', 'time' => '06:00', 'team1_ar' => 'إيران',        'team1_en' => 'Iran',                'flag1' => 'ir',
             'team2_ar' => 'مصر',           'team2_en' => 'Egypt',                'flag2' => 'eg'],
            ['date' => '2026-06-27', 'time' => '06:00', 'team1_ar' => 'بلجيكا',       'team1_en' => 'Belgium',             'flag1' => 'be',
             'team2_ar' => 'نيوزيلندا',     'team2_en' => 'New Zealand',          'flag2' => 'nz'],

            // ====== 6/28 الأحد ======
            ['date' => '2026-06-28', 'time' => '00:00', 'team1_ar' => 'إنجلترا',      'team1_en' => 'England',             'flag1' => 'gb-eng',
             'team2_ar' => 'بنما',          'team2_en' => 'Panama',               'flag2' => 'pa'],
            ['date' => '2026-06-28', 'time' => '00:00', 'team1_ar' => 'غانا',         'team1_en' => 'Ghana',               'flag1' => 'gh',
             'team2_ar' => 'كرواتيا',       'team2_en' => 'Croatia',              'flag2' => 'hr'],
            ['date' => '2026-06-28', 'time' => '02:30', 'team1_ar' => 'البرتغال',     'team1_en' => 'Portugal',            'flag1' => 'pt',
             'team2_ar' => 'كولومبيا',      'team2_en' => 'Colombia',             'flag2' => 'co'],
            ['date' => '2026-06-28', 'time' => '02:30', 'team1_ar' => 'الكونغو',      'team1_en' => 'Congo',               'flag1' => 'cg',
             'team2_ar' => 'أوزبكستان',    'team2_en' => 'Uzbekistan',           'flag2' => 'uz'],
            ['date' => '2026-06-28', 'time' => '05:00', 'team1_ar' => 'النمسا',       'team1_en' => 'Austria',             'flag1' => 'at',
             'team2_ar' => 'الجزائر',       'team2_en' => 'Algeria',              'flag2' => 'dz'],
            ['date' => '2026-06-28', 'time' => '05:00', 'team1_ar' => 'الأرجنتين',   'team1_en' => 'Argentina',           'flag1' => 'ar',
             'team2_ar' => 'الأردن',        'team2_en' => 'Jordan',               'flag2' => 'jo'],
        ];

        // Arabic day names map
        $dayNames = [
            0 => 'الأحد', 1 => 'الاثنين', 2 => 'الثلاثاء',
            3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت',
        ];

        foreach ($matches as $index => $m) {
            SportMatch::create([
                'tournament_id'   => $tournament->id,
                'team_one_name'   => $m['team1_en'],
                'team_one_name_ar'=> $m['team1_ar'],
                'team_one_flag'   => $flagBase . $m['flag1'] . '.png',
                'team_two_name'   => $m['team2_en'],
                'team_two_name_ar'=> $m['team2_ar'],
                'team_two_flag'   => $flagBase . $m['flag2'] . '.png',
                'team_one_score'  => 0,
                'team_two_score'  => 0,
                'match_time'      => $m['time'],
                'match_date'      => $m['date'],
                'is_live'         => false,
                'is_world_cup'    => true,
                'stream_url'      => null, // to be assigned manually per match
                'is_active'       => true,
                'sort_order'      => $index + 1,
            ]);
        }

        $this->command->info('✅ تم إدراج ' . count($matches) . ' مباراة لكأس العالم 2026 بنجاح!');
    }
}
