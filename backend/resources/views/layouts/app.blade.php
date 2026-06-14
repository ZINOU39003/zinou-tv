<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>@yield('title', 'Zinou TV')</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --bg: #f4f7f6;
      --card: #ffffff;
      --card-hover: #f9fafa;
      --txt: #111827;
      --txt2: #4b5563;
      --txt3: #9ca3af;
      --border: #e5e7eb;
      --primary: #0f7a6b;
      --primary-d: #0b5e53;
      --primary-l: #e6f2f0;
      --blue: #1d4ed8;
      --red: #ef4444;
      --r: 12px;
      
      --nav-bg: linear-gradient(90deg, #094b41, #0f7a6b);
      --nav-txt: #ffffff;
    }

    body.darkmode {
      --bg: #0f1115;
      --card: #16191f;
      --card-hover: #1c2026;
      --txt: #f3f4f6;
      --txt2: #9ca3af;
      --txt3: #6b7280;
      --border: #2d3139;
      --primary: #39dbbf;
      --primary-d: #14b8a6;
      --primary-l: rgba(57,219,191,0.1);
      
      --nav-bg: #16191f;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body {
      font-family: 'Cairo', sans-serif;
      background: var(--bg);
      color: var(--txt);
      min-height: 100vh;
      -webkit-tap-highlight-color: transparent;
      transition: background 0.3s, color 0.3s;
    }

    a { text-decoration: none; color: inherit; }
    button { font-family: inherit; border: none; outline: none; cursor: pointer; }

    /* ── Header / Navbar ── */
    header {
      background: var(--nav-bg);
      color: var(--nav-txt);
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .nav-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 20px;
    }

    .brand {
      font-size: 20px;
      font-weight: 900;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #fff;
    }

    .brand-box {
      background: rgba(0,0,0,0.2);
      padding: 6px 14px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.1);
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .nav-link {
      background: rgba(0,0,0,0.15);
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      padding: 8px 16px;
      border-radius: 20px;
      transition: background 0.2s;
    }

    .nav-link:hover {
      background: rgba(0,0,0,0.3);
    }

    .nav-link.active {
      background: rgba(0,0,0,0.4);
      border: 1px solid rgba(255,255,255,0.2);
    }

    .theme-toggle {
      background: none;
      color: #fff;
      font-size: 18px;
      padding: 8px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
    }
    .theme-toggle:hover { background: rgba(255,255,255,0.1); }

    .main-content {
      max-width: 1000px;
      margin: 20px auto;
      padding: 0 10px;
    }

    @media (max-width: 768px) {
      .nav-links { display: none; } /* On mobile, we might want a hamburger menu or horizontal scroll */
      .nav-container { padding: 12px 15px; }
      .brand { font-size: 18px; }
    }

    /* Modals for standings/scorers */
    .modal-overlay {
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.6);
      z-index: 9999;
      display: none; align-items: center; justify-content: center;
      backdrop-filter: blur(4px);
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
      background: var(--card);
      width: 90%; max-width: 500px;
      max-height: 85vh;
      border-radius: 16px;
      display: flex; flex-direction: column;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      overflow: hidden;
    }
    .modal-head {
      padding: 16px;
      background: var(--card);
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      font-weight: 800; font-size: 16px;
    }
    .modal-close {
      background: var(--bg);
      border: 1px solid var(--border);
      width: 32px; height: 32px; border-radius: 50%;
      font-weight: bold; font-size: 18px; color: var(--txt);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer;
    }
    .modal-body {
      padding: 16px;
      overflow-y: auto;
      flex: 1;
    }

  </style>
  @yield('styles')
</head>
<body>

  <header>
    <div class="nav-container">
      <a href="{{ route('scores.index') }}" class="brand">
        <div class="brand-box">Zinou TV</div>
      </a>
      
      <div class="nav-links">
        <a href="{{ route('scores.index') }}" class="nav-link {{ request()->routeIs('scores.index') ? 'active' : '' }}">الرئيسية</a>
        <button class="nav-link" onclick="openStandingsModal()">ترتيب الدوريات</button>
        <button class="nav-link" onclick="openScorersModal()">ترتيب الهدافين</button>
        <a href="{{ route('scores.index') }}" class="nav-link">جدول المباريات</a>
        <a href="{{ route('download') }}" class="nav-link {{ request()->routeIs('download') ? 'active' : '' }}">تحميل التطبيق</a>
        <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn">🌙</button>
      </div>
    </div>
  </header>

  <main class="main-content">
    @yield('content')
  </main>

  <!-- Global Modals for Standings / Scorers -->
  <div class="modal-overlay" id="globalModal" onclick="if(event.target===this) closeGlobalModal()">
    <div class="modal-box">
      <div class="modal-head">
        <span id="gModalTitle">العنوان</span>
        <button class="modal-close" onclick="closeGlobalModal()">&times;</button>
      </div>
      <div class="modal-body" id="gModalBody">
        <!-- Content loaded via JS -->
      </div>
    </div>
  </div>

  <script>
    // Theme logic
    function toggleTheme() {
      const isDark = document.body.classList.toggle('darkmode');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      document.getElementById('themeBtn').textContent = isDark ? '☀️' : '🌙';
    }
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('darkmode');
      document.getElementById('themeBtn').textContent = '☀️';
    }

    // Modal logic
    function closeGlobalModal() {
      document.getElementById('globalModal').classList.remove('open');
    }
    function openGlobalModal(title, html) {
      document.getElementById('gModalTitle').textContent = title;
      document.getElementById('gModalBody').innerHTML = html;
      document.getElementById('globalModal').classList.add('open');
    }

    function openStandingsModal() {
      // In a real scenario, this would show a list of leagues, then fetch standings
      openGlobalModal('ترتيب الدوريات', '<div style="text-align:center;color:var(--txt3);padding:20px;">سيتم ربط ترتيب الدوريات هنا.</div>');
    }
    
    function openScorersModal() {
      openGlobalModal('ترتيب الهدافين', '<div style="text-align:center;color:var(--txt3);padding:20px;">سيتم ربط الهدافين هنا.</div>');
    }
  </script>
  @yield('scripts')
</body>
</html>
