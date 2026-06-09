@extends('admin.layouts.app')

@section('title', 'المباريات المباشرة')
@section('header_title', 'المباريات المباشرة')
@section('header_subtitle', 'إدارة المباريات المباشرة والنتائج والبث')

@section('actions')
    <div class="d-flex gap-2 align-center" style="flex-wrap:wrap;">
        <form action="{{ route('admin.matches.index') }}" method="GET" class="d-flex gap-2 align-center">
            <div class="search-input-wrapper" style="min-width:180px;">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" name="search" class="search-input" placeholder="ابحث عن مباراة..." value="{{ $search }}">
            </div>
            <select name="tournament_id" class="form-control" style="width:180px;" onchange="this.form.submit()">
                <option value="">جميع البطولات</option>
                @foreach($tournaments as $tournament)
                    <option value="{{ $tournament->id }}" {{ $tournamentId == $tournament->id ? 'selected' : '' }}>{{ $tournament->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">🔍</button>
        </form>

        {{-- زر حذف جميع المباريات --}}
        <form action="{{ route('admin.matches.destroy-all') }}" method="POST"
              onsubmit="return confirm('⚠️ تحذير!\n\nهل أنت متأكد من حذف جميع المباريات نهائياً؟\nلا يمكن التراجع عن هذا الإجراء.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" style="background:linear-gradient(135deg,#ff3a5c,#c0203e); box-shadow:0 4px 15px rgba(255,58,92,0.35);">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                مسح جميع المباريات
            </button>
        </form>

        <a href="{{ route('admin.matches.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/></svg>
            إضافة مباراة
        </a>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="section-header">
            <h2>
                <span class="icon">⚽</span>
                قائمة المباريات
                <span class="badge badge-info" style="font-size:12px; margin-right:8px;">{{ $matches->total() }} مباراة</span>
            </h2>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell"><input type="checkbox" id="selectAll"></th>
                        <th>الفريق الأول</th>
                        <th style="text-align:center;">النتيجة</th>
                        <th>الفريق الثاني</th>
                        <th>البطولة</th>
                        <th>الموعد</th>
                        <th>حالة البث</th>
                        <th>كأس العالم</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matches as $match)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="select-item" value="{{ $match->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-center gap-2">
                                    @if($match->team_one_flag)
                                        <img src="{{ $match->team_one_flag }}" alt="" style="width:30px; height:21px; object-fit:cover; border-radius:3px; box-shadow:0 1px 4px rgba(0,0,0,0.3);">
                                    @endif
                                    <span style="font-weight:700;">{{ $match->team_one_name }}</span>
                                </div>
                                @if($match->team_one_name_ar)
                                    <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ $match->team_one_name_ar }}</div>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span style="font-family:monospace; font-weight:900; font-size:18px; color:var(--accent-primary); background:rgba(0,212,170,0.08); padding:4px 12px; border-radius:8px;">
                                    {{ $match->team_one_score ?? '0' }} - {{ $match->team_two_score ?? '0' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-center gap-2">
                                    @if($match->team_two_flag)
                                        <img src="{{ $match->team_two_flag }}" alt="" style="width:30px; height:21px; object-fit:cover; border-radius:3px; box-shadow:0 1px 4px rgba(0,0,0,0.3);">
                                    @endif
                                    <span style="font-weight:700;">{{ $match->team_two_name }}</span>
                                </div>
                                @if($match->team_two_name_ar)
                                    <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ $match->team_two_name_ar }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $match->tournament->name ?? '—' }}</span>
                            </td>
                            <td style="font-size:13px; color:var(--text-muted);">
                                @php
                                    // match_time may be stored as "HH:MM" or as a full datetime string
                                    $rawTime = $match->match_time ?? '';
                                    $rawDate = $match->match_date ?? '';

                                    // If match_date exists, show it formatted
                                    if ($rawDate) {
                                        try {
                                            echo \Carbon\Carbon::parse($rawDate)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            echo $rawDate;
                                        }
                                        echo '<br>';
                                    }

                                    // Display match_time as-is (it's stored as time string like "20:30")
                                    if ($rawTime) {
                                        // If it looks like a full datetime, parse it
                                        if (strlen($rawTime) > 10) {
                                            try {
                                                echo '<span style="font-weight:700;">' . \Carbon\Carbon::parse($rawTime)->format('H:i') . '</span>';
                                            } catch (\Exception $e) {
                                                echo '<span style="font-weight:700;">' . $rawTime . '</span>';
                                            }
                                        } else {
                                            // It's already a time string like "20:30" or "32:15"
                                            echo '<span style="font-weight:700;">' . htmlspecialchars($rawTime) . '</span>';
                                        }
                                    }
                                @endphp
                            </td>
                            <td>
                                @if($match->is_live)
                                    <span class="badge badge-live">🔴 مباشر</span>
                                @else
                                    <span class="badge badge-warning">⏰ قادمة</span>
                                @endif
                            </td>
                            <td>
                                @if($match->is_world_cup)
                                    <span class="badge badge-warning">🏆 كأس العالم</span>
                                @else
                                    <span style="color:var(--border-glass);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $match->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $match->is_active ? '• نشط' : '○ معطل' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-end">
                                    <a href="{{ route('admin.matches.edit', $match->id) }}" class="btn btn-secondary btn-xs">✏️</a>
                                    <form action="{{ route('admin.matches.destroy', $match->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المباراة؟')">
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
                                <div style="font-size:50px; margin-bottom:16px;">⚽</div>
                                <div style="font-size:16px; font-weight:700; margin-bottom:8px;">لا توجد مباريات</div>
                                <div style="font-size:13px;">اضغط "إضافة مباراة" لإنشاء مباراة جديدة</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $matches->appends(['search' => $search, 'tournament_id' => $tournamentId])->links() }}
        </div>
    </div>

@endsection
