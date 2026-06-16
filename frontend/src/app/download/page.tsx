'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

interface Plan {
  title: string;
  kicker: string;
  price: string;
  priceInfo: string;
  popular?: boolean;
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
  whatsappNumber: string;
  socials: {
    twitter: string;
    facebook: string;
    instagram: string;
    youtube: string;
  };
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

const DEFAULT_SETTINGS: Settings = {
  hero: {
    kicker: "عالم من المتعة بين يديك!",
    title: "ZINOU TV",
    subtitle: "شاهد ما تحب، أينما كنت",
    description: "استمتع بمشاهدة آلاف القنوات والأفلام والمسلسلات بجودة عالية وبدون تقطيع.",
    backgroundImage: "/assets/images/banner/zinou-banner-bg.png",
    playersImage: "/assets/images/banner/zinou-home-players.png",
    downloadBtnText: "تحميل كود الزائر"
  },
  sponsorsText: "يمكنك الاستمتاع بجميع الباقات مجانًا",
  whatsappNumber: "213XXXXXXXXX",
  socials: {
    twitter: "#0",
    facebook: "#0",
    instagram: "#0",
    youtube: "#0"
  },
  features: [
    {
      kicker: "وداعًا للتقطعات .",
      title: "جودات متنوعة !",
      description: "جميع الجودات متوفرة لدينا { 4K 2160p-1080p-720p-480p-360p-240p-144p } لذا لا تقلق ، فلن تعاني من انقطاع في المشاهدة بسبب ضعف الانترنت ^^",
      image: "/assets/images/feature/ugeen-feature-quality.png"
    },
    {
      kicker: "شاهد عبر كل الاجهزة",
      title: "خدماتنا تدعم مختلف الاجهزة",
      description: "يمكنك الاستفادة من خدماتنا والاستمتاع بالمشاهدة المباشرة على اي جهاز بالعالم يدعم نظام TV - كما ان خدماتنا تتميز بمعدل عمل ثابت يصل الى 99%",
      image: "/assets/images/feature/ugeen-feature-devices.png"
    },
    {
      kicker: "لا تفوتك مباراة !",
      title: "شاهد اينما كنت",
      description: "يمكنك استخدام خدماتنا من أي مكان في هذا العالم. لا توجد قيود جغرافية، يمكنك بدء المشاهدة في أي وقت تريده على مدار الساعة وطوال أيام الأسبوع!",
      image: "/assets/images/feature/ugeen-feature-anywhere.png"
    }
  ],
  "pricing": {
    "title": "الأسعار",
    "subtitle": "خطط الاشتراك المتنوعة للجميع",
    "description": "اختر خطتك المناسبة وابدأ تجربتك المميزة مع ZINOU TV",
    "plans": [
      {
        "title": "باقة الشهر الواحد",
        "kicker": "30 يوم",
        "price": "500",
        "priceInfo": "DZD 500",
        "features": [
          "بدون إعلانات تماماً",
          "جودة عالية FHD / UHD",
          "جميع باقات القنوات والسينما",
          "دعم فني 24/7"
        ]
      },
      {
        "title": "باقة 3 أشهر",
        "kicker": "90 يوم",
        "price": "1200",
        "priceInfo": "DZD 1200",
        "features": [
          "بدون إعلانات تماماً",
          "جودة عالية FHD / UHD",
          "جميع باقات القنوات والسينما",
          "دعم فني 24/7"
        ]
      },
      {
        "title": "باقة 6 أشهر",
        "kicker": "180 يوم",
        "price": "2000",
        "priceInfo": "DZD 2000",
        "features": [
          "بدون إعلانات تماماً",
          "جودة عالية FHD / UHD",
          "جميع باقات القنوات والسينما",
          "دعم فني 24/7",
          "تحديثات قائمة مجانية"
        ]
      },
      {
        "title": "باقة 12 شهراً",
        "kicker": "365 يوم",
        "price": "3500",
        "priceInfo": "DZD 3500",
        "popular": true,
        "features": [
          "بدون إعلانات تماماً",
          "جودة عالية FHD / UHD",
          "جميع باقات القنوات والسينما",
          "دعم فني 24/7",
          "تحديثات قائمة مجانية",
          "خصم خاص للدفع السنوي"
        ]
      }
    ]
  },
  "faqs": [
    {
      "q": "ما هي خدمة ZINOU TV؟",
      "a": "ZINOU TV خدمة مشاهدة عبر الإنترنت تمنحك تجربة بث سهلة بدون صحن لاقط، اعتمادًا على بروتوكول TV.",
      "icon": "fas fa-tv"
    },
    {
      "q": "ما هو أفضل خيار للمشاهدة عبر Android؟",
      "a": "ننصح بتحميل تطبيق ZINOU TV لأجهزة أندرويد للحصول على تجربة مشاهدة مباشرة وسهلة. انقر هنا وحمله الآن.",
      "icon": "fab fa-android",
      "link": "/zinou-tv.apk"
    },
    {
      "q": "أعاني من تقطعات مستمرة، ما الحل؟",
      "a": "تأكد أولاً من سرعة الإنترنت عبر SPEEDTEST. إذا كانت السرعة جيدة، جرّب إعادة تشغيل الراوتر للحصول على اتصال أكثر استقرارًا.",
      "icon": "fas fa-tachometer-alt"
    },
    {
      "q": "هل التشغيل يتطلب سرعة عالية؟",
      "a": "لا، لأن الجودات المتنوعة تساعدك على المشاهدة حسب سرعة الإنترنت، لكن الأفضل وجود اتصال مستقر لتجربة أنعم.",
      "icon": "fas fa-rocket"
    },
    {
      "q": "بماذا يتميز تطبيق ZINOU TV عن التطبيقات المعتادة؟",
      "a": "يتميز بتجربة مشاهدة أبسط، أقسام متنوعة، جودات متعددة، وميزة اختيار الجودة المناسبة خلال الأحداث المهمة.",
      "icon": "fas fa-shield-alt"
    }
  ],
  "activationModal": {
    "hostUrl": "http://zinou.live:8000",
    "username": "guest_zinoutv_741",
    "password": "zinou_guest_pass",
    "m3uUrl": "/api/iptv/playlist?user=guest_zinoutv_741&pass=zinou_guest_pass"
  }
};

export default function DownloadPage() {
  const [settings, setSettings] = useState<Settings>(DEFAULT_SETTINGS);
  
  // Visitor activation state
  const [activationCode, setActivationCode] = useState('');
  const [selectedPackage, setSelectedPackage] = useState('all');
  const [isActivating, setIsActivating] = useState(false);
  const [activationSuccess, setActivationSuccess] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [copiedKey, setCopiedKey] = useState<string | null>(null);

  // FAQ open/close state
  const [faqOpen, setFaqOpen] = useState<Record<number, boolean>>({
    0: true, // First one open by default
  });

  const toggleFaq = (index: number) => {
    setFaqOpen((prev) => ({
      ...prev,
      [index]: !prev[index],
    }));
  };

  const handleCopy = (text: string, key: string) => {
    navigator.clipboard.writeText(text);
    setCopiedKey(key);
    setTimeout(() => setCopiedKey(null), 2000);
  };

  const handleActivation = (e: React.FormEvent) => {
    e.preventDefault();
    if (!activationCode) return;

    setIsActivating(true);
    setShowModal(true);

    // Simulate activation process
    setTimeout(() => {
      setIsActivating(false);
      setActivationSuccess(true);
    }, 2000);
  };

  const closeModal = () => {
    setShowModal(false);
    setActivationSuccess(false);
    setActivationCode('');
  };

  // Fetch current configurations from API on mount
  useEffect(() => {
    const loadSettings = async () => {
      try {
        const res = await fetch('/api-admin/settings');
        if (res.ok) {
          const data = await res.json();
          setSettings(data);
        }
      } catch (err) {
        console.error('Failed to load settings, using fallback static data:', err);
      }
    };
    loadSettings();
  }, []);

  // Dynamically load Zinou's minified CSS on client mount to avoid SSR hydration mismatches
  useEffect(() => {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = '/assets/css/minified.css';
    link.id = 'ugeen-minified-css';
    document.head.appendChild(link);

    // Add FontAwesome CDN link if it's not already there
    let faLink = document.getElementById('font-awesome-cdn') as HTMLLinkElement;
    if (!faLink) {
      faLink = document.createElement('link');
      faLink.rel = 'stylesheet';
      faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
      faLink.id = 'font-awesome-cdn';
      faLink.crossOrigin = 'anonymous';
      faLink.referrerPolicy = 'no-referrer';
      document.head.appendChild(faLink);
    }

    return () => {
      const el = document.getElementById('ugeen-minified-css');
      if (el) el.remove();
    };
  }, []);

  return (
    <div className="min-h-screen bg-[#070b1e] text-white relative overflow-hidden font-sans select-none" dir="rtl">

      {/* Floating decorative spheres (Zinou balls) */}
      <img src="/assets/images/balls/1.png" alt="balls" className="absolute top-[12%] right-[10%] w-16 h-16 animate-bounce opacity-30 pointer-events-none select-none filter-gold" />
      <img src="/assets/images/balls/2.png" alt="balls" className="absolute top-[45%] left-[8%] w-20 h-20 animate-pulse opacity-20 pointer-events-none select-none filter-gold" />
      <img src="/assets/images/balls/3.png" alt="balls" className="absolute top-[75%] right-[5%] w-14 h-14 opacity-25 pointer-events-none select-none filter-gold" />
      <img src="/assets/images/balls/4.png" alt="balls" className="absolute bottom-[10%] left-[12%] w-16 h-16 opacity-30 pointer-events-none select-none filter-gold" />

      {/* Header bar */}
      <header className="header-section">
        <div className="container h-full">
          <div className="header-wrapper">
            <div className="logo">
              <Link href="/" className="ugeen-home-logo-text">
                ZINOU TV
              </Link>
            </div>
            <div className="header-right">
              <a
                href="/zinou-tv.apk"
                download="Zinou-TV.apk"
                className="header-button d-inline-blockk navActionDownload"
              >
                <span>{settings.hero.downloadBtnText}</span>
                <i className="fas fa-download pr-2"></i>
              </a>
            </div>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section 
        className="banner-14 oh bg_img pos-rel"
        style={{ 
          backgroundImage: `url("${settings.hero.backgroundImage}")`,
          backgroundSize: 'cover',
          backgroundPosition: 'center'
        }}
      >
        <div className="bottom-shape d-lg-block d-none">
          <img src="/assets/css/img/banner-shape-14.png" alt="banner" className="filter-gold" />
        </div>
        
        <div className="container">
          <div className="row align-items-center justify-content-between flex-wrap-reverse">
            
            {/* Hero Left: Players graphic */}
            <div className="col-lg-6 rtl">
              <div className="banner-video-14 ugeen-home-players-wrap">
                <img
                  src={settings.hero.playersImage}
                  alt={settings.hero.title}
                />
              </div>
            </div>

            {/* Hero Right: Title and descriptions */}
            <div className="col-lg-5">
              <div className="banner-content-14 cl-white">
                <h5 className="cate ugeen-hero-kicker">{settings.hero.kicker}</h5>
                <h1 className="title ugeen-title-gradient">{settings.hero.title}</h1>
                <p className="ugeen-hero-subtitle">{settings.hero.subtitle}</p>
                <p className="ugeen-hero-desc">
                  {settings.hero.description}
                </p>
              </div>
            </div>
          </div>

          {/* Sponsors Section */}
          <div className="sponsor-slider-wrapper cl-white text-center">
            <span className="slider-heading">{settings.sponsorsText}</span>
            
            <div className="ugeen-sponsor-text-logos">
              
              <div className="sponsor-thumb">
                <div className="ugeen-sponsor-logo ugeen-logo-bein">
                  <strong>beIN</strong>
                  <small>SPORTS</small>
                </div>
              </div>
              
              <div className="sponsor-thumb">
                <div className="ugeen-sponsor-logo ugeen-logo-osn">
                  <strong>OSN</strong>
                </div>
              </div>
              
              <div className="sponsor-thumb">
                <div className="ugeen-sponsor-logo ugeen-logo-mbc">
                  <strong>mbc</strong>
                </div>
              </div>
              
              <div className="sponsor-thumb">
                <div className="ugeen-sponsor-logo ugeen-logo-shahid">
                  <strong>شاهد</strong>
                  <i></i><i></i><i></i>
                </div>
              </div>
              
              <div className="sponsor-thumb">
                <div className="ugeen-sponsor-logo ugeen-logo-ad">
                  <strong>AD</strong>
                  <small>SPORTS</small>
                </div>
              </div>
              
              <div className="sponsor-thumb">
                <div className="ugeen-sponsor-logo ugeen-logo-art">
                  <strong>ART</strong>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="advance-feature-section padding-top-2 padding-bottom-2">
        <div className="container">
          
          {settings.features.map((feat, idx) => (
            <div key={idx} className="advance-feature-item padding-top-2 padding-bottom-2">
              {idx % 2 === 0 ? (
                <>
                  <div className="advance-feature-thumb">
                    <img
                      src={feat.image}
                      alt={feat.title}
                      className="ugeen-feature-visual filter-gold-visual"
                      style={{ width: '92%' }}
                    />
                  </div>
                  <div className="advance-feature-content">
                    <div className="section-header left-style mb-olpo">
                      <h5 className="cate">{feat.kicker}</h5>
                      <h2 className="title">{feat.title}</h2>
                      <p>{feat.description}</p>
                    </div>
                  </div>
                </>
              ) : (
                <>
                  <div className="advance-feature-content">
                    <div className="section-header left-style mb-olpo">
                      <h5 className="cate">{feat.kicker}</h5>
                      <h2 className="title">{feat.title}</h2>
                      <p>{feat.description}</p>
                    </div>
                  </div>
                  <div className="advance-feature-thumb">
                    <img
                      src={feat.image}
                      alt={feat.title}
                      className="ugeen-feature-visual filter-gold-visual"
                      style={{ width: '92%' }}
                    />
                  </div>
                </>
              )}
            </div>
          ))}

        </div>
      </section>

      {/* Pricing Section (4 plans matching the 4th image layout) */}
      <section 
        className="pricing-section ugeen-pricing-v6 padding-top oh padding-bottom pb-lg-half bg_img pos-rel" 
        id="pricing"
        style={{ backgroundColor: '#050914' }}
      >
        <div className="container">
          
          {/* Section Header */}
          <div className="section-header cl-white text-center">
            <h5 className="cate">{settings.pricing.title}</h5>
            <h2 className="title">{settings.pricing.subtitle}</h2>
            <p>{settings.pricing.description}</p>
          </div>

          {/* 4-Plan Responsive Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto px-4 mt-12">
            {settings.pricing.plans.map((plan, index) => {
              const waNumber = settings.whatsappNumber || '213XXXXXXXXX';
              const textMessage = `مرحباً ZINOU TV، أود الاشتراك في ${plan.title} (بسعر ${plan.priceInfo})`;
              const whatsappUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(textMessage)}`;
              const isPopular = plan.popular || false;

              return (
                <div 
                  key={index} 
                  className={`ugeen-plan-card relative flex flex-col justify-between transition-all duration-300 transform hover:-translate-y-1 ${
                    isPopular ? 'border border-[#d4af37] shadow-[0_15px_45px_rgba(212,175,55,0.15)] scale-[1.02] md:scale-[1.03]' : 'border border-slate-800'
                  }`}
                  style={{
                    background: 'linear-gradient(180deg, rgba(22, 18, 10, 0.95), rgba(9, 8, 5, 0.98))',
                    borderRadius: '24px',
                    padding: '36px 28px',
                    minHeight: 'auto',
                  }}
                >
                  {isPopular && (
                    <div className="absolute top-4 left-4 bg-gradient-to-r from-amber-500 to-yellow-600 text-black text-[10px] font-black px-3.5 py-1 rounded-full shadow-lg">
                      ★ الأكثر شعبية
                    </div>
                  )}

                  <div>
                    <div className="mb-6">
                      <h5 className="text-xl font-black text-slate-100 mb-1">{plan.title}</h5>
                      <p className="text-xs text-amber-500 font-bold tracking-wide">{plan.kicker}</p>
                    </div>

                    <div className="mb-6 pb-6 border-b border-slate-850">
                      <h2 className="text-3xl font-black text-white flex items-baseline gap-1.5">
                        <span className="text-sm font-bold text-amber-500">DZD</span>
                        {plan.price}
                      </h2>
                    </div>

                    <ul className="space-y-3.5 mb-8">
                      {plan.features.map((item, fidx) => (
                        <li key={fidx} className="flex items-start gap-2.5 text-xs text-slate-300 font-medium leading-relaxed">
                          <span className="text-amber-500 font-bold text-sm leading-none">✓</span>
                          <span>{item}</span>
                        </li>
                      ))}
                    </ul>
                  </div>

                  <a 
                    href={whatsappUrl}
                    target="_blank"
                    rel="noopener noreferrer"
                    className={`w-full py-4 rounded-xl font-black text-xs text-center flex items-center justify-center gap-2 transition-all duration-300 ${
                      isPopular 
                        ? 'bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-black shadow-lg shadow-amber-500/25' 
                        : 'bg-[#1b233a] hover:bg-[#25304e] text-white border border-[#2d3a5f]/50'
                    }`}
                  >
                    <span>شراء واشتراك عبر واتساب</span>
                    <i className="fab fa-whatsapp text-sm"></i>
                  </a>
                </div>
              );
            })}
          </div>

        </div>
      </section>

      {/* FAQ Section */}
      <section className="faq-section ugeen-modern-faq padding-top">
        <div className="container">
          
          {/* Section Header */}
          <div className="ugeen-faq-head">
            <span className="ugeen-faq-kicker">FAQ</span>
            <h2 className="ugeen-faq-title">أسئلة شائعة</h2>
            <p className="ugeen-faq-subtitle">إجابات سريعة على أكثر الأسئلة التي يحتاجها مستخدمو ZINOU TV.</p>
          </div>

          {/* Accordion Grid */}
          <div className="ugeen-faq-grid">
            {settings.faqs.map((item, idx) => (
              <details
                key={idx}
                className={`ugeen-faq-card ${faqOpen[idx] ? 'open' : ''}`}
                open={faqOpen[idx] || false}
              >
                <summary
                  onClick={(e) => {
                    e.preventDefault();
                    toggleFaq(idx);
                  }}
                  className="faq-summary"
                >
                  <span className="ugeen-faq-icon">
                    <i className={item.icon}></i>
                  </span>
                  <span>{item.q}</span>
                  <span className="ugeen-faq-plus">
                    <i className="fas fa-plus"></i>
                  </span>
                </summary>
                <div className="ugeen-faq-answer">
                  {item.a}
                  {item.link && (
                    <a href={item.link} download className="text-amber-500 hover:underline mr-1 block mt-2">
                      انقر هنا وحمل تطبيق الـ APK الآن <i className="fas fa-link"></i>
                    </a>
                  )}
                </div>
              </details>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section (Refactored to download APK directly) */}
      <section className="trial-section ugeen-modern-trial padding-bottom padding-top">
        <div className="container">
          <div className="ugeen-cta-wrapper">
            <div className="ugeen-cta-content cl-white">
              <span className="ugeen-cta-kicker">ابدأ الآن</span>
              <h3 className="ugeen-cta-title">
                حان وقت <span>الاستمتاع بالمشاهدة!</span>
              </h3>
              <p className="ugeen-cta-desc">
                قم بتحميل تطبيقنا الآن واستمتع بعالم من الترفيه والمشاهدة الحية.
              </p>
            </div>

            <div className="ugeen-cta-actions">
              <div className="trial-button">
                <a 
                  href="/zinou-tv.apk" 
                  download="Zinou-TV.apk"
                  className="transparent-button flex items-center justify-center gap-2.5 px-6"
                >
                  <span>تحميل التطبيق</span>
                  <i className="fas fa-download text-sm"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="footer-section ugeen-footer-modern">
        <div className="container">
          <div className="ugeen-footer-panel">
            <div className="ugeen-footer-main">
              <div className="ugeen-footer-brand">
                <Link href="/" className="ugeen-footer-logo" aria-label="ZINOU TV">
                  <span className="ugeen-footer-mark bg-amber-500" aria-hidden="true"></span>
                  <span>ZINOU TV</span>
                </Link>
                <p className="ugeen-footer-text">
                  منصة متكاملة لمشاهدة أحدث الأفلام والمسلسلات بجودة عالية وتجربة فريدة لك.
                </p>
              </div>

              <div className="ugeen-footer-center">
                <ul className="ugeen-footer-links grid grid-cols-2 sm:grid-cols-4 gap-4 w-full">
                  <li>
                    <Link href="/">
                      <i className="fas fa-home"></i>
                      <span>الرئيسية</span>
                    </Link>
                  </li>
                  <li>
                    <Link href="/aboutus">
                      <i className="fas fa-globe"></i>
                      <span>من نحن</span>
                    </Link>
                  </li>
                  <li>
                    <Link href="/contact">
                      <i className="fas fa-headset"></i>
                      <span>إتصل بنا</span>
                    </Link>
                  </li>
                  <li>
                    <Link href="/privacy">
                      <i className="fas fa-shield-alt"></i>
                      <span>سياسة الخصوصية</span>
                    </Link>
                  </li>
                </ul>
                
                <ul className="ugeen-footer-social">
                  <li className="facebook">
                    <a href={settings.socials?.facebook || "#0"} target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                      <i className="fab fa-facebook-f"></i>
                    </a>
                  </li>
                  <li className="twitter">
                    <a href={settings.socials?.twitter || "#0"} target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                      <i className="fab fa-twitter"></i>
                    </a>
                  </li>
                  <li className="instagram">
                    <a href={settings.socials?.instagram || "#0"} target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                      <i className="fab fa-instagram"></i>
                    </a>
                  </li>
                  <li className="youtube">
                    <a href={settings.socials?.youtube || "#0"} target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                      <i className="fab fa-youtube"></i>
                    </a>
                  </li>
                </ul>
              </div>
            </div>

            <div className="ugeen-footer-copy">
              <p>جميع الحقوق محفوظة 2026 © <a href="#0">ZINOU TV</a></p>
            </div>
          </div>
        </div>
      </footer>



    </div>
  );
}
