@extends('admin.layouts.app')

@section('title', 'الأفلام والمسلسلات')
@section('header_title', 'الأفلام والمسلسلات')
@section('header_subtitle', 'إدارة محتوى الأفلام والمسلسلات والـ VOD')

@section('actions')
    <div class="d-flex gap-2 align-center" style="flex-wrap:wrap;">
        <form action="{{ route('admin.movies.index') }}" method="GET" class="d-flex gap-2 align-center">
            <select name="type" class="form-control" style="width:150px;" onchange="this.form.submit()">
                <option value="">جميع الأنواع</option>
                <option value="movie" {{ $type == 'movie' ? 'selected' : '' }}>🎬 أفلام</option>
                <option value="series" {{ $type == 'series' ? 'selected' : '' }}>📺 مسلسلات</option>
            </select>
            <div class="search-input-wrapper" style="min-width:180px;">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" name="search" class="search-input" placeholder="ابحث عن فيلم..." value="{{ $search }}">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">🔍</button>
        </form>
        <a href="{{ route('admin.movies.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/></svg>
            إضافة فيلم
        </a>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="section-header">
            <h2>
                <span class="icon">🎬</span>
                قائمة الأفلام والمسلسلات
            </h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell"><input type="checkbox" id="selectAll"></th>
                        <th style="width:55px;">الغلاف</th>
                        <th>العنوان</th>
                        <th>العنوان بالعربية</th>
                        <th>النوع</th>
                        <th>السنة</th>
                        <th>التقييم</th>
                        <th>الأحدث</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movies as $movie)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="select-item" value="{{ $movie->id }}">
                            </td>
                            <td>
                                @if($movie->poster_url)
                                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
                                         style="width:38px; height:54px; object-fit:cover; border-radius:6px; border:1px solid var(--border-glass);"
                                         onerror="this.style.display='none'">
                                @else
                                    <div style="width:38px; height:54px; border-radius:6px; background:rgba(79,126,249,0.1); border:1px solid rgba(79,126,249,0.2); display:flex; align-items:center; justify-content:center; font-size:18px;">🎬</div>
                                @endif
                            </td>
                            <td style="font-weight:700; max-width:180px;" class="text-ellipsis">{{ $movie->title }}</td>
                            <td style="color:var(--text-muted); max-width:160px;" class="text-ellipsis">{{ $movie->title_ar ?: '—' }}</td>
                            <td>
                                <span class="badge {{ $movie->type === 'movie' ? 'badge-info' : 'badge-warning' }}">
                                    {{ $movie->type === 'movie' ? '🎬 فيلم' : '📺 مسلسل' }}
                                </span>
                            </td>
                            <td style="font-family:monospace; color:var(--text-muted);">{{ $movie->year ?: '—' }}</td>
                            <td>
                                @if($movie->rating)
                                    <span style="font-weight:700; color:var(--accent-gold);">⭐ {{ $movie->rating }}</span>
                                @else
                                    <span style="color:var(--border-glass);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($movie->is_latest)
                                    <span class="badge badge-success">✨ جديد</span>
                                @else
                                    <span style="color:var(--border-glass);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $movie->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $movie->is_active ? '• نشط' : '○ معطل' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-end">
                                    <a href="{{ route('admin.movies.edit', $movie->id) }}" class="btn btn-secondary btn-xs">✏️</a>
                                    <form action="{{ route('admin.movies.destroy', $movie->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفيلم؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted" style="padding:60px 0;">
                                <div style="font-size:50px; margin-bottom:16px;">🎬</div>
                                <div style="font-size:16px; font-weight:700; margin-bottom:8px;">لا توجد أفلام</div>
                                <div style="font-size:13px;">اضغط "إضافة فيلم" لإضافة محتوى جديد</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $movies->appends(['search' => $search, 'type' => $type])->links() }}
        </div>
    </div>

@endsection
