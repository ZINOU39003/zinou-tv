'use client';

import { useEffect } from 'react';

interface AdSenseProps {
  client?: string;
  slot: string;
  format?: 'auto' | 'fluid' | 'rectangle' | 'horizontal' | 'vertical';
  responsive?: 'true' | 'false';
  className?: string;
  style?: React.CSSProperties;
}

export default function AdSense({
  client = 'ca-pub-9426985477419089', // Replace with your real AdSense publisher ID
  slot,
  format = 'auto',
  responsive = 'true',
  className = '',
  style = {},
}: AdSenseProps) {
  useEffect(() => {
    try {
      // @ts-ignore
      (window.adsbygoogle = window.adsbygoogle || []).push({});
    } catch (e) {
      // Catch errors quietly when ads block or double-pushing
      console.log('AdSense script push ignored.');
    }
  }, []);

  return (
    <div className={`w-full my-6 flex flex-col items-center justify-center ${className}`}>
      <span className="text-[9px] text-[#374151] font-black mb-1.5 uppercase tracking-wider select-none">
        إعلان ممول / Sponsored Ad
      </span>
      <div className="w-full bg-[#111827]/40 border border-dashed border-[#1f2937] hover:border-[#10b981]/20 rounded-2xl flex items-center justify-center p-4 transition-all duration-300 min-h-[100px] overflow-hidden">
        <ins
          className="adsbygoogle"
          style={{ display: 'block', minWidth: '250px', ...style }}
          data-ad-client={client}
          data-ad-slot={slot}
          data-ad-format={format}
          data-ad-full-width-responsive={responsive}
        />
        {/* Development visual placeholder */}
        <div className="flex flex-col items-center gap-1 text-[#4b5563] text-center select-none py-2 pointer-events-none">
          <div className="text-xs font-black tracking-wider text-[#10b981]/70">Google AdSense</div>
          <div className="text-[10px] font-bold">المعرف (Slot): {slot}</div>
        </div>
      </div>
    </div>
  );
}
