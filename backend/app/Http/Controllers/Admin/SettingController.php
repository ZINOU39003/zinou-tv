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
        $admobInterstitialId = Setting::get('admob_interstitial_ad_unit_id', 'ca-app-pub-3940256099942544/1033173712');
        $adVideoUrl = Setting::get('ad_video_url', 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4');

        return view('admin.settings.ads', compact('adsEnabled', 'admobInterstitialId', 'adVideoUrl'));
    }

    /**
     * Update Ads settings.
     */
    public function updateAds(Request $request): RedirectResponse
    {
        $request->validate([
            'admob_interstitial_ad_unit_id' => 'required|string',
            'ad_video_url' => 'required|url',
        ]);

        $adsEnabled = $request->has('ads_enabled') ? '1' : '0';
        $admobInterstitialId = $request->input('admob_interstitial_ad_unit_id');
        $adVideoUrl = $request->input('ad_video_url');

        Setting::set('ads_enabled', $adsEnabled);
        Setting::set('admob_interstitial_ad_unit_id', $admobInterstitialId);
        Setting::set('ad_video_url', $adVideoUrl);

        return redirect()->back()->with('success', 'تم تحديث إعدادات الإعلانات بنجاح.');
    }
}
