<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZINOU SCORES — نتائج مباشرة</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   DESIGN TOKENS
══════════════════════════════════════════ */
:root {
  --bg:          #0b0e14;
  --bg2:         #111520;
  --bg3:         #171d2b;
  --card:        #1a2035;
  --card2:       #1f2640;
  --border:      rgba(255,255,255,0.07);
  --border2:     rgba(255,255,255,0.12);
  --accent:      #34B349;
  --accent2:     #2a9039;
  --accent-glow: rgba(52,179,73,0.25);
  --blue:        #3b82f6;
  --red:         #ef4444;
  --yellow:      #f59e0b;
  --txt:         #e8ecf4;
  --txt2:        #8a92a6;
  --txt3:        #556075;
  --live-clr:    #ef4444;
  --sched-clr:   #3b82f6;
  --ended-clr:   #8a92a6;
  --shadow:      0 8px 32px rgba(0,0,0,0.4);
  --shadow-sm:   0 2px 12px rgba(0,0,0,0.25);
  --r:           12px;
  --r-sm:        8px;
}

/* ── RESET ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth;height:100%}
body{font-family:'Cairo',sans-serif;background:var(--bg);color:var(--txt);font-size:14px;height:100%;overflow:hidden}
a{text-decoration:none;color:inherit}
button{font-family:'Cairo',sans-serif;cursor:pointer;border:none;background:none;color:inherit;font-size:inherit}
img{display:block}
::-webkit-scrollbar{width:4px;height:4px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--border2);border-radius:4px}

/* ══════════════════════════════════════════
   APP SHELL
══════════════════════════════════════════ */
.app {
  display: grid;
  grid-template-rows: 56px 1fr;
  grid-template-columns: 320px 1fr;
  height: 100vh;
  gap: 0;
}

/* ══════════════════════════════════════════
   TOPBAR
══════════════════════════════════════════ */
.topbar {
  grid-column: 1 / -1;
  background: var(--bg2);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  gap: 16px;
  position: relative;
  z-index: 20;
}

.topbar-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1.2rem;
  font-weight: 900;
  letter-spacing: -0.5px;
  color: var(--txt);
  flex-shrink: 0;
}
.topbar-logo .dot {
  width: 10px; height: 10px;
  background: var(--accent);
  border-radius: 50%;
  box-shadow: 0 0 10px var(--accent-glow);
}

/* Date Strip */
.date-strip {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 4px;
  overflow-x: auto;
  padding: 4px 0;
  scrollbar-width: none;
}
.date-strip::-webkit-scrollbar { display: none; }

.date-btn {
  flex-shrink: 0;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  color: var(--txt2);
  transition: all 0.2s;
  white-space: nowrap;
  background: transparent;
  border: 1px solid transparent;
}
.date-btn:hover { background: var(--card); color: var(--txt); }
.date-btn.active {
  background: var(--accent);
  color: #fff;
  border-color: var(--accent);
  box-shadow: 0 4px 16px var(--accent-glow);
}
.date-nav-btn {
  width: 32px; height: 32px;
  border-radius: 8px;
  background: var(--card);
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; color: var(--txt2);
  flex-shrink: 0;
  transition: 0.15s;
}
.date-nav-btn:hover { background: var(--card2); color: var(--txt); }

/* Live counter */
.live-count {
  display: flex; align-items: center; gap: 6px;
  background: rgba(239,68,68,0.1);
  border: 1px solid rgba(239,68,68,0.25);
  border-radius: 20px; padding: 5px 12px;
  font-size: 12px; font-weight: 700; color: var(--red);
  flex-shrink: 0;
}
.live-count .pulse {
  width: 8px; height: 8px; background: var(--red);
  border-radius: 50%;
  animation: pulse 1.2s ease-in-out infinite;
}
@keyframes pulse {
  0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.7); }
  50% { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
}

/* ══════════════════════════════════════════
   LEFT SIDEBAR
══════════════════════════════════════════ */
.sidebar {
  background: var(--bg2);
  border-left: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Sidebar filters */
.sidebar-filters {
  padding: 12px 14px;
  display: flex; gap: 6px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.filter-btn {
  flex: 1; padding: 7px 0;
  border-radius: var(--r-sm);
  font-size: 12px; font-weight: 700;
  color: var(--txt2);
  background: var(--bg3);
  transition: 0.15s;
  text-align: center;
}
.filter-btn:hover { color: var(--txt); }
.filter-btn.active-all { background: var(--accent); color: #fff; }
.filter-btn.active-live { background: rgba(239,68,68,0.15); color: var(--red); border: 1px solid rgba(239,68,68,0.3); }
.filter-btn.active-sched { background: rgba(59,130,246,0.15); color: var(--blue); border: 1px solid rgba(59,130,246,0.3); }
.filter-btn.active-ended { background: var(--bg3); color: var(--txt2); }

/* Match list */
.match-list {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
}

/* Competition group */
.comp-header {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 14px 6px;
  position: sticky; top: 0;
  background: var(--bg2);
  z-index: 5;
}
.comp-logo {
  width: 18px; height: 18px; object-fit: contain; flex-shrink: 0;
  filter: brightness(0.9);
}
.comp-name {
  font-size: 11px; font-weight: 800;
  color: var(--txt3);
  letter-spacing: 0.5px;
  text-transform: uppercase;
  flex: 1;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.comp-count {
  font-size: 10px; font-weight: 700;
  color: var(--txt3);
  background: var(--bg3);
  border-radius: 10px;
  padding: 1px 6px;
  flex-shrink: 0;
}

/* Match card */
.match-card {
  padding: 10px 14px;
  display: grid;
  grid-template-columns: 1fr 60px 1fr;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  border-bottom: 1px solid var(--border);
  transition: background 0.12s;
  border-right: 3px solid transparent;
  position: relative;
}
.match-card:hover { background: rgba(255,255,255,0.03); }
.match-card.active {
  background: rgba(52,179,73,0.08);
  border-right-color: var(--accent);
}

.mc-team {
  display: flex; align-items: center; gap: 8px;
  min-width: 0;
}
.mc-team.away { flex-direction: row-reverse; }

.mc-team-logo {
  width: 22px; height: 22px; object-fit: contain; flex-shrink: 0;
}
.mc-team-name {
  font-size: 13px; font-weight: 600; color: var(--txt);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  max-width: 90px;
}

.mc-center { text-align: center; }
.mc-score {
  font-size: 15px; font-weight: 800;
  color: var(--txt);
  display: block;
  direction: ltr;
  letter-spacing: 1px;
}
.mc-status {
  display: block;
  font-size: 10px; font-weight: 700;
  margin-top: 2px;
}
.mc-status.live { color: var(--red); }
.mc-status.sched { color: var(--blue); }
.mc-status.ended { color: var(--txt3); }
.mc-status.halftime { color: var(--yellow); }

/* ══════════════════════════════════════════
   CENTER — MATCH DETAIL
══════════════════════════════════════════ */
.center {
  background: var(--bg);
  overflow-y: auto;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
}

/* Empty state */
.empty-state {
  flex: 1;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 16px; padding: 40px;
}
.empty-ball { font-size: 64px; filter: grayscale(0.5); opacity: 0.3; }
.empty-title { font-size: 18px; font-weight: 700; color: var(--txt2); }
.empty-sub { font-size: 13px; color: var(--txt3); text-align: center; }

/* Match header */
.mh {
  background: linear-gradient(180deg, var(--bg3) 0%, var(--bg) 100%);
  padding: 28px 24px 0;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}

.mh-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 4px 12px;
  background: var(--card);
  border-radius: 20px;
  font-size: 11px; font-weight: 700; color: var(--txt2);
  margin-bottom: 20px;
  border: 1px solid var(--border2);
}
.mh-badge img { width: 14px; height: 14px; object-fit: contain; }

.mh-teams {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: 20px;
  max-width: 580px;
  margin: 0 auto 20px;
}
.mh-team {
  display: flex; flex-direction: column;
  align-items: center; gap: 12px;
}
.mh-team-logo {
  width: 80px; height: 80px; object-fit: contain;
  filter: drop-shadow(0 4px 16px rgba(0,0,0,0.5));
  transition: transform 0.2s;
}
.mh-team-logo:hover { transform: scale(1.05); }
.mh-team-name {
  font-size: 15px; font-weight: 800;
  color: var(--txt);
  text-align: center;
  line-height: 1.2;
}

.mh-score-box { text-align: center; }
.mh-status-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 12px; border-radius: 20px;
  font-size: 12px; font-weight: 800;
  margin-bottom: 8px;
}
.mh-status-badge.live { background: rgba(239,68,68,0.15); color: var(--red); border: 1px solid rgba(239,68,68,0.3); }
.mh-status-badge.live::before { content: ''; width: 7px; height: 7px; background: var(--red); border-radius: 50%; animation: pulse 1.2s infinite; display: inline-block; }
.mh-status-badge.sched { background: rgba(59,130,246,0.1); color: var(--blue); border: 1px solid rgba(59,130,246,0.25); }
.mh-status-badge.ended { background: var(--card); color: var(--txt3); border: 1px solid var(--border); }
.mh-status-badge.halftime { background: rgba(245,158,11,0.1); color: var(--yellow); border: 1px solid rgba(245,158,11,0.25); }

.mh-score {
  font-size: 48px; font-weight: 900;
  direction: ltr; letter-spacing: 4px;
  color: var(--txt);
  line-height: 1;
  text-shadow: 0 2px 20px rgba(0,0,0,0.5);
}
.mh-date { font-size: 11px; color: var(--txt3); margin-top: 6px; font-weight: 600; }

/* TV Channels */
.tv-bar {
  display: flex; flex-wrap: wrap; gap: 6px;
  padding: 12px 24px;
  border-bottom: 1px solid var(--border);
  background: var(--bg2);
}
.tv-chip {
  display: flex; align-items: center; gap: 5px;
  padding: 4px 10px; border-radius: 6px;
  background: var(--card); border: 1px solid var(--border2);
  font-size: 11px; font-weight: 700; color: var(--txt2);
}

/* Match tabs */
.mh-tabs {
  display: flex;
  background: var(--bg2);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  overflow-x: auto;
  scrollbar-width: none;
}
.mh-tabs::-webkit-scrollbar { display: none; }
.mh-tab {
  padding: 14px 18px;
  font-size: 13px; font-weight: 700;
  color: var(--txt3);
  white-space: nowrap;
  border-bottom: 2px solid transparent;
  transition: 0.15s;
  flex-shrink: 0;
}
.mh-tab:hover { color: var(--txt); }
.mh-tab.active { color: var(--accent); border-bottom-color: var(--accent); }

/* Tab panes */
.tab-content { flex: 1; }
.tab-pane { display: none; padding: 20px 24px; }
.tab-pane.active { display: block; }

/* ── TIMELINE ── */
.tl-empty {
  text-align: center; padding: 48px 24px;
  color: var(--txt3); font-weight: 600; font-size: 14px;
}
.tl-section-label {
  text-align: center; font-size: 11px; font-weight: 800;
  color: var(--txt3); letter-spacing: 1px;
  padding: 16px 0 8px;
  text-transform: uppercase;
}
.tl-item {
  display: grid; grid-template-columns: 1fr 50px 1fr;
  gap: 8px; align-items: start;
  margin-bottom: 12px;
  position: relative;
}
.tl-item::after {
  content: '';
  position: absolute;
  top: 20px; bottom: -12px;
  left: 50%; transform: translateX(-50%);
  width: 1px;
  background: var(--border);
}
.tl-item:last-child::after { display: none; }

.tl-home { text-align: left; }
.tl-away { text-align: right; }
.tl-mid {
  display: flex; flex-direction: column; align-items: center;
  background: var(--bg);
  padding: 2px 0;
  position: relative; z-index: 1;
}
.tl-min {
  font-size: 11px; font-weight: 900; color: var(--txt2);
  background: var(--card);
  border: 1px solid var(--border2);
  border-radius: 10px;
  padding: 2px 7px;
  margin-bottom: 4px;
}
.tl-icon {
  font-size: 16px;
  width: 32px; height: 32px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  background: var(--card);
  border: 1px solid var(--border2);
}

.tl-player { display: flex; align-items: center; gap: 8px; cursor: pointer; }
.tl-home .tl-player { flex-direction: row; }
.tl-away .tl-player { flex-direction: row-reverse; }
.tl-player-img { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border2); flex-shrink: 0; }
.tl-player-name { font-size: 12px; font-weight: 700; color: var(--txt); }
.tl-player-name:hover { color: var(--accent); }
.tl-assist { font-size: 11px; color: var(--txt3); margin-top: 2px; }

/* ── STATS ── */
.stat-item { margin-bottom: 18px; }
.stat-labels {
  display: flex; justify-content: space-between;
  font-size: 12px; margin-bottom: 8px;
}
.stat-val { font-weight: 800; color: var(--txt); }
.stat-name { font-weight: 600; color: var(--txt3); font-size: 11px; }
.stat-bar-wrap {
  display: flex; align-items: center; gap: 6px;
}
.stat-bar-track {
  flex: 1; height: 5px; background: var(--card);
  border-radius: 3px; overflow: hidden;
  display: flex;
}
.stat-bar-home {
  height: 100%;
  background: linear-gradient(90deg, var(--accent2), var(--accent));
  border-radius: 3px 0 0 3px;
  transition: width 0.8s cubic-bezier(.4,0,.2,1);
}
.stat-bar-away {
  height: 100%;
  background: linear-gradient(90deg, var(--blue), #60a5fa);
  border-radius: 0 3px 3px 0;
  flex: 1;
  transition: width 0.8s;
}

/* ── LINEUPS ── */
.lineup-grid {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.lineup-col {
  background: var(--card);
  border-radius: var(--r);
  overflow: hidden;
  border: 1px solid var(--border);
}
.lineup-col-head {
  padding: 12px 14px;
  font-size: 13px; font-weight: 800;
  color: var(--txt);
  background: var(--card2);
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; gap: 8px;
}
.lineup-col-head img { width: 20px; height: 20px; object-fit: contain; }

.lineup-player {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 14px;
  border-bottom: 1px solid var(--border);
  cursor: pointer;
  transition: background 0.1s;
}
.lineup-player:last-child { border-bottom: none; }
.lineup-player:hover { background: rgba(255,255,255,0.04); }
.lp-num {
  font-size: 11px; font-weight: 900; color: var(--txt3);
  width: 18px; text-align: center; flex-shrink: 0;
}
.lp-img {
  width: 32px; height: 32px; border-radius: 50%; object-fit: cover;
  border: 2px solid var(--border2); flex-shrink: 0;
  background: var(--bg3);
}
.lp-name { font-size: 13px; font-weight: 600; flex: 1; }
.lp-pos {
  font-size: 10px; font-weight: 800;
  padding: 2px 6px; border-radius: 4px;
  background: var(--bg3); color: var(--txt3);
}

/* ── STANDINGS ── */
.standings-table {
  width: 100%; border-collapse: collapse;
  font-size: 12px;
}
.standings-table th {
  padding: 10px 8px;
  text-align: center;
  color: var(--txt3); font-weight: 700;
  font-size: 11px;
  border-bottom: 1px solid var(--border);
  background: var(--card);
}
.standings-table td {
  padding: 10px 8px;
  text-align: center;
  border-bottom: 1px solid var(--border);
  color: var(--txt2);
}
.standings-table tr:hover td { background: rgba(255,255,255,0.03); }
.st-team-cell {
  display: flex; align-items: center; gap: 8px;
  text-align: right; color: var(--txt);
}
.st-team-cell img { width: 18px; height: 18px; object-fit: contain; }
.st-pos { font-weight: 800; color: var(--txt3); width: 24px; text-align: center; }
.st-pts { font-weight: 900; color: var(--accent); }

/* ── H2H ── */
.h2h-item {
  display: grid; grid-template-columns: 1fr 60px 1fr;
  align-items: center; gap: 8px;
  padding: 12px 0; border-bottom: 1px solid var(--border);
  font-size: 12px; font-weight: 700;
}
.h2h-home { display: flex; align-items: center; gap: 6px; }
.h2h-away { display: flex; align-items: center; gap: 6px; flex-direction: row-reverse; }
.h2h-item img { width: 18px; height: 18px; object-fit: contain; }
.h2h-score {
  text-align: center;
  background: var(--card); border: 1px solid var(--border2);
  border-radius: 6px; padding: 4px 8px;
  font-weight: 900; font-size: 14px; direction: ltr;
}
.h2h-date { font-size: 10px; color: var(--txt3); text-align: center; margin-top: 2px; }
.h2h-empty {
  text-align: center; padding: 48px; color: var(--txt3); font-weight: 600;
}

/* ══════════════════════════════════════════
   PLAYER MODAL
══════════════════════════════════════════ */
.modal-bg {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.75);
  backdrop-filter: blur(4px);
  z-index: 100;
  display: flex; align-items: center; justify-content: center;
  opacity: 0; pointer-events: none;
  transition: opacity 0.2s;
}
.modal-bg.open { opacity: 1; pointer-events: all; }

.player-modal {
  background: var(--card);
  border: 1px solid var(--border2);
  border-radius: 20px;
  width: 340px;
  overflow: hidden;
  transform: scale(0.94) translateY(12px);
  transition: transform 0.25s cubic-bezier(.34,1.56,.64,1);
  box-shadow: 0 32px 80px rgba(0,0,0,0.6);
}
.modal-bg.open .player-modal { transform: scale(1) translateY(0); }

.pm-header {
  background: linear-gradient(135deg, #1a3a2a 0%, #0d1f17 100%);
  padding: 28px 20px 20px;
  text-align: center;
  position: relative;
}
.pm-close {
  position: absolute; top: 12px; left: 12px;
  width: 30px; height: 30px; border-radius: 50%;
  background: rgba(255,255,255,0.1);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; color: var(--txt2);
  transition: 0.15s;
}
.pm-close:hover { background: rgba(255,255,255,0.2); color: var(--txt); }
.pm-img {
  width: 88px; height: 88px; border-radius: 50%; object-fit: cover;
  border: 3px solid var(--accent);
  box-shadow: 0 0 24px var(--accent-glow);
  margin: 0 auto 12px;
}
.pm-name { font-size: 18px; font-weight: 900; color: var(--txt); }
.pm-team { font-size: 12px; color: var(--txt3); margin-top: 4px; }
.pm-body { padding: 16px; }
.pm-row {
  display: flex; justify-content: space-between;
  padding: 10px 0; border-bottom: 1px solid var(--border);
}
.pm-row:last-child { border-bottom: none; }
.pm-key { font-size: 12px; color: var(--txt3); font-weight: 600; }
.pm-val { font-size: 13px; font-weight: 800; color: var(--txt); }

/* ══════════════════════════════════════════
   SPINNER
══════════════════════════════════════════ */
.spinner-wrap { padding: 48px; text-align: center; }
.spinner {
  width: 32px; height: 32px;
  border: 3px solid var(--border2);
  border-top-color: var(--accent);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  margin: 0 auto;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ══════════════════════════════════════════
   FADE IN ANIMATION
══════════════════════════════════════════ */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.fade-up { animation: fadeUp 0.25s ease-out forwards; }

/* ══════════════════════════════════════════
   EMPTY COMP DIVIDER
══════════════════════════════════════════ */
.comp-divider {
  height: 1px;
  background: var(--border);
  margin: 4px 0;
}

/* ══════════════════════════════════════════
   COMPETITOR MODAL
══════════════════════════════════════════ */
.comp-modal {
  background: var(--card);
  border: 1px solid var(--border2);
  border-radius: 20px;
  width: 500px;
  max-width: 95vw;
  max-height: 80vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transform: scale(0.94) translateY(12px);
  transition: transform 0.25s cubic-bezier(.34,1.56,.64,1);
  box-shadow: 0 32px 80px rgba(0,0,0,0.6);
}
.modal-bg.open .comp-modal { transform: scale(1) translateY(0); }

.cm-header {
  background: linear-gradient(135deg, var(--bg3) 0%, var(--bg) 100%);
  padding: 24px 20px;
  text-align: center;
  position: relative;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.cm-close {
  position: absolute; top: 12px; left: 12px;
  width: 30px; height: 30px; border-radius: 50%;
  background: rgba(255,255,255,0.1);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; color: var(--txt2);
  transition: 0.15s;
  border: none;
  cursor: pointer;
}
.cm-close:hover { background: rgba(255,255,255,0.2); color: var(--txt); }

.cm-logo {
  width: 72px; height: 72px; object-fit: contain;
  margin: 0 auto 12px;
  filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
}
.cm-name { font-size: 1.25rem; font-weight: 800; color: var(--txt); }
.cm-sub { font-size: 12px; color: var(--txt2); margin-top: 4px; }

.cm-tabs {
  display: flex;
  background: var(--bg2);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.cm-tab {
  flex: 1;
  padding: 12px;
  text-align: center;
  font-size: 13px; font-weight: 700;
  color: var(--txt3);
  border-bottom: 2px solid transparent;
  transition: 0.15s;
  cursor: pointer;
  background: transparent;
  border: none;
}
.cm-tab:hover { color: var(--txt); }
.cm-tab.active { color: var(--accent); border-bottom-color: var(--accent); }

.cm-body {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  background: var(--bg);
}

.cm-pane { display: none; }
.cm-pane.active { display: block; }

/* Competitor Game Card */
.cg-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--r-sm);
  padding: 10px 14px;
  margin-bottom: 10px;
  display: grid;
  grid-template-columns: 1fr 60px 1fr;
  align-items: center;
  gap: 8px;
  transition: border-color 0.15s;
}
.cg-card:hover { border-color: var(--border2); }
.cg-team { display: flex; align-items: center; gap: 8px; min-width: 0; }
.cg-team.away { flex-direction: row-reverse; }
.cg-team-logo { width: 20px; height: 20px; object-fit: contain; flex-shrink: 0; }
.cg-team-name {
  font-size: 12px; font-weight: 600; color: var(--txt);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.cg-center { text-align: center; }
.cg-score {
  font-size: 13px; font-weight: 800; color: var(--txt);
  direction: ltr; letter-spacing: 1px;
}
.cg-meta {
  font-size: 9px; color: var(--txt3); margin-top: 2px;
  white-space: nowrap;
}
.cg-comp-name {
  font-size: 9px; color: var(--accent); font-weight: 700;
  margin-bottom: 4px; text-align: center;
}

/* ══════════════════════════════════════════
   RESPONSIVE DESIGN (MOBILE APP FEEL)
   ══════════════════════════════════════════ */
@media (max-width: 768px) {
  .app {
    grid-template-columns: 1fr !important;
  }
  
  .sidebar {
    grid-column: 1 / -1;
    display: flex;
  }
  
  .center {
    position: fixed;
    top: 56px;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--bg);
    z-index: 50;
    transform: translateX(100%);
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  .app.match-open .center {
    transform: translateX(0);
  }
  
  .mobile-back-btn {
    display: inline-flex !important;
  }
}

.mobile-back-btn {
  display: none;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--card);
  border: 1px solid var(--border2);
  color: var(--txt);
  font-size: 16px;
  cursor: pointer;
  transition: 0.15s;
}
.mobile-back-btn:hover {
  background: var(--card2);
}

/* ══════════════════════════════════════════
   CALENDAR DATE PICKER MODAL
   ══════════════════════════════════════════ */
.dp-cell {
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
  color: var(--txt2);
}
.dp-cell:hover {
  background: var(--card2);
  color: var(--txt);
}
.dp-cell.active {
  background: var(--accent);
  color: #fff;
  box-shadow: 0 4px 12px var(--accent-glow);
}
.dp-cell.empty {
  cursor: default;
  pointer-events: none;
  opacity: 0.15;
}
.dp-cell.today {
  border: 1px solid var(--accent);
  color: var(--accent);
}

/* ══════════════════════════════════════════
   VISUAL FOOTBALL PITCH LINEUPS
   ══════════════════════════════════════════ */
.pitch-player {
  position: absolute;
  transform: translate(-50%, -50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
  cursor: pointer;
  z-index: 10;
  width: 65px;
}
.pitch-player-img {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: 2px solid var(--accent);
  background: var(--card);
  box-shadow: 0 4px 10px rgba(0,0,0,0.4);
  object-fit: cover;
  transition: transform 0.15s, border-color 0.15s;
}
.pitch-player-no {
  position: absolute;
  top: -4px;
  right: -4px;
  background: var(--accent);
  color: #fff;
  font-size: 8px;
  font-weight: 800;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #fff;
}
.pitch-player-name {
  font-size: 9px;
  font-weight: 700;
  color: #fff;
  background: rgba(0,0,0,0.7);
  padding: 1px 5px;
  border-radius: 8px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  max-width: 65px;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
.pitch-player:hover .pitch-player-img {
  transform: scale(1.15);
  border-color: #fff !important;
}
</style>
</head>
<body>

<div class="app">

  <!-- ── TOPBAR ── -->
  <header class="topbar">
    <div class="topbar-logo">
      <div class="dot"></div>
      ZINOU SCORES
    </div>

    <div class="date-strip">
      <button class="date-nav-btn" onclick="openDatePickerModal()" style="margin-left: 6px; background: rgba(52,179,73,0.15); border: 1px solid rgba(52,179,73,0.3); color: var(--accent); font-size: 14px;">📅</button>
      <button class="date-nav-btn" onclick="shiftDates(-3)">›</button>
      <div id="dateStrip" style="display:flex;gap:4px;"></div>
      <button class="date-nav-btn" onclick="shiftDates(3)">‹</button>
    </div>

    <div class="live-count" id="liveCount" style="display:none">
      <div class="pulse"></div>
      <span id="liveCountNum">0</span> مباشر
    </div>
  </header>

  <!-- ── SIDEBAR ── -->
  <aside class="sidebar">
    <div class="sidebar-filters">
      <button class="filter-btn active-all" id="fAll"  onclick="setFilter('all')">الكل</button>
      <button class="filter-btn" id="fLive" onclick="setFilter('live')">⚡ مباشر</button>
      <button class="filter-btn" id="fSched" onclick="setFilter('sched')">🕐 قادمة</button>
      <button class="filter-btn" id="fEnded" onclick="setFilter('ended')">✓ انتهت</button>
    </div>
    <div class="match-list" id="matchList">
      <div class="spinner-wrap"><div class="spinner"></div></div>
    </div>
  </aside>

  <!-- ── CENTER ── -->
  <main class="center" id="center">
    <div class="empty-state">
      <div class="empty-ball">⚽</div>
      <div class="empty-title">اختر مباراة</div>
      <div class="empty-sub">انقر على أي مباراة في القائمة لعرض تفاصيلها الكاملة</div>
    </div>
  </main>

</div>

<!-- Player Modal -->
<div class="modal-bg" id="playerModal">
  <div class="player-modal">
    <div class="pm-header">
      <button class="pm-close" onclick="closePlayerModal()">✕</button>
      <img class="pm-img" id="pmImg" src="" alt="">
      <div class="pm-name" id="pmName">—</div>
      <div class="pm-team" id="pmTeam">—</div>
    </div>
    <div class="pm-body" id="pmBody"></div>
  </div>
</div>

<!-- Competitor Modal -->
<div class="modal-bg" id="competitorModal">
  <div class="comp-modal">
    <div class="cm-header">
      <button class="cm-close" onclick="closeCompetitorModal()">✕</button>
      <img class="cm-logo" id="cmLogo" src="" alt="">
      <div class="cm-name" id="cmName">—</div>
      <div class="cm-sub" id="cmSub">—</div>
    </div>
    <div class="cm-tabs">
      <button class="cm-tab active" id="cmTabGames" onclick="switchCmTab('games')">المباريات</button>
      <button class="cm-tab" id="cmTabInfo" onclick="switchCmTab('info')">معلومات الفريق</button>
    </div>
    <div class="cm-body">
      <div class="cm-pane active" id="cmp-games">
        <div class="spinner-wrap"><div class="spinner"></div></div>
      </div>
      <div class="cm-pane" id="cmp-info">
        <div class="spinner-wrap"><div class="spinner"></div></div>
      </div>
    </div>
  </div>
</div>

<!-- Date Picker Modal -->
<div class="modal-bg" id="datePickerModal">
  <div class="player-modal" style="width: 360px;">
    <div class="pm-header" style="background: linear-gradient(135deg, var(--bg3) 0%, var(--bg) 100%); border-bottom: 1px solid var(--border);">
      <button class="pm-close" onclick="closeDatePickerModal()">✕</button>
      <div class="pm-name" id="dpMonthYear">شهر سنة</div>
      <div style="display:flex; justify-content:center; gap:24px; margin-top:10px; direction: ltr;">
        <button onclick="changeDpMonth(-1)" style="font-size:18px; color:var(--accent); font-weight:bold; cursor:pointer;">◀</button>
        <button onclick="changeDpMonth(1)" style="font-size:18px; color:var(--accent); font-weight:bold; cursor:pointer;">▶</button>
      </div>
    </div>
    <div class="pm-body" style="padding: 12px;">
      <div style="display:grid; grid-template-columns: repeat(7, 1fr); gap:4px; text-align:center; font-weight:bold; color:var(--txt3); font-size:11px; margin-bottom:8px;">
        <span>أحد</span>
        <span>إثنين</span>
        <span>ثلاثاء</span>
        <span>أربعاء</span>
        <span>خميس</span>
        <span>جمعة</span>
        <span>سبت</span>
      </div>
      <div id="dpGrid" style="display:grid; grid-template-columns: repeat(7, 1fr); gap:6px;"></div>
    </div>
  </div>
</div>

<script>
/* ══════════════════════════════════════════
   CONFIG
══════════════════════════════════════════ */
const API    = '/api/scores';
const CDN    = 'https://imagecache.365scores.com/image/upload';
const teamImg = id => id ? `${CDN}/f_png,w_60,h_60,c_limit/v5/Competitors/${id}` : '';
const compImg = id => id ? `${CDN}/f_png,w_48,h_48,c_limit/v5/Competitions/${id}` : '';
const athImg  = id => id ? `${CDN}/f_png,w_120,h_120,c_limit/v5/Athletes/${id}` : '';

/* ══════════════════════════════════════════
   STATE
══════════════════════════════════════════ */
let selectedDate  = new Date();
let allData       = {};
let currentFilter = 'all';
let activeGameId  = null;
let currentGame   = null;
let members       = [];
let dateOffset    = 0; // how many days the strip is shifted

const MONTHS = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
const DAYS   = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];

/* ══════════════════════════════════════════
   DATE STRIP
══════════════════════════════════════════ */
function buildDateStrip() {
  const strip = document.getElementById('dateStrip');
  strip.innerHTML = '';
  const today = new Date(); today.setHours(0,0,0,0);

  for (let i = dateOffset - 3; i <= dateOffset + 3; i++) {
    const d = new Date(today); d.setDate(today.getDate() + i);
    const isSel = fmtDate(d) === fmtDate(selectedDate);
    let label;
    if (i === 0) label = 'اليوم';
    else if (i === -1) label = 'أمس';
    else if (i === 1) label = 'غداً';
    else label = `${d.getDate()} ${MONTHS[d.getMonth()]}`;

    const btn = document.createElement('button');
    btn.className = 'date-btn' + (isSel ? ' active' : '');
    btn.textContent = label;
    btn.onclick = () => { selectedDate = d; buildDateStrip(); loadMatches(); };
    strip.appendChild(btn);
  }
}
function shiftDates(n) { dateOffset += n; buildDateStrip(); }

/* ══════════════════════════════════════════
   HELPERS
══════════════════════════════════════════ */
function fmtDate(d) {
  return `${String(d.getDate()).padStart(2,'0')}-${String(d.getMonth()+1).padStart(2,'0')}-${d.getFullYear()}`;
}
function fmtTime(iso) {
  if (!iso) return '--:--';
  try {
    const d = new Date(iso);
    if (isNaN(d.getTime())) return '--:--';
    return String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
  } catch(e) { return '--:--'; }
}
function scoreVal(s) {
  if (s === undefined || s === null || s === '' || Number(s) < 0) return '-';
  return s;
}
function getStatus(g) {
  const sg = g.statusGroup;
  const ko = fmtTime(g.startTime);
  if (!sg || sg === 1) return { key: 'sched',    txt: ko };
  if (sg === 2)        return { key: 'live',     txt: g.gameTimeDisplay || (g.gameMinute != null ? `${g.gameMinute}'` : '🔴') };
  if (sg === 3)        return { key: 'halftime', txt: 'استراحة' };
  if (sg === 4)        return { key: 'ended',    txt: 'انتهت' };
  if (sg === 5)        return { key: 'ended',    txt: 'موقوف' };
  return { key: 'sched', txt: ko };
}
function esc(s) { return (s||'').replace(/'/g,"\\'"); }

/* ══════════════════════════════════════════
   FILTER
══════════════════════════════════════════ */
function setFilter(f) {
  currentFilter = f;
  const map = { all: 'fAll', live: 'fLive', sched: 'fSched', ended: 'fEnded' };
  const cls = { all: 'active-all', live: 'active-live', sched: 'active-sched', ended: 'active-ended' };
  ['fAll','fLive','fSched','fEnded'].forEach(id => {
    const btn = document.getElementById(id);
    btn.className = 'filter-btn';
  });
  document.getElementById(map[f]).className = 'filter-btn ' + cls[f];
  renderMatches();
}

function applyFilter(games) {
  if (currentFilter === 'live')  return games.filter(g => g.statusGroup === 2 || g.statusGroup === 3);
  if (currentFilter === 'sched') return games.filter(g => g.statusGroup === 1 || !g.statusGroup);
  if (currentFilter === 'ended') return games.filter(g => g.statusGroup === 4 || g.statusGroup === 5);
  return games;
}

/* ══════════════════════════════════════════
   LOAD
══════════════════════════════════════════ */
async function loadMatches() {
  document.getElementById('matchList').innerHTML = '<div class="spinner-wrap"><div class="spinner"></div></div>';
  try {
    const r = await fetch(`${API}/date/${fmtDate(selectedDate)}`);
    allData = await r.json();
    renderMatches();
    updateLiveCount();
  } catch(e) {
    document.getElementById('matchList').innerHTML = '<div class="spinner-wrap" style="color:var(--txt3)">تعذّر التحميل</div>';
  }
}

function updateLiveCount() {
  const live = (allData.games||[]).filter(g => g.statusGroup === 2 || g.statusGroup === 3);
  const el = document.getElementById('liveCount');
  if (live.length > 0) {
    el.style.display = 'flex';
    document.getElementById('liveCountNum').textContent = live.length;
  } else {
    el.style.display = 'none';
  }
}

/* ══════════════════════════════════════════
   RENDER MATCH LIST
══════════════════════════════════════════ */
function renderMatches() {
  const el = document.getElementById('matchList');
  const comps = allData.competitions || [];
  const games = applyFilter(allData.games || []);

  if (!games.length) {
    el.innerHTML = `<div class="spinner-wrap" style="color:var(--txt3);font-size:13px">
      ${currentFilter !== 'all' ? 'لا توجد مباريات في هذا الفلتر' : 'لا توجد مباريات اليوم'}
    </div>`;
    return;
  }

  // Group by competition, sort by popularityRank
  const groups = {};
  games.forEach(g => {
    const cid = g.competitionId;
    if (!groups[cid]) {
      const c = comps.find(c => c.id === cid) || {};
      groups[cid] = { comp: c, games: [], rank: c.popularityRank || 9999999 };
    }
    groups[cid].games.push(g);
  });

  const sorted = Object.values(groups).sort((a,b) => a.rank - b.rank);

  el.innerHTML = sorted.map(({ comp, games }) => `
    <div>
      <div class="comp-header">
        <img class="comp-logo" src="${compImg(comp.id)}" onerror="this.style.opacity=0" alt="">
        <span class="comp-name">${comp.name || 'بطولة'}</span>
        <span class="comp-count">${games.length}</span>
      </div>
      ${games.map(g => renderMatchCard(g)).join('')}
    </div>
  `).join('');
}

function renderMatchCard(g) {
  const st = getStatus(g);
  const active = g.id === activeGameId;
  const hScore = scoreVal(g.homeCompetitor?.score);
  const aScore = scoreVal(g.awayCompetitor?.score);
  const scoreStr = (hScore === '-' && aScore === '-') ? '-' : `${hScore} - ${aScore}`;

  return `<div class="match-card${active ? ' active' : ''}" id="mc-${g.id}" onclick="openMatch(${g.id})">
    <div class="mc-team" onclick="event.stopPropagation(); openCompetitorModal(${g.homeCompetitor?.id})">
      <img class="mc-team-logo" src="${teamImg(g.homeCompetitor?.id)}" onerror="this.style.opacity=0.2" alt="">
      <span class="mc-team-name">${g.homeCompetitor?.name || '—'}</span>
    </div>
    <div class="mc-center">
      <span class="mc-score">${scoreStr}</span>
      <span class="mc-status ${st.key}">${st.txt}</span>
    </div>
    <div class="mc-team away" onclick="event.stopPropagation(); openCompetitorModal(${g.awayCompetitor?.id})">
      <img class="mc-team-logo" src="${teamImg(g.awayCompetitor?.id)}" onerror="this.style.opacity=0.2" alt="">
      <span class="mc-team-name">${g.awayCompetitor?.name || '—'}</span>
    </div>
  </div>`;
}

/* ══════════════════════════════════════════
   OPEN MATCH
══════════════════════════════════════════ */
async function openMatch(gid) {
  // Update active in list
  activeGameId = gid;
  document.querySelectorAll('.match-card').forEach(c => c.classList.remove('active'));
  const card = document.getElementById(`mc-${gid}`);
  if (card) { card.classList.add('active'); card.scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }

  // Add mobile open state class
  document.querySelector('.app').classList.add('match-open');

  // Show spinner in center
  const center = document.getElementById('center');
  center.innerHTML = '<div class="spinner-wrap" style="padding:80px"><div class="spinner"></div></div>';

  try {
    const r = await fetch(`${API}/match/${gid}`);
    const data = await r.json();
    const game = data.game;
    currentGame = game;
    members = game.members || [];

    renderMatchDetail(game);
    // Lazy load other tabs
    loadStats(gid);
    loadStandings(game.competitionId);
    loadH2H(gid);
  } catch(e) {
    center.innerHTML = '<div class="spinner-wrap" style="color:var(--red)">تعذّر التحميل</div>';
  }
}

function renderMatchDetail(game) {
  const center = document.getElementById('center');
  const st = getStatus(game);
  const comp = (allData.competitions||[]).find(c=>c.id===game.competitionId) || {};
  const hScore = scoreVal(game.homeCompetitor?.score);
  const aScore = scoreVal(game.awayCompetitor?.score);

  let stBadgeClass = st.key;
  if (st.key === 'sched') stBadgeClass = 'sched';
  if (st.key === 'ended') stBadgeClass = 'ended';

  center.innerHTML = `
  <div class="fade-up">
    <div class="mh">
      <div style="display:flex; align-items:center; gap:12px; margin-bottom: 20px;">
        <button class="mobile-back-btn" onclick="closeMatchMobile()">➔</button>
        <div class="mh-badge" style="margin-bottom:0;">
          <img src="${compImg(comp.id)}" onerror="this.style.display='none'" alt="">
          ${comp.name || 'مباراة'}
        </div>
      </div>
      <div class="mh-teams">
        <div class="mh-team" onclick="openCompetitorModal(${game.homeCompetitor?.id})" style="cursor:pointer">
          <img class="mh-team-logo" src="${teamImg(game.homeCompetitor?.id)}" onerror="this.style.opacity=0.3" alt="">
          <div class="mh-team-name">${game.homeCompetitor?.name}</div>
        </div>
        <div class="mh-score-box">
          <div class="mh-status-badge ${stBadgeClass}">${st.txt}</div>
          <div class="mh-score">${hScore} - ${aScore}</div>
          <div class="mh-date">${fmtDate(new Date(game.startTime))} · ${fmtTime(game.startTime)}</div>
        </div>
        <div class="mh-team" onclick="openCompetitorModal(${game.awayCompetitor?.id})" style="cursor:pointer">
          <img class="mh-team-logo" src="${teamImg(game.awayCompetitor?.id)}" onerror="this.style.opacity=0.3" alt="">
          <div class="mh-team-name">${game.awayCompetitor?.name}</div>
        </div>
      </div>
    </div>

    ${buildTvBar(game.tvNetworks)}

    <div class="mh-tabs">
      <button class="mh-tab active" onclick="switchTab('timeline', this)">التفاصيل</button>
      <button class="mh-tab" onclick="switchTab('lineups', this)">التشكيلة</button>
      <button class="mh-tab" onclick="switchTab('stats', this)">الإحصائيات</button>
      <button class="mh-tab" onclick="switchTab('standings', this)">الترتيب</button>
      <button class="mh-tab" onclick="switchTab('h2h', this)">مواجهات سابقة</button>
    </div>

    <div class="tab-content">
      <div class="tab-pane active" id="tab-timeline">${buildTimeline(game)}</div>
      <div class="tab-pane" id="tab-lineups">${buildLineups(game)}</div>
      <div class="tab-pane" id="tab-stats"><div class="spinner-wrap"><div class="spinner"></div></div></div>
      <div class="tab-pane" id="tab-standings"><div class="spinner-wrap"><div class="spinner"></div></div></div>
      <div class="tab-pane" id="tab-h2h"><div class="spinner-wrap"><div class="spinner"></div></div></div>
    </div>
  </div>`;
}

function switchTab(id, btn) {
  document.querySelectorAll('.mh-tab').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById(`tab-${id}`).classList.add('active');
}

/* ── TV BAR ── */
function buildTvBar(nets) {
  if (!nets || !nets.length) return '';
  const chips = nets.map(n => {
    const comm = n.commentator || n.commentators?.[0] || '';
    return `<span class="tv-chip">📺 ${n.name}${comm ? ` · تعليق: ${comm}` : ''}</span>`;
  }).join('');
  return `<div class="tv-bar">${chips}</div>`;
}

/* ── TIMELINE ── */
const EVT = { 1:'⚽', 2:'⚽', 3:'🟨', 4:'🟥', 5:'🔄', 6:'⚽', 7:'❌', 8:'🟥', 9:'⛔' };

function buildTimeline(game) {
  const evts = (game.events || []).sort((a,b) => (a.gameTime||0)-(b.gameTime||0));
  if (!evts.length) return '<div class="tl-empty">لم تقع أحداث بعد</div>';

  let html = '';
  let lastHalf = null;

  evts.forEach(ev => {
    const half = ev.gameTime > 45 ? '⚽ الشوط الثاني' : '⚽ الشوط الأول';
    if (half !== lastHalf) {
      html += `<div class="tl-section-label">${half}</div>`;
      lastHalf = half;
    }

    const p1 = members.find(m => m.id === ev.playerId);
    const p2 = members.find(m => m.id === ev.extraPlayers?.[0]);
    const pName = p1?.name || ev.playerName || '';
    const pId   = p1?.id || ev.playerId;
    const icon  = EVT[ev.eventType?.id] || '📌';
    const isHome = ev.competitorId === game.homeCompetitor?.id;

    const playerHtml = pName ? `
      <div class="tl-player" onclick="openPlayerModal(${pId},'${esc(pName)}',${ev.competitorId})">
        <img class="tl-player-img" src="${athImg(pId)}" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(pName)}&background=1a2035&color=8a92a6&size=60'">
        <div>
          <div class="tl-player-name">${pName}</div>
          ${p2 ? `<div class="tl-assist">${ev.eventType?.id===5?'⬇️':'🅰️'} ${p2.name}</div>` : ''}
        </div>
      </div>` : '';

    html += `<div class="tl-item">
      <div class="tl-home">${isHome ? playerHtml : ''}</div>
      <div class="tl-mid">
        <div class="tl-min">${ev.gameTimeDisplay || ev.gameTime+"'"}</div>
        <div class="tl-icon">${icon}</div>
      </div>
      <div class="tl-away">${!isHome ? playerHtml : ''}</div>
    </div>`;
  });

  return html;
}

/* ── LINEUPS ── */
function buildLineups(game) {
  const homeStarters = (game.homeCompetitor?.lineups?.members || []).filter(l => l.status === 1);
  const homeSubs = (game.homeCompetitor?.lineups?.members || []).filter(l => l.status === 2);
  const awayStarters = (game.awayCompetitor?.lineups?.members || []).filter(l => l.status === 1);
  const awaySubs = (game.awayCompetitor?.lineups?.members || []).filter(l => l.status === 2);
  
  if (!homeStarters.length && !awayStarters.length) {
    return '<div class="tl-empty">التشكيلة غير متوفرة</div>';
  }
  
  const hasPitch = homeStarters.some(l => l.yardFormation) && awayStarters.some(l => l.yardFormation);
  const homeFormation = game.homeCompetitor?.lineups?.formation || '';
  const awayFormation = game.awayCompetitor?.lineups?.formation || '';
  
  let pitchHtml = '';
  if (hasPitch) {
    pitchHtml = `
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding:0 8px;">
        <span style="font-size:12px; font-weight:800; color:var(--txt2);">تشكيلة ${game.homeCompetitor?.name || 'صاحب الأرض'}: <strong style="color:var(--accent);">${homeFormation}</strong></span>
        <span style="font-size:12px; font-weight:800; color:var(--txt2);">${game.awayCompetitor?.name || 'الضيف'}: <strong style="color:var(--blue);">${awayFormation}</strong></span>
      </div>
      
      <!-- Football Pitch visual board -->
      <div class="pitch-container" style="position: relative; width: 100%; max-width: 420px; aspect-ratio: 2/3; background: linear-gradient(135deg, #1b5e20 0%, #0c330e 100%); border-radius: 16px; margin: 0 auto 24px; overflow: hidden; border: 2px solid rgba(255,255,255,0.15); box-shadow: 0 16px 48px rgba(0,0,0,0.55); direction: ltr;">
        <!-- Grass stripes -->
        <div style="position:absolute; inset:0; display:flex; flex-direction:column; pointer-events:none;">
          ${Array.from({length: 10}).map((_, idx) => `
            <div style="flex:1; background:${idx % 2 === 0 ? 'rgba(255,255,255,0.02)' : 'transparent'};"></div>
          `).join('')}
        </div>
        <!-- Center line -->
        <div style="position: absolute; top: 50%; left: 0; right: 0; height: 2px; background: rgba(255,255,255,0.25); pointer-events: none;"></div>
        <!-- Center circle -->
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); border: 2px solid rgba(255,255,255,0.25); border-radius: 50%; width: 80px; height: 80px; pointer-events: none;"></div>
        <!-- Penalty areas -->
        <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); border: 2px solid rgba(255,255,255,0.25); width: 160px; height: 60px; border-top: none; pointer-events: none;"></div>
        <div style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); border: 2px solid rgba(255,255,255,0.25); width: 160px; height: 60px; border-bottom: none; pointer-events: none;"></div>
        
        <!-- Home Players (Bottom Half) -->
        ${homeStarters.map(l => {
          const p = members.find(m => m.id === l.id) || {};
          const pName = p.name || 'لاعب';
          const shortName = p.shortName || pName;
          const pId = p.id;
          const yf = l.yardFormation || { fieldLine: 0, fieldSide: 50 };
          
          // Map line (0-99) to bottom half (53% to 93%)
          const y = 53 + (yf.fieldLine / 99) * 40;
          const x = yf.fieldSide;
          const fb = `https://ui-avatars.com/api/?name=${encodeURIComponent(pName)}&background=1a2035&color=34B349&size=60`;
          
          return `
            <div class="pitch-player" style="left:${x}%; top:${y}%;" onclick="openPlayerModal(${pId}, '${esc(pName)}', ${game.homeCompetitor?.id})">
              <img class="pitch-player-img" src="${athImg(pId)}" onerror="this.onerror=null;this.src='${fb}'">
              <span class="pitch-player-no">${p.jerseyNumber || '-'}</span>
              <span class="pitch-player-name">${shortName}</span>
            </div>
          `;
        }).join('')}
        
        <!-- Away Players (Top Half) -->
        ${awayStarters.map(l => {
          const p = members.find(m => m.id === l.id) || {};
          const pName = p.name || 'لاعب';
          const shortName = p.shortName || pName;
          const pId = p.id;
          const yf = l.yardFormation || { fieldLine: 0, fieldSide: 50 };
          
          // Map line (0-99) to top half (7% to 47%)
          const y = 7 + (yf.fieldLine / 99) * 40;
          const x = 100 - yf.fieldSide; // Mirror horizontally
          const fb = `https://ui-avatars.com/api/?name=${encodeURIComponent(pName)}&background=1a2035&color=3b82f6&size=60`;
          
          return `
            <div class="pitch-player" style="left:${x}%; top:${y}%;" onclick="openPlayerModal(${pId}, '${esc(pName)}', ${game.awayCompetitor?.id})">
              <img class="pitch-player-img" style="border-color:var(--blue);" src="${athImg(pId)}" onerror="this.onerror=null;this.src='${fb}'">
              <span class="pitch-player-no" style="background:var(--blue);">${p.jerseyNumber || '-'}</span>
              <span class="pitch-player-name">${shortName}</span>
            </div>
          `;
        }).join('')}
      </div>
    `;
  }
  
  const renderRosterCol = (list, name, logo, cid) => {
    const rows = list.map(l => {
      const p = members.find(m => m.id === l.id);
      const pName = p?.name || 'لاعب';
      const pId = p?.id;
      const fb = `https://ui-avatars.com/api/?name=${encodeURIComponent(pName)}&background=1a2035&color=34B349&size=60`;
      return `
        <div class="lineup-player" onclick="openPlayerModal(${pId},'${esc(pName)}',${cid})">
          <span class="lp-num">${p?.jerseyNumber || '-'}</span>
          <img class="lp-img" src="${athImg(pId)}" onerror="this.onerror=null;this.src='${fb}'" alt="">
          <span class="lp-name">${pName}</span>
        </div>
      `;
    }).join('');
    
    return `
      <div class="lineup-col">
        <div class="lineup-col-head" onclick="openCompetitorModal(${cid})" style="cursor:pointer">
          <img src="${teamImg(cid)}" onerror="this.style.opacity=0" alt="">
          ${name}
        </div>
        ${rows || '<div class="tl-empty">لا توجد تفاصيل</div>'}
      </div>
    `;
  };
  
  const classicStartersHtml = `
    <div style="font-size:13px; font-weight:800; color:var(--txt2); margin-bottom:10px; padding-right:4px;">الأساسيون</div>
    <div class="lineup-grid" style="margin-bottom:24px;">
      ${renderRosterCol(homeStarters, game.homeCompetitor?.name, game.homeCompetitor?.id, game.homeCompetitor?.id)}
      ${renderRosterCol(awayStarters, game.awayCompetitor?.name, game.awayCompetitor?.id, game.awayCompetitor?.id)}
    </div>
  `;
  
  const subsHtml = (homeSubs.length || awaySubs.length) ? `
    <div style="font-size:13px; font-weight:800; color:var(--txt2); margin-bottom:10px; padding-right:4px;">الاحتياط</div>
    <div class="lineup-grid">
      ${renderRosterCol(homeSubs, game.homeCompetitor?.name, game.homeCompetitor?.id, game.homeCompetitor?.id)}
      ${renderRosterCol(awaySubs, game.awayCompetitor?.name, game.awayCompetitor?.id, game.awayCompetitor?.id)}
    </div>
  ` : '';
  
  return `
    ${pitchHtml}
    ${classicStartersHtml}
    ${subsHtml}
  `;
}

/* ── STATS ── */
async function loadStats(gid) {
  const tab = document.getElementById('tab-stats');
  try {
    const r = await fetch(`${API}/stats/${gid}`);
    const data = await r.json();
    const stats = data.statistics || data.stats || [];
    if (!stats.length) { tab.innerHTML = '<div class="tl-empty">الإحصائيات غير متوفرة</div>'; return; }

    const rows = stats.map(s => {
      const hv = parseFloat(s.homeValue ?? s.home ?? 0);
      const av = parseFloat(s.awayValue ?? s.away ?? 0);
      const tot = hv + av || 1;
      const hp = Math.round((hv/tot)*100);
      const ap = 100 - hp;
      return `<div class="stat-item">
        <div class="stat-labels">
          <span class="stat-val">${hv}${s.name?.includes('%')?'%':''}</span>
          <span class="stat-name">${s.name}</span>
          <span class="stat-val">${av}${s.name?.includes('%')?'%':''}</span>
        </div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-track" style="flex:none;width:calc(50% - 3px);justify-content:flex-end">
            <div class="stat-bar-home" style="width:${hp}%"></div>
          </div>
          <div style="width:6px;text-align:center;font-size:9px;color:var(--txt3)">·</div>
          <div class="stat-bar-track" style="flex:none;width:calc(50% - 3px)">
            <div class="stat-bar-away" style="width:${ap}%"></div>
          </div>
        </div>
      </div>`;
    }).join('');

    tab.innerHTML = `<div style="padding:4px 0">${rows}</div>`;
  } catch(e) {
    tab.innerHTML = '<div class="tl-empty" style="color:var(--red)">تعذّر التحميل</div>';
  }
}

/* ── STANDINGS ── */
async function loadStandings(cid) {
  const tab = document.getElementById('tab-standings');
  try {
    const r = await fetch(`${API}/standings/${cid}`);
    const data = await r.json();
    const rows = data.standings?.[0]?.rows || data.standing?.rows || [];
    if (!rows.length) { tab.innerHTML = '<div class="tl-empty">الترتيب غير متوفر</div>'; return; }

    const hid = currentGame?.homeCompetitor?.id;
    const aid = currentGame?.awayCompetitor?.id;

    tab.innerHTML = `<div style="overflow-x:auto">
    <table class="standings-table">
      <thead><tr>
        <th>#</th><th style="text-align:right;padding-right:12px">الفريق</th>
        <th>لعب</th><th>ف</th><th>ت</th><th>خ</th><th dir="ltr">+/-</th><th>نقاط</th>
      </tr></thead>
      <tbody>${rows.map(row => {
        const isActive = row.competitor?.id === hid || row.competitor?.id === aid;
        return `<tr style="${isActive ? 'background:rgba(52,179,73,0.06)' : ''}">
          <td class="st-pos">${row.position}</td>
          <td><div class="st-team-cell" onclick="openCompetitorModal(${row.competitor?.id})" style="padding-right:8px;cursor:pointer">
            <img src="${teamImg(row.competitor?.id)}" onerror="this.style.opacity=0" alt="">
            <span>${row.competitor?.name}</span>
          </div></td>
          <td>${row.gamesPlayed ?? '-'}</td>
          <td>${row.gamesWon ?? '-'}</td>
          <td>${row.gamesEven ?? '-'}</td>
          <td>${row.gamesLost ?? '-'}</td>
          <td dir="ltr">${row.for??'-'}-${row.against??'-'}</td>
          <td class="st-pts">${row.points ?? '-'}</td>
        </tr>`;
      }).join('')}
      </tbody>
    </table></div>`;
  } catch(e) {
    tab.innerHTML = '<div class="tl-empty" style="color:var(--red)">تعذّر التحميل</div>';
  }
}

/* ── H2H ── */
async function loadH2H(gid) {
  const tab = document.getElementById('tab-h2h');
  try {
    const r = await fetch(`${API}/h2h/${gid}`);
    const data = await r.json();
    const games = data.headToHead?.games || [];
    if (!games.length) { tab.innerHTML = '<div class="h2h-empty">لا توجد مواجهات سابقة</div>'; return; }

    tab.innerHTML = games.map(g => `<div class="h2h-item">
      <div class="h2h-home" onclick="openCompetitorModal(${g.homeCompetitor?.id})" style="cursor:pointer">
        <img src="${teamImg(g.homeCompetitor?.id)}" onerror="this.style.opacity=0" alt="">
        <span>${g.homeCompetitor?.name}</span>
      </div>
      <div>
        <div class="h2h-score">${scoreVal(g.homeCompetitor?.score)} - ${scoreVal(g.awayCompetitor?.score)}</div>
        <div class="h2h-date">${g.startTime ? fmtDate(new Date(g.startTime)) : ''}</div>
      </div>
      <div class="h2h-away" onclick="openCompetitorModal(${g.awayCompetitor?.id})" style="cursor:pointer">
        <img src="${teamImg(g.awayCompetitor?.id)}" onerror="this.style.opacity=0" alt="">
        <span>${g.awayCompetitor?.name}</span>
      </div>
    </div>`).join('');
  } catch(e) {
    tab.innerHTML = '<div class="h2h-empty" style="color:var(--red)">تعذّر التحميل</div>';
  }
}

/* ══════════════════════════════════════════
   PLAYER MODAL
══════════════════════════════════════════ */
function openPlayerModal(pid, name, cid) {
  const validName = (name && name !== 'undefined') ? name : 'لاعب';
  const fb = `https://ui-avatars.com/api/?name=${encodeURIComponent(validName)}&background=1a2035&color=34B349&size=120`;
  const img = document.getElementById('pmImg');
  img.src = pid ? athImg(pid) : fb;
  img.onerror = () => { img.onerror=null; img.src=fb; };
  document.getElementById('pmName').textContent = validName;
  
  const isHome = cid === currentGame?.homeCompetitor?.id;
  const tname = isHome ? currentGame?.homeCompetitor?.name : currentGame?.awayCompetitor?.name;
  document.getElementById('pmTeam').textContent = tname || '';
  
  // Find player stats in lineups
  const homeLineups = currentGame?.homeCompetitor?.lineups?.members || [];
  const awayLineups = currentGame?.awayCompetitor?.lineups?.members || [];
  const pLineup = [...homeLineups, ...awayLineups].find(l => l.id === pid);
  
  let statsHtml = `
    <div class="pm-row"><span class="pm-key">النادي</span><span class="pm-val">${tname||'—'}</span></div>
  `;
  
  if (pLineup) {
    if (pLineup.ranking) {
      statsHtml += `
        <div class="pm-row">
          <span class="pm-key">تقييم اللاعب</span>
          <span class="pm-val" style="color:var(--accent); font-weight:900; background:rgba(52,179,73,0.1); padding:2px 8px; border-radius:4px;">
            ${pLineup.ranking}
          </span>
        </div>
      `;
    }
    
    if (pLineup.stats && pLineup.stats.length) {
      statsHtml += '<div style="margin-top:16px; border-top:1px solid var(--border); padding-top:12px;">';
      statsHtml += '<div style="font-size:11px; font-weight:800; color:var(--txt3); margin-bottom:8px; text-transform:uppercase;">إحصائيات المباراة</div>';
      
      pLineup.stats.forEach(s => {
        if (s.name) {
          statsHtml += `
            <div class="pm-row" style="padding:6px 0; font-size:12px;">
              <span class="pm-key" style="color:var(--txt2);">${s.name}</span>
              <span class="pm-val">${s.value}</span>
            </div>
          `;
        }
      });
      statsHtml += '</div>';
    }
  }
  
  document.getElementById('pmBody').innerHTML = statsHtml;
  document.getElementById('playerModal').classList.add('open');
}

function closePlayerModal() { document.getElementById('playerModal').classList.remove('open'); }
document.getElementById('playerModal').addEventListener('click', e => {
  if (e.target === e.currentTarget) closePlayerModal();
});

/* ══════════════════════════════════════════
   COMPETITOR MODAL & DATE PICKER LOGIC
   ══════════════════════════════════════════ */
let activeCompetitorId = null;

async function openCompetitorModal(cid) {
  if (!cid) return;
  activeCompetitorId = cid;
  
  document.getElementById('cmLogo').src = teamImg(cid);
  document.getElementById('cmName').textContent = 'جاري التحميل...';
  document.getElementById('cmSub').textContent = '—';
  
  document.getElementById('cmp-games').innerHTML = '<div class="spinner-wrap"><div class="spinner"></div></div>';
  document.getElementById('cmp-info').innerHTML = '<div class="spinner-wrap"><div class="spinner"></div></div>';
  
  document.getElementById('competitorModal').classList.add('open');
  switchCmTab('games');
  
  try {
    const rDetails = await fetch(`${API}/competitor/${cid}`);
    const detailsData = await rDetails.json();
    const comp = detailsData.competitors?.[0] || {};
    
    document.getElementById('cmName').textContent = comp.name || '—';
    const country = detailsData.countries?.find(c => c.id === comp.countryId)?.name || '';
    const mainComp = detailsData.competitions?.find(c => c.id === comp.mainCompetitionId)?.name || '';
    document.getElementById('cmSub').textContent = [country, mainComp].filter(Boolean).join(' · ') || '—';
    
    renderCompetitorInfo(comp, detailsData);
    
    const rGames = await fetch(`${API}/competitor/games/${cid}`);
    const gamesData = await rGames.json();
    
    renderCompetitorGames(gamesData, cid);
  } catch (e) {
    console.error(e);
    document.getElementById('cmName').textContent = 'خطأ في التحميل';
    document.getElementById('cmp-games').innerHTML = '<div class="tl-empty" style="color:var(--red)">تعذّر تحميل المباريات</div>';
    document.getElementById('cmp-info').innerHTML = '<div class="tl-empty" style="color:var(--red)">تعذّر تحميل المعلومات</div>';
  }
}

function closeCompetitorModal() {
  document.getElementById('competitorModal').classList.remove('open');
}

document.getElementById('competitorModal').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeCompetitorModal();
});

function switchCmTab(tab) {
  document.querySelectorAll('.cm-tab').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.cm-pane').forEach(p => p.classList.remove('active'));
  
  if (tab === 'games') {
    document.getElementById('cmTabGames').classList.add('active');
    document.getElementById('cmp-games').classList.add('active');
  } else {
    document.getElementById('cmTabInfo').classList.add('active');
    document.getElementById('cmp-info').classList.add('active');
  }
}

function renderCompetitorInfo(comp, data) {
  const container = document.getElementById('cmp-info');
  const country = data.countries?.find(c => c.id === comp.countryId)?.name || '—';
  const mainComp = data.competitions?.find(c => c.id === comp.mainCompetitionId)?.name || '—';
  
  let html = `
    <div style="display:flex; flex-direction:column; gap:12px; padding:10px 0;">
      <div class="pm-row"><span class="pm-key">البلد</span><span class="pm-val">${country}</span></div>
      <div class="pm-row"><span class="pm-key">البطولة الرئيسية</span><span class="pm-val">${mainComp}</span></div>
      ${comp.symbolicName ? `<div class="pm-row"><span class="pm-key">الرمز</span><span class="pm-val">${comp.symbolicName}</span></div>` : ''}
      ${comp.popularityRank ? `<div class="pm-row"><span class="pm-key">التصنيف الشعبي</span><span class="pm-val">#${comp.popularityRank}</span></div>` : ''}
      ${comp.color ? `
        <div class="pm-row">
          <span class="pm-key">لون الفريق</span>
          <span class="pm-val" style="display:inline-flex; align-items:center; gap:6px;">
            <span style="display:inline-block; width:16px; height:16px; border-radius:4px; background:${comp.color}; border:1px solid var(--border2);"></span>
            ${comp.color}
          </span>
        </div>` : ''}
    </div>
  `;
  container.innerHTML = html;
}

function renderCompetitorGames(data, competitorId) {
  const container = document.getElementById('cmp-games');
  const games = data.games || [];
  if (!games.length) {
    container.innerHTML = '<div class="tl-empty">لا توجد مباريات مسجلة</div>';
    return;
  }
  
  games.sort((a, b) => new Date(b.startTime) - new Date(a.startTime));
  
  let html = games.map(g => {
    const isHome = g.homeCompetitor?.id == competitorId;
    const opp = isHome ? g.awayCompetitor : g.homeCompetitor;
    const oppName = opp?.name || '—';
    const compName = g.competitionDisplayName || 'بطولة';
    
    const st = getStatus(g);
    const hScore = scoreVal(g.homeCompetitor?.score);
    const aScore = scoreVal(g.awayCompetitor?.score);
    
    let scoreText = '';
    if (st.key === 'sched') {
      scoreText = fmtTime(g.startTime);
    } else {
      scoreText = `${hScore} - ${aScore}`;
    }
    
    let dateStr = '';
    if (g.startTime) {
      const d = new Date(g.startTime);
      dateStr = `${d.getDate()} ${MONTHS[d.getMonth()]} ${d.getFullYear()}`;
    }
    
    let resultBadge = '';
    if (st.key === 'ended') {
      const myScore = isHome ? g.homeCompetitor?.score : g.awayCompetitor?.score;
      const oppScore = isHome ? g.awayCompetitor?.score : g.homeCompetitor?.score;
      if (myScore > oppScore) {
        resultBadge = '<span style="color:var(--accent); font-size:10px; font-weight:800; background:rgba(52,179,73,0.1); padding:2px 6px; border-radius:4px; margin-right:6px;">فوز</span>';
      } else if (myScore < oppScore) {
        resultBadge = '<span style="color:var(--red); font-size:10px; font-weight:800; background:rgba(239,68,68,0.1); padding:2px 6px; border-radius:4px; margin-right:6px;">خسارة</span>';
      } else {
        resultBadge = '<span style="color:var(--yellow); font-size:10px; font-weight:800; background:rgba(245,158,11,0.1); padding:2px 6px; border-radius:4px; margin-right:6px;">تعادل</span>';
      }
    }
    
    let statusClass = 'sched';
    if (st.key === 'live' || st.key === 'halftime') {
      statusClass = 'live';
    } else if (st.key === 'ended') {
      statusClass = 'ended';
    }
    
    return `
      <div class="cg-card" onclick="closeCompetitorModal(); openMatch(${g.id});" style="cursor:pointer;">
        <div class="cg-team">
          <img class="cg-team-logo" src="${teamImg(g.homeCompetitor?.id)}" onerror="this.style.opacity=0.2" alt="">
          <span class="cg-team-name">${g.homeCompetitor?.name || '—'}</span>
        </div>
        <div class="cg-center">
          <div class="cg-comp-name">${compName}</div>
          <div class="cg-score" style="color:${statusClass==='live' ? 'var(--red)' : 'var(--txt)'}">${scoreText}</div>
          <div class="cg-meta">${dateStr}</div>
          <div style="margin-top:4px;">${resultBadge}</div>
        </div>
        <div class="cg-team away">
          <img class="cg-team-logo" src="${teamImg(g.awayCompetitor?.id)}" onerror="this.style.opacity=0.2" alt="">
          <span class="cg-team-name">${g.awayCompetitor?.name || '—'}</span>
        </div>
      </div>
    `;
  }).join('');
  
  container.innerHTML = html;
}

/* ── DATE PICKER LOGIC ── */
let dpCurrentDate = new Date();

function openDatePickerModal() {
  dpCurrentDate = new Date(selectedDate);
  renderDatePicker();
  document.getElementById('datePickerModal').classList.add('open');
}

function closeDatePickerModal() {
  document.getElementById('datePickerModal').classList.remove('open');
}

document.getElementById('datePickerModal').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeDatePickerModal();
});

function changeDpMonth(offset) {
  dpCurrentDate.setMonth(dpCurrentDate.getMonth() + offset);
  renderDatePicker();
}

function renderDatePicker() {
  const month = dpCurrentDate.getMonth();
  const year = dpCurrentDate.getFullYear();
  
  document.getElementById('dpMonthYear').textContent = `${MONTHS[month]} ${year}`;
  
  const grid = document.getElementById('dpGrid');
  grid.innerHTML = '';
  
  const firstDay = new Date(year, month, 1).getDay();
  const totalDays = new Date(year, month + 1, 0).getDate();
  
  for (let i = 0; i < firstDay; i++) {
    const cell = document.createElement('div');
    cell.className = 'dp-cell empty';
    grid.appendChild(cell);
  }
  
  const today = new Date();
  for (let day = 1; day <= totalDays; day++) {
    const cell = document.createElement('div');
    cell.textContent = day;
    
    const d = new Date(year, month, day);
    const isToday = d.getDate() === today.getDate() && d.getMonth() === today.getMonth() && d.getFullYear() === today.getFullYear();
    const isSelected = d.getDate() === selectedDate.getDate() && d.getMonth() === selectedDate.getMonth() && d.getFullYear() === selectedDate.getFullYear();
    
    cell.className = 'dp-cell';
    if (isToday) cell.classList.add('today');
    if (isSelected) cell.classList.add('active');
    
    cell.onclick = () => {
      selectedDate = d;
      const diffTime = selectedDate.getTime() - today.getTime();
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      dateOffset = diffDays;
      
      buildDateStrip();
      loadMatches();
      closeDatePickerModal();
    };
    
    grid.appendChild(cell);
  }
}

/* ── MOBILE ACTIONS ── */
function closeMatchMobile() {
  document.querySelector('.app').classList.remove('match-open');
}

/* ══════════════════════════════════════════
   INIT + AUTO REFRESH
   ══════════════════════════════════════════ */
buildDateStrip();
loadMatches();
setInterval(() => {
  loadMatches();
  if (activeGameId) openMatch(activeGameId);
}, 45000);</script>
</body>
</html>
