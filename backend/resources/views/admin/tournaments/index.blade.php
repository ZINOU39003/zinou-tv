@extends('admin.layouts.app')

@section('title', 'البطولات')
@section('header_title', 'البطولات والبطولات')
@section('header_subtitle', 'إدارة البطولات والدوريات الرياضية')

@section('actions')
    <div class="d-flex gap-2 align-center">
        <form action="{{ route('admin.tournaments.index') }}" method="GET" class="d-flex gap-2 align-center">
            <div class="search-input-wrapper" style="min-width:200px;">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" name="search" class="search-input" placeholder="ابحث عن بطولة..." value="{{ $search }}">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">🔍</button>
        </form>
        <a href="{{ route('admin.tournaments.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/></svg>
            إضافة بطولة
        </a>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="section-header">
            <h2>
                <span class="icon">🏆</span>
                قائمة البطولات
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
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tournaments as $tournament)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="select-item" value="{{ $tournament->id }}">
                            </td>
                            <td style="font-family:monospace; font-weight:700; color:var(--text-muted); font-size:12px;">{{ $tournament->sort_order }}</td>
                            <td>
                                @if($tournament->logo_url)
                                    <img src="{{ $tournament->logo_url }}" alt="{{ $tournament->name }}"
                                         style="width:42px; height:42px; object-fit:contain; border-radius:8px; background:rgba(255,255,255,0.05); padding:3px; border:1px solid var(--border-glass);"
                                         onerror="this.style.display='none'">
                                @else
                                    <div style="width:42px; height:42px; border-radius:8px; background:rgba(240,180,41,0.1); border:1px solid rgba(240,180,41,0.2); display:flex; align-items:center; justify-content:center; font-size:20px;">🏆</div>
                                @endif
                            </td>
                            <td style="font-weight:700;">{{ $tournament->name }}</td>
                            <td style="color:var(--text-muted);">{{ $tournament->name_ar ?: '—' }}</td>
                            <td>
                                <span class="badge {{ $tournament->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $tournament->is_active ? '• نشط' : '○ معطل' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-end">
                                    <a href="{{ route('admin.tournaments.edit', $tournament->id) }}" class="btn btn-secondary btn-xs">✏️ تعديل</a>
                                    <form action="{{ route('admin.tournaments.destroy', $tournament->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه البطولة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs">🗑 حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding:60px 0;">
                                <div style="font-size:50px; margin-bottom:16px;">🏆</div>
                                <div style="font-size:16px; font-weight:700; margin-bottom:8px;">لا توجد بطولات</div>
                                <div style="font-size:13px;">اضغط "إضافة بطولة" لإنشاء بطولة جديدة</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $tournaments->appends(['search' => $search])->links() }}
        </div>
    </div>

@endsection
