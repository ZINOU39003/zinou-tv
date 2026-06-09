<?php

namespace App\Services;

use App\Models\Category;

class ChannelClassifierService
{
    /**
     * Auto-classify a channel's category_id, country, language, and continent.
     */
    public function classify(string $name, ?string $groupTitle = null): array
    {
        $nameLower = strtolower($name);
        $groupLower = $groupTitle ? strtolower($groupTitle) : '';
        $combined = ' ' . $nameLower . ' ' . $groupLower . ' ';

        // 1. Classify Category ID (Content Type)
        $contentType = 'General';
        if (
            str_contains($combined, 'sport') || str_contains($combined, 'liga') || str_contains($combined, 'cup') || 
            str_contains($combined, 'f1') || str_contains($combined, 'rugby') || str_contains($combined, 'football') || 
            str_contains($combined, 'cricket') || str_contains($combined, 'espn') || str_contains($combined, 'sky sports') || 
            str_contains($combined, 'dazn') || str_contains($combined, 'bein') || str_contains($combined, 'match') ||
            str_contains($combined, 'رياضة') || str_contains($combined, 'كورة')
        ) {
            $contentType = 'Sports';
        } elseif (
            str_contains($combined, 'movie') || str_contains($combined, 'cinema') || str_contains($combined, 'cine') || 
            str_contains($combined, 'film') || str_contains($combined, 'action') || str_contains($combined, 'drama') || 
            str_contains($combined, 'hbo') || str_contains($combined, 'showtime') || str_contains($combined, 'netflix') ||
            str_contains($combined, 'أفلام') || str_contains($combined, 'افلام') || str_contains($combined, 'سينما')
        ) {
            $contentType = 'Movies';
        } elseif (
            str_contains($combined, 'kid') || str_contains($combined, 'toon') || str_contains($combined, 'child') || 
            str_contains($combined, 'disney') || str_contains($combined, 'pogo') || str_contains($combined, 'nickelodeon') || 
            str_contains($combined, 'yay') || str_contains($combined, 'cartoon') ||
            str_contains($combined, 'أطفال') || str_contains($combined, 'اطفال') || str_contains($combined, 'كرتون') ||
            str_contains($combined, 'سبيستون')
        ) {
            $contentType = 'Kids';
        } elseif (
            str_contains($combined, 'news') || str_contains($combined, 'bbc') || str_contains($combined, 'cnn') || 
            str_contains($combined, 'al jazeera') || str_contains($combined, 'al-jazeera') || str_contains($combined, 'sky news') || 
            str_contains($combined, 'reuters') || str_contains($combined, 'أخبار') || str_contains($combined, 'اخبار') ||
            str_contains($combined, 'العربية الحدث') || str_contains($combined, 'الجزيرة')
        ) {
            $contentType = 'News';
        } elseif (
            str_contains($combined, 'doc') || str_contains($combined, 'history') || str_contains($combined, 'discovery') || 
            str_contains($combined, 'nat geo') || str_contains($combined, 'geographic') || str_contains($combined, 'وثائقي') ||
            str_contains($combined, 'وثائقية')
        ) {
            $contentType = 'Documentary';
        } elseif (
            str_contains($combined, 'quran') || str_contains($combined, 'islam') || str_contains($combined, 'makkah') || 
            str_contains($combined, 'madinah') || str_contains($combined, 'deen') || str_contains($combined, 'قرآن') ||
            str_contains($combined, 'اسلام') || str_contains($combined, 'ديني') || str_contains($combined, 'مكة')
        ) {
            $contentType = 'Religious';
        } elseif (
            str_contains($combined, 'music') || str_contains($combined, 'song') || str_contains($combined, 'sing') || 
            str_contains($combined, 'موسيقى') || str_contains($combined, 'اغاني')
        ) {
            $contentType = 'Music';
        }

        // Get category_id for this content type category
        $category = Category::where('type', 'content_type')
            ->where(function ($q) use ($contentType) {
                $q->where('name', $contentType)
                  ->orWhere('name_ar', $contentType);
            })->first();

        // If not found, look up by slug or create it
        if (!$category) {
            $slugs = [
                'Sports' => 'sports',
                'Movies' => 'movies',
                'Kids' => 'kids',
                'News' => 'news',
                'Documentary' => 'documentary',
                'Religious' => 'religious',
                'Music' => 'music',
                'General' => 'general'
            ];
            $namesAr = [
                'Sports' => 'رياضة',
                'Movies' => 'أفلام',
                'Kids' => 'أطفال',
                'News' => 'أخبار',
                'Documentary' => 'وثائقيات',
                'Religious' => 'ديني',
                'Music' => 'موسيقى',
                'General' => 'ترفيه'
            ];

            $category = Category::updateOrCreate(
                ['slug' => $slugs[$contentType] ?? strtolower($contentType)],
                [
                    'name' => $contentType,
                    'name_ar' => $namesAr[$contentType] ?? $contentType,
                    'type' => 'content_type',
                    'is_active' => true,
                    'sort_order' => 1
                ]
            );
        }

        $categoryId = $category->id;

        // 2. Classify Country & Continent & Language
        $geo = $this->matchCountry($combined, $groupLower, $nameLower);
        $country = $geo['country'];
        $language = $geo['language'];
        $continent = $geo['continent'];

        // Fallbacks for general language match if country is not set
        if (!$country) {
            if (str_contains($combined, ' arabic ') || str_contains($combined, 'عربي') || str_contains($combined, 'العربية')) {
                $language = 'العربية';
            } elseif (str_contains($combined, ' english ') || str_contains($combined, 'انجليزي') || str_contains($combined, 'إنجليزي')) {
                $language = 'الإنجليزية';
            } elseif (str_contains($combined, ' hindi ') || str_contains($combined, 'هندي')) {
                $language = 'الهندية';
            } elseif (str_contains($combined, ' bangla ') || str_contains($combined, 'بنغالي')) {
                $language = 'البنغالية';
            }
        }

        return [
            'category_id' => $categoryId,
            'country' => $country,
            'language' => $language,
            'continent' => $continent,
        ];
    }

    private function matchCountry(string $combined, string $groupLower, string $nameLower): array
    {
        // Bangladesh
        if (
            str_contains($combined, 'bangladesh') || str_contains($combined, 'bangla') || 
            str_contains($combined, 'ekushey') || str_contains($combined, 'deepto') || 
            str_contains($combined, 'deshe bideshe') ||
            $this->isCodeMatch($nameLower, $groupLower, 'bd')
        ) {
            return ['country' => 'بنغلاديش', 'language' => 'البنغالية', 'continent' => 'آسيا'];
        }

        // India
        if (
            str_contains($combined, 'india') || str_contains($combined, 'hindi') || 
            str_contains($combined, 'pogo') || str_contains($combined, 'sony yay') || 
            str_contains($combined, 'star plus') || str_contains($combined, 'zeetv') || 
            str_contains($combined, 'colors') ||
            $this->isCodeMatch($nameLower, $groupLower, 'in')
        ) {
            return ['country' => 'الهند', 'language' => 'الهندية', 'continent' => 'آسيا'];
        }

        // Saudi Arabia
        if (
            str_contains($combined, 'saudi') || str_contains($combined, 'ksa') || 
            str_contains($combined, 'سعودي') || str_contains($combined, 'السعودية') || 
            str_contains($combined, 'مكة') || str_contains($combined, 'ssc') ||
            $this->isCodeMatch($nameLower, $groupLower, 'ksa')
        ) {
            return ['country' => 'السعودية', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Egypt
        if (
            str_contains($combined, 'egypt') || str_contains($combined, 'مصر') || 
            str_contains($combined, 'مصري') || str_contains($combined, 'dmc') || 
            str_contains($combined, 'cbc') || str_contains($combined, 'ontv') ||
            $this->isCodeMatch($nameLower, $groupLower, 'egy') ||
            $this->isCodeMatch($nameLower, $groupLower, 'eg')
        ) {
            return ['country' => 'مصر', 'language' => 'العربية', 'continent' => 'أفريقيا'];
        }

        // UAE
        if (
            str_contains($combined, 'uae') || str_contains($combined, 'dubai') || 
            str_contains($combined, 'abu dhabi') || str_contains($combined, 'abudhabi') || 
            str_contains($combined, 'الإمارات') || str_contains($combined, 'امارات')
        ) {
            return ['country' => 'الإمارات', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Qatar
        if (
            str_contains($combined, 'qatar') || str_contains($combined, 'قطر') || 
            str_contains($combined, 'alkass')
        ) {
            return ['country' => 'قطر', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Kuwait
        if (
            str_contains($combined, 'kuwait') || str_contains($combined, 'الكويت')
        ) {
            return ['country' => 'الكويت', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Bahrain
        if (str_contains($combined, 'bahrain') || str_contains($combined, 'البحرين')) {
            return ['country' => 'البحرين', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Oman
        if (str_contains($combined, 'oman') || str_contains($combined, 'عمان')) {
            return ['country' => 'عمان', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Yemen
        if (str_contains($combined, 'yemen') || str_contains($combined, 'اليمن')) {
            return ['country' => 'اليمن', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Palestine
        if (str_contains($combined, 'palestine') || str_contains($combined, 'فلسطين')) {
            return ['country' => 'فلسطين', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Iraq
        if (str_contains($combined, 'iraq') || str_contains($combined, 'العراق')) {
            return ['country' => 'العراق', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Syria
        if (str_contains($combined, 'syria') || str_contains($combined, 'سوريا')) {
            return ['country' => 'سوريا', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Lebanon
        if (str_contains($combined, 'lebanon') || str_contains($combined, 'لبنان')) {
            return ['country' => 'لبنان', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Jordan
        if (str_contains($combined, 'jordan') || str_contains($combined, 'الأردن')) {
            return ['country' => 'الأردن', 'language' => 'العربية', 'continent' => 'آسيا'];
        }

        // Morocco
        if (
            str_contains($combined, 'morocco') || str_contains($combined, 'maroc') || 
            str_contains($combined, 'المغرب')
        ) {
            return ['country' => 'المغرب', 'language' => 'العربية', 'continent' => 'أفريقيا'];
        }

        // Algeria
        if (
            str_contains($combined, 'algeria') || str_contains($combined, 'algerie') || 
            str_contains($combined, 'الجزائر')
        ) {
            return ['country' => 'الجزائر', 'language' => 'العربية', 'continent' => 'أفريقيا'];
        }

        // Tunisia
        if (
            str_contains($combined, 'tunisia') || str_contains($combined, 'tunisie') || 
            str_contains($combined, 'تونس')
        ) {
            return ['country' => 'تونس', 'language' => 'العربية', 'continent' => 'أفريقيا'];
        }

        // Libya
        if (str_contains($combined, 'libya') || str_contains($combined, 'ليبيا')) {
            return ['country' => 'ليبيا', 'language' => 'العربية', 'continent' => 'أفريقيا'];
        }

        // Sudan
        if (str_contains($combined, 'sudan') || str_contains($combined, 'السودان')) {
            return ['country' => 'السودان', 'language' => 'العربية', 'continent' => 'أفريقيا'];
        }

        // UK
        if (
            str_contains($combined, 'united kingdom') || str_contains($combined, 'british') || 
            str_contains($combined, 'bbc') ||
            $this->isCodeMatch($nameLower, $groupLower, 'uk')
        ) {
            return ['country' => 'بريطانيا', 'language' => 'الإنجليزية', 'continent' => 'أوروبا'];
        }

        // USA
        if (
            str_contains($combined, 'america') || str_contains($combined, 'usa') || 
            $this->isCodeMatch($nameLower, $groupLower, 'us')
        ) {
            return ['country' => 'أمريكا', 'language' => 'الإنجليزية', 'continent' => 'أمريكا الشمالية'];
        }

        // Germany
        if (
            str_contains($combined, 'deutsch') || str_contains($combined, 'germany') || 
            $this->isCodeMatch($nameLower, $groupLower, 'de')
        ) {
            return ['country' => 'ألمانيا', 'language' => 'الألمانية', 'continent' => 'أوروبا'];
        }

        // France
        if (
            str_contains($combined, 'france') || str_contains($combined, 'french') || 
            $this->isCodeMatch($nameLower, $groupLower, 'fr')
        ) {
            return ['country' => 'فرنسا', 'language' => 'الفرنسية', 'continent' => 'أوروبا'];
        }

        // Spain
        if (
            str_contains($combined, 'spain') || str_contains($combined, 'spanish') || 
            str_contains($combined, 'espanol') ||
            $this->isCodeMatch($nameLower, $groupLower, 'es')
        ) {
            return ['country' => 'إسبانيا', 'language' => 'الإسبانية', 'continent' => 'أوروبا'];
        }

        // Italy
        if (
            str_contains($combined, 'italy') || str_contains($combined, 'italian') || 
            $this->isCodeMatch($nameLower, $groupLower, 'it')
        ) {
            return ['country' => 'إيطاليا', 'language' => 'الإيطالية', 'continent' => 'أوروبا'];
        }

        // Portugal
        if (
            str_contains($combined, 'portugal') || str_contains($combined, 'portuguese') || 
            $this->isCodeMatch($nameLower, $groupLower, 'pt')
        ) {
            return ['country' => 'البرتغال', 'language' => 'البرتغالية', 'continent' => 'أوروبا'];
        }

        // Brazil
        if (
            str_contains($combined, 'brazil') || 
            $this->isCodeMatch($nameLower, $groupLower, 'br')
        ) {
            return ['country' => 'البرازيل', 'language' => 'البرتغالية', 'continent' => 'أمريكا الجنوبية'];
        }

        // Argentina
        if (
            str_contains($combined, 'argentina') || 
            $this->isCodeMatch($nameLower, $groupLower, 'ar')
        ) {
            return ['country' => 'الأرجنتين', 'language' => 'الإسبانية', 'continent' => 'أمريكا الجنوبية'];
        }

        // Turkey
        if (
            str_contains($combined, 'turkey') || str_contains($combined, 'turkish') || 
            $this->isCodeMatch($nameLower, $groupLower, 'tr')
        ) {
            return ['country' => 'تركيا', 'language' => 'التركية', 'continent' => 'أوروبا'];
        }

        // Pakistan
        if (
            str_contains($combined, 'pakistan') || str_contains($combined, 'urdu') || 
            $this->isCodeMatch($nameLower, $groupLower, 'pak') ||
            $this->isCodeMatch($nameLower, $groupLower, 'pk')
        ) {
            return ['country' => 'باكستان', 'language' => 'الأردية', 'continent' => 'آسيا'];
        }

        return ['country' => null, 'language' => null, 'continent' => null];
    }

    private function isCodeMatch(string $nameLower, string $groupLower, string $code): bool
    {
        $code = strtolower($code);
        
        // Check if name starts with code prefixes
        if (
            str_starts_with($nameLower, $code . ' ') ||
            str_starts_with($nameLower, $code . '-') ||
            str_starts_with($nameLower, $code . ':') ||
            str_starts_with($nameLower, $code . '|') ||
            str_starts_with($nameLower, '[' . $code . ']') ||
            str_starts_with($nameLower, '(' . $code . ')')
        ) {
            return true;
        }

        // Check if group matches or contains code as distinct part
        if (
            $groupLower === $code ||
            str_starts_with($groupLower, $code . ' ') ||
            str_starts_with($groupLower, $code . '-') ||
            str_starts_with($groupLower, $code . ':') ||
            str_contains($groupLower, ' ' . $code . ' ') ||
            str_contains($groupLower, ' ' . $code . '-') ||
            str_contains($groupLower, ' ' . $code . ':')
        ) {
            return true;
        }

        return false;
    }
}
