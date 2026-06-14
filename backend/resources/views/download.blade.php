@extends('layouts.app')

@section('title', 'تحميل التطبيق | Zinou TV')

@section('styles')
<style>
  .download-page {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 70vh;
    text-align: center;
  }
  .download-card {
    background: var(--card);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    border: 1px solid var(--border);
    max-width: 500px;
    width: 100%;
  }
  .app-icon {
    width: 100px;
    height: 100px;
    background: var(--nav-bg);
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 40px;
    margin: 0 auto 20px;
    box-shadow: 0 10px 20px rgba(15, 122, 107, 0.3);
  }
  .dl-title {
    font-size: 24px;
    font-weight: 900;
    color: var(--txt);
    margin-bottom: 10px;
  }
  .dl-desc {
    font-size: 14px;
    color: var(--txt2);
    margin-bottom: 30px;
    line-height: 1.6;
  }
  .dl-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--primary);
    color: white;
    font-size: 16px;
    font-weight: 800;
    padding: 14px 30px;
    border-radius: 30px;
    transition: all 0.3s;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(15, 122, 107, 0.2);
  }
  .dl-btn:hover {
    background: var(--primary-d);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15, 122, 107, 0.3);
  }
</style>
@endsection

@section('content')
<div class="download-page">
  <div class="download-card fade-up">
    <div class="app-icon">⚽</div>
    <h1 class="dl-title">تطبيق Zinou TV</h1>
    <p class="dl-desc">
      حمل التطبيق الآن وتابع أحدث نتائج المباريات، الترتيب، الإحصائيات، والأهداف لحظة بلحظة مباشرة على هاتفك.
    </p>
    
    <!-- ضع الرابط الحقيقي لتطبيقك هنا -->
    <a href="#" class="dl-btn">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
        <polyline points="7 10 12 15 17 10"></polyline>
        <line x1="12" y1="15" x2="12" y2="3"></line>
      </svg>
      تحميل البرنامج (APK)
    </a>
  </div>
</div>
@endsection
