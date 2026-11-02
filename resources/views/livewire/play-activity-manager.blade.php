<div class="space-y-6">
    @if (session('status'))<div class="rounded-xl border border-emerald-400/20 bg-emerald-400/10 p-3 text-sm text-emerald-300">{{ session('status') }}</div>@endif
    <section class="rounded-2xl border border-white/10 bg-white/[.035] p-5">
        <h2 class="text-lg font-bold">Aktivitas bermain · 7 hari terakhir</h2>
        <p class="mt-1 text-xs text-slate-500">Grafik dihitung dari sesi yang kamu catat sendiri.</p>
        <div class="mt-5 grid h-48 grid-cols-7 items-end gap-3">
            @foreach($chart as $day)
                <div class="flex h-full flex-col items-center justify-end gap-2">
                    <span class="text-[10px] text-slate-400">{{ $day['minutes'] ? number_format($day['minutes']/60, 1).'j' : '—' }}</span>
                    <div class="w-full rounded-t-lg bg-violet-500/80" style="height: {{ $day['minutes'] ? max(5, min(100, ($day['minutes'] / max(1, $chart->max('minutes'))) * 100)) : 3 }}%"></div>
                    <span class="text-[10px] text-slate-500">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-white/10 bg-white/[.035] p-5">
            <h2 class="text-lg font-bold">Catat sesi bermain</h2>
            <form wire:submit="saveSession" class="mt-4 space-y-3">
                <select wire:model="inventory_id" class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-3 text-sm"><option value="">Pilih game dari inventaris</option>@foreach($inventories as $item)<option value="{{ $item->id }}">{{ $item->game->title }}</option>@endforeach</select>@error('inventory_id')<p class="text-xs text-rose-300">{{ $message }}</p>@enderror
                <div class="grid gap-3 sm:grid-cols-2"><div><label class="text-xs text-slate-400">Mulai</label><input wire:model="started_at" type="datetime-local" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-3 text-sm">@error('started_at')<p class="text-xs text-rose-300">{{ $message }}</p>@enderror</div><div><label class="text-xs text-slate-400">Selesai</label><input wire:model="ended_at" type="datetime-local" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-3 text-sm">@error('ended_at')<p class="text-xs text-rose-300">{{ $message }}</p>@enderror</div></div>
                <textarea wire:model="session_notes" rows="2" placeholder="Catatan sesi (opsional)" class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-3 text-sm"></textarea>
                <button class="rounded-xl bg-violet-500 px-4 py-3 text-sm font-bold">Simpan sesi</button>
            </form>
        </section>
        <section class="rounded-2xl border border-white/10 bg-white/[.035] p-5">
            <h2 class="text-lg font-bold">Catat achievement</h2>
            <p class="mt-1 text-xs text-slate-500">Achievement dicatat manual; belum mengambil data dari Steam/PSN/Xbox.</p>
            <form wire:submit="saveAchievement" class="mt-4 space-y-3">
                <select wire:model="achievement_inventory_id" class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-3 text-sm"><option value="">Pilih game dari inventaris</option>@foreach($inventories as $item)<option value="{{ $item->id }}">{{ $item->game->title }}</option>@endforeach</select>@error('achievement_inventory_id')<p class="text-xs text-rose-300">{{ $message }}</p>@enderror
                <input wire:model="achievement_name" placeholder="Nama achievement" class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-3 text-sm">@error('achievement_name')<p class="text-xs text-rose-300">{{ $message }}</p>@enderror
                <textarea wire:model="achievement_description" rows="2" placeholder="Deskripsi (opsional)" class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-3 text-sm"></textarea>
                <input wire:model="achievement_unlocked_at" type="datetime-local" class="w-full rounded-xl border border-white/10 bg-slate-900 px-3 py-3 text-sm">
                <button class="rounded-xl bg-cyan-500/20 px-4 py-3 text-sm font-bold text-cyan-200">Simpan achievement</button>
            </form>
        </section>
    </div>

    <section class="rounded-2xl border border-white/10 bg-white/[.035] p-5">
        <h2 class="text-lg font-bold">Riwayat sesi</h2>
        <div class="mt-4 space-y-2">@forelse($sessions as $session)<div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-950/50 p-3"><div><p class="text-sm font-semibold">{{ $session->inventory->game->title }}</p><p class="text-xs text-slate-500">{{ $session->started_at->format('d M Y H:i') }} – {{ $session->ended_at->format('H:i') }} · {{ intdiv($session->duration_minutes,60) }}j {{ $session->duration_minutes%60 }}m</p>@if($session->notes)<p class="mt-1 text-xs text-slate-400">{{ $session->notes }}</p>@endif</div><button wire:click="deleteSession({{ $session->id }})" wire:confirm="Hapus sesi ini?" class="text-xs text-rose-300">Hapus</button></div>@empty<p class="text-sm text-slate-500">Belum ada sesi yang dicatat.</p>@endforelse</div>
    </section>
    <section class="rounded-2xl border border-white/10 bg-white/[.035] p-5">
        <h2 class="text-lg font-bold">Achievement tercatat</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">@forelse($achievements as $achievement)<div class="rounded-xl bg-slate-950/50 p-3"><div class="flex justify-between gap-3"><div><p class="text-sm font-semibold">{{ $achievement->name }}</p><p class="text-xs text-violet-300">{{ $achievement->inventory->game->title }}</p></div><button wire:click="deleteAchievement({{ $achievement->id }})" wire:confirm="Hapus achievement ini?" class="text-xs text-rose-300">Hapus</button></div>@if($achievement->description)<p class="mt-2 text-xs text-slate-400">{{ $achievement->description }}</p>@endif<p class="mt-2 text-[10px] text-slate-500">{{ $achievement->unlocked_at?->format('d M Y H:i') ?? 'Tanggal belum dicatat' }}</p></div>@empty<p class="text-sm text-slate-500">Belum ada achievement.</p>@endforelse</div>
    </section>
</div>
