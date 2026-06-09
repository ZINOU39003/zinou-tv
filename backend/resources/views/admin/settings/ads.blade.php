@extends('admin.layouts.app')

@section('title', 'إدارة الإعلانات')
@section('header_title', 'إدارة الإعلانات')
@section('header_subtitle', 'تكوين إعلانات Google AdMob والإعلانات المخصصة للتطبيق')

@section('content')

<div class="card" style="max-width: 750px; margin: 0 auto;">
    <form action="{{ route('admin.settings.ads.update') }}" method="POST">
        @csrf

        <div class="section-header">
            <h2>
                <span class="icon">📢</span>
                إعلانات Google AdMob البينية (Interstitial Ads)
            </h2>
        </div>

        <!-- Toggle switch for enabling/disabling ads -->
        <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.02); padding:16px 20px; border-radius:var(--radius-md); border:1px solid var(--border-glass); margin-bottom:24px;">
            <div>
                <span style="font-weight:700; display:block; font-size:15px; color:#fff;">تفعيل الإعلانات البينية</span>
                <span style="font-size:12px; color:var(--text-muted);">عند التفعيل، ستظهر إعلانات AdMob البينية للمستخدمين غير المشتركين (النسخة المجانية) عند فتح القنوات.</span>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="ads_enabled" name="ads_enabled" value="1" {{ $adsEnabled ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <!-- AdMob Interstitial Unit ID input -->
        <div class="form-group">
            <label for="admob_interstitial_ad_unit_id">معرف الوحدة الإعلانية البينية (AdMob Interstitial Ad Unit ID)</label>
            <input type="text" id="admob_interstitial_ad_unit_id" name="admob_interstitial_ad_unit_id" class="form-control" value="{{ old('admob_interstitial_ad_unit_id', $admobInterstitialId) }}" required style="font-family:monospace; font-size:14px; letter-spacing:0.5px;">
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">المعرف التجريبي الافتراضي من جوجل هو: <code style="background:rgba(255,255,255,0.06); padding:2px 6px; border-radius:3px;">ca-app-pub-3940256099942544/1033173712</code></p>
        </div>

        <hr class="divider">

        <div class="section-header">
            <h2>
                <span class="icon">📺</span>
                إعلانات الفيديو المخصصة (Custom Video Ads)
            </h2>
        </div>

        <!-- Custom Video URL Input -->
        <div class="form-group">
            <label for="ad_video_url">رابط فيديو الإعلان المخصص (URL)</label>
            <input type="text" id="ad_video_url" name="ad_video_url" class="form-control" value="{{ old('ad_video_url', $adVideoUrl) }}" required style="font-family:monospace; font-size:13px;">
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">رابط مباشر لملف فيديو (MP4) يتم تشغيله كإعلان بديل في مشغل الفيديو.</p>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
            <button type="submit" class="btn btn-primary" style="padding:11px 28px;">حفظ إعدادات الإعلانات</button>
        </div>
    </form>
</div>

@endsection
