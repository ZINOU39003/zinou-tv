@extends('admin.layouts.app')

@section('title', 'الباقات')
@section('header_title', 'الباقات')
@section('header_subtitle', 'إدارة باقات البث المباشر لكل شبكة')

@section('actions')
    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/></svg>
        إضافة باقة
    </a>
@endsection

@section('content')

    {{-- Category Filter --}}
    <div class="card" style="margin-bottom: 20px; padding: 16px;">
        <form method="GET" action="{{ route('admin.packages.index') }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <select name="category_id" class="form-control" style="max-width:250px;" onchange="this.form.submit()">
                <option value="">— جميع الشبكات —</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar ?: $cat->name }}</option>
                @endforeach
            </select>
            @if($categoryId)
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-xs">✕ مسح الفلتر</a>
            @endif
        </form>
    </div>

    <div class="card">
        <div class="section-header">
            <h2>
                <span class="icon">📦</span>
                قائمة الباقات
                <span class="badge badge-info" style="font-size:12px; margin-right:8px;">{{ $packages->count() }} باقة</span>
            </h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell"><input type="checkbox" id="selectAll"></th>
                        <th style="width:50px;">#</th>
                        <th style="width:60px;">الشعار</th>
                        <th>الاسم بالإنجليزية</th>
                        <th>الاسم بالعربية</th>
                        <th>الشبكة</th>
                        <th>المعرّف (Slug)</th>
                        <th>عدد القنوات</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $package)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="select-item" value="{{ $package->id }}">
                            </td>
                            <td style="font-family:monospace; font-weight:700; color:var(--text-muted); font-size:12px;">{{ $package->sort_order }}</td>
                            <td>
                                @if($package->logo_url)
                                    <img src="{{ $package->logo_url }}" alt="Logo" style="width:40px; height:40px; border-radius:8px; object-fit:cover; background:rgba(255,255,255,0.05);">
                                @else
                                    <div style="width:40px; height:40px; border-radius:8px; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; font-size:18px;">📦</div>
                                @endif
                            </td>
                            <td style="font-weight:700;">{{ $package->name }}</td>
                            <td style="color:var(--text-muted);">{{ $package->name_ar ?: '—' }}</td>
                            <td>
                                <span class="badge badge-info">{{ $package->category ? ($package->category->name_ar ?: $package->category->name) : '—' }}</span>
                            </td>
                            <td>
                                <code style="font-family:monospace; background:rgba(255,255,255,0.04); padding:3px 8px; border-radius:5px; font-size:12px; color:var(--accent-secondary);">{{ $package->slug }}</code>
                            </td>
                            <td>
                                <span style="font-weight:700; color:var(--text-main);">{{ $package->channels_count }}</span>
                                <span style="font-size:11px; color:var(--text-muted);"> قناة</span>
                            </td>
                            <td>
                                <span class="badge {{ $package->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $package->is_active ? '• نشط' : '○ معطل' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-end">
                                    <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-secondary btn-xs">✏️ تعديل</a>
                                    <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الباقة؟')">
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
                                <div style="font-size:50px; margin-bottom:16px;">📦</div>
                                <div style="font-size:16px; font-weight:700; margin-bottom:8px;">لا توجد باقات</div>
                                <div style="font-size:13px;">اضغط "إضافة باقة" لإنشاء باقة جديدة</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
