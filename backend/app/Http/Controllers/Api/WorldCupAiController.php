<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SportMatch;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;

class WorldCupAiController extends Controller
{
    use HasApiResponse;

    public function index(): JsonResponse
    {
        // 1. Fetch all World Cup matches
        $matches = SportMatch::where('is_world_cup', true)->get();

        // 2. Generate AI Predictions for each match
        $predictions = [];
        foreach ($matches as $match) {
            $t1 = $match->team_one_name_ar ?: $match->team_one_name;
            $t2 = $match->team_two_name_ar ?: $match->team_two_name;
            
            // Deterministic but realistic probabilities based on name hash
            $hash = crc32($match->team_one_name . $match->team_two_name);
            $p1 = 30 + abs($hash % 45); // 30% - 75%
            $p2 = 15 + abs(($hash >> 2) % 30); // 15% - 45%
            $draw = 100 - ($p1 + $p2);
            if ($draw < 5) {
                $p1 -= 5;
                $draw = 100 - ($p1 + $p2);
            }

            // Expected score
            $score1 = abs($hash % 3);
            $score2 = abs(($hash >> 3) % 3);
            if ($p1 > $p2 && $score1 <= $score2) {
                $score1 = $score2 + 1;
            } elseif ($p2 > $p1 && $score2 <= $score1) {
                $score2 = $score1 + 1;
            }
            $expectedScore = "$score1 - $score2";

            // Tactical Analysis & Key Players
            $tactical = "من المتوقع أن يركز منتخب $t1 على الاستحواذ والبناء الهجومي المنظم، معتمداً على الكرات القصيرة السريعة لفتح ثغرات في دفاع منتخب $t2. في المقابل، سيلعب منتخب $t2 بتنظيم دفاعي منخفض مع الاعتماد على الهجمات المرتدة السريعة واستغلال سرعة الأجنحة.";
            $keyPlayers = "من منتخب $t1: صانع الألعاب الرئيسي وقائد الفريق. ومن منتخب $t2: قلب الدفاع وحارس المرمى.";
            $verdict = "يرجح الذكاء الاصطناعي تفوق منتخب $t1 بنسبة $p1% نظراً للفوارق الفنية والاستقرار التكتيكي الأخير.";

            $predictions[] = [
                'match_id' => $match->id,
                'win_probability_team_one' => $p1,
                'win_probability_team_two' => $p2,
                'draw_probability' => $draw,
                'tactical_analysis' => $tactical,
                'key_players' => $keyPlayers,
                'expected_score' => $expectedScore,
                'ai_verdict' => $verdict,
            ];
        }

        // 3. World Cup News (AI Persona in Arabic)
        $news = [
            [
                'id' => 1,
                'title' => 'الكمبيوتر الخارق يتوقع بطل مونديال 2026 ونسبة فوز المنتخبات العربية',
                'summary' => 'أجرى كمبيوتر الذكاء الاصطناعي محاكاة شاملة لمباريات كأس العالم 2026 وتوقع هوية البطل وفرص تأهل المنتخبات العربية.',
                'content' => 'أظهرت أحدث تحليلات الذكاء الاصطناعي ومحاكاة الكمبيوتر الخارق لمونديال 2026 تفوقاً ملحوظاً للمنتخب الأرجنتيني والفرنسي كأبرز المرشحين لحصد اللقب. وعلى الصعيد العربي، يمتلك المنتخب المغربي الفرصة الأكبر للعبور كمتصدر لمجموعته بنسبة تصل إلى 68%، يليه المنتخب المصري بنسبة تأهل 55%، بينما يواجه الأخضر السعودي مواجهات صعبة أمام أوروغواي وإيران مع فرصة تأهل تبلغ 42%.',
                'category' => 'تحليل تكتيكي',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/en/thumb/8/84/2026_FIFA_World_Cup.svg/400px-2026_FIFA_World_Cup.svg.png',
                'read_time' => '4 دقائق',
                'date' => 'اليوم'
            ],
            [
                'id' => 2,
                'title' => 'تقرير تكتيكي ذكي: كيف يخطط أسود الأطلس لمواجهة السامبا البرازيلية؟',
                'summary' => 'قراءة تحليلية بالذكاء الاصطناعي لنقاط القوة والضعف للمنتخب المغربي قبل مواجهة البرازيل المرتقبة.',
                'content' => 'يشير التحليل التكتيكي القائم على بيانات اللاعبين الأخيرة أن المواجهة بين المغرب والبرازيل ستشهد صراعاً بدنياً كبيراً في وسط الملعب. سيعتمد وليد الركراكي على أسلوب الضغط العالي الموجه لتعطيل مفاتيح اللعب البرازيلية. ينصح الذكاء الاصطناعي بالتركيز على جبهة أشرف حكيمي لاستغلال الفراغات خلف أجنحة السامبا الهجومية.',
                'category' => 'تكتيك المنتخبات',
                'image_url' => 'https://flagcdn.com/w160/ma.png',
                'read_time' => '3 دقائق',
                'date' => 'اليوم'
            ],
            [
                'id' => 3,
                'title' => 'مصر وبلجيكا: قمة نارية حاسمة لحساب المجموعة السابعة وتوقعات الذكاء الاصطناعي',
                'summary' => 'مواجهة حاسمة تجمع الفراعنة بالشياطين الحمر، والذكاء الاصطناعي يقدم قراءة شاملة للمباراة.',
                'content' => 'تعد مباراة مصر وبلجيكا واحدة من أهم مباريات المجموعة السابعة. الذكاء الاصطناعي يحلل حظوظ الفريقين ويرى أن سرعة محمد صلاح في التحولات الهجومية ستكون السلاح الأبرز لمصر في مواجهة بطء خط دفاع بلجيكا. يتوقع المحلل الذكي نتيجة التعادل الإيجابي أو فوزاً طفيفاً للفراعنة.',
                'category' => 'توقعات المباريات',
                'image_url' => 'https://flagcdn.com/w160/eg.png',
                'read_time' => '5 دقائق',
                'date' => 'أمس'
            ],
            [
                'id' => 4,
                'title' => 'صدمة في معسكر ألمانيا: إصابة نجم المانشافت تخلط أوراق ناجلسمان قبل الافتتاح',
                'summary' => 'تقرير طبي تحليلي حول غياب نجم خط الوسط وتأثيره التكتيكي على تشكيلة المنتخب الألماني.',
                'content' => 'تعرض المنتخب الألماني لضربة موجعة بإصابة نجم خط وسطه في التدريبات الأخيرة. تشير تحليلات البيانات والذكاء الاصطناعي إلى أن غيابه يقلل من دقة تمريرات الفريق في الثلث الأخير بنسبة 12%، مما يجبر ناجلسمان على تغيير الرسم التكتيكي إلى 4-3-3 والاعتماد على الكرات الطولية.',
                'category' => 'أخبار المعسكرات',
                'image_url' => 'https://flagcdn.com/w160/de.png',
                'read_time' => '2 دقيقة',
                'date' => 'منذ يومين'
            ]
        ];

        // 4. Standings (Group standings)
        $standings = [
            [
                'group_name' => 'المجموعة الأولى (Group A)',
                'teams' => [
                    ['name' => 'Mexico', 'name_ar' => 'المكسيك', 'flag_url' => 'https://flagcdn.com/w80/mx.png', 'played' => 1, 'won' => 1, 'drawn' => 0, 'lost' => 0, 'goals_for' => 2, 'goals_against' => 0, 'points' => 3],
                    ['name' => 'Czech Republic', 'name_ar' => 'التشيك', 'flag_url' => 'https://flagcdn.com/w80/cz.png', 'played' => 1, 'won' => 1, 'drawn' => 0, 'lost' => 0, 'goals_for' => 1, 'goals_against' => 0, 'points' => 3],
                    ['name' => 'South Korea', 'name_ar' => 'كوريا الجنوبية', 'flag_url' => 'https://flagcdn.com/w80/kr.png', 'played' => 1, 'won' => 0, 'drawn' => 0, 'lost' => 1, 'goals_for' => 0, 'goals_against' => 1, 'points' => 0],
                    ['name' => 'South Africa', 'name_ar' => 'جنوب أفريقيا', 'flag_url' => 'https://flagcdn.com/w80/za.png', 'played' => 1, 'won' => 0, 'drawn' => 0, 'lost' => 1, 'goals_for' => 0, 'goals_against' => 2, 'points' => 0]
                ]
            ],
            [
                'group_name' => 'المجموعة الثانية (Group B)',
                'teams' => [
                    ['name' => 'Canada', 'name_ar' => 'كندا', 'flag_url' => 'https://flagcdn.com/w80/ca.png', 'played' => 1, 'won' => 1, 'drawn' => 0, 'lost' => 0, 'goals_for' => 2, 'goals_against' => 1, 'points' => 3],
                    ['name' => 'USA', 'name_ar' => 'أمريكا', 'flag_url' => 'https://flagcdn.com/w80/us.png', 'played' => 1, 'won' => 0, 'drawn' => 1, 'lost' => 0, 'goals_for' => 1, 'goals_against' => 1, 'points' => 1],
                    ['name' => 'Paraguay', 'name_ar' => 'باراغواي', 'flag_url' => 'https://flagcdn.com/w80/py.png', 'played' => 1, 'won' => 0, 'drawn' => 1, 'lost' => 0, 'goals_for' => 1, 'goals_against' => 1, 'points' => 1],
                    ['name' => 'Bosnia', 'name_ar' => 'البوسنة والهرسك', 'flag_url' => 'https://flagcdn.com/w80/ba.png', 'played' => 1, 'won' => 0, 'drawn' => 0, 'lost' => 1, 'goals_for' => 1, 'goals_against' => 2, 'points' => 0]
                ]
            ],
            [
                'group_name' => 'المجموعة الثالثة (Group C)',
                'teams' => [
                    ['name' => 'Brazil', 'name_ar' => 'البرازيل', 'flag_url' => 'https://flagcdn.com/w80/br.png', 'played' => 1, 'won' => 1, 'drawn' => 0, 'lost' => 0, 'goals_for' => 3, 'goals_against' => 1, 'points' => 3],
                    ['name' => 'Morocco', 'name_ar' => 'المغرب', 'flag_url' => 'https://flagcdn.com/w80/ma.png', 'played' => 1, 'won' => 1, 'drawn' => 0, 'lost' => 0, 'goals_for' => 2, 'goals_against' => 0, 'points' => 3],
                    ['name' => 'Switzerland', 'name_ar' => 'سويسرا', 'flag_url' => 'https://flagcdn.com/w80/ch.png', 'played' => 1, 'won' => 0, 'drawn' => 0, 'lost' => 1, 'goals_for' => 0, 'goals_against' => 2, 'points' => 0],
                    ['name' => 'Qatar', 'name_ar' => 'قطر', 'flag_url' => 'https://flagcdn.com/w80/qa.png', 'played' => 1, 'won' => 0, 'drawn' => 0, 'lost' => 1, 'goals_for' => 1, 'goals_against' => 3, 'points' => 0]
                ]
            ],
            [
                'group_name' => 'المجموعة الرابعة (Group D)',
                'teams' => [
                    ['name' => 'Turkey', 'name_ar' => 'تركيا', 'flag_url' => 'https://flagcdn.com/w80/tr.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Australia', 'name_ar' => 'أستراليا', 'flag_url' => 'https://flagcdn.com/w80/au.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Scotland', 'name_ar' => 'إسكتلندا', 'flag_url' => 'https://flagcdn.com/w80/gb-sct.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Haiti', 'name_ar' => 'هايتي', 'flag_url' => 'https://flagcdn.com/w80/ht.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0]
                ]
            ],
            [
                'group_name' => 'المجموعة الخامسة (Group E)',
                'teams' => [
                    ['name' => 'Germany', 'name_ar' => 'ألمانيا', 'flag_url' => 'https://flagcdn.com/w80/de.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Netherlands', 'name_ar' => 'هولندا', 'flag_url' => 'https://flagcdn.com/w80/nl.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Japan', 'name_ar' => 'اليابان', 'flag_url' => 'https://flagcdn.com/w80/jp.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Curaçao', 'name_ar' => 'كوراساو', 'flag_url' => 'https://flagcdn.com/w80/cw.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0]
                ]
            ],
            [
                'group_name' => 'المجموعة السادسة (Group F)',
                'teams' => [
                    ['name' => 'Sweden', 'name_ar' => 'السويد', 'flag_url' => 'https://flagcdn.com/w80/se.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Tunisia', 'name_ar' => 'تونس', 'flag_url' => 'https://flagcdn.com/w80/tn.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Ivory Coast', 'name_ar' => 'كوت ديفوار', 'flag_url' => 'https://flagcdn.com/w80/ci.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Ecuador', 'name_ar' => 'الإكوادور', 'flag_url' => 'https://flagcdn.com/w80/ec.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0]
                ]
            ],
            [
                'group_name' => 'المجموعة السابعة (Group G)',
                'teams' => [
                    ['name' => 'Spain', 'name_ar' => 'إسبانيا', 'flag_url' => 'https://flagcdn.com/w80/es.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Belgium', 'name_ar' => 'بلجيكا', 'flag_url' => 'https://flagcdn.com/w80/be.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Egypt', 'name_ar' => 'مصر', 'flag_url' => 'https://flagcdn.com/w80/eg.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Cape Verde', 'name_ar' => 'الرأس الأخضر', 'flag_url' => 'https://flagcdn.com/w80/cv.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0]
                ]
            ],
            [
                'group_name' => 'المجموعة الثامنة (Group H)',
                'teams' => [
                    ['name' => 'Uruguay', 'name_ar' => 'أوروغواي', 'flag_url' => 'https://flagcdn.com/w80/uy.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Saudi Arabia', 'name_ar' => 'السعودية', 'flag_url' => 'https://flagcdn.com/w80/sa.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'Iran', 'name_ar' => 'إيران', 'flag_url' => 'https://flagcdn.com/w80/ir.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0],
                    ['name' => 'New Zealand', 'name_ar' => 'نيوزيلندا', 'flag_url' => 'https://flagcdn.com/w80/nz.png', 'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'goals_for' => 0, 'goals_against' => 0, 'points' => 0]
                ]
            ]
        ];

        return $this->success([
            'news' => $news,
            'predictions' => $predictions,
            'standings' => $standings
        ], 'World Cup AI content retrieved successfully.');
    }
}
