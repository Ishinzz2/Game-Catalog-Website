@extends('layouts.app')
@section('title','Admin Dashboard')
@section('page-heading','Admin Dashboard')
@section('content')
<p class="mb-6 text-sm text-slate-400">Ringkasan data utama GameVault untuk administrator.</p>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
@foreach([['Total pengguna',$stats['users']],['Game katalog',$stats['games']],['Platform tersedia',$stats['platforms']],['Akun platform pengguna',$stats['accounts']]] as [$label,$value])
<div class="rounded-2xl border border-white/10 bg-slate-900 p-5"><p class="text-sm text-slate-400">{{$label}}</p><p class="mt-3 text-3xl font-bold">{{number_format($value)}}</p></div>
@endforeach
</div>
<div class="mt-6 rounded-2xl border border-white/10 bg-slate-900 p-5"><h2 class="font-semibold">Kontrol website</h2><p class="mt-2 text-sm text-slate-400">Kelola katalog, kategori, akun pengguna, dan daftar platform dari panel administrator.</p><div class="mt-4 flex flex-wrap gap-3"><a href="{{route('admin.games.index')}}" class="rounded-xl bg-violet-500 px-4 py-3 text-sm font-semibold">Katalog game</a><a href="{{route('admin.categories.index')}}" class="rounded-xl border border-white/10 px-4 py-3 text-sm">Kategori</a><a href="{{route('admin.users.index')}}" class="rounded-xl border border-white/10 px-4 py-3 text-sm">Pengguna</a><a href="{{route('admin.platforms.index')}}" class="rounded-xl border border-white/10 px-4 py-3 text-sm">Platform</a></div><p class="mt-4 text-xs text-slate-500">Konfigurasi integrasi API dikelola pada tahap integrasi terpisah.</p></div>
@endsection