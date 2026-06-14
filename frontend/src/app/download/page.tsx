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

const DEFAULT_SETTINGS: Settings = {
  hero: {
    kicker: "عالم من المتعة بين يديك!",
    title: "UGEEN TV",
    subtitle: "شاهد ما تحب، أينما كنت",
    description: "استمتع بمشاهدة آلاف القنوات والأفلام والمسلسلات بجودة عالية وبدون تقطيع.",
    backgroundImage: "/assets/images/banner/banner-bg-14.jpg",
    playersImage: "/assets/images/banner/ugeen-home-players.png",
    downloadBtnText: "تحميل كود الزائر"
  },
  sponsorsText: "يمكنك الاستمتاع بجميع الباقات مجانًا",
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
    "subtitle": "خطط مجانية مصممة للجميع",
    "description": "اختر خطتك المناسبة وابدأ تجربتك مع UGEEN مجاناً",
    "plans": [
      {
        "title": "الأعضاء",
        "kicker": "الخيار الأمثل لعشاق التميز",
        "price": "0",
        "priceInfo": "مجاناً - الأكثر شعبية",
        "features": [
          "لمدة 24 ساعة",
          "جميع القنوات",
          "صيغ تحميل مختلفة ومتنوعة",
          "جميع الباقات والقنوات",
          "تحكم كامل ببيانات الاكستريم",
          "دعم على مدار الساعة 24/7"
        ]
      },
      {
        "title": "الزوار",
        "kicker": "تجربة سريعة ومجانية",
        "price": "0",
        "priceInfo": "مجاناً",
        "features": [
          "لمدة 24 ساعة",
          "قنوات محددة",
          "تحميل عن طريق M3U فقط",
          "باقات محددة",
          "لا يوجد تحكم ببيانات الاكستريم"
        ]
      }
    ]
  },
  "faqs": [
    {
      "q": "ما هي خدمة UGEEN TV؟",
      "a": "UGEEN TV خدمة مشاهدة عبر الإنترنت تمنحك تجربة بث سهلة بدون صحن لاقط، اعتمادًا على بروتوكول TV.",
      "icon": "fas fa-tv"
    },
    {
      "q": "ما هو أفضل خيار للمشاهدة عبر Android؟",
      "a": "ننصح بتحميل تطبيق UGEEN TV لأجهزة أندرويد للحصول على تجربة مشاهدة مباشرة وسهلة. انقر هنا وحمله الآن.",
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
      "q": "بماذا يتميز تطبيق UGEEN TV عن التطبيقات المعتادة؟",
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

  // Dynamically load Ugeen's minified CSS on client mount to avoid SSR hydration mismatches
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

  const membersPlan = settings.pricing.plans[0] || DEFAULT_SETTINGS.pricing.plans[0];
  const visitorsPlan = settings.pricing.plans[1] || DEFAULT_SETTINGS.pricing.plans[1];

  return (
    <div className="min-h-screen bg-[#070b1e] text-white relative overflow-hidden font-sans select-none" dir="rtl">

      {/* Floating decorative spheres (Ugeen balls) */}
      <img src="/assets/images/balls/1.png" alt="balls" className="absolute top-[12%] right-[10%] w-16 h-16 animate-bounce opacity-30 pointer-events-none select-none" />
      <img src="/assets/images/balls/2.png" alt="balls" className="absolute top-[45%] left-[8%] w-20 h-20 animate-pulse opacity-20 pointer-events-none select-none" />
      <img src="/assets/images/balls/3.png" alt="balls" className="absolute top-[75%] right-[5%] w-14 h-14 opacity-25 pointer-events-none select-none" />
      <img src="/assets/images/balls/4.png" alt="balls" className="absolute bottom-[10%] left-[12%] w-16 h-16 opacity-30 pointer-events-none select-none" />

      {/* Header bar */}
      <header className="header-section">
        <div className="container h-full">
          <div className="header-wrapper">
            <div className="logo">
              <Link href="/" className="ugeen-home-logo-text">
                UGEEN
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
          <img src="/assets/css/img/banner-shape-14.png" alt="banner" />
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
                      className="ugeen-feature-visual"
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
                      className="ugeen-feature-visual"
                      style={{ width: '92%' }}
                    />
                  </div>
                </>
              )}
            </div>
          ))}

        </div>
      </section>

      {/* Pricing Section */}
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

          {/* Grid Layout */}
          <div className="ugeen-pricing-grid-v6">
            
            {/* Card 1: Members (الأعضاء) */}
            {membersPlan && (
              <div className="ugeen-plan-card ugeen-plan-member">
                <div className="ugeen-plan-top">
                  <div>
                    <h5 className="cate">{membersPlan.title}</h5>
                    <p className="ugeen-plan-kicker">{membersPlan.kicker}</p>
                  </div>
                  <div className="ugeen-plan-visual member-media-stage" aria-hidden="true">
                    <div className="member-devices">
                      <span className="member-monitor"></span>
                      <span className="member-tablet"></span>
                      <span className="member-phone"></span>
                    </div>
                    <span className="member-shield"></span>
                  </div>
                </div>

                <div className="ugeen-plan-price">
                  <h2 className="title"><sup>$</sup>{membersPlan.price}</h2>
                  <span className="info">{membersPlan.priceInfo}</span>
                </div>

                <ul className="pricing-content-3">
                  {membersPlan.features.map((item, fidx) => (
                    <li key={fidx}>{item}</li>
                  ))}
                </ul>

                <div className="trial-button ugeen-member-actions">
                  <Link href="/signup" className="transparent-button">
                    <span>حساب جديد</span>
                    <i className="fas fa-arrow-left"></i>
                  </Link>
                  <Link href="/signin" className="transparent-button">
                    <span>تسجيل الدخول</span>
                    <i className="fas fa-arrow-left"></i>
                  </Link>
                </div>
              </div>
            )}

            {/* Card 2: Visitors (الزوار) */}
            {visitorsPlan && (
              <div className="ugeen-plan-card ugeen-plan-visitor">
                <div className="ugeen-plan-top">
                  <div>
                    <h5 className="cate">{visitorsPlan.title}</h5>
                    <p className="ugeen-plan-kicker">{visitorsPlan.kicker}</p>
                  </div>
                  <div className="ugeen-plan-visual visitor-media-stage" aria-hidden="true">
                    <div className="visitor-screen">
                      <span className="visitor-play"></span>
                    </div>
                    <div className="visitor-pass">
                      <strong>FREE</strong>
                      <small>ACCESS</small>
                    </div>
                  </div>
                </div>

                <div className="ugeen-plan-price">
                  <h2 className="title"><sup>$</sup>{visitorsPlan.price}</h2>
                  <span className="info">{visitorsPlan.priceInfo}</span>
                </div>

                <ul className="pricing-content-3">
                  {visitorsPlan.features.map((item, fidx) => (
                    <li key={fidx}>{item}</li>
                  ))}
                </ul>

                {/* Visitor Activation Form */}
                <div className="card-btn ugeen-visitor-form">
                  <form onSubmit={handleActivation} autoComplete="off">
                    <label className="ugeen-field-label" htmlFor="code">أدخل كود التفعيل</label>
                    <input
                      type="text"
                      id="code"
                      placeholder="أدخل كود التفعيل هنا"
                      value={activationCode}
                      onChange={(e) => setActivationCode(e.target.value)}
                      className="form-control mb-2"
                      required
                    />
                    
                    <label className="ugeen-field-label" htmlFor="select">اختر الباقة</label>
                    <select
                      id="select"
                      value={selectedPackage}
                      onChange={(e) => setSelectedPackage(e.target.value)}
                      className="form-control form-select mb-2"
                    >
                      <option value="all">باقة جميع القنوات</option>
                      <option value="sports">باقة القنوات الرياضية</option>
                      <option value="worldcup">باقة كأس العالم</option>
                    </select>

                    <button
                      type="submit"
                      id="snd"
                      className="get-button"
                      style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}
                    >
                      <span>تفــعيل</span>
                      <i className="fas fa-arrow-left"></i>
                    </button>
                  </form>
                </div>
              </div>
            )}

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
            <p className="ugeen-faq-subtitle">إجابات سريعة على أكثر الأسئلة التي يحتاجها مستخدمو UGEEN TV.</p>
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
                    <a href={item.link} download className="text-cyan-400 hover:underline mr-1 block mt-2">
                      انقر هنا وحمل تطبيق الـ APK الآن <i className="fas fa-link"></i>
                    </a>
                  )}
                </div>
              </details>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="trial-section ugeen-modern-trial padding-bottom padding-top">
        <div className="container">
          <div className="ugeen-cta-wrapper">
            <div className="ugeen-cta-content cl-white">
              <span className="ugeen-cta-kicker">ابدأ الآن</span>
              <h3 className="ugeen-cta-title">
                حان وقت <span>الاستمتاع بالمشاهدة!</span>
              </h3>
              <p className="ugeen-cta-desc">
                قم بالحصول على اشتراكك المجاني الان واستمتع بعالم من الترفيه.
              </p>
            </div>

            <div className="ugeen-cta-actions">
              <div className="trial-button">
                <Link href="/signup" className="transparent-button">
                  <span>حساب جديد</span>
                  <i className="fas fa-arrow-left"></i>
                </Link>
                <Link href="/signin" className="transparent-button">
                  <span>تسجيل الدخول</span>
                  <i className="fas fa-arrow-left"></i>
                </Link>
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
                <Link href="/" className="ugeen-footer-logo" aria-label="UGEEN TV">
                  <span className="ugeen-footer-mark" aria-hidden="true"></span>
                  <span>UGEEN</span>
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
                  <li className="twitter">
                    <a href="#0" aria-label="Twitter">
                      <i className="fab fa-twitter"></i>
                    </a>
                  </li>
                  <li className="facebook">
                    <a href="#0" aria-label="Facebook">
                      <i className="fab fa-facebook-f"></i>
                    </a>
                  </li>
                  <li className="instagram">
                    <a href="#0" aria-label="Instagram">
                      <i className="fab fa-instagram"></i>
                    </a>
                  </li>
                  <li className="youtube">
                    <a href="#0" aria-label="YouTube">
                      <i className="fab fa-youtube"></i>
                    </a>
                  </li>
                </ul>
              </div>
            </div>

            <div className="ugeen-footer-copy">
              <p>جميع الحقوق محفوظة 2026 © <a href="#0">UGEEN TV</a></p>
            </div>
          </div>
        </div>
      </footer>

      {/* ACTIVATION MODAL (INTERACTIVE USER EXPERINCE) */}
      {showModal && (
        <div className="ugeen-guest-modal-overlay">
          <div className="ugeen-guest-modal-content">
            
            {/* Modal Header */}
            <div className="ugeen-guest-modal-header">
              <h3 className="ugeen-guest-modal-title">تفعيل كود الزائر</h3>
              <button
                onClick={closeModal}
                className="ugeen-guest-modal-close"
              >
                ✕
              </button>
            </div>

            {/* Modal Body */}
            <div className="ugeen-guest-modal-body">
              {isActivating ? (
                <div className="ugeen-guest-loading-card">
                  <div className="ugeen-guest-loader-ring" />
                  <h3>جاري الاتصال بالسيرفر وتوليد الكود...</h3>
                  <p>يرجى الانتظار، جاري تفعيل كود الزائر الخاص بك.</p>
                </div>
              ) : activationSuccess ? (
                <div>
                  {/* Success Header */}
                  <div className="ugeen-guest-success-head">
                    <div className="ugeen-guest-success-icon">
                      ✓
                    </div>
                    <h3>تم تفعيل كود الزائر بنجاح!</h3>
                    <p>كودك نشط حالياً لمدة 24 ساعة.</p>
                  </div>

                  {/* Credentials Grid */}
                  <div className="ugeen-guest-credentials">
                    
                    <div className="ugeen-guest-row">
                      <span className="ugeen-guest-icon">📡</span>
                      <span className="ugeen-guest-label">Host URL</span>
                      <span className="ugeen-guest-value select-all">{settings.activationModal.hostUrl}</span>
                      <button
                        onClick={() => handleCopy(settings.activationModal.hostUrl, 'host')}
                        className="ugeen-guest-copy"
                      >
                        {copiedKey === 'host' ? 'تم النسخ!' : 'نسخ'}
                      </button>
                    </div>

                    <div className="ugeen-guest-row">
                      <span className="ugeen-guest-icon">👤</span>
                      <span className="ugeen-guest-label">Username</span>
                      <span className="ugeen-guest-value select-all">{settings.activationModal.username}</span>
                      <button
                        onClick={() => handleCopy(settings.activationModal.username, 'user')}
                        className="ugeen-guest-copy"
                      >
                        {copiedKey === 'user' ? 'تم النسخ!' : 'نسخ'}
                      </button>
                    </div>

                    <div className="ugeen-guest-row">
                      <span className="ugeen-guest-icon">🔑</span>
                      <span className="ugeen-guest-label">Password</span>
                      <span className="ugeen-guest-value select-all">{settings.activationModal.password}</span>
                      <button
                        onClick={() => handleCopy(settings.activationModal.password, 'pass')}
                        className="ugeen-guest-copy"
                      >
                        {copiedKey === 'pass' ? 'تم النسخ!' : 'نسخ'}
                      </button>
                    </div>

                    <div className="ugeen-guest-row ugeen-guest-download-row">
                      <span className="ugeen-guest-icon">📂</span>
                      <span className="ugeen-guest-label">M3U File</span>
                      <span className="ugeen-guest-value">
                        <a
                          href={settings.activationModal.m3uUrl}
                          target="_blank"
                          rel="noreferrer"
                          className="ugeen-guest-m3u"
                        >
                          تحميل ملف القنوات M3U
                        </a>
                      </span>
                      <button
                        onClick={() => handleCopy(settings.activationModal.m3uUrl, 'm3u')}
                        className="ugeen-guest-copy"
                      >
                        {copiedKey === 'm3u' ? 'تم النسخ!' : 'نسخ'}
                      </button>
                    </div>
                  </div>

                  {/* Close button */}
                  <button
                    onClick={closeModal}
                    className="ugeen-guest-close-btn"
                  >
                    إغلاق وبدء المشاهدة
                  </button>
                </div>
              ) : null}
            </div>

          </div>
        </div>
      )}

    </div>
  );
}
