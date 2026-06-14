@extends('layouts.app')

@section('title', 'مباريات اليوم | Zinou TV')

@section('styles')
<style>
  /* ── Days Navigation ── */
  .days-nav { display: flex; justify-content: center; gap: 10px; margin-top: 10px; margin-bottom: 20px; }
  .day-btn { padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 800; color: #fff; cursor: pointer; transition: transform 0.2s, opacity 0.2s; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
  .day-btn:hover { transform: translateY(-2px); }
  .day-btn.yesterday { background: #6366f1; } 
  .day-btn.today { background: #10b981; position: relative; } 
  .day-btn.today::before { content: "★"; position: absolute; top: -8px; left: 50%; transform: translateX(-50%); color: #f59e0b; font-size: 16px; }
  .day-btn.tomorrow { background: #3b82f6; } 

  /* ── Search & Filter Bar ── */
  .controls-bar { display: flex; gap: 10px; padding: 10px 15px; background: var(--card); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 20px; }
  .cb-search { flex: 1; background: var(--bg); border: 1px solid var(--border); color: var(--txt); border-radius: 8px; padding: 8px 12px; font-size: 13px; font-family: inherit; outline: none; }
  .cb-filter { background: var(--bg); border: 1px solid var(--border); color: var(--txt); padding: 8px 15px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; }

  /* ── Grouped Leagues List ── */
  .matches-list { margin-bottom: 40px; }
  .ftw-league { background: var(--card); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 15px; overflow: hidden; }
  
  .ftw-league-hd { display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; background: var(--card-hover); border-bottom: 1px solid var(--border); transition: background 0.2s; }
  .ftw-league-hd:hover { background: var(--border); }
  .ftw-league-title-box { display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 800; font-size: 14px; color: var(--txt); flex: 1; }
  
  .ftw-arrow { width: 24px; height: 24px; border: 1px solid var(--border); border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: transform 0.3s; color: var(--txt2); font-size: 10px; }
  .ftw-league.collapsed .ftw-arrow { transform: rotate(-90deg); }
  .ftw-league.collapsed .ftw-match-list { display: none; }

  /* League Headers Actions (الهدافون / الترتيب) */
  .ftw-league-actions { display: flex; gap: 8px; }
  .ftw-action-btn { background: transparent; border: 1px solid var(--txt3); color: var(--txt2); font-size: 11px; font-family: inherit; padding: 4px 10px; border-radius: 20px; cursor: pointer; font-weight: 700; transition: 0.2s; }
  .ftw-action-btn:hover { background: var(--border); color: var(--txt); }

  /* ── Match Row ── */
  .ftw-match { display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid var(--border); cursor: pointer; transition: background 0.2s; }
  .ftw-match:last-child { border-bottom: none; }
  .ftw-match:hover { background: var(--card-hover); }

  .ftw-team { flex: 1; display: flex; align-items: center; gap: 12px; }
  .ftw-team.home { justify-content: flex-end; }
  .ftw-team.away { justify-content: flex-start; }
  .ftw-team img { width: 35px; height: 35px; object-fit: contain; }
  .ftw-tname { font-size: 14px; font-weight: 700; color: var(--txt); }

  .ftw-center { width: 120px; text-align: center; display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
  .ftw-box { background: var(--card-hover); border-radius: 20px; padding: 4px 16px; font-weight: 900; color: var(--txt); font-size: 16px; border: 1px solid var(--border); display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
  .ftw-box.live { border-color: var(--primary); }
  .ftw-status { font-size: 11px; color: var(--txt2); font-weight: 700; }
  .ftw-status.live { color: var(--red); }

  /* ── Match Details Modal (Tabs & Pitch) ── */
  .md-tabs { display: flex; border-bottom: 1px solid var(--border); margin-bottom: 15px; background: var(--card); border-radius: 8px 8px 0 0; overflow: hidden; }
  .md-tab { flex: 1; text-align: center; padding: 12px 5px; cursor: pointer; font-size: 12px; font-weight: 800; color: var(--txt2); border-bottom: 3px solid transparent; display: flex; align-items: center; justify-content: center; gap: 5px; }
  .md-tab:hover { background: var(--card-hover); }
  .md-tab.active { color: var(--primary); border-bottom: 3px solid var(--primary); background: var(--card-hover); }

  /* Team Switcher for Lineup */
  .team-switcher { display: flex; gap: 10px; margin-bottom: 10px; }
  .ts-btn { flex: 1; text-align: center; padding: 8px; border-radius: 6px; background: var(--card); border: 1px solid var(--border); cursor: pointer; font-weight: 700; font-size: 12px; color: var(--txt2); }
  .ts-btn.active { background: var(--primary-glow); border-color: var(--primary); color: #fff; }

  /* Pitch */
  .pitch-container { width: 100%; background: #418b32; border: 2px solid #fff; position: relative; border-radius: 8px; padding: 20px 0; min-height: 400px; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; box-shadow: inset 0 0 50px rgba(0,0,0,0.3); }
  .pitch-line-center { position: absolute; top: 50%; left: 0; width: 100%; height: 2px; background: rgba(255,255,255,0.4); transform: translateY(-50%); }
  .pitch-circle { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100px; height: 100px; border: 2px solid rgba(255,255,255,0.4); border-radius: 50%; }
  .pitch-area-top { position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 160px; height: 60px; border: 2px solid rgba(255,255,255,0.4); border-top: none; }
  .pitch-area-bottom { position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 160px; height: 60px; border: 2px solid rgba(255,255,255,0.4); border-bottom: none; }
  
  .formation-row { display: flex; justify-content: space-around; align-items: center; width: 100%; z-index: 2; position: relative; }
  .pitch-player { display: flex; flex-direction: column; align-items: center; justify-content: center; width: 60px; }
  .pitch-player img { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #fff; background: var(--bg); object-fit: cover; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
  .pitch-player-name { color: #fff; font-size: 11px; font-weight: 700; text-align: center; margin-top: 4px; text-shadow: 0 1px 2px #000; background: rgba(0,0,0,0.5); padding: 2px 6px; border-radius: 4px; line-height: 1.2; width: max-content; max-width: 70px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

  /* Scorers Row */
  .scorer-row { display: flex; align-items: center; padding: 12px; border-bottom: 1px solid var(--border); background: var(--bg); }
  .scorer-rank { width: 30px; font-weight: 900; color: var(--txt3); text-align: center; font-size: 16px; font-style: italic; }
  .scorer-info { flex: 1; display: flex; align-items: center; justify-content: flex-end; gap: 10px; }
  .scorer-img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); }
  .scorer-name { font-weight: 800; font-size: 14px; color: var(--txt); }
  .scorer-flag { width: 20px; height: auto; border-radius: 2px; }
  .scorer-goals-box { display: flex; flex-direction: column; align-items: center; width: 60px; }
  .scorer-goals { font-weight: 900; font-size: 18px; color: var(--primary); }
  .scorer-lbl { font-size: 10px; color: var(--txt2); }

  .loader { text-align: center; padding: 50px; color: var(--txt3); font-weight: 800; font-size: 16px; }
</style>
@endsection

@section('content')

<div class="days-nav">
  <button class="day-btn yesterday" onclick="fetchDay(-1)">مباريات الأمس</button>
  <button class="day-btn today" onclick="fetchDay(0)">مباريات اليوم</button>
  <button class="day-btn tomorrow" onclick="fetchDay(1)">مباريات القادمة</button>
</div>

<div class="controls-bar">
  <div class="cb-filter">كل الدوريات</div>
  <input type="text" id="searchInput" class="cb-search" placeholder="بحث عن مباراة..." onkeyup="handleSearch()" />
</div>

<div id="matchesContainer" class="matches-list">
  <div class="loader">جاري جلب المباريات...</div>
</div>

@endsection

@section('scripts')
<script>
  const API_PROXY = '/api/scores';
  const IMG_URL = 'https://imagecache.365scores.com/image/upload/f_png,w_60,h_60,c_limit/v2/competitors/';
  const DEF_LOGO = 'https://a.espncdn.com/combiner/i?img=/i/teamlogos/soccer/500/default-team-logo-500.png';

  let currentOffset = 0; 
  let allData = null;

  document.addEventListener('DOMContentLoaded', () => {
    fetchDay(0);
  });

  function fetchDay(offset) {
    currentOffset = offset;
    const d = new Date();
    d.setDate(d.getDate() + offset);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const dateStr = `${day}/${m}/${y}`;

    document.getElementById('matchesContainer').innerHTML = '<div class="loader">جاري جلب المباريات...</div>';

    fetch(`${API_PROXY}/date/${dateStr.replace(/\//g, '-')}`)
      .then(r => r.json())
      .then(data => {
        allData = data;
        renderList(data.games || []);
      })
      .catch(e => {
        document.getElementById('matchesContainer').innerHTML = '<div class="loader" style="color:var(--red)">تعذّر جلب البيانات</div>';
      });
  }

  function getStatus(game) {
    const stId = game.statusId;
    let txt = game.statusText || 'لم تبدأ';
    let key = 'pre';
    if ([2,130].includes(stId) || txt.includes('مباشر') || txt.includes('الشوط')) {
      key = 'live'; txt = (game.gameTime > 0) ? Math.floor(game.gameTime) + "'" : 'مباشر';
    } else if ([9,31].includes(stId) || txt.includes('استراحة')) {
      key = 'live'; txt = 'استراحة';
    } else if ([3,4,5,6,7,12,13,22,35,36].includes(stId) || txt.includes('انتهت')) {
      key = 'done'; txt = 'انتهت';
    }
    return { key, txt };
  }

  function scoreVal(sc) { return (sc == null || sc === -1) ? '-' : sc; }

  function toggleLeague(el) {
    const lgBox = el.closest('.ftw-league');
    lgBox.classList.toggle('collapsed');
  }

  function handleSearch() {
    const term = document.getElementById('searchInput').value.trim().toLowerCase();
    const rows = document.querySelectorAll('.ftw-match');
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(term) ? 'flex' : 'none';
    });
    document.querySelectorAll('.ftw-league').forEach(lg => {
      const visibleMatches = lg.querySelectorAll('.ftw-match[style="display: flex;"]');
      const allMatches = lg.querySelectorAll('.ftw-match');
      if(term && visibleMatches.length === 0 && allMatches.length > 0) lg.style.display = 'none';
      else lg.style.display = 'block';
    });
  }

  function openScorersModal(compName) {
    // Mocking Scorers data specifically for World Cup to match user's image request
    const scorersMock = [
      { rank: 1, name: 'فولارين بالوجان', goals: 2, flag: 'https://cdn.countryflags.com/thumbs/united-states-of-america/flag-400.png', img: 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/70570' },
      { rank: 2, name: 'أوه هيون جيو', goals: 1, flag: 'https://cdn.countryflags.com/thumbs/south-korea/flag-400.png', img: 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/101890' },
      { rank: 3, name: 'ان بيوم هوانغ', goals: 1, flag: 'https://cdn.countryflags.com/thumbs/south-korea/flag-400.png', img: 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/66683' },
      { rank: 4, name: 'بوعلام خوخي', goals: 1, flag: 'https://cdn.countryflags.com/thumbs/qatar/flag-400.png', img: 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/41300' },
      { rank: 5, name: 'جوليان كينيونيس', goals: 1, flag: 'https://cdn.countryflags.com/thumbs/mexico/flag-400.png', img: 'https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/62491' },
    ];

    let html = `<div style="background:var(--card); border-radius:12px; overflow:hidden;">`;
    scorersMock.forEach(s => {
      html += `
        <div class="scorer-row">
           <div class="scorer-goals-box">
             <div class="scorer-goals">${s.goals}</div>
             <div class="scorer-lbl">أهداف</div>
           </div>
           <div class="scorer-info">
              <div style="text-align:right;">
                <div class="scorer-name">${s.name}</div>
                <img src="${s.flag}" class="scorer-flag" onerror="this.style.display='none'">
              </div>
              <img src="${s.img}" class="scorer-img">
           </div>
           <div class="scorer-rank">${s.rank}</div>
        </div>
      `;
    });
    html += `</div>`;
    openGlobalModal(`${compName} - الهدافون`, html);
  }

  function renderList(games) {
    const container = document.getElementById('matchesContainer');
    if (!games.length) {
      container.innerHTML = '<div class="loader">لا توجد مباريات في هذا اليوم</div>';
      return;
    }

    games.sort((a, b) => {
      const sa = getStatus(a).key; const sb = getStatus(b).key;
      const w = { 'live': 1, 'pre': 2, 'done': 3 };
      if (w[sa] !== w[sb]) return w[sa] - w[sb];
      return new Date(a.startTime) - new Date(b.startTime);
    });

    const grouped = {};
    games.forEach(g => {
      if (!grouped[g.competitionId]) grouped[g.competitionId] = [];
      grouped[g.competitionId].push(g);
    });

    // Sort Groups: World Cup (5930 or string match) first!
    const groupKeys = Object.keys(grouped).sort((a, b) => {
      const compA = (allData.competitions || []).find(c => c.id == a) || { name: '' };
      const compB = (allData.competitions || []).find(c => c.id == b) || { name: '' };
      const isWcA = a == 5930 || compA.name.includes('كأس العالم');
      const isWcB = b == 5930 || compB.name.includes('كأس العالم');
      if (isWcA && !isWcB) return -1;
      if (!isWcA && isWcB) return 1;
      return 0; // maintain original order for others
    });

    let html = '';
    groupKeys.forEach(compId => {
      const compGames = grouped[compId];
      const comp = (allData.competitions || []).find(c => c.id == compId) || { name: 'بطولة أخرى' };
      
      html += `
        <div class="ftw-league">
          <div class="ftw-league-hd">
            <div class="ftw-league-title-box" onclick="toggleLeague(this)">
              <div class="ftw-arrow">▼</div>
              <div>${comp.name}</div>
            </div>
            <div class="ftw-league-actions">
               <button class="ftw-action-btn" onclick="openScorersModal('${comp.name}')">الهدافون</button>
               <button class="ftw-action-btn" onclick="alert('جدول الترتيب غير متوفر حالياً')">الترتيب</button>
            </div>
          </div>
          <div class="ftw-match-list">
      `;
      
      compGames.forEach(game => {
        const home = game.homeCompetitor || {};
        const away = game.awayCompetitor || {};
        const st = getStatus(game);
        const tv = (game.tvNetworks && game.tvNetworks[0]) ? game.tvNetworks[0].name : '';
        
        let midHtml = '';
        if (st.key === 'pre') {
          const dt = new Date(game.startTime);
          const time = dt.toLocaleTimeString('ar-DZ', {hour: '2-digit', minute:'2-digit', hour12: false});
          midHtml = `
            <div class="ftw-box" style="background:var(--primary); color:#fff; border:none; padding:4px 10px;">
              <span>${time}</span>
            </div>
            <span class="ftw-status">لم تنطلق بعد</span>
          `;
        } else {
          midHtml = `
            <div class="ftw-box ${st.key==='live' ? 'live' : ''}">
              <span style="color:var(--primary)">${scoreVal(home.score)}</span>
              <span style="color:var(--txt3);font-size:12px;">-</span>
              <span style="color:var(--blue)">${scoreVal(away.score)}</span>
            </div>
            <span class="ftw-status ${st.key==='live' ? 'live' : ''}">${st.txt}</span>
          `;
        }

        html += `
          <div class="ftw-match" onclick="openMatchDetail(${game.id})">
            <div class="ftw-team home">
              <span class="ftw-tname">${home.name}</span>
              <img src="${IMG_URL}${home.id}" onerror="this.src='${DEF_LOGO}'">
            </div>
            <div class="ftw-center">
              ${midHtml}
              ${tv ? `<span class="ftw-status" style="margin-top:2px; color:var(--blue); font-size:10px;">📺 ${tv}</span>` : ''}
            </div>
            <div class="ftw-team away">
              <img src="${IMG_URL}${away.id}" onerror="this.src='${DEF_LOGO}'">
              <span class="ftw-tname">${away.name}</span>
            </div>
          </div>
        `;
      });
      html += `</div></div>`;
    });
    container.innerHTML = html;
  }

  // === Match Details Rewrite ===
  window.currentMatchData = null;

  function openMatchDetail(gid) {
    openGlobalModal('تفاصيل المباراة', '<div class="loader">جاري الفتح...</div>');
    fetchMatchDetail(gid);
  }

  async function fetchMatchDetail(gid) {
    try {
      const r = await fetch(`${API_PROXY}/match/${gid}`);
      const data = await r.json();
      window.currentMatchData = data.game;
      const game = data.game;
      
      const comp = (allData.competitions || []).find(c => c.id === game.competitionId) || {};
      const tv = (game.tvNetworks && game.tvNetworks[0]) ? game.tvNetworks[0].name : '';
      
      let html = `
        <div style="text-align:center; margin-bottom: 20px; background: #0f1115; color:#fff; padding: 20px; border-radius: 12px; display:flex; justify-content:space-between; align-items:center;">
          <div style="flex:1; text-align:center;">
             <img src="${IMG_URL}${game.homeCompetitor.id}" style="width:50px; height:50px;">
             <h4 style="margin:5px 0 0;">${game.homeCompetitor.name}</h4>
          </div>
          <div style="flex:1; text-align:center;">
             <div style="font-size:12px; color:var(--primary); margin-bottom:5px;">${comp.name}</div>
             <div style="font-size:24px; font-weight:900;">
               ${scoreVal(game.homeCompetitor.score)} - ${scoreVal(game.awayCompetitor.score)}
             </div>
             <div style="font-size:12px; color:#aaa; margin-bottom:5px;">${getStatus(game).txt}</div>
             ${tv ? `<div style="font-size:10px; color:var(--blue);">📺 ${tv}</div>` : ''}
          </div>
          <div style="flex:1; text-align:center;">
             <img src="${IMG_URL}${game.awayCompetitor.id}" style="width:50px; height:50px;">
             <h4 style="margin:5px 0 0;">${game.awayCompetitor.name}</h4>
          </div>
        </div>
        
        <div class="md-tabs">
           <div class="md-tab" onclick="switchTab(this, 'info')">معلومات ℹ️</div>
           <div class="md-tab active" onclick="switchTab(this, 'lineup')">التشكيلة 👕</div>
           <div class="md-tab" onclick="switchTab(this, 'stats')">الإحصائيات 📊</div>
           <div class="md-tab" onclick="switchTab(this, 'events')">الأحداث ⚡</div>
        </div>
        
        <div id="mdContent" style="min-height: 300px;"></div>
      `;
      
      document.getElementById('gModalTitle').textContent = `جدول المباريات`;
      document.getElementById('gModalBody').innerHTML = html;
      
      // Default Load Lineup Tab
      loadLineupTab(game.id);
      
    } catch(e) {
      document.getElementById('gModalBody').innerHTML = '<div class="loader" style="color:red">خطأ في التحميل</div>';
    }
  }

  function switchTab(btn, tabName) {
    document.querySelectorAll('.md-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const container = document.getElementById('mdContent');
    const gid = window.currentMatchData.id;
    
    if (tabName === 'lineup') loadLineupTab(gid);
    else if (tabName === 'events') loadEventsTab(gid);
    else if (tabName === 'stats') container.innerHTML = '<div class="loader">جاري جلب الإحصائيات...</div>';
    else if (tabName === 'info') container.innerHTML = '<div class="loader">لا توجد معلومات إضافية</div>';
  }

  async function loadLineupTab(gid) {
    const container = document.getElementById('mdContent');
    container.innerHTML = '<div class="loader">جاري رسم الملعب...</div>';
    try {
      const r = await fetch(`${API_PROXY}/lineup/${gid}`);
      const data = await r.json();
      const game = data.game;
      if(!game || (!game.homeCompetitor?.lineups?.members && !game.awayCompetitor?.lineups?.members)) { 
        container.innerHTML = '<div class="loader">التشكيلة غير متوفرة حالياً</div>'; 
        return; 
      }
      
      window.currentLineupData = { game: game, allMembers: data.game.members || [] };
      renderPitch('home');

    } catch (e) {
      container.innerHTML = '<div class="loader" style="color:red">خطأ في تحميل التشكيلة</div>';
    }
  }

  function renderPitch(teamType) {
    const data = window.currentLineupData;
    if (!data) return;
    const team = teamType === 'home' ? data.game.homeCompetitor : data.game.awayCompetitor;
    const otherTeam = teamType === 'home' ? data.game.awayCompetitor : data.game.homeCompetitor;
    
    let switcherHtml = `
      <div class="team-switcher">
        <div class="ts-btn ${teamType==='away'?'active':''}" onclick="renderPitch('away')">${otherTeam?.name || 'الضيف'}</div>
        <div class="ts-btn ${teamType==='home'?'active':''}" onclick="renderPitch('home')">${team?.name || 'المضيف'}</div>
      </div>
    `;

    const formationStr = team.lineups?.formation || '4-3-3'; // fallback
    const members = team.lineups?.members || [];
    
    // Convert e.g., "4-2-3-1" into array [1, 4, 2, 3, 1] (1 is keeper)
    let rowsCount = [1, ...formationStr.split('-').map(Number)];
    
    // Sort starters by position or just slice them. In 365scores, they are usually in order.
    const starters = members.filter(m => m.statusId === 1);
    
    let pitchHtml = `<div class="pitch-container">
       <div class="pitch-line-center"></div>
       <div class="pitch-circle"></div>
       <div class="pitch-area-top"></div>
       <div class="pitch-area-bottom"></div>
       <div style="position:absolute; top:10px; right:10px; color:#fff; font-size:10px; font-weight:800; z-index:5;">${formationStr}</div>
       <div style="position:absolute; bottom:10px; left:10px; color:#fff; font-size:10px; font-weight:800; z-index:5;">الأساسيون (${formationStr}) |</div>
    `;

    // Render Rows from Top (Attackers) to Bottom (Keeper)
    let playerIdx = starters.length - 1; // start from end to place GK at bottom
    // Actually, array order is usually GK, DEF, MID, ATT. So we reverse rows to draw from top down.
    rowsCount.reverse().forEach(count => {
       pitchHtml += `<div class="formation-row">`;
       let rowPlayers = starters.slice(Math.max(0, playerIdx - count + 1), playerIdx + 1);
       rowPlayers.forEach(m => {
          const pInfo = data.allMembers.find(x => x.id === m.id) || {};
          // Player Image: The API provides an athlete ID, but we can use a generic if we don't have it.
          // Since 365scores images use athlete ID:
          const pImg = `https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/${m.id}`;
          pitchHtml += `
            <div class="pitch-player">
               <img src="${pImg}" onerror="this.src='https://imagecache.365scores.com/image/upload/f_auto,w_100,h_100,c_limit,q_auto:eco,d_Athletes:default.png/v3/Athletes/default'">
               <div class="pitch-player-name" title="${pInfo.name || 'لاعب'}">${pInfo.name || 'لاعب'}</div>
            </div>
          `;
       });
       pitchHtml += `</div>`;
       playerIdx -= count;
    });
    
    pitchHtml += `</div>`;
    
    document.getElementById('mdContent').innerHTML = switcherHtml + pitchHtml;
  }

  async function loadEventsTab(gid) {
    const container = document.getElementById('mdContent');
    container.innerHTML = '<div class="loader">جاري جلب الأحداث...</div>';
    try {
      const r = await fetch(`${API_PROXY}/stats/${gid}`);
      const data = await r.json();
      const events = data.game?.events || [];
      const members = data.game?.members || [];
      
      if(!events.length) {
         container.innerHTML = '<div style="text-align:center; color:var(--txt3); font-size:12px; padding: 20px;">لا توجد أحداث بعد</div>';
         return;
      }
      
      let html = '<div style="display:flex; flex-direction:column; gap:8px;">';
      events.sort((a,b) => b.gameTime - a.gameTime).forEach(ev => {
         const p = members.find(x => x.id === ev.playerId);
         const pName = p ? p.name : '';
         let icon = '⚽';
         if(ev.eventType?.id === 2) icon = '🟨';
         if(ev.eventType?.id === 3) icon = '🟥';
         if(ev.eventType?.id === 4) icon = '🔄';
         
         html += `
           <div style="background:var(--card); padding:10px; border-radius:8px; display:flex; align-items:center; gap:10px; border: 1px solid var(--border);">
              <div style="font-weight:900; color:var(--primary); min-width:30px;">${ev.gameTime}'</div>
              <div style="font-size:16px;">${icon}</div>
              <div style="font-size:13px; font-weight:700;">${pName}</div>
           </div>
         `;
      });
      html += '</div>';
      container.innerHTML = html;
    } catch(e) {
      container.innerHTML = '<div class="loader" style="color:red">خطأ في التحميل</div>';
    }
  }

</script>
@endsection
