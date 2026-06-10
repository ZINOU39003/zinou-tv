@extends('admin.layouts.app')

@section('title', 'إعدادات التطبيق')
@section('header_title', 'إعدادات التطبيق')
@section('header_subtitle', 'رفع APK، إجبار التحديث، وإدارة النسخ')

@section('content')

<div class="card" style="max-width: 750px; margin: 0 auto;">
    <form action="{{ route('admin.settings.app.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="section-header">
            <h2><span class="icon">📱</span> إدارة النسخ والتحديث</h2>
        </div>

        <div class="form-group">
            <label for="latest_app_version">رقم النسخة الحالية (Latest Version)</label>
            <input type="text" id="latest_app_version" name="latest_app_version" class="form-control"
                   value="{{ old('latest_app_version', $latestAppVersion) }}" required placeholder="1.0.1">
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">النسخة المتوفرة للتحميل بعد رفع APK</p>
        </div>

        <div class="form-group">
            <label for="min_app_version">الحد الأدنى للنسخة المطلوبة (Min Version)</label>
            <input type="text" id="min_app_version" name="min_app_version" class="form-control"
                   value="{{ old('min_app_version', $minAppVersion) }}" required placeholder="1.0.0">
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">إذا كان إصدار التطبيق أقل من هذا الرقم + تفعيل الإجبار → يتوقف التطبيق</p>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.02);padding:16px 20px;border-radius:var(--radius-md);border:1px solid var(--border-glass);margin-bottom:24px;">
            <div>
                <span style="font-weight:700;display:block;font-size:15px;color:#fff;">إجبار التحديث</span>
                <span style="font-size:12px;color:var(--text-muted);">عند التفعيل، لن يعمل التطبيق حتى يحدّث المستخدم</span>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="force_update" value="1" {{ $forceUpdate ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group">
            <label for="update_message">رسالة التحديث</label>
            <textarea id="update_message" name="update_message" class="form-control" rows="3">{{ old('update_message', $updateMessage) }}</textarea>
        </div>

        <div class="form-group">
            <label for="apk_file">رفع ملف APK جديد</label>
            <input type="file" id="apk_file" name="apk_file" class="form-control" accept=".apk">
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px;">الحد الأقصى 100 MB — حجم APK التطبيق ~30 MB</p>
            @if($latestApkUrl)
                <p style="font-size:11px;color:var(--accent-secondary);margin-top:8px;">
                    ✓ رابط التحميل الحالي:
                    <a href="{{ $latestApkUrl }}" target="_blank" style="color:var(--accent-secondary);word-break:break-all;">{{ $latestApkUrl }}</a>
                </p>
            @endif
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:32px;border-top:1px solid var(--border-glass);padding-top:20px;">
            <button type="submit" class="btn btn-primary" style="padding:11px 28px;">حفظ إعدادات التطبيق</button>
        </div>
    </form>
</div>

@endsection
