@extends('admin.layouts.app')

@section('title', 'إضافة قناة جديدة')
@section('header_title', 'إضافة قناة جديدة')
@section('header_subtitle', 'إضافة قناة بث مباشر جديدة وإعداد جودة التشغيل')

@section('actions')
    <a href="{{ route('admin.channels.index') }}" class="btn btn-secondary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        العودة للقائمة
    </a>
@endsection

@section('content')

    <div style="max-width: 760px; margin: 0 auto;">
        <div class="card">
            <form action="{{ route('admin.channels.store') }}" method="POST">
                @csrf

                <!-- Basic Info -->
                <h3 style="font-size:15px; font-weight:800; color:var(--accent-primary); margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid var(--border-glass);">
                    📺 المعلومات الأساسية
                </h3>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="name">اسم القناة (إنجليزي) *</label>
                        <input type="text" id="name" name="name" class="form-control"
                               placeholder="BeIN Sports 1 HD" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <label for="name_ar">اسم القناة (عربي)</label>
                        <input type="text" id="name_ar" name="name_ar" class="form-control"
                               placeholder="بي إن سبورت 1 HD" value="{{ old('name_ar') }}">
                    </div>
                </div>

                @include('admin.channels._network_package_fields')

                <div class="form-group">
                    <label for="logo_url">رابط شعار القناة</label>
                    <input type="url" id="logo_url" name="logo_url" class="form-control"
                           placeholder="https://cdn.example.com/logo.png" value="{{ old('logo_url') }}">
                </div>

                <!-- Classification -->
                <h3 style="font-size:15px; font-weight:800; color:var(--accent-primary); margin: 24px 0 16px; padding-bottom:12px; border-bottom:1px solid var(--border-glass);">
                    🌍 التصنيف الجغرافي
                </h3>

                <div class="grid-3">
                    <div class="form-group">
                        <label for="country">الدولة</label>
                        <input type="text" id="country" name="country" class="form-control"
                               placeholder="السعودية / مصر" value="{{ old('country') }}">
                    </div>
                    <div class="form-group">
                        <label for="language">اللغة</label>
                        <input type="text" id="language" name="language" class="form-control"
                               placeholder="العربية / الإنجليزية" value="{{ old('language') }}">
                    </div>
                    <div class="form-group">
                        <label for="continent">القارة</label>
                        <input type="text" id="continent" name="continent" class="form-control"
                               placeholder="آسيا / أفريقيا / أوروبا" value="{{ old('continent') }}">
                    </div>
                </div>

                <!-- Stream Settings -->
                <h3 style="font-size:15px; font-weight:800; color:var(--accent-primary); margin: 24px 0 16px; padding-bottom:12px; border-bottom:1px solid var(--border-glass);">
                    🔗 إعدادات البث
                </h3>

                <div class="form-group">
                    <label for="stream_url">رابط البث الرئيسي * (سيتم تشفيره تلقائياً)</label>
                    <input type="text" id="stream_url" name="stream_url" class="form-control"
                           placeholder="https://stream.example.com/live/channel/index.m3u8" required value="{{ old('stream_url') }}">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="stream_type">نوع البث *</label>
                        <select id="stream_type" name="stream_type" class="form-control" required>
                            <option value="m3u8" {{ old('stream_type') == 'm3u8' ? 'selected' : '' }}>HLS (.m3u8) - الأكثر شيوعاً</option>
                            <option value="mpd" {{ old('stream_type') == 'mpd' ? 'selected' : '' }}>DASH (.mpd)</option>
                            <option value="ts" {{ old('stream_type') == 'ts' ? 'selected' : '' }}>MPEG-TS (.ts)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quality">جودة الفيديو *</label>
                        <select id="quality" name="quality" class="form-control" required>
                            <option value="FHD" {{ old('quality') == 'FHD' ? 'selected' : '' }}>Full HD (1080p)</option>
                            <option value="HD" {{ old('quality', 'HD') == 'HD' ? 'selected' : '' }}>HD (720p)</option>
                            <option value="SD" {{ old('quality') == 'SD' ? 'selected' : '' }}>SD (480p)</option>
                            <option value="4K" {{ old('quality') == '4K' ? 'selected' : '' }}>Ultra HD (4K)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="backup_url">رابط البث الاحتياطي (اختياري، سيتم تشفيره)</label>
                    <input type="text" id="backup_url" name="backup_url" class="form-control"
                           placeholder="https://backup.example.com/live/channel/index.m3u8" value="{{ old('backup_url') }}">
                </div>

                <!-- DRM (Widevine) Settings -->
                <div style="background:rgba(229,169,60,0.05); border:1px solid rgba(229,169,60,0.15); border-radius:12px; padding:16px; margin: 20px 0;">
                    <h4 style="font-size:14px; font-weight:800; color:var(--accent-primary); margin-top:0; margin-bottom:12px;">
                        🔑 إعدادات فك التشفير وحماية DRM (Widevine) - اختياري
                    </h4>
                    <div class="form-group">
                        <label for="drm_license_url">رابط سيرفر الترخيص لفك التشفير (DRM License URL)</label>
                        <input type="url" id="drm_license_url" name="drm_license_url" class="form-control"
                               placeholder="https://connect-live.bein.com/widevine" value="{{ old('drm_license_url') }}">
                        <span style="font-size:11px; color:var(--text-muted); display:block; margin-top:4px;">
                            مطلوب لقنوات MPEG-DASH (.mpd) المحمية مثل قنوات beIN Connect.
                        </span>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="drm_headers">رؤوس طلب الترخيص DRM Headers (بتنسيق JSON)</label>
                        <textarea id="drm_headers" name="drm_headers" class="form-control" rows="2"
                                  placeholder='{"User-Agent": "Mozilla/5.0...", "Referer": "https://..."}'>{{ old('drm_headers') }}</textarea>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="sort_order">ترتيب العرض</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                    </div>
                    <div class="form-group" style="display:flex; align-items:center; gap:12px; padding-top:26px;">
                        <label class="toggle-switch">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <label for="is_active" style="margin-bottom:0; cursor:pointer; font-weight:700;">نشط ومتاح للبث</label>
                    </div>
                </div>

                <!-- Multiple Servers -->
                <div style="margin-top:28px; padding-top:24px; border-top:1px solid var(--border-glass);">
                    <div class="d-flex justify-between align-center" style="margin-bottom:16px;">
                        <h3 style="font-size:15px; font-weight:800; color:var(--accent-primary);">
                            🖥️ سيرفرات البث المتعددة (اختياري)
                        </h3>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addServerRow()">
                            + إضافة سيرفر
                        </button>
                    </div>
                    <div style="font-size:12px; color:var(--text-muted); margin-bottom:16px;">
                        أضف سيرفرات بديلة لتوفير بدائل عند تعطل السيرفر الرئيسي. يستطيع المستخدم التبديل بينها في التطبيق.
                    </div>
                    <div id="servers-container" style="display:flex; flex-direction:column; gap:14px;"></div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-end gap-2" style="margin-top:32px; padding-top:20px; border-top:1px solid var(--border-glass);">
                    <a href="{{ route('admin.channels.index') }}" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        حفظ القناة
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let serverIndex = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const streamTypeSelect = document.getElementById('stream_type');
            if (streamTypeSelect) {
                streamTypeSelect.addEventListener('change', function() {
                    const type = this.value;
                    const licenseInput = document.getElementById('drm_license_url');
                    const headersInput = document.getElementById('drm_headers');
                    
                    if (type === 'mpd') {
                        if (!licenseInput.value) {
                            licenseInput.value = 'https://beinsports.live.ott.irdeto.com/Widevine/getlicense?CrmId=beinsports';
                        }
                        if (!headersInput.value) {
                            headersInput.value = '{\n  "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",\n  "Origin": "https://connect.bein.com",\n  "Referer": "https://connect.bein.com/live/75"\n}';
                        }
                    }
                });
            }
        });

        function addServerRow(data = {}) {
            const container = document.getElementById('servers-container');
            const row = document.createElement('div');
            row.style.cssText = 'background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:16px; position:relative;';
            row.id = `server-row-${serverIndex}`;

            row.innerHTML = `
                <button type="button" onclick="removeServerRow(${serverIndex})"
                    style="position:absolute; top:12px; left:12px; background:rgba(255,90,126,0.15); border:1px solid rgba(255,90,126,0.3); color:#ff5a7e; border-radius:6px; padding:4px 10px; cursor:pointer; font-size:12px; font-family:Cairo,sans-serif; font-weight:700;">
                    ✕ إزالة
                </button>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-top:10px;">
                    <div class="form-group">
                        <label>اسم السيرفر</label>
                        <input type="text" name="servers[${serverIndex}][name]" class="form-control" required
                               value="${data.name || ''}" placeholder="سيرفر 1 / Server 1">
                    </div>
                    <div class="form-group">
                        <label>الجودة</label>
                        <select name="servers[${serverIndex}][quality]" class="form-control">
                            <option value="HD" ${data.quality === 'HD' ? 'selected' : ''}>HD (720p)</option>
                            <option value="FHD" ${data.quality === 'FHD' ? 'selected' : ''}>Full HD (1080p)</option>
                            <option value="SD" ${data.quality === 'SD' ? 'selected' : ''}>SD (480p)</option>
                            <option value="4K" ${data.quality === '4K' ? 'selected' : ''}>4K (2160p)</option>
                        </select>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:2fr 1fr; gap:14px;">
                    <div class="form-group">
                        <label>رابط البث</label>
                        <input type="text" name="servers[${serverIndex}][stream_url]" class="form-control" required
                               value="${data.stream_url || ''}" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>نوع البث</label>
                        <select name="servers[${serverIndex}][stream_type]" class="form-control">
                            <option value="m3u8" ${data.stream_type === 'm3u8' ? 'selected' : ''}>HLS (.m3u8)</option>
                            <option value="mpd" ${data.stream_type === 'mpd' ? 'selected' : ''}>DASH (.mpd)</option>
                            <option value="ts" ${data.stream_type === 'ts' ? 'selected' : ''}>MPEG-TS (.ts)</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="servers[${serverIndex}][sort_order]" value="${serverIndex}">
            `;

            container.appendChild(row);
            serverIndex++;
        }

        function removeServerRow(index) {
            const el = document.getElementById(`server-row-${index}`);
            if (el) el.remove();
        }
    </script>

@endsection
