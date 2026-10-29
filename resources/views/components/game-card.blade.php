@props(['inventory'])
<div class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[.035] transition hover:-translate-y-1 hover:border-violet-400/40 hover:bg-white/[.055]">
    <a href="{{ route('games.show',$inventory->game) }}" class="block">
        <div class="relative aspect-[16/10] overflow-hidden bg-slate-800">
            @if($inventory->game->cover_url)
                <img src="{{ $inventory->game->cover_url }}" alt="Sampul {{ $inventory->game->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
            @else
                <div class="grid h-full place-items-center bg-gradient-to-br from-violet-950 via-slate-800 to-cyan-950"><span class="px-4 text-center text-2xl font-black text-white/80">{{ $inventory->game->title }}</span></div>
            @endif
            <span class="absolute left-3 top-3 rounded-lg border border-white/15 bg-slate-950/80 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-cyan-200">{{ $inventory->platformAccount?->platform?->name ?? 'Platform' }}</span>
        </div>
        <div class="p-4">
            <h3 class="truncate font-bold">{{ $inventory->game->title }}</h3>
            <p class="mt-1 text-xs text-slate-500">{{ $inventory->game->developer?->name ?? 'Developer tidak diketahui' }}</p>
            <div class="mt-4 flex items-center justify-between gap-2">
                <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold {{ match($inventory->playSession?->completion_status) { 'Completed','100% Platinum'=>'bg-emerald-400/10 text-emerald-300', 'Playing'=>'bg-cyan-400/10 text-cyan-300', 'Dropped'=>'bg-rose-400/10 text-rose-300', default=>'bg-amber-400/10 text-amber-300' } }}">{{ $inventory->playSession?->completion_status ?? 'Backlog' }}</span>
                <span class="text-xs text-slate-400">{{ number_format(($inventory->playSession?->playtime_minutes ?? 0)/60,1) }} jam</span>
            </div>
        </div>
    </a>
</div>
