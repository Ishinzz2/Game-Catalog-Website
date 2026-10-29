@props(['label','value','detail','icon'])
<div class="rounded-2xl border border-white/10 bg-white/[.035] p-5">
    <div class="flex items-start justify-between gap-3"><p class="text-sm text-slate-400">{{ $label }}</p><span class="grid h-9 w-9 place-items-center rounded-xl bg-violet-400/10 text-lg text-violet-300">{{ $icon }}</span></div>
    <p class="mt-4 text-3xl font-black tracking-tight">{{ $value }}</p><p class="mt-2 text-xs text-slate-500">{{ $detail }}</p>
</div>
