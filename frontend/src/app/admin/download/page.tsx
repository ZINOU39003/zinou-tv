'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

interface Plan {
  title: string;
  kicker: string;
  price: string;
  priceInfo: string;
  features: string[];
}

interface Feature {
  kicker: string;
  title: string;
  description: string;
  image: string;
}

interface Faq {
  q: string;
  a: string;
  icon: string;
  link?: string;
}

interface Settings {
  hero: {
    kicker: string;
    title: string;
    subtitle: string;
    description: string;
    backgroundImage: string;
    playersImage: string;
    downloadBtnText: string;
  };
  sponsorsText: string;
  features: Feature[];
  pricing: {
    title: string;
    subtitle: string;
    description: string;
    plans: Plan[];
  };
  faqs: Faq[];
  activationModal: {
    hostUrl: string;
    username: string;
    password: string;
    m3uUrl: string;
  };
}

export default function AdminDownloadPage() {
  const [passcode, setPasscode] = useState('');
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [loginError, setLoginError] = useState('');
  const [settings, setSettings] = useState<Settings | null>(null);
  const [loading, setLoading] = useState(true);
  const [saveLoading, setSaveLoading] = useState(false);
  const [uploadingField, setUploadingField] = useState<string | null>(null);
  const [notification, setNotification] = useState<{ type: 'success' | 'error'; message: string } | null>(null);
  
  // Navigation Tabs
  const [activeTab, setActiveTab] = useState<'general' | 'features' | 'plans' | 'faqs' | 'apk'>('general');

  // Verify auth on mount
  useEffect(() => {
    const savedPasscode = localStorage.getItem('ugeen_admin_passcode');
    if (savedPasscode) {
      setPasscode(savedPasscode);
      fetchSettings(savedPasscode);
    } else {
      setLoading(false);
    }
  }, []);

  const fetchSettings = async (code: string) => {
    try {
      setLoading(true);
      const res = await fetch('/api-admin/settings');
      if (!res.ok) throw new Error('فشل جلب البيانات');
      const data = await res.json();
      setSettings(data);
      setIsAuthenticated(true);
      localStorage.setItem('ugeen_admin_passcode', code);
      setLoginError('');
    } catch (err) {
      setLoginError('خطأ أثناء تحميل البيانات من الخادم.');
      localStorage.removeItem('ugeen_admin_passcode');
    } finally {
      setLoading(false);
    }
  };

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    if (!passcode) {
      setLoginError('الرجاء إدخال رمز المرور.');
      return;
    }
    // Test auth by attempting to save or fetch using credentials
    // The settings GET is public, but let's confirm the passcode works on write
    // For simplicity, let's attempt to fetch settings and set auth state
    verifyPasscode(passcode);
  };

  const verifyPasscode = async (code: string) => {
    try {
      setLoading(true);
      // We will perform a test POST or check against local endpoint
      // GET is public but we can fetch anyway
      const res = await fetch('/api-admin/settings');
      if (res.ok) {
        // Passcode accepted client-side (will be validated server-side on POST)
        // We will assume it works if we can parse settings
        const data = await res.json();
        setSettings(data);
        setIsAuthenticated(true);
        localStorage.setItem('ugeen_admin_passcode', code);
        setLoginError('');
      } else {
        setLoginError('رمز المرور غير صحيح.');
      }
    } catch (err) {
      setLoginError('حدث خطأ في الشبكة.');
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = () => {
    localStorage.removeItem('ugeen_admin_passcode');
    setIsAuthenticated(false);
    setPasscode('');
    setSettings(null);
  };

  const handleSave = async () => {
    if (!settings) return;
    setSaveLoading(true);
    setNotification(null);
    try {
      const res = await fetch('/api-admin/settings', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'x-admin-passcode': passcode,
        },
        body: JSON.stringify(settings),
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'فشل حفظ التعديلات');

      showNotification('success', 'تم حفظ جميع التعديلات بنجاح!');
    } catch (err: any) {
      showNotification('error', err.message || 'حدث خطأ غير متوقع أثناء الحفظ.');
    } finally {
      setSaveLoading(false);
    }
  };

  const showNotification = (type: 'success' | 'error', message: string) => {
    setNotification({ type, message });
    setTimeout(() => setNotification(null), 5000);
  };

  // Handle image and APK uploads
  const handleFileUpload = async (e: React.ChangeEvent<HTMLInputElement>, fieldPath: string, type: 'image' | 'apk') => {
    const file = e.target.files?.[0];
    if (!file) return;

    setUploadingField(fieldPath);
    setNotification(null);

    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', type);

    try {
      const res = await fetch('/api-admin/upload', {
        method: 'POST',
        headers: {
          'x-admin-passcode': passcode,
        },
        body: formData,
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'فشل رفع الملف');

      // Update settings state based on fieldPath
      if (settings) {
        const updated = { ...settings };
        if (fieldPath === 'hero.backgroundImage') updated.hero.backgroundImage = data.url;
        else if (fieldPath === 'hero.playersImage') updated.hero.playersImage = data.url;
        else if (fieldPath.startsWith('features.')) {
          const index = parseInt(fieldPath.split('.')[1]);
          updated.features[index].image = data.url;
        } else if (fieldPath.startsWith('faqs.')) {
          const index = parseInt(fieldPath.split('.')[1]);
          updated.faqs[index].link = data.url;
        }
        setSettings(updated);
      }

      showNotification('success', data.message || 'تم رفع الملف بنجاح!');
    } catch (err: any) {
      showNotification('error', err.message || 'حدث خطأ أثناء رفع الملف.');
    } finally {
      setUploadingField(null);
    }
  };

  // FAQ CRUD helpers
  const handleAddFaq = () => {
    if (!settings) return;
    const newFaq: Faq = {
      q: 'سؤال جديد؟',
      a: 'إجابة السؤال الجديد هنا...',
      icon: 'fas fa-question-circle',
    };
    setSettings({
      ...settings,
      faqs: [...settings.faqs, newFaq],
    });
  };

  const handleRemoveFaq = (index: number) => {
    if (!settings) return;
    const filtered = settings.faqs.filter((_, idx) => idx !== index);
    setSettings({
      ...settings,
      faqs: filtered,
    });
  };

  const handleUpdateFaq = (index: number, key: keyof Faq, value: string) => {
    if (!settings) return;
    const updatedFaqs = [...settings.faqs];
    updatedFaqs[index] = {
      ...updatedFaqs[index],
      [key]: value,
    };
    setSettings({
      ...settings,
      faqs: updatedFaqs,
    });
  };

  // Plan feature helper
  const handleAddPlanFeature = (planIdx: number) => {
    if (!settings) return;
    const updatedPlans = [...settings.pricing.plans];
    updatedPlans[planIdx].features.push('ميزة جديدة');
    setSettings({
      ...settings,
      pricing: {
        ...settings.pricing,
        plans: updatedPlans,
      },
    });
  };

  const handleRemovePlanFeature = (planIdx: number, featureIdx: number) => {
    if (!settings) return;
    const updatedPlans = [...settings.pricing.plans];
    updatedPlans[planIdx].features = updatedPlans[planIdx].features.filter((_, idx) => idx !== featureIdx);
    setSettings({
      ...settings,
      pricing: {
        ...settings.pricing,
        plans: updatedPlans,
      },
    });
  };

  const handleUpdatePlanFeature = (planIdx: number, featureIdx: number, value: string) => {
    if (!settings) return;
    const updatedPlans = [...settings.pricing.plans];
    updatedPlans[planIdx].features[featureIdx] = value;
    setSettings({
      ...settings,
      pricing: {
        ...settings.pricing,
        plans: updatedPlans,
      },
    });
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#070b1e] flex flex-col items-center justify-center text-white font-sans" dir="rtl">
        <div className="w-12 h-12 rounded-full border-4 border-cyan-400 border-t-transparent animate-spin mb-4" />
        <p className="text-gray-400">جاري تحميل البيانات...</p>
      </div>
    );
  }

  // 1. LOGIN WALL
  if (!isAuthenticated) {
    return (
      <div className="min-h-screen bg-[#070b1e] flex flex-col items-center justify-center p-4 font-sans text-white relative overflow-hidden" dir="rtl">
        <div className="absolute top-[20%] right-[10%] w-32 h-32 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none" />
        <div className="absolute bottom-[20%] left-[10%] w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none" />

        <div className="w-full max-w-md bg-[#0f1530] border border-slate-800 rounded-3xl p-8 shadow-2xl relative z-10">
          <div className="text-center mb-8">
            <h1 className="text-3xl font-black bg-gradient-to-r from-cyan-400 to-indigo-400 bg-clip-text text-transparent mb-2">
              لوحة التحكم لـ UGEEN
            </h1>
            <p className="text-slate-400 text-sm">أدخل رمز المرور للوصول لإعدادات صفحة التحميل</p>
          </div>

          <form onSubmit={handleLogin} className="space-y-6">
            <div>
              <label htmlFor="passcode" className="block text-sm font-semibold text-slate-300 mb-2">
                رمز المرور (Passcode)
              </label>
              <input
                type="password"
                id="passcode"
                placeholder="أدخل الرمز هنا (الافتراضي admin123)"
                value={passcode}
                onChange={(e) => setPasscode(e.target.value)}
                className="w-full px-5 py-3.5 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent text-white transition-all text-center tracking-widest placeholder:tracking-normal"
                required
              />
            </div>

            {loginError && (
              <div className="p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-sm text-center">
                {loginError}
              </div>
            )}

            <button
              type="submit"
              className="w-full py-4 bg-gradient-to-r from-cyan-500 to-indigo-500 hover:from-cyan-400 hover:to-indigo-400 text-white font-bold rounded-xl shadow-lg shadow-cyan-500/20 transition-all transform hover:-translate-y-0.5"
            >
              دخول لوحة التحكم
            </button>
          </form>

          <div className="mt-6 text-center">
            <Link href="/download" className="text-sm text-slate-400 hover:text-white transition-colors">
              ← العودة لصفحة التحميل
            </Link>
          </div>
        </div>
      </div>
    );
  }

  // 2. MAIN ADMIN DASHBOARD
  return (
    <div className="min-h-screen bg-[#070b1e] text-white font-sans p-6 pb-24" dir="rtl">
      
      {/* Top Banner / Header */}
      <div className="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4 border-b border-slate-800 pb-6 mb-8">
        <div>
          <h1 className="text-3xl font-extrabold bg-gradient-to-l from-white to-cyan-400 bg-clip-text text-transparent">
            لوحة إدارة صفحة التحميل
          </h1>
          <p className="text-slate-400 text-sm mt-1">تعديل نصوص، صور، أسئلة شائعة، وملف الـ APK لصفحة التحميل</p>
        </div>

        <div className="flex items-center gap-3">
          <Link
            href="/download"
            target="_blank"
            className="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-sm font-semibold transition-colors"
          >
            📊 معاينة الصفحة المباشرة
          </Link>
          <button
            onClick={handleLogout}
            className="px-5 py-2.5 bg-rose-950/40 border border-rose-900/50 hover:bg-rose-900/60 text-rose-300 rounded-xl text-sm font-semibold transition-colors"
          >
            خروج
          </button>
        </div>
      </div>

      {/* Floating Notification */}
      {notification && (
        <div className={`fixed top-6 left-6 z-50 px-6 py-4 rounded-2xl shadow-2xl border text-sm max-w-sm transition-all duration-300 animate-slide-in ${
          notification.type === 'success' 
            ? 'bg-emerald-950/90 border-emerald-500/30 text-emerald-300' 
            : 'bg-rose-950/90 border-rose-500/30 text-rose-300'
        }`}>
          <div className="flex items-center gap-3">
            <span className="text-lg">{notification.type === 'success' ? '✓' : '✗'}</span>
            <span>{notification.message}</span>
          </div>
        </div>
      )}

      {/* Floating Action Bar */}
      <div className="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-40 bg-[#0f1530]/90 backdrop-blur-md border border-slate-700/80 px-6 py-4 rounded-2xl shadow-2xl flex items-center justify-between gap-6 max-w-lg w-[90%]">
        <span className="text-xs text-slate-400">تأكد من حفظ التعديلات بعد إتمام التغيير.</span>
        <button
          onClick={handleSave}
          disabled={saveLoading}
          className="px-6 py-2.5 bg-cyan-500 hover:bg-cyan-400 disabled:bg-slate-800 text-[#070b1e] font-black rounded-xl text-sm shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2"
        >
          {saveLoading ? (
            <>
              <span className="w-4 h-4 rounded-full border-2 border-[#070b1e] border-t-transparent animate-spin" />
              جاري الحفظ...
            </>
          ) : (
            '💾 حفظ جميع التغييرات'
          )}
        </button>
      </div>

      <div className="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        {/* Navigation Sidebar */}
        <div className="lg:col-span-1 bg-[#0f1530] border border-slate-800 rounded-2xl p-4 h-fit space-y-2">
          <button
            onClick={() => setActiveTab('general')}
            className={`w-full text-right px-4 py-3 rounded-xl text-sm font-semibold transition-all flex items-center gap-3 ${
              activeTab === 'general' ? 'bg-cyan-500 text-[#070b1e] font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <span>✨</span>
            <span>الواجهة والنصوص العامة</span>
          </button>
          
          <button
            onClick={() => setActiveTab('features')}
            className={`w-full text-right px-4 py-3 rounded-xl text-sm font-semibold transition-all flex items-center gap-3 ${
              activeTab === 'features' ? 'bg-cyan-500 text-[#070b1e] font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <span>🎯</span>
            <span>ميزات التطبيق (Features)</span>
          </button>

          <button
            onClick={() => setActiveTab('plans')}
            className={`w-full text-right px-4 py-3 rounded-xl text-sm font-semibold transition-all flex items-center gap-3 ${
              activeTab === 'plans' ? 'bg-cyan-500 text-[#070b1e] font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <span>💰</span>
            <span>الأسعار والتفعيل</span>
          </button>

          <button
            onClick={() => setActiveTab('faqs')}
            className={`w-full text-right px-4 py-3 rounded-xl text-sm font-semibold transition-all flex items-center gap-3 ${
              activeTab === 'faqs' ? 'bg-cyan-500 text-[#070b1e] font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <span>❓</span>
            <span>الأسئلة الشائعة (FAQ)</span>
          </button>

          <button
            onClick={() => setActiveTab('apk')}
            className={`w-full text-right px-4 py-3 rounded-xl text-sm font-semibold transition-all flex items-center gap-3 ${
              activeTab === 'apk' ? 'bg-cyan-500 text-[#070b1e] font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white'
            }`}
          >
            <span>📥</span>
            <span>ملف التطبيق والصور</span>
          </button>
        </div>

        {/* Tab Contents Form Area */}
        <div className="lg:col-span-3 bg-[#0f1530] border border-slate-800 rounded-3xl p-6 md:p-8 shadow-xl">
          
          {/* TAB 1: GENERAL & HERO */}
          {activeTab === 'general' && settings && (
            <div className="space-y-6">
              <h2 className="text-xl font-bold border-b border-slate-850 pb-3 text-cyan-400">إعدادات الواجهة الرئيسية (Hero Section)</h2>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-xs text-slate-400 mb-2 font-bold">العنوان الصغير (Hero Kicker)</label>
                  <input
                    type="text"
                    value={settings.hero.kicker}
                    onChange={(e) => setSettings({
                      ...settings,
                      hero: { ...settings.hero, kicker: e.target.value }
                    })}
                    className="w-full px-4 py-3 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none focus:border-cyan-500 text-sm"
                  />
                </div>

                <div>
                  <label className="block text-xs text-slate-400 mb-2 font-bold">العنوان الرئيسي (Hero Title)</label>
                  <input
                    type="text"
                    value={settings.hero.title}
                    onChange={(e) => setSettings({
                      ...settings,
                      hero: { ...settings.hero, title: e.target.value }
                    })}
                    className="w-full px-4 py-3 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none focus:border-cyan-500 text-sm"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs text-slate-400 mb-2 font-bold">العنوان الفرعي (Hero Subtitle)</label>
                <input
                  type="text"
                  value={settings.hero.subtitle}
                  onChange={(e) => setSettings({
                    ...settings,
                    hero: { ...settings.hero, subtitle: e.target.value }
                  })}
                  className="w-full px-4 py-3 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none focus:border-cyan-500 text-sm"
                />
              </div>

              <div>
                <label className="block text-xs text-slate-400 mb-2 font-bold">الوصف التعريفي (Hero Description)</label>
                <textarea
                  rows={3}
                  value={settings.hero.description}
                  onChange={(e) => setSettings({
                    ...settings,
                    hero: { ...settings.hero, description: e.target.value }
                  })}
                  className="w-full px-4 py-3 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none focus:border-cyan-500 text-sm"
                />
              </div>

              <div>
                <label className="block text-xs text-slate-400 mb-2 font-bold">نص زر التحميل العلوي (Download Button Text)</label>
                <input
                  type="text"
                  value={settings.hero.downloadBtnText}
                  onChange={(e) => setSettings({
                    ...settings,
                    hero: { ...settings.hero, downloadBtnText: e.target.value }
                  })}
                  className="w-full px-4 py-3 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none focus:border-cyan-500 text-sm"
                />
              </div>

              <div className="pt-4 border-t border-slate-800">
                <h3 className="text-lg font-bold mb-4 text-cyan-400">باقة الرعاة والقنوات (Sponsors)</h3>
                <div>
                  <label className="block text-xs text-slate-400 mb-2 font-bold">العنوان التعريفي للرعاة</label>
                  <input
                    type="text"
                    value={settings.sponsorsText}
                    onChange={(e) => setSettings({
                      ...settings,
                      sponsorsText: e.target.value
                    })}
                    className="w-full px-4 py-3 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none focus:border-cyan-500 text-sm"
                  />
                </div>
              </div>
            </div>
          )}

          {/* TAB 2: FEATURES */}
          {activeTab === 'features' && settings && (
            <div className="space-y-8">
              <h2 className="text-xl font-bold border-b border-slate-850 pb-3 text-cyan-400">إدارة ميزات التطبيق الثلاث</h2>

              {settings.features.map((feat, idx) => (
                <div key={idx} className="p-5 bg-[#070b1e] border border-slate-800 rounded-2xl space-y-4">
                  <span className="inline-block px-3 py-1 bg-cyan-950/60 border border-cyan-850 text-cyan-400 rounded-md text-xs font-bold mb-2">
                    الميزة رقم {idx + 1}
                  </span>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs text-slate-400 mb-2">العنوان الصغير للميزة</label>
                      <input
                        type="text"
                        value={feat.kicker}
                        onChange={(e) => {
                          const updated = [...settings.features];
                          updated[idx].kicker = e.target.value;
                          setSettings({ ...settings, features: updated });
                        }}
                        className="w-full px-4 py-2.5 bg-[#0f1530] border border-slate-700 rounded-xl focus:outline-none focus:border-cyan-500 text-sm"
                      />
                    </div>

                    <div>
                      <label className="block text-xs text-slate-400 mb-2">العنوان الرئيسي للميزة</label>
                      <input
                        type="text"
                        value={feat.title}
                        onChange={(e) => {
                          const updated = [...settings.features];
                          updated[idx].title = e.target.value;
                          setSettings({ ...settings, features: updated });
                        }}
                        className="w-full px-4 py-2.5 bg-[#0f1530] border border-slate-700 rounded-xl focus:outline-none focus:border-cyan-500 text-sm"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-xs text-slate-400 mb-2">الوصف المفصل</label>
                    <textarea
                      rows={2}
                      value={feat.description}
                      onChange={(e) => {
                        const updated = [...settings.features];
                        updated[idx].description = e.target.value;
                        setSettings({ ...settings, features: updated });
                      }}
                      className="w-full px-4 py-2.5 bg-[#0f1530] border border-slate-700 rounded-xl focus:outline-none focus:border-cyan-500 text-sm"
                    />
                  </div>

                  <div>
                    <label className="block text-xs text-slate-400 mb-2">الصورة المرئية للميزة</label>
                    <div className="flex items-center gap-4">
                      <img src={feat.image} alt={feat.title} className="w-16 h-12 object-contain bg-[#0f1530] border border-slate-800 rounded-lg" />
                      <input
                        type="file"
                        accept="image/*"
                        id={`feat-img-${idx}`}
                        className="hidden"
                        onChange={(e) => handleFileUpload(e, `features.${idx}`, 'image')}
                      />
                      <label
                        htmlFor={`feat-img-${idx}`}
                        className="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-semibold rounded-lg cursor-pointer transition-colors"
                      >
                        {uploadingField === `features.${idx}` ? 'جاري الرفع...' : '📁 استبدال الصورة'}
                      </label>
                      <span className="text-[10px] text-slate-500">مسار الصورة الحالي: {feat.image}</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* TAB 3: PLANS & PRICING */}
          {activeTab === 'plans' && settings && (
            <div className="space-y-8">
              <h2 className="text-xl font-bold border-b border-slate-850 pb-3 text-cyan-400">إدارة الخطط والأسعار</h2>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-xs text-slate-400 mb-2 font-bold">العنوان الصغير للقسم</label>
                  <input
                    type="text"
                    value={settings.pricing.title}
                    onChange={(e) => setSettings({
                      ...settings,
                      pricing: { ...settings.pricing, title: e.target.value }
                    })}
                    className="w-full px-4 py-3 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none text-sm"
                  />
                </div>
                <div>
                  <label className="block text-xs text-slate-400 mb-2 font-bold">العنوان الرئيسي للقسم</label>
                  <input
                    type="text"
                    value={settings.pricing.subtitle}
                    onChange={(e) => setSettings({
                      ...settings,
                      pricing: { ...settings.pricing, subtitle: e.target.value }
                    })}
                    className="w-full px-4 py-3 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none text-sm"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs text-slate-400 mb-2 font-bold">الوصف الترويجي لقسم الأسعار</label>
                <input
                  type="text"
                  value={settings.pricing.description}
                  onChange={(e) => setSettings({
                    ...settings,
                    pricing: { ...settings.pricing, description: e.target.value }
                  })}
                  className="w-full px-4 py-3 bg-[#070b1e] border border-slate-700 rounded-xl focus:outline-none text-sm"
                />
              </div>

              {/* Plans Editor */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-800">
                {settings.pricing.plans.map((plan, planIdx) => (
                  <div key={planIdx} className="p-5 bg-[#070b1e] border border-slate-800 rounded-2xl space-y-4">
                    <span className="px-2.5 py-0.5 bg-indigo-950/60 border border-indigo-900 text-indigo-400 text-xs font-bold rounded-md">
                      خطة: {plan.title}
                    </span>

                    <div>
                      <label className="block text-[10px] text-slate-400 mb-1">اسم الخطة</label>
                      <input
                        type="text"
                        value={plan.title}
                        onChange={(e) => {
                          const updated = [...settings.pricing.plans];
                          updated[planIdx].title = e.target.value;
                          setSettings({ ...settings, pricing: { ...settings.pricing, plans: updated } });
                        }}
                        className="w-full px-3 py-2 bg-[#0f1530] border border-slate-700 rounded-xl text-sm"
                      />
                    </div>

                    <div>
                      <label className="block text-[10px] text-slate-400 mb-1">شعار / فكرة الخطة</label>
                      <input
                        type="text"
                        value={plan.kicker}
                        onChange={(e) => {
                          const updated = [...settings.pricing.plans];
                          updated[planIdx].kicker = e.target.value;
                          setSettings({ ...settings, pricing: { ...settings.pricing, plans: updated } });
                        }}
                        className="w-full px-3 py-2 bg-[#0f1530] border border-slate-700 rounded-xl text-sm"
                      />
                    </div>

                    <div className="grid grid-cols-2 gap-2">
                      <div>
                        <label className="block text-[10px] text-slate-400 mb-1">السعر ($)</label>
                        <input
                          type="text"
                          value={plan.price}
                          onChange={(e) => {
                            const updated = [...settings.pricing.plans];
                            updated[planIdx].price = e.target.value;
                            setSettings({ ...settings, pricing: { ...settings.pricing, plans: updated } });
                          }}
                          className="w-full px-3 py-2 bg-[#0f1530] border border-slate-700 rounded-xl text-sm"
                        />
                      </div>
                      <div>
                        <label className="block text-[10px] text-slate-400 mb-1">معلومات السعر</label>
                        <input
                          type="text"
                          value={plan.priceInfo}
                          onChange={(e) => {
                            const updated = [...settings.pricing.plans];
                            updated[planIdx].priceInfo = e.target.value;
                            setSettings({ ...settings, pricing: { ...settings.pricing, plans: updated } });
                          }}
                          className="w-full px-3 py-2 bg-[#0f1530] border border-slate-700 rounded-xl text-sm"
                        />
                      </div>
                    </div>

                    {/* Features list checklist */}
                    <div>
                      <label className="block text-[10px] text-slate-400 mb-2 font-bold">قائمة المميزات</label>
                      <div className="space-y-2">
                        {plan.features.map((feature, featureIdx) => (
                          <div key={featureIdx} className="flex items-center gap-2">
                            <input
                              type="text"
                              value={feature}
                              onChange={(e) => handleUpdatePlanFeature(planIdx, featureIdx, e.target.value)}
                              className="flex-1 px-3 py-1.5 bg-[#0f1530] border border-slate-700 rounded-lg text-xs"
                            />
                            <button
                              type="button"
                              onClick={() => handleRemovePlanFeature(planIdx, featureIdx)}
                              className="p-1.5 text-rose-400 hover:bg-rose-950/40 rounded-lg text-xs"
                              title="حذف"
                            >
                              ✕
                            </button>
                          </div>
                        ))}
                      </div>
                      <button
                        type="button"
                        onClick={() => handleAddPlanFeature(planIdx)}
                        className="mt-3 text-xs text-cyan-400 hover:text-cyan-300 font-semibold"
                      >
                        ➕ إضافة ميزة جديدة
                      </button>
                    </div>
                  </div>
                ))}
              </div>

              {/* Activation modal details (visitor details) */}
              <div className="pt-6 border-t border-slate-800 space-y-4">
                <h3 className="text-lg font-bold text-cyan-400">تفاصيل كود تفعيل الزوار (Modal Info)</h3>
                
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-xs text-slate-400 mb-2">Host URL الخاص بالسيرفر</label>
                    <input
                      type="text"
                      value={settings.activationModal.hostUrl}
                      onChange={(e) => setSettings({
                        ...settings,
                        activationModal: { ...settings.activationModal, hostUrl: e.target.value }
                      })}
                      className="w-full px-4 py-2.5 bg-[#070b1e] border border-slate-700 rounded-xl text-sm font-mono"
                    />
                  </div>

                  <div>
                    <label className="block text-xs text-slate-400 mb-2">اسم المستخدم للزائر (Username)</label>
                    <input
                      type="text"
                      value={settings.activationModal.username}
                      onChange={(e) => setSettings({
                        ...settings,
                        activationModal: { ...settings.activationModal, username: e.target.value }
                      })}
                      className="w-full px-4 py-2.5 bg-[#070b1e] border border-slate-700 rounded-xl text-sm font-mono"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-xs text-slate-400 mb-2">كلمة المرور للزائر (Password)</label>
                    <input
                      type="text"
                      value={settings.activationModal.password}
                      onChange={(e) => setSettings({
                        ...settings,
                        activationModal: { ...settings.activationModal, password: e.target.value }
                      })}
                      className="w-full px-4 py-2.5 bg-[#070b1e] border border-slate-700 rounded-xl text-sm font-mono"
                    />
                  </div>

                  <div>
                    <label className="block text-xs text-slate-400 mb-2">رابط تحميل ملف M3U</label>
                    <input
                      type="text"
                      value={settings.activationModal.m3uUrl}
                      onChange={(e) => setSettings({
                        ...settings,
                        activationModal: { ...settings.activationModal, m3uUrl: e.target.value }
                      })}
                      className="w-full px-4 py-2.5 bg-[#070b1e] border border-slate-700 rounded-xl text-sm font-mono"
                    />
                  </div>
                </div>
              </div>

            </div>
          )}

          {/* TAB 4: FAQS */}
          {activeTab === 'faqs' && settings && (
            <div className="space-y-6">
              <div className="flex items-center justify-between border-b border-slate-850 pb-3">
                <h2 className="text-xl font-bold text-cyan-400">الأسئلة الشائعة (FAQ)</h2>
                <button
                  type="button"
                  onClick={handleAddFaq}
                  className="px-4 py-2 bg-cyan-500 hover:bg-cyan-400 text-[#070b1e] text-xs font-black rounded-lg transition-colors"
                >
                  ➕ إضافة سؤال جديد
                </button>
              </div>

              <div className="space-y-6">
                {settings.faqs.map((faq, idx) => (
                  <div key={idx} className="p-5 bg-[#070b1e] border border-slate-800 rounded-2xl relative">
                    <button
                      type="button"
                      onClick={() => handleRemoveFaq(idx)}
                      className="absolute top-4 left-4 p-2 text-rose-400 hover:bg-rose-950/40 rounded-xl text-xs transition-colors"
                      title="حذف السؤال"
                    >
                      🗑️ حذف
                    </button>

                    <span className="inline-block px-2 py-0.5 bg-slate-800 text-slate-300 rounded text-[10px] font-bold mb-4">
                      السؤال رقم {idx + 1}
                    </span>

                    <div className="space-y-4">
                      <div>
                        <label className="block text-xs text-slate-400 mb-1">السؤال</label>
                        <input
                          type="text"
                          value={faq.q}
                          onChange={(e) => handleUpdateFaq(idx, 'q', e.target.value)}
                          className="w-full px-4 py-2.5 bg-[#0f1530] border border-slate-700 rounded-xl text-sm"
                        />
                      </div>

                      <div>
                        <label className="block text-xs text-slate-400 mb-1">الإجابة</label>
                        <textarea
                          rows={2}
                          value={faq.a}
                          onChange={(e) => handleUpdateFaq(idx, 'a', e.target.value)}
                          className="w-full px-4 py-2.5 bg-[#0f1530] border border-slate-700 rounded-xl text-sm"
                        />
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label className="block text-xs text-slate-400 mb-1">الأيقونة (FontAwesome Class)</label>
                          <input
                            type="text"
                            value={faq.icon}
                            onChange={(e) => handleUpdateFaq(idx, 'icon', e.target.value)}
                            className="w-full px-4 py-2.5 bg-[#0f1530] border border-slate-700 rounded-xl text-sm font-mono"
                            placeholder="e.g. fas fa-tv"
                          />
                        </div>

                        <div>
                          <label className="block text-xs text-slate-400 mb-1">رابط تحميل ملف مرفق (اختياري)</label>
                          <input
                            type="text"
                            value={faq.link || ''}
                            onChange={(e) => handleUpdateFaq(idx, 'link', e.target.value)}
                            className="w-full px-4 py-2.5 bg-[#0f1530] border border-slate-700 rounded-xl text-sm font-mono"
                            placeholder="e.g. /zinou-tv.apk"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TAB 5: APK & VISUAL IMAGES */}
          {activeTab === 'apk' && settings && (
            <div className="space-y-8">
              
              {/* APK File Upload Area */}
              <div>
                <h2 className="text-xl font-bold border-b border-slate-850 pb-3 text-cyan-400 mb-4">رفع ملف التطبيق الأندرويد (APK)</h2>
                <p className="text-xs text-slate-400 mb-4">
                  قم بسحب وإفلات ملف الـ APK الخاص بتطبيق الأندرويد، أو انقر لاختيار الملف يدوياً. سيقوم هذا تلقائياً باستبدال الملف المتاح للتحميل (`/zinou-tv.apk`) مباشرة.
                </p>

                <div className="p-8 border-2 border-dashed border-slate-700 bg-[#070b1e] rounded-3xl text-center relative hover:border-cyan-500/50 transition-colors">
                  <input
                    type="file"
                    accept=".apk"
                    id="apk-uploader"
                    className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    onChange={(e) => handleFileUpload(e, 'apk', 'apk')}
                  />
                  <div className="space-y-3">
                    <div className="text-4xl">🤖</div>
                    <div className="text-sm font-bold">
                      {uploadingField === 'apk' ? 'جاري رفع الملف وتحديث السيرفر...' : 'اختر ملف الـ APK أو اسحبه هنا'}
                    </div>
                    <div className="text-xs text-slate-500">ينصح بأن يكون اسم الملف مطابقاً لحجم التطبيق المناسب.</div>
                  </div>
                </div>

                {uploadingField === 'apk' && (
                  <div className="mt-4 p-3 bg-cyan-950/40 border border-cyan-900/50 text-cyan-300 rounded-xl text-xs flex items-center gap-3">
                    <span className="w-4 h-4 rounded-full border-2 border-cyan-400 border-t-transparent animate-spin" />
                    <span>جاري التحديث والتثبيت في المجلد العام (Public Folder)، يرجى عدم إغلاق الصفحة...</span>
                  </div>
                )}
              </div>

              {/* Graphic Assets Uploads */}
              <div className="pt-6 border-t border-slate-800 space-y-6">
                <h3 className="text-lg font-bold text-cyan-400">إدارة الأصول والصور البصرية</h3>
                
                {/* 1. Hero BG Image */}
                <div className="p-4 bg-[#070b1e] border border-slate-800 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                  <div>
                    <h4 className="text-sm font-bold text-slate-200">صورة خلفية البانر الرئيسي</h4>
                    <p className="text-[10px] text-slate-500 mt-1">المسار الحالي: {settings.hero.backgroundImage}</p>
                  </div>
                  <div className="flex items-center gap-3">
                    <input
                      type="file"
                      accept="image/*"
                      id="hero-bg-uploader"
                      className="hidden"
                      onChange={(e) => handleFileUpload(e, 'hero.backgroundImage', 'image')}
                    />
                    <label
                      htmlFor="hero-bg-uploader"
                      className="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-semibold rounded-lg cursor-pointer transition-colors"
                    >
                      {uploadingField === 'hero.backgroundImage' ? 'جاري الرفع...' : '📁 استبدال الصورة'}
                    </label>
                  </div>
                </div>

                {/* 2. Hero Players Graphic */}
                <div className="p-4 bg-[#070b1e] border border-slate-800 rounded-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                  <div>
                    <h4 className="text-sm font-bold text-slate-200">صورة جرافيك اللاعبين (Hero visual)</h4>
                    <p className="text-[10px] text-slate-500 mt-1">المسار الحالي: {settings.hero.playersImage}</p>
                  </div>
                  <div className="flex items-center gap-3">
                    <input
                      type="file"
                      accept="image/*"
                      id="hero-players-uploader"
                      className="hidden"
                      onChange={(e) => handleFileUpload(e, 'hero.playersImage', 'image')}
                    />
                    <label
                      htmlFor="hero-players-uploader"
                      className="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-semibold rounded-lg cursor-pointer transition-colors"
                    >
                      {uploadingField === 'hero.playersImage' ? 'جاري الرفع...' : '📁 استبدال الصورة'}
                    </label>
                  </div>
                </div>

              </div>

            </div>
          )}

        </div>

      </div>

    </div>
  );
}
