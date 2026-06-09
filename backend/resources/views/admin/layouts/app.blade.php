<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - Zinou TV</title>
    <!-- Arabic + Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-primary: #070b14;
            --bg-secondary: #0d1322;
            --bg-glass: rgba(13, 19, 35, 0.45);
            --bg-glass-hover: rgba(13, 19, 35, 0.7);
            --bg-card: rgba(13, 19, 35, 0.65);
            
            --accent-primary: #00f0ff;
            --accent-primary-hover: #00c8db;
            --accent-primary-glow: rgba(0, 240, 255, 0.12);
            
            --accent-gold: #f59e0b;
            --accent-gold-hover: #d97706;
            --accent-gold-glow: rgba(245, 158, 11, 0.12);

            --accent-secondary: #10b981;
            --accent-secondary-hover: #059669;
            --accent-secondary-glow: rgba(16, 185, 129, 0.12);
            
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-dark: #0f172a;
            
            --border-glass: rgba(255, 255, 255, 0.05);
            --border-glass-active: rgba(0, 240, 255, 0.35);
            
            --danger: #ff4b72;
            --danger-hover: #e12d56;
            --danger-glow: rgba(255, 75, 114, 0.15);
            --warning: #f59e0b;
            --success: #10b981;
            
            --shadow-premium: 0 20px 50px -12px rgba(0, 0, 0, 0.8), 0 0 1px 1px rgba(255, 255, 255, 0.03) inset;
            --shadow-glow: 0 0 40px rgba(0, 240, 255, 0.08);
            --radius-lg: 20px;
            --radius-md: 14px;
            --radius-sm: 10px;
            
            --transition-smooth: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-primary) var(--bg-primary);
        }

        *::-webkit-scrollbar { width: 5px; }
        *::-webkit-scrollbar-track { background: var(--bg-primary); }
        *::-webkit-scrollbar-thumb { background-color: var(--accent-primary); border-radius: 10px; }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            background-image: 
                radial-gradient(ellipse at 10% 20%, rgba(0, 212, 170, 0.04) 0%, transparent 60%),
                radial-gradient(ellipse at 90% 80%, rgba(79, 126, 249, 0.04) 0%, transparent 60%);
        }

        /* ============ SIDEBAR ============ */
        aside {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #0c1523 0%, #091019 100%);
            border-left: 1px solid var(--border-glass);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            right: 0;
            top: 0;
            z-index: 100;
            overflow: hidden;
        }

        aside::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(180deg, rgba(0,212,170,0.03) 0%, transparent 40%);
            pointer-events: none;
        }

        /* Logo Area */
        .logo-area {
            padding: 22px 20px;
            border-bottom: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
        }

        .logo-icon {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 18px;
            color: #06080f;
            box-shadow: 0 0 20px rgba(0, 212, 170, 0.35), 0 4px 15px rgba(0,0,0,0.3);
            flex-shrink: 0;
            letter-spacing: -1px;
        }

        .logo-text-block { display: flex; flex-direction: column; }

        .logo-text {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 0.5px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.1;
        }

        .logo-sub {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Navigation */
        .nav-section-label {
            font-size: 9px;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 16px 20px 6px;
            opacity: 0.7;
        }

        .nav-links {
            list-style: none;
            padding: 12px 12px 0;
            display: flex;
            flex-direction: column;
            gap: 3px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 14px;
            transition: var(--transition-smooth);
            position: relative;
        }

        .nav-links a .nav-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.04);
            transition: var(--transition-smooth);
            flex-shrink: 0;
        }

        .nav-links a .nav-icon svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        .nav-links a:hover {
            background-color: rgba(0, 212, 170, 0.07);
            color: var(--accent-primary);
        }

        .nav-links a:hover .nav-icon {
            background: rgba(0, 212, 170, 0.15);
            color: var(--accent-primary);
        }

        .nav-links li.active a {
            background: linear-gradient(90deg, rgba(0,212,170,0.12), rgba(0,212,170,0.04));
            color: var(--accent-primary);
            border-right: 3px solid var(--accent-primary);
        }

        .nav-links li.active a .nav-icon {
            background: rgba(0, 212, 170, 0.2);
            color: var(--accent-primary);
        }

        /* Nav badge */
        .nav-badge {
            margin-right: auto;
            background: var(--accent-gold);
            color: #000;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 20px;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border-glass);
            background: rgba(0,0,0,0.2);
        }

        .admin-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            background: rgba(0,212,170,0.06);
            border: 1px solid rgba(0,212,170,0.12);
        }

        .admin-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: #06080f;
            flex-shrink: 0;
        }

        .admin-info { flex: 1; min-width: 0; }

        .admin-name {
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text-main);
        }

        .admin-role {
            font-size: 10px;
            color: var(--accent-primary);
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .btn-logout {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
            border-radius: var(--radius-sm);
            transition: var(--transition-smooth);
            flex-shrink: 0;
        }

        .btn-logout:hover { color: var(--danger); }

        /* ============ MAIN CONTENT ============ */
        main {
            margin-right: var(--sidebar-width);
            flex-grow: 1;
            min-height: 100vh;
            padding: 32px 36px;
            max-width: calc(100% - var(--sidebar-width));
        }

        /* Top Header Bar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-glass);
        }

        .page-title h1 {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .page-title p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-time {
            font-size: 12px;
            color: var(--text-muted);
            background: var(--bg-glass);
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent-primary);
            box-shadow: 0 0 8px var(--accent-primary);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.8); }
        }

        /* ============ CARDS ============ */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 24px;
            backdrop-filter: blur(16px);
            box-shadow: var(--shadow-premium);
            transition: var(--transition-smooth);
            margin-bottom: 24px;
        }

        .card:hover {
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-premium), 0 0 0 1px rgba(0,212,170,0.05);
        }

        /* Stats Grid */
        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            opacity: 0;
            transition: var(--transition-smooth);
        }

        .stat-card:hover::before { opacity: 1; }
        .stat-card.blue::before { background: linear-gradient(90deg, var(--accent-secondary), transparent); }
        .stat-card.green::before { background: linear-gradient(90deg, var(--accent-primary), transparent); }
        .stat-card.red::before { background: linear-gradient(90deg, var(--danger), transparent); }
        .stat-card.gold::before { background: linear-gradient(90deg, var(--accent-gold), transparent); }

        .stat-info h3 {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 900;
            margin-top: 8px;
            color: #fff;
            line-height: 1;
        }

        .stat-trend {
            font-size: 11px;
            color: var(--accent-primary);
            margin-top: 6px;
            font-weight: 600;
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-blue { background: var(--accent-secondary-glow); color: var(--accent-secondary); border: 1px solid rgba(79,126,249,0.2); }
        .icon-green { background: var(--accent-primary-glow); color: var(--accent-primary); border: 1px solid rgba(0,212,170,0.2); }
        .icon-red { background: var(--danger-glow); color: var(--danger); border: 1px solid rgba(255,90,126,0.2); }
        .icon-gold { background: var(--accent-gold-glow); color: var(--accent-gold); border: 1px solid rgba(240,180,41,0.2); }

        /* ============ TABLES ============ */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        th {
            padding: 14px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-glass);
            white-space: nowrap;
        }

        td {
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.025);
            vertical-align: middle;
        }

        tr:hover td { background-color: rgba(0, 212, 170, 0.02); }

        /* ============ FORMS ============ */
        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            background-color: rgba(6, 8, 15, 0.7);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            color: var(--text-main);
            font-size: 14px;
            font-family: 'Cairo', sans-serif;
            transition: var(--transition-smooth);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(0, 212, 170, 0.1);
            background-color: rgba(6, 8, 15, 0.9);
        }

        .form-control::placeholder { color: var(--text-muted); opacity: 0.6; }

        textarea.form-control { min-height: 120px; resize: vertical; }

        select.form-control option { background-color: var(--bg-secondary); }

        /* ============ BUTTONS ============ */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 22px;
            font-size: 14px;
            font-weight: 700;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: var(--transition-smooth);
            text-decoration: none;
            border: none;
            font-family: 'Cairo', sans-serif;
            white-space: nowrap;
        }

        .btn svg { width: 17px; height: 17px; fill: currentColor; flex-shrink: 0; }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), #00b894);
            color: #06080f;
            box-shadow: 0 4px 15px rgba(0, 212, 170, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 212, 170, 0.45);
        }

        .btn-secondary {
            background-color: rgba(255,255,255,0.07);
            color: var(--text-main);
            border: 1px solid var(--border-glass);
        }

        .btn-secondary:hover { background-color: rgba(255,255,255,0.12); }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #c0394f);
            color: #fff;
            box-shadow: 0 4px 15px rgba(255, 90, 126, 0.25);
        }

        .btn-danger:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(255, 90, 126, 0.4); }

        .btn-gold {
            background: linear-gradient(135deg, var(--accent-gold), #d4841a);
            color: #06080f;
            box-shadow: 0 4px 15px rgba(240, 180, 41, 0.25);
        }

        .btn-gold:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(240, 180, 41, 0.4); }

        .btn-sm { padding: 8px 14px; font-size: 12px; }
        .btn-xs { padding: 5px 10px; font-size: 11px; }

        /* ============ BADGES ============ */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Cairo', sans-serif;
        }

        .badge-success { background: rgba(0,212,170,0.12); color: var(--success); border: 1px solid rgba(0,212,170,0.25); }
        .badge-danger { background: rgba(255,90,126,0.12); color: var(--danger); border: 1px solid rgba(255,90,126,0.25); }
        .badge-warning { background: rgba(240,180,41,0.12); color: var(--warning); border: 1px solid rgba(240,180,41,0.25); }
        .badge-info { background: rgba(79,126,249,0.12); color: var(--accent-secondary); border: 1px solid rgba(79,126,249,0.25); }
        .badge-live { background: rgba(255,90,126,0.15); color: var(--danger); border: 1px solid rgba(255,90,126,0.3); animation: badge-pulse 2s infinite; }

        @keyframes badge-pulse { 0%,100% { opacity:1; } 50% { opacity:0.7; } }

        /* ============ ALERTS ============ */
        .alert {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            border: 1px solid transparent;
            font-size: 14px;
        }

        .alert-success { background: rgba(0,212,170,0.1); color: var(--success); border-color: rgba(0,212,170,0.2); }
        .alert-error { background: rgba(255,90,126,0.1); color: var(--danger); border-color: rgba(255,90,126,0.2); }
        .alert-warning { background: rgba(240,180,41,0.1); color: var(--warning); border-color: rgba(240,180,41,0.2); }

        .alert-close { background:none; border:none; color:inherit; cursor:pointer; font-size:18px; padding: 0 4px; opacity:0.7; }
        .alert-close:hover { opacity: 1; }

        /* ============ PAGINATION ============ */
        .pagination {
            display: flex;
            list-style: none;
            gap: 6px;
            margin-top: 24px;
            justify-content: center;
        }

        .pagination li a, .pagination li span {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition-smooth);
            font-size: 13px;
            font-weight: 700;
        }

        .pagination li a:hover { color: var(--accent-primary); border-color: var(--accent-primary); }
        .pagination li.active span { background: var(--accent-primary); color: #06080f; border-color: var(--accent-primary); }

        /* ============ SEARCH BAR ============ */
        .search-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input-wrapper {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .search-input-wrapper svg {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            fill: var(--text-muted);
        }

        .search-input {
            width: 100%;
            background: rgba(6,8,15,0.6);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-sm);
            padding: 11px 40px 11px 16px;
            color: var(--text-main);
            font-size: 14px;
            font-family: 'Cairo', sans-serif;
            transition: var(--transition-smooth);
        }

        .search-input:focus { outline:none; border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(0,212,170,0.1); }
        .search-input::placeholder { color: var(--text-muted); opacity: 0.6; }

        /* ============ SECTION HEADER ============ */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h2 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header h2 span.icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--accent-primary-glow);
            border: 1px solid rgba(0,212,170,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* ============ CHANNEL LOGO ============ */
        .channel-logo {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            object-fit: cover;
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
        }

        .channel-logo-placeholder {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-primary-glow), var(--accent-secondary-glow));
            border: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: var(--text-main);
        }

        /* ============ UTILITIES ============ */
        .d-flex { display: flex; }
        .flex-col { flex-direction: column; }
        .justify-between { justify-content: space-between; }
        .justify-end { justify-content: flex-end; }
        .align-center { align-items: center; }
        .gap-1 { gap: 6px; }
        .gap-2 { gap: 10px; }
        .gap-3 { gap: 16px; }
        .gap-4 { gap: 20px; }
        .w-full { width: 100%; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-muted { color: var(--text-muted); }
        .text-success { color: var(--success); }
        .text-danger { color: var(--danger); }
        .text-gold { color: var(--accent-gold); }
        .mt-1 { margin-top: 6px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 16px; }
        .mt-4 { margin-top: 22px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 22px; }
        .fw-700 { font-weight: 700; }
        .fw-800 { font-weight: 800; }
        .fs-12 { font-size: 12px; }
        .fs-13 { font-size: 13px; }
        .fs-14 { font-size: 14px; }
        .flex-1 { flex: 1; }
        .overflow-hidden { overflow: hidden; }
        .text-ellipsis { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Grid Layouts */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }

        /* Divider */
        .divider { border: none; border-top: 1px solid var(--border-glass); margin: 20px 0; }

        /* Image Preview */
        .img-preview {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-sm);
            object-fit: cover;
            border: 1px solid var(--border-glass);
        }

        /* Flag display */
        .flag-emoji { font-size: 20px; }

        /* Skeleton Loading Placeholder */
        .skeleton {
            background: linear-gradient(90deg, var(--bg-glass) 25%, rgba(30,45,70,0.4) 50%, var(--bg-glass) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: var(--radius-sm);
        }

        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* Custom Checkbox Toggle */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input { opacity: 0; width: 0; height: 0; }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(255,255,255,0.1);
            border-radius: 24px;
            border: 1px solid var(--border-glass);
            transition: 0.3s;
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            left: 4px;
            top: 3px;
            background: var(--text-muted);
            border-radius: 50%;
            transition: 0.3s;
        }

        input:checked + .toggle-slider { background: rgba(0,212,170,0.3); border-color: rgba(0,212,170,0.5); }
        input:checked + .toggle-slider::before { transform: translateX(20px); background: var(--accent-primary); }

        /* Custom Table Checkboxes */
        .checkbox-cell {
            width: 40px;
            text-align: center;
            vertical-align: middle;
        }
        .select-item, #selectAll {
            width: 18px;
            height: 18px;
            accent-color: var(--accent-primary);
            cursor: pointer;
            border-radius: 4px;
            border: 1px solid var(--border-glass);
            background: rgba(0,0,0,0.3);
            transition: var(--transition-smooth);
        }
        .select-item:hover, #selectAll:hover {
            box-shadow: 0 0 8px var(--accent-primary-glow);
        }

        @media (max-width: 1024px) {
            aside { right: -280px; }
            main { margin-right: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside>
        <div class="logo-area">
            <div class="logo-icon">Z</div>
            <div class="logo-text-block">
                <div class="logo-text">Zinou TV</div>
                <div class="logo-sub">لوحة الإدارة</div>
            </div>
        </div>

        <ul class="nav-links">
            <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    </span>
                    لوحة التحكم
                </a>
            </li>
            <li class="{{ Request::is('admin/users*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </span>
                    المستخدمون والأجهزة
                </a>
            </li>
            <li class="{{ Request::is('admin/categories*') ? 'active' : '' }}">
                <a href="{{ route('admin.categories.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/></svg>
                    </span>
                    الشبكات
                </a>
            </li>
            <li class="{{ Request::is('admin/packages*') ? 'active' : '' }}">
                <a href="{{ route('admin.packages.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 9h-2V5h2v6zm0 4h-2v-2h2v2z"/></svg>
                    </span>
                    الباقات
                </a>
            </li>
            <li class="{{ Request::is('admin/settings/packages*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.packages') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 8.25c-.97 0-1.75-.78-1.75-1.75s.78-1.75 1.75-1.75 1.75.78 1.75 1.75-.78 1.75-1.75 1.75z"/></svg>
                    </span>
                    أسعار الاشتراك والواتساب
                </a>
            </li>
            <li class="{{ Request::is('admin/channels*') ? 'active' : '' }}">
                <a href="{{ route('admin.channels.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M21 3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5v2h8v-2h5c1.1 0 1.9-.9 1.9-2l.01-12c0-1.1-.9-2-1.9-2zm0 14H3V5h18v12z"/></svg>
                    </span>
                    القنوات المباشرة
                    <span class="nav-badge">LIVE</span>
                </a>
            </li>
            <li class="{{ Request::is('admin/tournaments*') ? 'active' : '' }}">
                <a href="{{ route('admin.tournaments.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94.63 1.5 1.98 2.63 3.61 2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z"/></svg>
                    </span>
                    البطولات
                </a>
            </li>
            <li class="{{ Request::is('admin/matches*') ? 'active' : '' }}">
                <a href="{{ route('admin.matches.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                    </span>
                    المباريات المباشرة
                </a>
            </li>
            <li class="{{ Request::is('admin/movies*') ? 'active' : '' }}">
                <a href="{{ route('admin.movies.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
                    </span>
                    الأفلام والمسلسلات
                </a>
            </li>
            <li class="{{ Request::is('admin/codes*') ? 'active' : '' }}">
                <a href="{{ route('admin.codes.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                    </span>
                    رموز التفعيل
                </a>
            </li>
            <li class="{{ Request::is('admin/pro-activation*') ? 'active' : '' }}">
                <a href="{{ route('admin.pro-activation.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
                    </span>
                    تفعيل حسابات PRO
                </a>
            </li>
            <li class="{{ Request::is('admin/subscriptions*') ? 'active' : '' }}">
                <a href="{{ route('admin.subscriptions.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/></svg>
                    </span>
                    الاشتراكات
                </a>
            </li>
            <li class="{{ Request::is('admin/har-analyzer*') ? 'active' : '' }}">
                <a href="{{ route('admin.har.index') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2zm2-4H5V5h14v4z"/></svg>
                    </span>
                    محلل ملفات HAR
                </a>
            </li>
            <li class="{{ Request::is('admin/settings/ads*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.ads') }}">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 8c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2h1 1.25-3.33l-1.42 1.42C18.47 10.74 19 11.81 19 13s-.53 2.26-1.17 2.92l1.42 1.42C20.24 16.29 21 14.74 21 13s-.76-3.29-1.75-4.33zM4 12c0-2.21 1.79-4 4-4v8c-2.21 0-4-1.79-4-4z"/></svg>
                    </span>
                    إدارة الإعلانات
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="admin-card">
                <div class="admin-avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                <div class="admin-info">
                    <div class="admin-name">{{ Auth::user()->name }}</div>
                    <div class="admin-role">مدير النظام</div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout" title="تسجيل الخروج">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main>
        <div class="topbar">
            <div class="page-title">
                <h1>@yield('header_title')</h1>
                <p>@yield('header_subtitle', 'Zinou TV — لوحة إدارة المحتوى')</p>
            </div>
            <div class="topbar-actions">
                <button type="button" id="bulkDeleteBtn" class="btn btn-danger" style="display:none; background:linear-gradient(135deg,#ff3a5c,#c0203e); box-shadow:0 4px 15px rgba(255,58,92,0.35); padding:8px 16px; font-size:13px; margin-left:8px;">
                    🗑️ حذف المحدد (<span id="bulkDeleteCount">0</span>)
                </button>
                <div class="topbar-time">
                    <span class="live-dot"></span>
                    <span id="live-clock">{{ now()->setTimezone('Asia/Riyadh')->format('H:i') }}</span>
                    <span>• نظام مباشر</span>
                </div>
                @yield('actions')
            </div>
        </div>

        <!-- System Alerts -->
        @if(session('success'))
            <div class="alert alert-success">
                <div class="d-flex align-center gap-2">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <span>{{ session('error') }}</span>
                <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <div>
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
                <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        <!-- Dynamic Content -->
        @yield('content')
    </main>

    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const el = document.getElementById('live-clock');
            if (el) el.textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Bulk Selection and Delete Logic
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const itemCheckboxes = document.querySelectorAll('.select-item');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkDeleteCount = document.getElementById('bulkDeleteCount');

            if (!bulkDeleteBtn) return;

            // Detect resource type based on route
            let bulkType = null;
            const path = window.location.pathname;
            if (path.includes('/admin/channels')) bulkType = 'channels';
            else if (path.includes('/admin/categories')) bulkType = 'categories';
            else if (path.includes('/admin/users')) bulkType = 'users';
            else if (path.includes('/admin/tournaments')) bulkType = 'tournaments';
            else if (path.includes('/admin/matches')) bulkType = 'matches';
            else if (path.includes('/admin/movies')) bulkType = 'movies';

            function updateBulkButton() {
                const checkedBoxes = document.querySelectorAll('.select-item:checked');
                const count = checkedBoxes.length;
                if (count > 0 && bulkType) {
                    bulkDeleteCount.textContent = count;
                    bulkDeleteBtn.style.display = 'inline-flex';
                } else {
                    bulkDeleteBtn.style.display = 'none';
                }
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    itemCheckboxes.forEach(cb => {
                        cb.checked = selectAllCheckbox.checked;
                    });
                    updateBulkButton();
                });
            }

            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (selectAllCheckbox) {
                        const allChecked = Array.from(itemCheckboxes).every(c => c.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                    updateBulkButton();
                });
            });

            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.select-item:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);

                if (ids.length === 0) return;

                let warningMsg = `هل أنت متأكد من حذف ${ids.length} عنصر محدد نهائياً؟`;
                if (bulkType === 'categories') {
                    warningMsg += '\nتنبيه: سيتم حذف التصنيفات الفارغة فقط. التصنيفات التي تحتوي على قنوات لن تُحذف.';
                }

                if (!confirm(warningMsg)) return;

                // Send AJAX Request
                fetch('{{ route("admin.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        type: bulkType,
                        ids: ids
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message || 'حدث خطأ أثناء الحذف.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ في الاتصال بالخادم.');
                });
            });
        });
    </script>

</body>
</html>
