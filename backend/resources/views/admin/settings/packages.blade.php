@extends('admin.layouts.app')

@section('title', 'تعديل الباقات والأسعار')
@section('header_title', 'تعديل الباقات والأسعار')
@section('header_subtitle', 'إدارة باقات الاشتراك، الأسعار، ورقم الاتصال للدعم الفني')

@section('content')

<div class="card">
    <form action="{{ route('admin.settings.packages.update') }}" method="POST">
        @csrf

        <!-- Support Number Config -->
        <div class="section-header">
            <h2>
                <span class="icon">📞</span>
                رقم التواصل والدعم الفني (WhatsApp)
            </h2>
        </div>
        <div class="form-group" style="max-width: 450px;">
            <label for="whatsapp_number">رقم الواتساب للتواصل (مع رمز الدولة وبدون إشارة + أو أصفار بالبداية)</label>
            <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $whatsappNumber) }}" placeholder="مثال: 213770000000" required>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">سيتم تحويل المستخدمين تلقائياً لهذا الرقم عند اختيارهم الباقة للشراء والدردشة عبر واتساب.</p>
        </div>

        <hr class="divider">

        <!-- Subscription Packages Config -->
        <div class="section-header">
            <h2>
                <span class="icon">🏷️</span>
                باقات الاشتراك ZINOU TV PRO
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">قم باختيار باقة واحدة لتكون الباقة الأكثر شعبية (Popular)</span>
        </div>

        <div class="grid-2" style="grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px; margin-bottom: 30px;">
            @foreach($packages as $index => $pkg)
                <div class="card" style="margin-bottom:0; background:rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); position:relative;">
                    
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <h3 style="font-size:16px; font-weight:700; color:var(--accent-primary);">{{ $pkg['nameAr'] }}</h3>
                        
                        <!-- Radio Popular Selection -->
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:12px;">
                            <input type="radio" name="popular_package_id" value="{{ $pkg['id'] }}" {{ isset($pkg['isPopular']) && $pkg['isPopular'] ? 'checked' : '' }} style="accent-color:var(--accent-gold);">
                            الأكثر شعبية ⭐
                        </label>
                    </div>

                    <!-- Hidden ID input -->
                    <input type="hidden" name="packages[{{ $index }}][id]" value="{{ $pkg['id'] }}">

                    <div class="form-group">
                        <label>الاسم بالعربية</label>
                        <input type="text" name="packages[{{ $index }}][nameAr]" class="form-control" value="{{ $pkg['nameAr'] }}" required>
                    </div>

                    <div class="form-group">
                        <label>الاسم بالإنجليزية</label>
                        <input type="text" name="packages[{{ $index }}][nameEn]" class="form-control" value="{{ $pkg['nameEn'] }}" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>السعر (بالعملة)</label>
                            <input type="text" name="packages[{{ $index }}][price]" class="form-control" value="{{ $pkg['price'] }}" placeholder="مثال: 500 DZD" required>
                        </div>
                        <div class="form-group">
                            <label>المدة المكتوبة</label>
                            <input type="text" name="packages[{{ $index }}][durationAr]" class="form-control" value="{{ $pkg['durationAr'] }}" placeholder="مثال: 30 يوم" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>المميزات (اكتب كل ميزة في سطر منفصل)</label>
                        <textarea name="packages[{{ $index }}][features]" class="form-control" rows="5" required>{{ implode("\n", $pkg['features'] ?? []) }}</textarea>
                    </div>

                </div>
            @endforeach
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
            <button type="submit" class="btn btn-primary" style="padding:12px 36px; font-size:15px;">حفظ جميع التغييرات</button>
        </div>
    </form>
</div>

@endsection
