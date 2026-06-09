<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed initial values
        $packages = [
            [
                'id' => '1_month',
                'nameAr' => 'باقة الشهر الواحد',
                'nameEn' => '1 Month Pro Plan',
                'durationAr' => '30 يوم',
                'price' => '500 DZD',
                'features' => ['بدون إعلانات تماماً', 'جودة عالية FHD / UHD', 'جميع باقات القنوات والسينما', 'دعم فني 24/7'],
                'isPopular' => false
            ],
            [
                'id' => '3_months',
                'nameAr' => 'باقة 3 أشهر',
                'nameEn' => '3 Months Pro Plan',
                'durationAr' => '90 يوم',
                'price' => '1200 DZD',
                'features' => ['بدون إعلانات تماماً', 'جودة عالية FHD / UHD', 'جميع باقات القنوات والسينما', 'دعم فني 24/7'],
                'isPopular' => false
            ],
            [
                'id' => '6_months',
                'nameAr' => 'باقة 6 أشهر',
                'nameEn' => '6 Months Pro Plan',
                'durationAr' => '180 يوم',
                'price' => '2000 DZD',
                'features' => ['بدون إعلانات تماماً', 'جودة عالية FHD / UHD', 'جميع باقات القنوات والسينما', 'دعم فني 24/7', 'تحديثات قائمة مجانية'],
                'isPopular' => false
            ],
            [
                'id' => '12_months',
                'nameAr' => 'باقة 12 شهراً',
                'nameEn' => '12 Months Gold Plan',
                'durationAr' => '365 يوم',
                'price' => '3500 DZD',
                'features' => ['بدون إعلانات تماماً', 'جودة عالية FHD / UHD', 'جميع باقات القنوات والسينما', 'دعم فني 24/7', 'تحديثات قائمة مجانية', 'خصم خاص للدفع السنوي'],
                'isPopular' => true
            ]
        ];

        DB::table('settings')->insert([
            [
                'key' => 'whatsapp_number',
                'value' => '213770000000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'subscription_packages',
                'value' => json_encode($packages, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ads_enabled',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'admob_interstitial_ad_unit_id',
                'value' => 'ca-app-pub-3940256099942544/1033173712',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ad_video_url',
                'value' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
