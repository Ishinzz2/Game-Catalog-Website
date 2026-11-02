@extends('layouts.app')
@section('title','Katalog Game')
@section('page-heading','Katalog Game')
@section('content')
<form method="GET" class="mb-6 flex gap-2"><input name="q" value="{{request('q')}}" placeholder="Cari game..." class="w-full rounded-xl border border-white/10 bg-slate-900 p-3"><button class="rounded-xl bg-violet-500 px-5">Cari</button></form>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">@forelse($games as $game)<a href="{{route('games.show',$game)}}" class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900 hover:border-violet-400/50">@if($game->cover_url)<img src="{{$game->cover_url}}" alt="" class="h-44 w-full object-cover">@else<div class="grid h-44 place-items-center bg-slate-800 text-slate-500">No cover</div>@endif<div class="p-4"><h2 class="font-semibold">{{$game->title}}</h2><p class="mt-1 text-sm text-slate-400">{{$game->categories->pluck('name')->join(' · ')}}</p></div></a>@empty<p class="text-slate-400">Game tidak ditemukan.</p>@endforelse</div><div class="mt-6">{{$games->links()}}</div>
@endsection