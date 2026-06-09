@extends('admin.layouts.app')

@section('title', 'استيراد القنوات')
@section('header_title', 'استيراد القنوات من M3U')
@section('header_subtitle', 'رفع ملف قائمة تشغيل M3U لاستيراد عدد كبير من القنوات دفعة واحدة')

@section('actions')
    <a href="{{ route('admin.channels.index') }}" class="btn btn-secondary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
        العودة للقنوات
    </a>
@endsection

@section('content')

    <div style="max-width: 850px; margin: 0 auto;">

        <!-- Info Banner -->
        <div class="card" style="background: linear-gradient(135deg, rgba(0,212,170,0.08), rgba(79,126,249,0.08)); border-color: rgba(0,212,170,0.2); padding: 20px 24px; margin-bottom: 20px;">
            <div class="d-flex align-center gap-3">
                <div style="font-size:36px;">📥</div>
                <div>
                    <div style="font-weight:800; font-size:16px; color:var(--accent-primary); margin-bottom:4px;">استيراد ذكي للقنوات</div>
                    <div style="font-size:13px; color:var(--text-muted); line-height:1.7;">
                        يقوم النظام تلقائياً بـ: تصنيف القنوات حسب الدولة واللغة والقارة ونوع المحتوى • استخراج الشعارات والأسماء •
                        إنشاء تصنيفات جديدة من علامات <code style="background:rgba(0,212,170,0.1); padding:1px 6px; border-radius:4px; color:var(--accent-primary);">group-title</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <form action="{{ route('admin.channels.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf

                <!-- File Upload Area -->
                <div class="form-group">
                    <label for="m3u_file">
                        📁 رفع ملف قائمة التشغيل (.m3u / .m3u8 / .txt)
                    </label>
                    <div id="drop-zone" style="border: 2px dashed var(--border-glass); border-radius: var(--radius-md); padding: 32px; text-align: center; cursor: pointer; transition: all 0.3s; position: relative;"
                         ondragover="event.preventDefault(); this.style.borderColor='var(--accent-primary)'; this.style.background='rgba(0,212,170,0.05)';"
                         ondragleave="this.style.borderColor='var(--border-glass)'; this.style.background='transparent';"
                         ondrop="handleDrop(event);"
                         onclick="document.getElementById('m3u_file').click();">
                        <input type="file" id="m3u_file" name="m3u_file" accept=".m3u,.m3u8,.txt" style="display:none;" onchange="showFileName(this)">
                        <div id="drop-content">
                            <div style="font-size:40px; margin-bottom:10px;">☁️</div>
                            <div style="font-weight:700; font-size:15px; margin-bottom:6px;">اسحب الملف هنا أو اضغط للاختيار</div>
                            <div style="font-size:12px; color:var(--text-muted);">يقبل ملفات M3U وM3U8 وTXT</div>
                        </div>
                        <div id="file-selected" style="display:none; color:var(--accent-primary); font-weight:700;"></div>
                    </div>
                </div>

                <!-- Divider -->
                <div style="display:flex; align-items:center; gap:16px; margin: 24px 0;">
                    <div style="flex:1; height:1px; background:var(--border-glass);"></div>
                    <span style="color:var(--text-muted); font-size:12px; font-weight:700; letter-spacing:2px;">أو الصق النص</span>
                    <div style="flex:1; height:1px; background:var(--border-glass);"></div>
                </div>

                <!-- Text Area -->
                <div class="form-group">
                    <label for="m3u_text">📋 محتوى قائمة التشغيل M3U</label>
                    <textarea id="m3u_text" name="m3u_text" class="form-control" rows="10"
                              placeholder="#EXTM3U&#10;#EXTINF:-1 tvg-name=&quot;اسم القناة&quot; tvg-logo=&quot;https://...&quot; group-title=&quot;رياضة&quot;,اسم القناة&#10;https://example.com/stream.m3u8"
                              style="font-family: monospace; font-size: 12px; resize: vertical;">{{ old('m3u_text') }}</textarea>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:6px;">الصق محتوى ملف M3U كاملاً أو جزءاً منه</div>
                </div>

                <!-- Import Settings -->
                <div style="background: rgba(0,0,0,0.2); border-radius: var(--radius-md); border: 1px solid var(--border-glass); padding: 20px; margin-top: 8px;">
                    <h3 style="color:var(--accent-primary); margin-bottom:18px; font-size:15px; font-weight:800;">⚙️ إعدادات الاستيراد</h3>

                    <div class="form-group">
                        <label for="default_category_id">📂 التصنيف الافتراضي (للقنوات غير المعروفة)</label>
                        <select id="default_category_id" name="default_category_id" class="form-control" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                    @if($category->name_ar && $category->name_ar != $category->name)
                                        ({{ $category->name_ar }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:5px;">يُستخدم عند عدم وجود علامة group-title أو عند عدم التعرف على التصنيف</div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:4px;">
                        <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer; padding: 14px; background:rgba(0,212,170,0.04); border-radius:var(--radius-sm); border:1px solid rgba(0,212,170,0.1);">
                            <input type="checkbox" id="auto_create_categories" name="auto_create_categories" value="1" checked
                                   style="width:18px; height:18px; accent-color:var(--accent-primary); margin-top:2px; flex-shrink:0;">
                            <div>
                                <div style="font-weight:700; font-size:13px; margin-bottom:4px;">إنشاء تصنيفات تلقائياً</div>
                                <div style="font-size:11px; color:var(--text-muted);">يُنشئ تصنيفات جديدة من علامات group-title الموجودة في الملف</div>
                            </div>
                        </label>

                        <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer; padding: 14px; background:rgba(79,126,249,0.04); border-radius:var(--radius-sm); border:1px solid rgba(79,126,249,0.1);">
                            <input type="checkbox" id="skip_existing" name="skip_existing" value="1" checked
                                   style="width:18px; height:18px; accent-color:var(--accent-secondary); margin-top:2px; flex-shrink:0;">
                            <div>
                                <div style="font-weight:700; font-size:13px; margin-bottom:4px;">تجاهل القنوات المكررة</div>
                                <div style="font-size:11px; color:var(--text-muted);">يتجاهل القنوات الموجودة مسبقاً بنفس الاسم أو الرابط</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-flex justify-end gap-2" style="margin-top:24px; padding-top:20px; border-top:1px solid var(--border-glass);">
                    <a href="{{ route('admin.channels.index') }}" class="btn btn-secondary">إلغاء</a>
                    <button type="submit" class="btn btn-primary" style="min-width:160px;" id="submitBtn">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                        بدء الاستيراد
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showFileName(input) {
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                const size = (input.files[0].size / 1024).toFixed(1) + ' KB';
                document.getElementById('drop-content').style.display = 'none';
                const sel = document.getElementById('file-selected');
                sel.style.display = 'block';
                sel.innerHTML = `✅ تم اختيار: <strong>${fileName}</strong> (${size})`;
                document.getElementById('drop-zone').style.borderColor = 'var(--accent-primary)';
                document.getElementById('drop-zone').style.background = 'rgba(0,212,170,0.05)';
            }
        }

        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('drop-zone').style.borderColor = 'var(--border-glass)';
            document.getElementById('drop-zone').style.background = 'transparent';
            const file = e.dataTransfer.files[0];
            if (file) {
                const input = document.getElementById('m3u_file');
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showFileName(input);
            }
        }

        document.getElementById('importForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '⏳ جاري الاستيراد...';
        });
    </script>

@endsection
