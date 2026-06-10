<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class SettingController extends Controller
{
    /**
     * Display Packages & Support contact page.
     */
    public function packages(): View
    {
        $whatsappNumber = Setting::get('whatsapp_number', '213770000000');
        $packagesJson = Setting::get('subscription_packages');
        
        $packages = json_decode($packagesJson, true) ?: [];

        return view('admin.settings.packages', compact('whatsappNumber', 'packages'));
    }

    /**
     * Update Packages & Support contact settings.
     */
    public function updatePackages(Request $request): RedirectResponse
    {
        $request->validate([
            'whatsapp_number' => 'required|string',
            'packages' => 'required|array',
            'packages.*.id' => 'required|string',
            'packages.*.nameAr' => 'required|string',
            'packages.*.nameEn' => 'required|string',
            'packages.*.durationAr' => 'required|string',
            'packages.*.price' => 'required|string',
            'packages.*.features' => 'required|string',
        ]);

        $whatsappNumber = $request->input('whatsapp_number');
        $inputPackages = $request->input('packages');
        $popularPackageId = $request->input('popular_package_id');

        $packages = [];
        foreach ($inputPackages as $pkg) {
            // Process features: Split by newlines or commas
            $featuresList = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $pkg['features']))));
            
            $packages[] = [
                'id' => $pkg['id'],
                'nameAr' => $pkg['nameAr'],
                'nameEn' => $pkg['nameEn'],
                'durationAr' => $pkg['durationAr'],
                'price' => $pkg['price'],
                'features' => array_values($featuresList),
                'isPopular' => ($pkg['id'] === $popularPackageId),
            ];
        }

        Setting::set('whatsapp_number', $whatsappNumber);
        Setting::set('subscription_packages', $packages);

        return redirect()->back()->with('success', 'تم تحديث الباقات ورقم الاتصال بنجاح.');
    }

    /**
     * Display Ads settings page.
     */
    public function ads(): View
    {
        $adsEnabled = Setting::get('ads_enabled', '1') === '1';
        $admobAppId = Setting::get('admob_app_id', 'ca-app-pub-3940256099942544~3347511713');
        $admobBannerId = Setting::get('admob_banner_ad_unit_id', 'ca-app-pub-3940256099942544/6300978111');
        $admobInterstitialId = Setting::get('admob_interstitial_ad_unit_id', 'ca-app-pub-3940256099942544/1033173712');
        $adVideoUrl = Setting::get('ad_video_url', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4');
        $streamTickerText = Setting::get('stream_ticker_text', '');

        return view('admin.settings.ads', compact(
            'adsEnabled',
            'admobAppId',
            'admobBannerId',
            'admobInterstitialId',
            'adVideoUrl',
            'streamTickerText'
        ));
    }

    /**
     * Update Ads settings.
     */
    public function updateAds(Request $request): RedirectResponse
    {
        $request->validate([
            'admob_app_id' => 'required|string',
            'admob_banner_ad_unit_id' => 'required|string',
            'admob_interstitial_ad_unit_id' => 'required|string',
            'ad_video_url' => 'required|url',
            'stream_ticker_text' => 'nullable|string|max:500',
        ]);

        $adsEnabled = $request->has('ads_enabled') ? '1' : '0';

        Setting::set('ads_enabled', $adsEnabled);
        Setting::set('admob_app_id', $request->input('admob_app_id'));
        Setting::set('admob_banner_ad_unit_id', $request->input('admob_banner_ad_unit_id'));
        Setting::set('admob_interstitial_ad_unit_id', $request->input('admob_interstitial_ad_unit_id'));
        Setting::set('ad_video_url', $request->input('ad_video_url'));
        Setting::set('stream_ticker_text', $request->input('stream_ticker_text', ''));

        return redirect()->back()->with('success', 'تم تحديث إعدادات الإعلانات بنجاح.');
    }

    public function app(): View
    {
        return view('admin.settings.app', [
            'minAppVersion' => Setting::get('min_app_version', '1.0.0'),
            'forceUpdate' => Setting::get('force_update', '0') === '1',
            'updateMessage' => Setting::get('update_message', 'يتوفر تحديث جديد. يرجى تحديث التطبيق للمتابعة.'),
            'latestApkUrl' => Setting::get('latest_apk_url', ''),
            'latestAppVersion' => Setting::get('latest_app_version', '1.0.0'),
        ]);
    }

    public function updateApp(Request $request): RedirectResponse
    {
        $request->validate([
            'min_app_version' => 'required|string|max:20',
            'latest_app_version' => 'required|string|max:20',
            'update_message' => 'nullable|string|max:500',
            'apk_file' => 'nullable|file|mimes:apk|max:102400',
        ]);

        Setting::set('min_app_version', $request->input('min_app_version'));
        Setting::set('latest_app_version', $request->input('latest_app_version'));
        Setting::set('update_message', $request->input('update_message', ''));
        Setting::set('force_update', $request->has('force_update') ? '1' : '0');

        if ($request->hasFile('apk_file')) {
            $path = $request->file('apk_file')->store('apks', 'public');
            Setting::set('latest_apk_url', asset('storage/'.$path));
        }

        return redirect()->back()->with('success', 'تم تحديث إعدادات التطبيق بنجاح.');
    }
}
