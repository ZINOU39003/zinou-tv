'use client';

export type TabType = 'info' | 'lineup' | 'stats' | 'events';

interface MatchTabsProps {
  activeTab: TabType;
  onSelectTab: (tab: TabType) => void;
  hasLineups: boolean;
  hasStats: boolean;
}

export default function MatchTabs({ activeTab, onSelectTab, hasLineups, hasStats }: MatchTabsProps) {
  const tabs: { id: TabType; label: string; icon: string; enabled: boolean }[] = [
    { id: 'info', label: 'معلومات', icon: 'ℹ️', enabled: true },
    { id: 'lineup', label: 'التشكيلة', icon: '👕', enabled: hasLineups },
    { id: 'stats', label: 'الإحصائيات', icon: '📊', enabled: hasStats },
    { id: 'events', label: 'الأحداث', icon: '⚡', enabled: true },
  ];

  return (
    <div className="flex bg-[#111827] border-b border-[#1f2937] overflow-hidden">
      {tabs.map((tab) => {
        if (!tab.enabled) return null;
        const isActive = activeTab === tab.id;
        return (
          <button
            key={tab.id}
            onClick={() => onSelectTab(tab.id)}
            className={`flex-1 text-center py-4 text-xs font-black transition-all border-b-2 flex items-center justify-center gap-1.5 ${
              isActive
                ? 'text-[#10b981] border-[#10b981] bg-[#1f2937]/30'
                : 'text-[#9ca3af] border-transparent hover:text-white hover:bg-[#1f2937]/10'
            }`}
          >
            <span>{tab.icon}</span>
            <span>{tab.label}</span>
          </button>
        );
      })}
    </div>
  );
}
