<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\MediaUrl;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    use HasApiResponse;

    public function index(): JsonResponse
    {
        $whatsappNumber = Setting::get('whatsapp_number', '213770000000');
        $packagesJson = Setting::get('subscription_packages');
        $packages = json_decode($packagesJson, true) ?: [];

        $adsEnabled = Setting::get('ads_enabled', '1') === '1';
        $admobAppId = Setting::get('admob_app_id', 'ca-app-pub-3940256099942544~3347511713');
        $admobBannerId = Setting::get('admob_banner_ad_unit_id', 'ca-app-pub-3940256099942544/6300978111');
        $admobInterstitialId = Setting::get('admob_interstitial_ad_unit_id', 'ca-app-pub-3940256099942544/1033173712');
        $adVideoUrl = Setting::get('ad_video_url', '');

        $minAppVersion = Setting::get('min_app_version', '1.0.0');
        $forceUpdate = Setting::get('force_update', '0') === '1';
        $updateMessage = Setting::get('update_message', 'يتوفر تحديث جديد. يرجى تحديث التطبيق للمتابعة.');
        $latestApkUrl = MediaUrl::resolve(Setting::get('latest_apk_url', '')) ?? '';
        $streamTickerText = Setting::get('stream_ticker_text', '');
        $onesignalAppId = Setting::get('onesignal_app_id', '');

        return $this->success([
            'whatsapp_number' => $whatsappNumber,
            'packages' => $packages,
            'ads_enabled' => $adsEnabled,
            'admob_app_id' => $admobAppId,
            'admob_banner_ad_unit_id' => $admobBannerId,
            'admob_interstitial_ad_unit_id' => $admobInterstitialId,
            'ad_video_url' => $adVideoUrl,
            'min_app_version' => $minAppVersion,
            'force_update' => $forceUpdate,
            'update_message' => $updateMessage,
            'latest_apk_url' => $latestApkUrl,
            'stream_ticker_text' => $streamTickerText,
            'onesignal_app_id' => $onesignalAppId,
        ], 'Configuration retrieved successfully.');
    }
}
