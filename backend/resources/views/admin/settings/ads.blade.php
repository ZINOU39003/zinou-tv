@extends('admin.layouts.app')

@section('title', 'إدارة الإعلانات')
@section('header_title', 'إدارة الإعلانات')
@section('header_subtitle', 'تكوين Google AdMob والنص أسفل البث')

@section('content')

<div class="card" style="max-width: 750px; margin: 0 auto;">
    <form action="{{ route('admin.settings.ads.update') }}" method="POST">
        @csrf

        <div class="section-header">
            <h2><span class="icon">📢</span> Google AdMob</h2>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.02);padding:16px 20px;border-radius:var(--radius-md);border:1px solid var(--border-glass);margin-bottom:24px;">
            <div>
                <span style="font-weight:700;display:block;font-size:15px;color:#fff;">تفعيل الإعلانات</span>
                <span style="font-size:12px;color:var(--text-muted);">للمستخدمين غير المشتركين</span>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="ads_enabled" name="ads_enabled" value="1" {{ $adsEnabled ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group">
            <label for="admob_app_id">AdMob App ID</label>
            <input type="text" id="admob_app_id" name="admob_app_id" class="form-control"
                   value="{{ old('admob_app_id', $admobAppId) }}" required style="font-family:monospace;">
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">يجب أن يطابق App ID في AndroidManifest.xml</p>
        </div>

        <div class="form-group">
            <label for="admob_banner_ad_unit_id">Banner Ad Unit ID (شريط القنوات)</label>
            <input type="text" id="admob_banner_ad_unit_id" name="admob_banner_ad_unit_id" class="form-control"
                   value="{{ old('admob_banner_ad_unit_id', $admobBannerId) }}" required style="font-family:monospace;">
        </div>

        <div class="form-group">
            <label for="admob_interstitial_ad_unit_id">Interstitial Ad Unit ID (قبل البث)</label>
            <input type="text" id="admob_interstitial_ad_unit_id" name="admob_interstitial_ad_unit_id" class="form-control"
                   value="{{ old('admob_interstitial_ad_unit_id', $admobInterstitialId) }}" required style="font-family:monospace;">
        </div>

        <hr class="divider">

        <div class="section-header">
            <h2><span class="icon">📺</span> نص البث</h2>
        </div>

        <div class="form-group">
            <label for="ad_video_url">رابط فيديو إعلاني (MP4) <span style="color:var(--text-muted);font-weight:400;">— اختياري</span></label>
            <input type="url" id="ad_video_url" name="ad_video_url" class="form-control"
                   value="{{ old('ad_video_url', $adVideoUrl) }}" style="font-family:monospace;font-size:13px;"
                   placeholder="اتركه فارغاً إذا كنت تستخدم AdMob فقط">
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">ليس مطلوباً — اتركه فارغاً لاستخدام إعلانات AdMob فقط</p>
        </div>

        <div class="form-group">
            <label for="stream_ticker_text">نص أسفل البث (شريط متحرك)</label>
            <input type="text" id="stream_ticker_text" name="stream_ticker_text" class="form-control"
                   value="{{ old('stream_ticker_text', $streamTickerText) }}" placeholder="تابعونا على فيسبوك — ZINOU TV">
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">يظهر أسفل مشغل الفيديو للمشاهدين</p>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:32px;border-top:1px solid var(--border-glass);padding-top:20px;">
            <button type="submit" class="btn btn-primary" style="padding:11px 28px;">حفظ إعدادات الإعلانات</button>
        </div>
    </form>
</div>

@endsection
