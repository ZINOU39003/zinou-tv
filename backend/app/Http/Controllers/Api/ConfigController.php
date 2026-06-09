<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    use HasApiResponse;

    /**
     * Get dynamic app configuration.
     */
    public function index(): JsonResponse
    {
        $whatsappNumber = Setting::get('whatsapp_number', '213770000000');
        $packagesJson = Setting::get('subscription_packages');
        $packages = json_decode($packagesJson, true) ?: [];

        $adsEnabled = Setting::get('ads_enabled', '1') === '1';
        $admobInterstitialId = Setting::get('admob_interstitial_ad_unit_id', 'ca-app-pub-3940256099942544/1033173712');
        $adVideoUrl = Setting::get('ad_video_url', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4');

        return $this->success([
            'whatsapp_number' => $whatsappNumber,
            'packages' => $packages,
            'ads_enabled' => $adsEnabled,
            'admob_interstitial_ad_unit_id' => $admobInterstitialId,
            'ad_video_url' => $adVideoUrl
        ], 'Configuration retrieved successfully.');
    }
}
