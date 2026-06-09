@extends('admin.layouts.app')

@section('header_title', 'محلل حركة الشبكة وروابط البث')
@section('header_subtitle', 'ارفع ملف HAR/Charles أو الصق روابط حركة الشبكة لاستخراج روابط البث الحقيقية وتوزيعها فوراً')

@section('content')
<div class="row" style="display: flex; justify-content: center; width: 100%;">
    <div class="col-md-8" style="width: 70%; max-width: 800px;">
        <div class="card">
            <div class="section-header">
                <h2>
                    <span class="icon">🔍</span>
                    تحليل واستخراج روابط البث المباشر
                </h2>
            </div>
            
            <p class="text-muted fs-14 mb-4" style="line-height: 1.6;">
                تتيح لك هذه الأداة استخراج روابط البث المباشر الأصلية (مثل روابط M3U8 أو MPD أو TS) بشكل آلي وسهل جداً إما برفع ملف الجلسة المسجل أو بلصق حركة المرور والروابط المنسوخة مباشرة.
            </p>

            <form action="{{ route('admin.har.analyze') }}" method="POST" enctype="multipart/form-data" id="harForm">
                @csrf
                
                <!-- Option 1: File Upload -->
                <div class="form-group mb-4">
                    <label for="har_file" class="fw-700 fs-13 mb-2">الخيار الأول: ارفع ملف السجل (HAR / Charles JSON / CSV / TXT):</label>
                    <input type="file" name="har_file" id="har_file" class="form-control" accept=".har,.json,.chlsj,.txt,.csv" style="padding: 15px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass);">
                    <small class="text-muted d-block mt-2" style="display: block; line-height: 1.5; font-size: 12px; color: var(--text-muted);">
                        * يدعم ملفات سجل المتصفح (.har) وملفات جلسات Charles Proxy المصدرة بصيغة (.chlsj) أو الملفات النصية العادية.
                    </small>
                </div>

                <!-- Separator -->
                <div style="text-align: center; margin: 25px 0; color: var(--text-muted); font-weight: 700; position: relative;">
                    <span style="background: #0f182a; padding: 0 15px; position: relative; z-index: 2; font-size: 12px; border: 1px solid var(--border-glass); border-radius: 20px;">أو (لصق حركة المرور مباشرة)</span>
                    <hr style="border: none; border-top: 1px solid var(--border-glass); position: absolute; top: 50%; left: 0; right: 0; z-index: 1; margin: 0;">
                </div>

                <!-- Option 2: Paste Raw Text -->
                <div class="form-group mb-4">
                    <label for="raw_text" class="fw-700 fs-13 mb-2">الخيار الثاني: الصق محتوى الملف أو الروابط المنسوخة هنا:</label>
                    <textarea name="raw_text" id="raw_text" class="form-control" placeholder="يمكنك تحديد وحفظ الطلبات من Charles Proxy أو المتصفح، ثم نسخها ولصقها هنا مباشرة لتصفيتها واستخراج روابط البث الفعالة..." style="height: 180px; font-family: monospace; font-size: 13px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass); padding: 15px; resize: vertical;"></textarea>
                    <small class="text-muted d-block mt-2" style="display: block; line-height: 1.5; font-size: 12px; color: var(--text-muted);">
                        * نصيحة: في برنامج Charles Proxy، يمكنك النقر على الطلبات وتحديدها ثم نسخها (Ctrl+C) ولصقها هنا فوراً دون الحاجة لحفظ أو رفع أي ملفات.
                    </small>
                </div>

                <div class="d-flex justify-end gap-2" style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="margin-left: 6px;"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/></svg>
                        تحليل واستخراج روابط البث الآن
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('harForm').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('har_file');
        const textarea = document.getElementById('raw_text');
        
        if (!fileInput.files.length && !textarea.value.trim()) {
            e.preventDefault();
            alert('الرجاء رفع ملف أو لصق نص حركة المرور للبدء بالتحليل.');
        }
    });
</script>
@endsection
