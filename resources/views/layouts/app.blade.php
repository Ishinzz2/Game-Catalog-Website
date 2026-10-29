<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Game Catalog') · Game Catalog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class',theme:{extend:{colors:{ink:'#090d18',panel:'#111827',accent:'#8b5cf6',cyan:'#22d3ee'}}}}</script>
    @livewireStyles
</head>
<body class="min-h-screen bg-[#090d18] text-slate-100 antialiased">
<div class="min-h-screen lg:flex">
    <aside class="border-b border-white/10 bg-slate-950/80 p-5 lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col lg:border-b-0 lg:border-r">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 text-lg font-black tracking-tight"><span class="grid h-10 w-10 place-items-center rounded-xl bg-violet-500 text-xl">G</span><span>GAME<span class="text-violet-400">VAULT</span><small class="block text-[10px] font-semibold uppercase tracking-[.25em] text-slate-500">Library command center</small></span></a>
        <nav class="mt-8 flex gap-2 lg:flex-col">
            <a href="{{ route('dashboard') }}" class="rounded-xl px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white">▦ &nbsp; Dashboard</a>
            @auth<a href="{{ route('inventory.manage') }}" class="rounded-xl px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white">▤ &nbsp; Inventaris Saya</a>@else<a href="{{ route('login') }}" class="rounded-xl px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white">↪ &nbsp; Masuk</a>@endauth
        </nav>
        <div class="mt-auto hidden rounded-2xl border border-violet-400/20 bg-violet-400/5 p-4 lg:block"><p class="text-xs font-semibold uppercase tracking-widest text-violet-300">Satu library</p><p class="mt-2 text-sm text-slate-400">Semua platform. Satu tempat. Progress tetap terpantau.</p>
        @auth<form method="POST" action="{{ route('logout') }}" class="mt-4">@csrf<button class="text-xs text-slate-500 hover:text-white">Keluar dari akun →</button></form>@endauth</div>
    </aside>
    <main class="min-w-0 flex-1 p-4 sm:p-7 lg:ml-64 lg:p-10">
        <header class="mb-8 flex items-center justify-between gap-4"><div><p class="text-xs font-semibold uppercase tracking-[.25em] text-cyan-300">Game library / inventory</p><h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">@yield('page-heading','Dashboard')</h1></div><span class="rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs text-slate-400">MULTI-PLATFORM</span></header>
        @if (session('status'))<div class="mb-5 rounded-xl border border-emerald-400/20 bg-emerald-400/10 p-3 text-sm text-emerald-300">{{ session('status') }}</div>@endif
        @yield('content')
        <footer class="mt-12 border-t border-white/10 pt-5 text-xs text-slate-600">GAMEVAULT · Merged Game Library · Laravel + Livewire</footer>
    </main>
</div>
@livewireScripts
</body>
</html>
