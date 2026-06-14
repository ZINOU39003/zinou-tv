'use client';

interface DaySelectorProps {
  currentOffset: number;
  onSelect: (offset: number) => void;
}

export default function DaySelector({ currentOffset, onSelect }: DaySelectorProps) {
  const days = [
    { offset: -1, label: 'مباريات الأمس', icon: '◀' },
    { offset: 0, label: 'مباريات اليوم', icon: '★' },
    { offset: 1, label: 'مباريات القادمة', icon: '▶' },
  ];

  return (
    <div className="flex justify-center gap-3 mb-6">
      {days.map((d) => (
        <button
          key={d.offset}
          onClick={() => onSelect(d.offset)}
          className={`relative px-6 py-3 rounded-xl text-sm font-extrabold transition-all duration-200 ${
            currentOffset === d.offset
              ? d.offset === 0
                ? 'bg-gradient-to-r from-[#10b981] to-[#059669] text-white shadow-lg shadow-[#10b981]/30 scale-105'
                : d.offset === -1
                ? 'bg-gradient-to-r from-[#6366f1] to-[#4f46e5] text-white shadow-lg shadow-[#6366f1]/30 scale-105'
                : 'bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white shadow-lg shadow-[#3b82f6]/30 scale-105'
              : 'bg-[#1f2937] text-[#9ca3af] hover:bg-[#374151] hover:text-white'
          }`}
        >
          <span className="flex items-center gap-2">
            <span>{d.icon}</span>
            <span>{d.label}</span>
          </span>
        </button>
      ))}
    </div>
  );
}
