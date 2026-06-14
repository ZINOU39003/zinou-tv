'use client';

import { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';

export default function Navbar() {
  const [menuOpen, setMenuOpen] = useState(false);
  const pathname = usePathname();

  if (pathname?.startsWith('/download')) {
    return null;
  }

  return (
    <nav className="fixed top-0 left-0 right-0 z-50 h-[70px] bg-[#111827]/90 backdrop-blur-xl border-b border-[#1f2937]">
      <div className="max-w-7xl mx-auto h-full px-4 flex items-center justify-between">
        {/* Logo */}
        <Link href="/" className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-[#10b981] to-[#059669] flex items-center justify-center text-white font-black text-lg shadow-lg shadow-[#10b981]/30">
            Z
          </div>
          <div>
            <span className="text-white font-black text-xl tracking-tight">Zinou</span>
            <span className="text-[#10b981] font-black text-xl"> TV</span>
          </div>
        </Link>

        {/* Desktop Nav Links */}
        <div className="hidden md:flex items-center gap-1">
          <Link 
            href="/" 
            className={`px-4 py-2 rounded-lg text-sm font-bold transition-colors ${
              pathname === '/' 
                ? 'text-[#10b981] bg-[#1f2937]' 
                : 'text-[#9ca3af] hover:text-white hover:bg-[#1f2937]'
            }`}
          >
            المباريات
          </Link>
          <Link 
            href="/news" 
            className={`px-4 py-2 rounded-lg text-sm font-bold transition-colors ${
              pathname === '/news' 
                ? 'text-[#10b981] bg-[#1f2937]' 
                : 'text-[#9ca3af] hover:text-white hover:bg-[#1f2937]'
            }`}
          >
            الأخبار
          </Link>
          <Link 
            href="/download" 
            className={`px-4 py-2 rounded-lg text-sm font-bold transition-colors ${
              pathname === '/download' 
                ? 'text-[#10b981] bg-[#1f2937]' 
                : 'text-[#9ca3af] hover:text-white hover:bg-[#1f2937]'
            }`}
          >
            تحميل التطبيق
          </Link>
        </div>

        {/* Mobile Menu Button */}
        <button 
          onClick={() => setMenuOpen(!menuOpen)} 
          className="md:hidden text-white p-2"
          aria-label="القائمة"
        >
          <svg width="24" height="24" fill="none" stroke="currentColor" strokeWidth={2}>
            {menuOpen 
              ? <path d="M6 18L18 6M6 6l12 12" />
              : <path d="M4 6h16M4 12h16M4 18h16" />
            }
          </svg>
        </button>
      </div>

      {/* Mobile Menu */}
      {menuOpen && (
        <div className="md:hidden bg-[#111827] border-t border-[#1f2937] p-4 flex flex-col gap-2">
          <Link 
            href="/" 
            className={`px-4 py-3 rounded-lg text-sm font-bold ${
              pathname === '/' ? 'text-[#10b981] bg-[#1f2937]' : 'text-[#9ca3af] hover:bg-[#1f2937]'
            }`} 
            onClick={() => setMenuOpen(false)}
          >
            المباريات
          </Link>
          <Link 
            href="/news" 
            className={`px-4 py-3 rounded-lg text-sm font-bold ${
              pathname === '/news' ? 'text-[#10b981] bg-[#1f2937]' : 'text-[#9ca3af] hover:bg-[#1f2937]'
            }`} 
            onClick={() => setMenuOpen(false)}
          >
            الأخبار
          </Link>
          <Link 
            href="/download" 
            className={`px-4 py-3 rounded-lg text-sm font-bold ${
              pathname === '/download' ? 'text-[#10b981] bg-[#1f2937]' : 'text-[#9ca3af] hover:bg-[#1f2937]'
            }`} 
            onClick={() => setMenuOpen(false)}
          >
            تحميل التطبيق
          </Link>
        </div>
      )}
    </nav>
  );
}

