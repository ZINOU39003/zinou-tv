export default function LiveBadge({ text }: { text: string }) {
  return (
    <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#ef4444]/20 text-[#ef4444] border border-[#ef4444]/30">
      <span className="w-2 h-2 rounded-full bg-[#ef4444] animate-live-pulse" />
      {text}
    </span>
  );
}
