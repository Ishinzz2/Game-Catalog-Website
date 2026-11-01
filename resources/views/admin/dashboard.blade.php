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
<div class="mt-6 rounded-2xl border border-white/10 bg-slate-900 p-5"><h2 class="font-semibold">Pengelolaan</h2><p class="mt-2 text-sm text-slate-400">Kelola master game melalui katalog admin. Pengaturan integrasi API tersedia di halaman khusus setelah Tahap 3 digabungkan.</p><a href="{{route('admin.games.index')}}" class="mt-4 inline-flex rounded-xl bg-violet-500 px-4 py-3 text-sm font-semibold">Kelola katalog game</a></div>
@endsection