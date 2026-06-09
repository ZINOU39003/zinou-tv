<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\SportMatch;
use App\Models\Tournament;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // --- Tournaments ---
        $worldCup = Tournament::create([
            'name' => 'World Cup 2026',
            'name_ar' => 'كأس العالم 2026',
            'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e3/2026_FIFA_World_Cup.svg/200px-2026_FIFA_World_Cup.svg.png',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $championsLeague = Tournament::create([
            'name' => 'Champions League',
            'name_ar' => 'دوري أبطال أوروبا',
            'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/b/bf/UEFA_Champions_League_logo_2024.svg/200px-UEFA_Champions_League_logo_2024.svg.png',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $europaLeague = Tournament::create([
            'name' => 'Europa League',
            'name_ar' => 'الدوري الأوروبي',
            'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/0/03/Europa_League_2024.svg/200px-Europa_League_2024.svg.png',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $copaAmerica = Tournament::create([
            'name' => 'Copa America',
            'name_ar' => 'كوبا أمريكا',
            'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5b/Copa_Am%C3%A9rica_logo.svg/200px-Copa_Am%C3%A9rica_logo.svg.png',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // --- Matches (7 World Cup, 2 Champions League) ---
        SportMatch::create([
            'tournament_id' => $worldCup->id,
            'team_one_name' => 'Mexico',
            'team_one_name_ar' => 'المكسيك',
            'team_one_flag' => 'https://flagcdn.com/w80/mx.png',
            'team_two_name' => 'South Africa',
            'team_two_name_ar' => 'جنوب أفريقيا',
            'team_two_flag' => 'https://flagcdn.com/w80/za.png',
            'team_one_score' => 1,
            'team_two_score' => 0,
            'match_time' => '32:15',
            'match_date' => '2026-06-11',
            'is_live' => true,
            'is_world_cup' => true,
            'stream_url' => 'https://www.youtube.com/watch?v=youtube_placeholder_live',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        SportMatch::create([
            'tournament_id' => $worldCup->id,
            'team_one_name' => 'USA',
            'team_one_name_ar' => 'الولايات المتحدة',
            'team_one_flag' => 'https://flagcdn.com/w80/us.png',
            'team_two_name' => 'Morocco',
            'team_two_name_ar' => 'المغرب',
            'team_two_flag' => 'https://flagcdn.com/w80/ma.png',
            'team_one_score' => 0,
            'team_two_score' => 0,
            'match_time' => 'HT',
            'match_date' => '2026-06-12',
            'is_live' => true,
            'is_world_cup' => true,
            'stream_url' => 'https://www.youtube.com/watch?v=youtube_placeholder_live_2',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        SportMatch::create([
            'tournament_id' => $worldCup->id,
            'team_one_name' => 'Canada',
            'team_one_name_ar' => 'كندا',
            'team_one_flag' => 'https://flagcdn.com/w80/ca.png',
            'team_two_name' => 'Bosnia & Herzegovina',
            'team_two_name_ar' => 'البوسنة والهرسك',
            'team_two_flag' => 'https://flagcdn.com/w80/ba.png',
            'team_one_score' => 0,
            'team_two_score' => 0,
            'match_time' => '21:00',
            'match_date' => '2026-06-12',
            'is_live' => false,
            'is_world_cup' => true,
            'stream_url' => 'https://stream.example.com/live/wc-can-bih.m3u8',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        SportMatch::create([
            'tournament_id' => $worldCup->id,
            'team_one_name' => 'Spain',
            'team_one_name_ar' => 'إسبانيا',
            'team_one_flag' => 'https://flagcdn.com/w80/es.png',
            'team_two_name' => 'Scotland',
            'team_two_name_ar' => 'اسكتلندا',
            'team_two_flag' => 'https://flagcdn.com/w80/gb-sct.png',
            'team_one_score' => 0,
            'team_two_score' => 0,
            'match_time' => '17:00',
            'match_date' => '2026-06-13',
            'is_live' => false,
            'is_world_cup' => true,
            'stream_url' => 'https://stream.example.com/live/wc-esp-sco.m3u8',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        SportMatch::create([
            'tournament_id' => $worldCup->id,
            'team_one_name' => 'France',
            'team_one_name_ar' => 'فرنسا',
            'team_one_flag' => 'https://flagcdn.com/w80/fr.png',
            'team_two_name' => 'Ecuador',
            'team_two_name_ar' => 'الإكوادور',
            'team_two_flag' => 'https://flagcdn.com/w80/ec.png',
            'team_one_score' => 0,
            'team_two_score' => 0,
            'match_time' => '19:00',
            'match_date' => '2026-06-14',
            'is_live' => false,
            'is_world_cup' => true,
            'stream_url' => 'https://stream.example.com/live/wc-fra-ecu.m3u8',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        SportMatch::create([
            'tournament_id' => $worldCup->id,
            'team_one_name' => 'Brazil',
            'team_one_name_ar' => 'البرازيل',
            'team_one_flag' => 'https://flagcdn.com/w80/br.png',
            'team_two_name' => 'Saudi Arabia',
            'team_two_name_ar' => 'السعودية',
            'team_two_flag' => 'https://flagcdn.com/w80/sa.png',
            'team_one_score' => 0,
            'team_two_score' => 0,
            'match_time' => '20:00',
            'match_date' => '2026-06-15',
            'is_live' => false,
            'is_world_cup' => true,
            'stream_url' => 'https://stream.example.com/live/wc-bra-ksa.m3u8',
            'is_active' => true,
            'sort_order' => 6,
        ]);

        SportMatch::create([
            'tournament_id' => $worldCup->id,
            'team_one_name' => 'Argentina',
            'team_one_name_ar' => 'الأرجنتين',
            'team_one_flag' => 'https://flagcdn.com/w80/ar.png',
            'team_two_name' => 'Uruguay',
            'team_two_name_ar' => 'أوروغواي',
            'team_two_flag' => 'https://flagcdn.com/w80/uy.png',
            'team_one_score' => 0,
            'team_two_score' => 0,
            'match_time' => '22:00',
            'match_date' => '2026-06-15',
            'is_live' => false,
            'is_world_cup' => true,
            'stream_url' => 'https://stream.example.com/live/wc-arg-uru.m3u8',
            'is_active' => true,
            'sort_order' => 7,
        ]);

        SportMatch::create([
            'tournament_id' => $championsLeague->id,
            'team_one_name' => 'Real Madrid',
            'team_one_name_ar' => 'ريال مدريد',
            'team_one_flag' => 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Real_Madrid_CF.svg/50px-Real_Madrid_CF.svg.png',
            'team_two_name' => 'Manchester City',
            'team_two_name_ar' => 'مانشستر سيتي',
            'team_two_flag' => 'https://upload.wikimedia.org/wikipedia/en/thumb/e/eb/Manchester_City_FC_badge.svg/50px-Manchester_City_FC_badge.svg.png',
            'team_one_score' => 1,
            'team_two_score' => 1,
            'match_time' => '62:00',
            'match_date' => '2026-06-18',
            'is_live' => true,
            'is_world_cup' => false,
            'stream_url' => 'https://stream.example.com/live/ucl-rma-mci.m3u8',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        SportMatch::create([
            'tournament_id' => $championsLeague->id,
            'team_one_name' => 'Bayern Munich',
            'team_one_name_ar' => 'بايرن ميونخ',
            'team_one_flag' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg/50px-FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg.png',
            'team_two_name' => 'Barcelona',
            'team_two_name_ar' => 'برشلونة',
            'team_two_flag' => 'https://upload.wikimedia.org/wikipedia/en/thumb/4/47/FC_Barcelona_%28crest%29.svg/50px-FC_Barcelona_%28crest%29.svg.png',
            'team_one_score' => 2,
            'team_two_score' => 3,
            'match_time' => 'FT',
            'match_date' => '2026-06-17',
            'is_live' => false,
            'is_world_cup' => false,
            'stream_url' => 'https://stream.example.com/live/ucl-bay-bar.m3u8',
            'is_active' => true,
            'sort_order' => 6,
        ]);

        // --- Movies & Series ---
        Movie::create([
            'title' => 'The Dark Knight',
            'title_ar' => 'فارس الظلام',
            'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_SX300.jpg',
            'type' => 'movie',
            'stream_url' => 'https://stream.example.com/vod/the-dark-knight.m3u8',
            'description' => 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
            'description_ar' => 'عندما يعيث المجرم المعروف بالجوكر فسادًا وفوضى في شعب غوثام، يجب على باتمان أن يقبل واحدة من أعظم الاختبارات النفسية والجسدية لقدرته على محاربة الظلم.',
            'year' => 2008,
            'rating' => 9.0,
            'is_latest' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Movie::create([
            'title' => 'Inception',
            'title_ar' => 'استهلال',
            'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_SX300.jpg',
            'type' => 'movie',
            'stream_url' => 'https://stream.example.com/vod/inception.m3u8',
            'description' => 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.',
            'description_ar' => 'لص يسرق أسرار الشركات من خلال استخدام تكنولوجيا مشاركة الأحلام يُعطى المهمة العكسية بزرع فكرة في عقل رئيس تنفيذي.',
            'year' => 2010,
            'rating' => 8.8,
            'is_latest' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Movie::create([
            'title' => 'Oppenheimer',
            'title_ar' => 'أوبنهايمر',
            'poster_url' => 'https://m.media-amazon.com/images/M/MV5BMDBmYTZjNjUtN2M1MS00MTQ2LTk2ODgtNzc2M2QyZGE5NTVjXkEyXkFqcGdeQXVyNzAwMjU2MTY@._V1_SX300.jpg',
            'type' => 'movie',
            'stream_url' => 'https://stream.example.com/vod/oppenheimer.m3u8',
            'description' => 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.',
            'description_ar' => 'قصة العالم الأمريكي جي روبرت أوبنهايمر ودوره في تطوير القنبلة الذرية.',
            'year' => 2023,
            'rating' => 8.5,
            'is_latest' => true,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Movie::create([
            'title' => 'Dune: Part Two',
            'title_ar' => 'كثبان: الجزء الثاني',
            'poster_url' => 'https://m.media-amazon.com/images/M/MV5BN2QyZGU4ZDctOWMzMy00NTc5LThlOGQtODhmNDI1NmY5YzAwXkEyXkFqcGdeQXVyMDM2NDM2MQ@@._V1_SX300.jpg',
            'type' => 'movie',
            'stream_url' => 'https://stream.example.com/vod/dune-part-two.m3u8',
            'description' => 'Paul Atreides unites with the Fremen while on a warpath of revenge against the conspirators who destroyed his family.',
            'description_ar' => 'يتحد بول أتريدس مع الفريمن في مسار حرب انتقامية ضد المتآمرين الذين دمروا عائلته.',
            'year' => 2024,
            'rating' => 8.6,
            'is_latest' => true,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        Movie::create([
            'title' => 'Breaking Bad',
            'title_ar' => 'بريكنج باد',
            'poster_url' => 'https://m.media-amazon.com/images/M/MV5BYmQ4YWMxYjUtNjZmYi00MDQ1LWFjMjMtNjA5ZDdiYjdiODU5XkEyXkFqcGdeQXVyMTMzNDExODE5._V1_SX300.jpg',
            'type' => 'series',
            'stream_url' => 'https://stream.example.com/vod/breaking-bad-s01.m3u8',
            'description' => 'A chemistry teacher diagnosed with inoperable lung cancer turns to manufacturing and selling methamphetamine with a former student to secure his family\'s future.',
            'description_ar' => 'مدرس كيمياء يتم تشخيصه بسرطان الرئة غير القابل للعلاج يتحول إلى تصنيع وبيع الميثامفيتامين مع طالب سابق لتأمين مستقبل عائلته.',
            'year' => 2008,
            'rating' => 9.5,
            'is_latest' => false,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        Movie::create([
            'title' => 'The Last of Us',
            'title_ar' => 'ذا لاست أوف أس',
            'poster_url' => 'https://m.media-amazon.com/images/M/MV5BZGUzYTI3M2EtZmM0Yy00NGUyLWI4ODEtN2Q3ZGJlYzhhZjU3XkEyXkFqcGdeQXVyNTM0OTY1OQ@@._V1_SX300.jpg',
            'type' => 'series',
            'stream_url' => 'https://stream.example.com/vod/the-last-of-us-s01.m3u8',
            'description' => 'Joel and Ellie, a pair connected through the harshness of the world they live in, must survive a brutal journey across what remains of the United States.',
            'description_ar' => 'جويل وإيلي، ثنائي متصل من خلال قسوة العالم الذي يعيشون فيه، يجب أن يبقيا على قيد الحياة في رحلة وحشية عبر ما تبقى من الولايات المتحدة.',
            'year' => 2023,
            'rating' => 8.8,
            'is_latest' => true,
            'is_active' => true,
            'sort_order' => 6,
        ]);
    }
}
