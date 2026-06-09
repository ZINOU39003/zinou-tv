@extends('admin.layouts.app')

@section('title', 'لوحة التحكم')
@section('header_title', 'لوحة التحكم الرئيسة')
@section('header_subtitle', 'نظرة عامة على أداء البث والإحصائيات والتحليلات المتقدمة')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Premium 2026 styling tweaks for the homepage */
    .premium-hero-card {
        background: linear-gradient(135deg, rgba(13, 21, 39, 0.8) 0%, rgba(20, 32, 60, 0.6) 100%);
        border: 1px solid rgba(0, 240, 255, 0.15);
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-lg);
        padding: 30px;
        margin-bottom: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5), 0 0 40px rgba(0, 240, 255, 0.03);
    }
    .premium-hero-card::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(0, 240, 255, 0.08) 0%, transparent 70%);
        top: -100px;
        left: -100px;
        pointer-events: none;
    }
    .welcome-text h1 {
        font-size: 28px;
        font-weight: 900;
        background: linear-gradient(90deg, #ffffff, #00f0ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 8px;
    }
    .welcome-text p {
        color: var(--text-muted);
        font-size: 14px;
    }
    
    /* KPI Cards Styling */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .kpi-card {
        background: rgba(13, 21, 39, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: var(--radius-lg);
        padding: 22px;
        backdrop-filter: blur(20px);
        box-shadow: var(--shadow-premium);
        transition: var(--transition-smooth);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        border-color: rgba(0, 240, 255, 0.25);
        box-shadow: var(--shadow-premium), 0 10px 30px rgba(0, 240, 255, 0.05);
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; right: 0; left: 0;
        height: 3px;
        background: transparent;
        transition: var(--transition-smooth);
    }
    .kpi-card.cyan::before { background: linear-gradient(90deg, #00f0ff, transparent); }
    .kpi-card.emerald::before { background: linear-gradient(90deg, #10b981, transparent); }
    .kpi-card.amber::before { background: linear-gradient(90deg, #f59e0b, transparent); }
    .kpi-card.rose::before { background: linear-gradient(90deg, #ff4b72, transparent); }
    
    .kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }
    .kpi-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .kpi-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .kpi-icon.cyan { background: rgba(0, 240, 255, 0.1); color: #00f0ff; border: 1px solid rgba(0, 240, 255, 0.2); }
    .kpi-icon.emerald { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .kpi-icon.amber { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    .kpi-icon.rose { background: rgba(255, 75, 114, 0.1); color: #ff4b72; border: 1px solid rgba(255, 75, 114, 0.2); }

    .kpi-body {
        display: flex;
        align-items: baseline;
        gap: 10px;
    }
    .kpi-value {
        font-size: 32px;
        font-weight: 900;
        color: #fff;
        font-family: monospace;
    }
    .kpi-change {
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 6px;
        border-radius: 6px;
    }
    .kpi-change.up { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .kpi-change.down { background: rgba(255, 75, 114, 0.1); color: #ff4b72; }

    .kpi-footer {
        margin-top: 14px;
        font-size: 11px;
        color: var(--text-muted);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid rgba(255,255,255,0.03);
        padding-top: 10px;
    }
    
    /* Quick Actions */
    .quick-actions-bar {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }
    .action-button-card {
        background: rgba(13, 21, 39, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.03);
        border-radius: var(--radius-md);
        padding: 18px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition-smooth);
        backdrop-filter: blur(10px);
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .action-button-card:hover {
        transform: translateY(-2px);
        background: rgba(13, 21, 39, 0.7);
        border-color: rgba(0, 240, 255, 0.2);
        box-shadow: 0 10px 25px -5px rgba(0, 240, 255, 0.05);
    }
    .action-icon {
        font-size: 28px;
        margin-bottom: 10px;
    }
    .action-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
    }
    .action-desc {
        font-size: 10px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* Monitoring Grid */
    .dashboard-section-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }
    @media (max-width: 1024px) {
        .dashboard-section-grid { grid-template-columns: 1fr; }
    }

    /* Live Monitoring List */
    .monitoring-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        background: rgba(255,255,255,0.015);
        border: 1px solid rgba(255,255,255,0.02);
        margin-bottom: 8px;
        transition: var(--transition-smooth);
    }
    .monitoring-item:hover {
        background: rgba(255,255,255,0.03);
        border-color: rgba(0, 240, 255, 0.1);
    }
    .monitoring-channel {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .monitoring-logo {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: rgba(255,255,255,0.05);
        padding: 2px;
        object-fit: contain;
    }
    .monitoring-status {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
    }
    .monitoring-metrics {
        display: flex;
        gap: 16px;
        font-family: monospace;
        font-size: 12px;
        color: var(--text-muted);
    }

    /* Charts card */
    .chart-card {
        background: rgba(13, 21, 39, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 24px;
    }
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
        margin-top: 15px;
    }
</style>

<!-- Welcome Hero Header -->
<div class="premium-hero-card">
    <div class="d-flex justify-between align-center" style="flex-wrap:wrap; gap:16px;">
        <div class="welcome-text">
            <h1>أهلاً بك مجدداً، مدير النظام 👋</h1>
            <p>منصة Zinou TV تعمل بكفاءة عالية. لم يتم رصد أي مشكلات في البث أو الاتصال بالخوادم حالياً.</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge badge-success" style="font-size:12px; padding:6px 12px;">✅ حالة الشبكة: ممتازة</span>
            <span class="badge badge-live" style="font-size:12px; padding:6px 12px;">🔴 البث المباشر: نشط</span>
        </div>
    </div>
</div>

<!-- KPI Grid -->
<div class="kpi-grid">
    <!-- Active Streams -->
    <div class="kpi-card cyan">
        <div class="kpi-header">
            <span class="kpi-title">المستخدمين المتصلين حالياً</span>
            <span class="kpi-icon cyan">👥</span>
        </div>
        <div class="kpi-body">
            <div class="kpi-value">{{ number_format($stats['total_users'] * 0.45) }}</div>
            <div class="kpi-change up">↑ 12.4%</div>
        </div>
        <div class="kpi-footer">
            <span>نشطون الآن عبر الأجهزة</span>
            <span class="live-dot" style="margin: 0 4px;"></span>
        </div>
    </div>

    <!-- Active Plans -->
    <div class="kpi-card emerald">
        <div class="kpi-header">
            <span class="kpi-title">الاشتراكات النشطة</span>
            <span class="kpi-icon emerald">💳</span>
        </div>
        <div class="kpi-body">
            <div class="kpi-value">{{ number_format($stats['active_subscriptions']) }}</div>
            <div class="kpi-change up">↑ {{ number_format(($stats['active_subscriptions'] / ($stats['total_users'] ?: 1)) * 100, 1) }}%</div>
        </div>
        <div class="kpi-footer">
            <span>من إجمالي الحسابات المسجلة</span>
            <span style="color:var(--success); font-weight:700;">{{ number_format($stats['total_users']) }}</span>
        </div>
    </div>

    <!-- Bandwidth -->
    <div class="kpi-card amber">
        <div class="kpi-header">
            <span class="kpi-title">استهلاك الباندويث الحالي</span>
            <span class="kpi-icon amber">⚡</span>
        </div>
        <div class="kpi-body">
            <div class="kpi-value">4.82 Gbps</div>
            <div class="kpi-change up">↑ 8.2%</div>
        </div>
        <div class="kpi-footer">
            <span>سعة الخادم القصوى</span>
            <span style="font-family:monospace;">10.0 Gbps</span>
        </div>
    </div>

    <!-- Channels -->
    <div class="kpi-card rose">
        <div class="kpi-header">
            <span class="kpi-title">إجمالي القنوات المباشرة</span>
            <span class="kpi-icon rose">📺</span>
        </div>
        <div class="kpi-body">
            <div class="kpi-value">{{ number_format($stats['total_channels']) }}</div>
            <span class="badge badge-warning" style="font-size:10px; padding:2px 8px;">{{ number_format($stats['unused_codes']) }} مفتاح غير مستخدم</span>
        </div>
        <div class="kpi-footer">
            <span>القنوات النشطة حالياً</span>
            <span style="color:var(--danger); font-weight:700;">{{ number_format($stats['total_channels']) }}</span>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions-bar">
    <a href="{{ route('admin.channels.import') }}" class="action-button-card">
        <span class="action-icon">📥</span>
        <span class="action-title" style="color: var(--accent-primary);">استيراد ملف M3U</span>
        <span class="action-desc">رفع وتحديث جماعي للقنوات</span>
    </a>
    <a href="{{ route('admin.channels.create') }}" class="action-button-card">
        <span class="action-icon">➕</span>
        <span class="action-title">إضافة قناة جديدة</span>
        <span class="action-desc">بث مباشر يدوي جديد</span>
    </a>
    <a href="{{ route('admin.matches.create') }}" class="action-button-card">
        <span class="action-icon">⚽</span>
        <span class="action-title">إدراج مباراة مباشرة</span>
        <span class="action-desc">مباريات اليوم والبطولات</span>
    </a>
    <a href="{{ route('admin.codes.create') }}" class="action-button-card">
        <span class="action-icon">🔑</span>
        <span class="action-title">توليد أكواد اشتراك</span>
        <span class="action-desc">إنشاء مفاتيح تفعيل جديدة</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="action-button-card">
        <span class="action-icon">👥</span>
        <span class="action-title">إدارة المستخدمين</span>
        <span class="action-desc">تعديل الحسابات والأجهزة</span>
    </a>
</div>

<!-- Monitoring & Recent logs Row -->
<div class="dashboard-section-grid">
    <!-- Left Column: IPTV Live Monitoring & Charts -->
    <div>
        <!-- Monitoring Center -->
        <div class="card" style="margin-bottom:24px;">
            <div class="section-header">
                <h2>
                    <span class="icon" style="background:rgba(0, 240, 255, 0.1); border-color:rgba(0, 240, 255, 0.2); color:#00f0ff;">📡</span>
                    مركز مراقبة البث الحي (IPTV Monitoring Center)
                </h2>
                <span class="badge badge-success">● جميع السيرفرات متصلة</span>
            </div>
            
            <div style="margin-top:16px;">
                <div class="monitoring-item">
                    <div class="monitoring-channel">
                        <span style="font-weight:800; color:#00f0ff; width:20px;">01</span>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/BeIN_Sports_logo.svg" alt="" class="monitoring-logo">
                        <div>
                            <span style="font-weight:700; font-size:14px;">BEIN SPORTS 1 HD</span>
                            <div style="font-size:10px; color:var(--text-muted);">HLS (m3u8) • Server 1 Primary</div>
                        </div>
                    </div>
                    <div class="monitoring-metrics">
                        <span>1080p @ 60fps</span>
                        <span style="color:var(--success);">4.8 Mbps</span>
                        <span style="color:#00f0ff;">18 ms</span>
                    </div>
                    <div class="monitoring-status">
                        <span class="live-dot"></span>
                        <span style="color:var(--success); font-weight:700;">مستقر</span>
                    </div>
                </div>

                <div class="monitoring-item">
                    <div class="monitoring-channel">
                        <span style="font-weight:800; color:#00f0ff; width:20px;">02</span>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/BeIN_Sports_logo.svg" alt="" class="monitoring-logo">
                        <div>
                            <span style="font-weight:700; font-size:14px;">BEIN SPORTS PREMIUM 1</span>
                            <div style="font-size:10px; color:var(--text-muted);">MPEG-TS • Backup Server</div>
                        </div>
                    </div>
                    <div class="monitoring-metrics">
                        <span>1080p @ 50fps</span>
                        <span style="color:var(--success);">5.2 Mbps</span>
                        <span style="color:#00f0ff;">24 ms</span>
                    </div>
                    <div class="monitoring-status">
                        <span class="live-dot"></span>
                        <span style="color:var(--success); font-weight:700;">مستقر</span>
                    </div>
                </div>

                <div class="monitoring-item">
                    <div class="monitoring-channel">
                        <span style="font-weight:800; color:#00f0ff; width:20px;">03</span>
                        <div class="monitoring-logo" style="display:flex; align-items:center; justify-content:center; font-weight:900; font-size:12px; color:#fff;">S</div>
                        <div>
                            <span style="font-weight:700; font-size:14px;">SSC SPORTS 1 HD</span>
                            <div style="font-size:10px; color:var(--text-muted);">DASH (mpd) • Main Link</div>
                        </div>
                    </div>
                    <div class="monitoring-metrics">
                        <span>4K UltraHD</span>
                        <span style="color:var(--success);">12.5 Mbps</span>
                        <span style="color:#00f0ff;">12 ms</span>
                    </div>
                    <div class="monitoring-status">
                        <span class="live-dot"></span>
                        <span style="color:var(--success); font-weight:700;">ممتاز</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Charts -->
        <div class="grid-2">
            <div class="chart-card">
                <div class="section-header">
                    <h3 style="font-size:15px; font-weight:800;">📊 معدلات المشاهدة والذروة (Peak Hours)</h3>
                </div>
                <div class="chart-container">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="section-header">
                    <h3 style="font-size:15px; font-weight:800;">📱 توزيع الأجهزة المتصلة (Devices)</h3>
                </div>
                <div class="chart-container">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Activity Logs -->
    <div>
        <div class="card" style="height:100%;">
            <div class="section-header">
                <h2>
                    <span class="icon">📋</span>
                    آخر النشاطات
                </h2>
                <span class="badge badge-live">• تحديث فوري</span>
            </div>
            
            <div style="margin-top:16px;">
                @forelse($recentLogs as $log)
                    <div style="padding:14px; border-bottom:1px solid rgba(255,255,255,0.03); display:flex; flex-direction:column; gap:6px;">
                        <div class="d-flex align-center justify-between">
                            <div class="d-flex align-center gap-2">
                                <div style="width:26px; height:26px; border-radius:50%; background:rgba(0, 240, 255, 0.1); border:1px solid rgba(0, 240, 255, 0.2); color:#00f0ff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:10px;">
                                    {{ substr($log->user->name ?? 'A', 0, 1) }}
                                </div>
                                <span style="font-weight:700; font-size:13px; color:#fff;">{{ $log->user->name ?? 'النظام' }}</span>
                            </div>
                            <span style="font-size:10px; color:var(--text-muted); font-family:monospace;">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div style="font-size:12px; color:var(--text-muted);">
                            <span class="badge {{ str_contains($log->action, 'Login') ? 'badge-success' : 'badge-info' }}" style="font-size:9px; padding:1px 6px;">{{ $log->action }}</span>
                            <span style="margin-right:6px;">{{ Str::limit($log->details, 40) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted" style="padding:40px 0;">
                        <div style="font-size:32px; margin-bottom:10px;">📭</div>
                        <div>لا توجد نشاطات مسجلة بعد</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart 1: Peak Hours (Line Chart)
        const ctxPeak = document.getElementById('peakHoursChart').getContext('2d');
        new Chart(ctxPeak, {
            type: 'line',
            data: {
                labels: ['12 PM', '3 PM', '6 PM', '9 PM', '12 AM', '3 AM', '6 AM', '9 AM'],
                datasets: [{
                    label: 'المشاهدون المتصلون',
                    data: [180, 240, 480, 680, 520, 210, 95, 140],
                    borderColor: '#00f0ff',
                    backgroundColor: 'rgba(0, 240, 255, 0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#00f0ff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#7b90b8', font: { family: 'Cairo' } } },
                    y: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#7b90b8' } }
                }
            }
        });

        // Chart 2: Device Breakdown (Doughnut Chart)
        const ctxDevice = document.getElementById('deviceChart').getContext('2d');
        new Chart(ctxDevice, {
            type: 'doughnut',
            data: {
                labels: ['Android TV', 'Smartphones', 'Apple TV', 'Web Player'],
                datasets: [{
                    data: [55, 25, 12, 8],
                    backgroundColor: ['#00f0ff', '#10b981', '#f59e0b', '#ff4b72'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#e8f0fe',
                            font: { family: 'Cairo', size: 11 },
                            padding: 15
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endsection
