import { useState, useEffect } from 'react';
import Login from './Login';
import Providers from './Providers';
import Channels from './Channels';
import Users from './Users';
import AdsSettings from './AdsSettings';
import AppSettings from './AppSettings';
import Movies from './Movies';
import Packages from './Packages';
import Networks from './Networks';
import Prices from './Prices';
import ActivationCodes from './ActivationCodes';
import ProActivation from './ProActivation';
import Subscriptions from './Subscriptions';
import HarAnalyzer from './HarAnalyzer';
import BrokenChannels from './BrokenChannels';
import Notifications from './Notifications';

function App() {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [token, setToken] = useState<string | null>(localStorage.getItem('zinou_token'));

  useEffect(() => {
    if (token) {
      localStorage.setItem('zinou_token', token);
    } else {
      localStorage.removeItem('zinou_token');
    }
  }, [token]);

  const handleLogout = () => setToken(null);

  const [stats, setStats] = useState({ channels: 0, providers: 0, users: 0, installs: 0, active_users: 0 });
  useEffect(() => {
    if (activeTab === 'dashboard' && token) {
      const baseUrl = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8787';
      fetch(`${baseUrl}/api/dashboard`, {
        headers: { 'Authorization': `Bearer ${token}` }
      })
      .then(res => res.json())
      .then(data => setStats(data))
      .catch(console.error);
    }
  }, [activeTab, token]);

  if (!token) {
    return <Login onLogin={setToken} />;
  }

  return (
    <div className="flex h-screen bg-gray-900 text-white font-sans overflow-hidden">
      {/* Sidebar */}
      <aside className="w-64 bg-gray-800 border-r border-gray-700 flex flex-col shadow-lg z-10 relative">
        <div className="p-6 border-b border-gray-700 flex items-center justify-center">
          <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center font-black text-xl mr-3 shadow-lg shadow-cyan-500/30">Z</div>
          <h1 className="text-2xl font-black tracking-tight bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
            Zinou TV
          </h1>
        </div>
        <nav className="flex-1 p-4 flex flex-col gap-2 overflow-y-auto">
          <p className="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-4 px-2">Menu</p>
          <button onClick={() => setActiveTab('dashboard')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'dashboard' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>Dashboard</button>
          <button onClick={() => setActiveTab('channels')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'channels' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>القنوات المباشرة <span className="bg-red-500/20 text-red-400 text-[10px] px-2 py-0.5 rounded-full ml-2 animate-pulse">{stats.channels > 0 ? `${(stats.channels/1000).toFixed(1)}k+` : '0'}</span></button>
          <button onClick={() => setActiveTab('movies')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'movies' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>الأفلام والمسلسلات</button>
          <button onClick={() => setActiveTab('networks')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'networks' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>الشبكات</button>
          <button onClick={() => setActiveTab('packages')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'packages' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>الباقات</button>
          <button onClick={() => setActiveTab('prices')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'prices' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>أسعار الاشتراك والواتساب</button>
          <button onClick={() => setActiveTab('providers')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'providers' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>مزودو الخدمة (XTREAM)</button>
          <button onClick={() => setActiveTab('matches')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'matches' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>المباريات المباشرة</button>
          <button onClick={() => setActiveTab('activation')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'activation' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>رموز التفعيل</button>
          <button onClick={() => setActiveTab('pro')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'pro' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>تفعيل حسابات PRO</button>
          <button onClick={() => setActiveTab('users')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'users' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>المستخدمون والأجهزة</button>
          <button onClick={() => setActiveTab('subscriptions')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'subscriptions' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>الاشتراكات</button>
          <button onClick={() => setActiveTab('har')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'har' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>محلل ملفات HAR</button>
          <button onClick={() => setActiveTab('ads')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'ads' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>إدارة الإعلانات</button>
          <button onClick={() => setActiveTab('broken_channels')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'broken_channels' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>القنوات المعطلة</button>
          <button onClick={() => setActiveTab('notifications')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'notifications' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>إرسال الإشعارات</button>
          <button onClick={() => setActiveTab('settings')} className={`p-3 text-sm font-bold text-left rounded-xl transition-all ${activeTab === 'settings' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white'}`}>إعدادات التطبيق</button>
        </nav>
      </aside>

      {/* Main Content */}
      <main className="flex-1 overflow-y-auto bg-[#0a0f1c]">
        {/* Topbar */}
        <header className="flex justify-between items-center p-8 pb-4 border-b border-gray-800/50 sticky top-0 bg-[#0a0f1c]/80 backdrop-blur-md z-10">
          <div>
            <h2 className="text-3xl font-extrabold capitalize text-white tracking-tight">{activeTab}</h2>
            <p className="text-gray-400 text-sm mt-1">Manage your IPTV resources and metrics</p>
          </div>
          <div className="flex items-center gap-4">
            <div className="bg-gray-800/80 border border-gray-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center shadow-inner">
              <span className="w-2 h-2 rounded-full bg-cyan-400 mr-2 shadow-[0_0_8px_rgba(34,211,238,0.8)]"></span>
              API Connected
            </div>
            <button onClick={handleLogout} className="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 px-4 py-2 rounded-lg text-sm font-medium transition-all">
              Logout
            </button>
          </div>
        </header>

        <div className="p-8">
          {activeTab === 'dashboard' && (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div className="bg-gray-800/40 p-6 rounded-2xl border border-gray-700/50 backdrop-blur-sm relative overflow-hidden group hover:border-cyan-500/30 transition-all shadow-xl">
                <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <h3 className="text-gray-400 font-semibold text-sm uppercase tracking-wider">Total Channels</h3>
                <p className="text-4xl font-black mt-3 text-white">{stats.channels.toLocaleString()}</p>
                <div className="mt-4 flex items-center text-xs font-bold text-emerald-400 bg-emerald-400/10 w-max px-2 py-1 rounded-md">
                  Live sync active
                </div>
              </div>
              <div className="bg-gray-800/40 p-6 rounded-2xl border border-gray-700/50 backdrop-blur-sm relative overflow-hidden group hover:border-purple-500/30 transition-all shadow-xl">
                <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-400 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <h3 className="text-gray-400 font-semibold text-sm uppercase tracking-wider">Active Providers</h3>
                <p className="text-4xl font-black mt-3 text-white">{stats.providers.toLocaleString()}</p>
                <div className="mt-4 flex items-center text-xs font-bold text-gray-400 bg-gray-700 w-max px-2 py-1 rounded-md">
                  All systems operational
                </div>
              </div>
              <div className="bg-gray-800/40 p-6 rounded-2xl border border-gray-700/50 backdrop-blur-sm relative overflow-hidden group hover:border-blue-500/30 transition-all shadow-xl">
                <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <h3 className="text-gray-400 font-semibold text-sm uppercase tracking-wider">Subscribers</h3>
                <p className="text-4xl font-black mt-3 text-white">{stats.users.toLocaleString()}</p>
                <div className="mt-4 flex items-center text-xs font-bold text-gray-400 bg-gray-700 w-max px-2 py-1 rounded-md">
                  Users active
                </div>
              </div>
              <div className="bg-gray-800/40 p-6 rounded-2xl border border-gray-700/50 backdrop-blur-sm relative overflow-hidden group hover:border-orange-500/30 transition-all shadow-xl">
                <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-orange-400 to-red-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <h3 className="text-gray-400 font-semibold text-sm uppercase tracking-wider">التطبيقات المثبتة</h3>
                <p className="text-4xl font-black mt-3 text-white">{stats.installs.toLocaleString()}</p>
                <div className="mt-4 flex items-center text-xs font-bold text-orange-400 bg-orange-500/10 w-max px-2 py-1 rounded-md">
                  App Installs
                </div>
              </div>
              <div className="bg-gray-800/40 p-6 rounded-2xl border border-gray-700/50 backdrop-blur-sm relative overflow-hidden group hover:border-green-500/30 transition-all shadow-xl">
                <div className="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-400 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <h3 className="text-gray-400 font-semibold text-sm uppercase tracking-wider">المستخدمين المتصلين حالياً</h3>
                <p className="text-4xl font-black mt-3 text-white">{stats.active_users.toLocaleString()}</p>
                <div className="mt-4 flex items-center text-xs font-bold text-emerald-400 bg-emerald-500/10 w-max px-2 py-1 rounded-md">
                  Active Today
                </div>
              </div>
            </div>
          )}
          
          {activeTab === 'providers' && <Providers />}
          {activeTab === 'channels' && <Channels />}
          {activeTab === 'users' && <Users />}
          {activeTab === 'ads' && <AdsSettings />}
          {activeTab === 'settings' && <AppSettings />}
          {activeTab === 'movies' && <Movies />}
          {activeTab === 'networks' && <Networks />}
          {activeTab === 'packages' && <Packages />}
          {activeTab === 'prices' && <Prices />}
          {activeTab === 'activation' && <ActivationCodes />}
          {activeTab === 'pro' && <ProActivation />}
          {activeTab === 'subscriptions' && <Subscriptions />}
          {activeTab === 'har' && <HarAnalyzer />}
          {activeTab === 'broken_channels' && <BrokenChannels />}
          {activeTab === 'notifications' && <Notifications />}
          
          {activeTab !== 'dashboard' && activeTab !== 'providers' && activeTab !== 'channels' && activeTab !== 'users' && activeTab !== 'ads' && activeTab !== 'settings' && activeTab !== 'movies' && activeTab !== 'networks' && activeTab !== 'packages' && activeTab !== 'prices' && activeTab !== 'activation' && activeTab !== 'pro' && activeTab !== 'subscriptions' && activeTab !== 'har' && activeTab !== 'broken_channels' && activeTab !== 'notifications' && (
            <div className="flex flex-col items-center justify-center h-64 border-2 border-dashed border-gray-700/50 rounded-2xl bg-gray-800/20">
              <p className="text-gray-400 font-medium">The {activeTab} panel is under construction.</p>
              <p className="text-sm text-gray-500 mt-2">Will be fully functional soon.</p>
            </div>
          )}
        </div>
      </main>
    </div>
  );
}

export default App;
