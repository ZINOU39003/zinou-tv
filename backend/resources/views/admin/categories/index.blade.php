@extends('admin.layouts.app')

@section('title', 'الشبكات')
@section('header_title', 'الشبكات')
@section('header_subtitle', 'إدارة الشبكات (Networks) للبث المباشر')

@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/></svg>
        إضافة شبكة
    </a>
@endsection

@section('content')

    <div class="card">
        <div class="section-header">
            <h2>
                <span class="icon">📂</span>
                قائمة الشبكات
                <span class="badge badge-info" style="font-size:12px; margin-right:8px;">{{ $categories->count() }} شبكة</span>
            </h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell"><input type="checkbox" id="selectAll"></th>
                        <th style="width:50px;">#</th>
                        <th>الاسم بالإنجليزية</th>
                        <th>الاسم بالعربية</th>
                        <th>المعرّف (Slug)</th>
                        <th>النوع</th>
                        <th>عدد القنوات</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="select-item" value="{{ $category->id }}">
                            </td>
                            <td style="font-family:monospace; font-weight:700; color:var(--text-muted); font-size:12px;">{{ $category->sort_order }}</td>
                            <td style="font-weight:700;">{{ $category->name }}</td>
                            <td style="color:var(--text-muted);">{{ $category->name_ar ?: '—' }}</td>
                            <td>
                                <code style="font-family:monospace; background:rgba(255,255,255,0.04); padding:3px 8px; border-radius:5px; font-size:12px; color:var(--accent-secondary);">{{ $category->slug }}</code>
                            </td>
                            <td>
                                @php
                                    $typeLabels = [
                                        'content_type' => ['label' => 'نوع المحتوى', 'badge' => 'badge-info'],
                                        'country' => ['label' => 'دولة', 'badge' => 'badge-warning'],
                                        'language' => ['label' => 'لغة', 'badge' => 'badge-success'],
                                        'continent' => ['label' => 'قارة', 'badge' => 'badge-info'],
                                        'network' => ['label' => 'شبكة / شركة باثة', 'badge' => 'badge-success'],
                                    ];
                                    $typeInfo = $typeLabels[$category->type] ?? ['label' => $category->type, 'badge' => 'badge-info'];
                                @endphp
                                <span class="badge {{ $typeInfo['badge'] }}">{{ $typeInfo['label'] }}</span>
                            </td>
                            <td>
                                <span style="font-weight:700; color:var(--text-main);">{{ $category->channels_count }}</span>
                                <span style="font-size:11px; color:var(--text-muted);"> قناة</span>
                            </td>
                            <td>
                                <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $category->is_active ? '• نشط' : '○ معطل' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-end">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-secondary btn-xs">✏️ تعديل</a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟ سيفشل إذا كان يحتوي على قنوات.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">🗑 حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted" style="padding:60px 0;">
                                <div style="font-size:50px; margin-bottom:16px;">📂</div>
                                <div style="font-size:16px; font-weight:700; margin-bottom:8px;">لا توجد تصنيفات</div>
                                <div style="font-size:13px;">اضغط "إضافة تصنيف" لإنشاء تصنيف جديد</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
