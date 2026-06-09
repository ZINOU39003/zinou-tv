@extends('admin.layouts.app')

@section('title', 'تعديل القناة - ' . $channel->name)
@section('header_title', 'تعديل القناة')
@section('header_subtitle', 'تحديث إعدادات مصدر البث للقناة')

@section('content')

    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <form action="{{ route('admin.channels.update', $channel->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="name">اسم القناة (إنجليزي) *</label>
                    <input type="text" id="name" name="name" class="form-control" required value="{{ old('name', $channel->name) }}">
                </div>

                <div class="form-group">
                    <label for="name_ar">اسم القناة (عربي)</label>
                    <input type="text" id="name_ar" name="name_ar" class="form-control" value="{{ old('name_ar', $channel->name_ar) }}">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="category_id">الباقة أو الشبكة *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $channel->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }} @if($category->name_ar && $category->name_ar != $category->name)({{ $category->name_ar }})@endif</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="logo_url">رابط شعار القناة</label>
                    <input type="url" id="logo_url" name="logo_url" class="form-control" value="{{ old('logo_url', $channel->logo_url) }}">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px; margin-top: 15px;">
                <div class="form-group">
                    <label for="country">الدولة</label>
                    <input type="text" id="country" name="country" class="form-control" placeholder="السعودية / مصر" value="{{ old('country', $channel->country) }}">
                </div>
                <div class="form-group">
                    <label for="language">اللغة</label>
                    <input type="text" id="language" name="language" class="form-control" placeholder="العربية / الإنجليزية" value="{{ old('language', $channel->language) }}">
                </div>
                <div class="form-group">
                    <label for="continent">Continent (القارة)</label>
                    <input type="text" id="continent" name="continent" class="form-control" placeholder="e.g. آسيا, أفريقيا, أوروبا" value="{{ old('continent', $channel->continent) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="stream_url">Stream Playback URL (Will be encrypted in Database automatically)</label>
                <input type="text" id="stream_url" name="stream_url" class="form-control" required value="{{ old('stream_url', $channel->decrypted_stream_url) }}">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="stream_type">Stream Type</label>
                    <select id="stream_type" name="stream_type" class="form-control" required>
                        <option value="m3u8" {{ old('stream_type', $channel->stream_type->value) == 'm3u8' ? 'selected' : '' }}>HLS (.m3u8)</option>
                        <option value="mpd" {{ old('stream_type', $channel->stream_type->value) == 'mpd' ? 'selected' : '' }}>DASH (.mpd)</option>
                        <option value="ts" {{ old('stream_type', $channel->stream_type->value) == 'ts' ? 'selected' : '' }}>MPEG-TS (.ts)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quality">Video Quality</label>
                    <select id="quality" name="quality" class="form-control" required>
                        <option value="FHD" {{ old('quality', $channel->quality->value) == 'FHD' ? 'selected' : '' }}>Full HD (1080p)</option>
                        <option value="HD" {{ old('quality', $channel->quality->value) == 'HD' ? 'selected' : '' }}>HD (720p)</option>
                        <option value="SD" {{ old('quality', $channel->quality->value) == 'SD' ? 'selected' : '' }}>SD (480p)</option>
                        <option value="4K" {{ old('quality', $channel->quality->value) == '4K' ? 'selected' : '' }}>Ultra HD (4K)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="backup_url">Backup Stream URL (Optional, will be encrypted)</label>
                <input type="text" id="backup_url" name="backup_url" class="form-control" value="{{ old('backup_url', $channel->decrypted_backup_url) }}">
            </div>

            <!-- DRM (Widevine) Settings -->
            <div style="background:rgba(229,169,60,0.05); border:1px solid rgba(229,169,60,0.15); border-radius:12px; padding:16px; margin: 20px 0;">
                <h4 style="font-size:14px; font-weight:800; color:var(--accent-primary); margin-top:0; margin-bottom:12px;">
                    🔑 إعدادات فك التشفير وحماية DRM (Widevine) - اختياري
                </h4>
                <div class="form-group">
                    <label for="drm_license_url">رابط سيرفر الترخيص لفك التشفير (DRM License URL)</label>
                    <input type="url" id="drm_license_url" name="drm_license_url" class="form-control"
                           placeholder="https://connect-live.bein.com/widevine" value="{{ old('drm_license_url', $channel->drm_license_url) }}">
                    <span style="font-size:11px; color:var(--text-muted); display:block; margin-top:4px;">
                        مطلوب لقنوات MPEG-DASH (.mpd) المحمية مثل قنوات beIN Connect.
                    </span>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label for="drm_headers">رؤوس طلب الترخيص DRM Headers (بتنسيق JSON)</label>
                    <textarea id="drm_headers" name="drm_headers" class="form-control" rows="2"
                              placeholder='{"User-Agent": "Mozilla/5.0...", "Referer": "https://..."}'>{{ old('drm_headers', $channel->drm_headers) }}</textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="sort_order">Display Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" required value="{{ old('sort_order', $channel->sort_order) }}">
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top: 24px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $channel->is_active) ? 'checked' : '' }} style="width:20px; height:20px; accent-color:var(--accent-primary);">
                <label for="is_active" style="margin-bottom:0; cursor:pointer;">Active and streamable inside Android application</label>
            </div>

            <!-- Multiple Streaming Servers Section -->
            <div style="margin-top: 32px; border-top: 1px solid var(--border-glass); padding-top: 24px;">
                <h3 style="color: var(--accent-primary); margin-bottom: 16px; font-size: 1.1rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>Multiple Streaming Servers (سيرفرات البث المتعددة)</span>
                    <button type="button" class="btn btn-secondary" style="font-size: 0.8rem; padding: 4px 12px;" onclick="addServerRow()">+ Add Server</button>
                </h3>
                
                <div id="servers-container" style="display: flex; flex-direction: column; gap: 16px;">
                    <!-- Server rows will be added here -->
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
                <a href="{{ route('admin.channels.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Channel</button>
            </div>
        </form>
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
            row.className = 'card';
            row.style.padding = '15px';
            row.style.background = 'rgba(255, 255, 255, 0.03)';
            row.style.border = '1px solid rgba(255, 255, 255, 0.08)';
            row.style.position = 'relative';
            row.style.borderRadius = '8px';
            row.id = `server-row-${serverIndex}`;

            row.innerHTML = `
                <button type="button" class="btn" style="position: absolute; top: 10px; right: 10px; padding: 2px 8px; font-size: 0.8rem; background: #ea868f; color: #000;" onclick="removeServerRow(${serverIndex})">Remove</button>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top: 15px;">
                    <div class="form-group">
                        <label>Server Name (English or Arabic)</label>
                        <input type="text" name="servers[${serverIndex}][name]" class="form-control" required value="${data.name || ''}" placeholder="Server 1 / سيرفر 1">
                    </div>
                    <div class="form-group">
                        <label>Quality</label>
                        <select name="servers[${serverIndex}][quality]" class="form-control">
                            <option value="HD" \${data.quality === 'HD' ? 'selected' : ''}>HD (720p)</option>
                            <option value="FHD" \${data.quality === 'FHD' ? 'selected' : ''}>Full HD (1080p)</option>
                            <option value="SD" \${data.quality === 'SD' ? 'selected' : ''}>SD (480p)</option>
                            <option value="4K" \${data.quality === '4K' ? 'selected' : ''}>4K (2160p)</option>
                        </select>
                    </div>
                </div>
                
                <div style="display:grid; grid-template-columns:2fr 1fr; gap:15px; margin-top: 10px;">
                    <div class="form-group">
                        <label>Playback Stream URL</label>
                        <input type="text" name="servers[${serverIndex}][stream_url]" class="form-control" required value="${data.stream_url || ''}" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>Stream Type</label>
                        <select name="servers[${serverIndex}][stream_type]" class="form-control">
                            <option value="m3u8" \${data.stream_type === 'm3u8' ? 'selected' : ''}>HLS (.m3u8)</option>
                            <option value="mpd" \${data.stream_type === 'mpd' ? 'selected' : ''}>DASH (.mpd)</option>
                            <option value="ts" \${data.stream_type === 'ts' ? 'selected' : ''}>MPEG-TS (.ts)</option>
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
            if (el) {
                el.remove();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            @foreach($channel->servers as $server)
                addServerRow({
                    name: "{{ $server->name }}",
                    quality: "{{ $server->quality->value ?? $server->quality }}",
                    stream_url: "{{ $server->decrypted_stream_url }}",
                    stream_type: "{{ $server->stream_type->value ?? $server->stream_type }}"
                });
            @endforeach
        });
    </script>

@endsection
