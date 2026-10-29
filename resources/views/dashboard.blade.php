@extends('layouts.app')
@section('title','Dashboard')
@section('page-heading','Your gaming universe')
@section('content')
<div class="relative mb-8 overflow-hidden rounded-3xl border border-violet-400/20 bg-gradient-to-br from-violet-950/70 via-slate-900 to-cyan-950/40 p-6 sm:p-9">
    <div class="relative z-10 max-w-2xl"><p class="text-xs font-bold uppercase tracking-[.3em] text-cyan-300">PLAYER OVERVIEW</p><h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Seluruh koleksi game,<br><span class="text-violet-300">dalam satu command center.</span></h2><p class="mt-4 max-w-xl text-sm leading-6 text-slate-300">Kelola game lintas platform, pantau backlog, dan lihat perjalanan bermainmu tanpa berpindah dashboard.</p><a href="{{ route('inventory.manage') }}" class="mt-6 inline-flex rounded-xl bg-violet-500 px-5 py-3 text-sm font-bold shadow-lg shadow-violet-950/40 transition hover:bg-violet-400">+ Kelola inventaris</a></div>
    <div class="pointer-events-none absolute -right-10 -top-20 h-72 w-72 rounded-full border-[35px] border-cyan-300/5"></div><div class="pointer-events-none absolute -right-2 -bottom-28 h-72 w-72 rounded-full border-[1px] border-violet-300/20"></div>
</div>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-stat-box label="Total koleksi" :value="number_format($stats['games'])" detail="Item dalam inventaris" icon="▣"/>
    <x-stat-box label="Total playtime" :value="number_format($stats['minutes']/60,1).' jam'" detail="Akumulasi waktu bermain" icon="◷"/>
    <x-stat-box label="Estimasi nilai library" :value=" 'Rp '.number_format($stats['value'],0,',','.')" detail="Berdasarkan harga perolehan" icon="◇"/>
    <x-stat-box label="Game selesai" :value="number_format($stats['completed'])" detail="Completed atau 100% Platinum" icon="✓"/>
</div>
<div class="mt-10 flex flex-wrap items-end justify-between gap-3"><div><p class="text-xs font-semibold uppercase tracking-[.2em] text-violet-300">Recently collected</p><h2 class="mt-2 text-xl font-bold">Koleksi terbaru</h2></div><a href="{{ route('inventory.manage') }}" class="text-sm font-semibold text-cyan-300 hover:text-cyan-200">Lihat semua →</a></div>
@if($inventories->isEmpty())
    <div class="mt-5 rounded-2xl border border-dashed border-white/15 p-10 text-center"><div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-white/5 text-2xl">＋</div><h3 class="mt-4 font-bold">Library kamu masih kosong</h3><p class="mt-2 text-sm text-slate-500">Tambahkan akun platform dan game untuk mulai membangun koleksi.</p><a href="{{ route('inventory.manage') }}" class="mt-5 inline-block rounded-xl border border-violet-400/30 px-4 py-2 text-sm text-violet-200">Tambah game pertama</a></div>
@else
    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">@foreach($inventories as $inventory)<x-game-card :inventory="$inventory"/>@endforeach</div>
@endif
@endsection
