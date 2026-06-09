@extends('admin.layouts.app')

@section('title', 'القنوات المباشرة')
@section('header_title', 'القنوات المباشرة')
@section('header_subtitle', 'إدارة روابط البث وبيانات القنوات')

@section('actions')
    <div class="d-flex gap-2 align-center" style="flex-wrap:wrap;">
        <form action="{{ route('admin.channels.index') }}" method="GET" class="d-flex gap-2 align-center" style="flex-wrap:wrap;">
            <div class="search-input-wrapper" style="min-width:180px;">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" name="search" class="search-input" placeholder="ابحث عن قناة..." value="{{ $search }}">
            </div>
            <select name="category_id" class="form-control" style="width:170px;" onchange="this.form.submit()">
                <option value="">جميع التصنيفات</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">🔍 بحث</button>
        </form>
        <a href="{{ route('admin.channels.import') }}" class="btn btn-gold">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            استيراد M3U
        </a>

        {{-- زر حذف جميع القنوات --}}
        <form action="{{ route('admin.channels.destroy-all') }}" method="POST"
              onsubmit="return confirmDeleteAll()">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" style="background:linear-gradient(135deg,#ff3a5c,#c0203e); box-shadow:0 4px 15px rgba(255,58,92,0.35);">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                مسح كل القنوات
            </button>
        </form>

        <a href="{{ route('admin.channels.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/></svg>
            إضافة قناة
        </a>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="section-header">
            <h2>
                <span class="icon">📺</span>
                قائمة القنوات
                <span class="badge badge-info" style="font-size:12px; margin-right:8px;">{{ $channels->total() }} قناة</span>
            </h2>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell"><input type="checkbox" id="selectAll"></th>
                        <th style="width:50px;">#</th>
                        <th style="width:60px;">الشعار</th>
                        <th>اسم القناة</th>
                        <th>الاسم بالعربي</th>
                        <th>التصنيف</th>
                        <th>الدولة</th>
                        <th>الجودة</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($channels as $channel)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="select-item" value="{{ $channel->id }}">
                            </td>
                            <td>
                                <span style="font-family:monospace; font-weight:700; color:var(--text-muted); font-size:12px;">{{ $channel->sort_order }}</span>
                            </td>
                            <td>
                                @if($channel->logo_url)
                                    <img src="{{ $channel->logo_url }}" alt="{{ $channel->name }}"
                                         style="width:40px; height:40px; object-fit:contain; border-radius:8px; background:rgba(255,255,255,0.05); padding:3px; border:1px solid var(--border-glass);"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display:none; width:40px; height:40px; border-radius:8px; background:linear-gradient(135deg,var(--accent-primary-glow),var(--accent-secondary-glow)); align-items:center; justify-content:center; font-weight:800; font-size:12px; color:var(--text-main); border:1px solid var(--border-glass);">
                                        {{ substr($channel->name, 0, 1) }}
                                    </div>
                                @else
                                    <div style="width:40px; height:40px; border-radius:8px; background:linear-gradient(135deg,var(--accent-primary-glow),var(--accent-secondary-glow)); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; color:var(--text-main); border:1px solid var(--border-glass);">
                                        {{ substr($channel->name, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight:700; font-size:14px;">{{ Str::limit($channel->name, 35) }}</span>
                                @if($channel->country)
                                    <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ $channel->country }}</div>
                                @endif
                            </td>
                            <td style="color:var(--text-muted); font-size:13px;">{{ $channel->name_ar ? Str::limit($channel->name_ar, 30) : '—' }}</td>
                            <td>
                                <span class="badge badge-info">{{ $channel->category->name ?? '—' }}</span>
                            </td>
                            <td style="font-size:13px;">
                                @if($channel->country)
                                    <span style="color:var(--text-muted);">{{ $channel->country }}</span>
                                @else
                                    <span style="color:var(--border-glass);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-warning">{{ $channel->quality->value ?? $channel->quality }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $channel->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $channel->is_active ? '• نشط' : '○ معطل' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-end">
                                    <a href="{{ route('admin.channels.edit', $channel->id) }}" class="btn btn-secondary btn-xs">✏️ تعديل</a>
                                    <form action="{{ route('admin.channels.destroy', $channel->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه القناة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">🗑 حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted" style="padding:60px 0;">
                                <div style="font-size:50px; margin-bottom:16px;">📺</div>
                                <div style="font-size:16px; font-weight:700; margin-bottom:8px;">لا توجد قنوات</div>
                                <div style="font-size:13px;">اضغط على "إضافة قناة" أو "استيراد M3U" لإضافة قنوات جديدة</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $channels->appends(['search' => $search, 'category_id' => $categoryId])->links() }}
        </div>
    </div>

<script>
function confirmDeleteAll() {
    // المرحلة الأولى
    if (!confirm('⚠️ تحذير خطير!\n\nأنت على وشك حذف جميع القنوات ({{ $channels->total() }} قناة) نهائياً!\n\nهل أنت متأكد؟')) {
        return false;
    }
    // المرحلة الثانية — تأكيد إضافي
    const input = prompt('للتأكيد، اكتب كلمة "حذف" في الحقل أدناه:');
    if (input !== 'حذف') {
        alert('تم إلغاء العملية. لم يتم حذف أي قناة.');
        return false;
    }
    return true;
}
</script>

@endsection
