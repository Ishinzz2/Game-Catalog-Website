<div class="space-y-6">
<form wire:submit="save" class="grid gap-3 rounded-2xl border border-white/10 bg-slate-900 p-5 md:grid-cols-2">
<h2 class="text-lg font-bold md:col-span-2">{{$editingId?'Edit game':'Tambah game ke katalog'}}</h2>
<input wire:model.live="title" placeholder="Judul game" class="rounded-xl bg-slate-950 p-3"><input wire:model="slug" placeholder="Slug URL" class="rounded-xl bg-slate-950 p-3">
<textarea wire:model="description" placeholder="Deskripsi" class="rounded-xl bg-slate-950 p-3 md:col-span-2"></textarea>
<input wire:model="cover_url" placeholder="URL cover (https://...)" class="rounded-xl bg-slate-950 p-3"><input wire:model="banner_url" placeholder="URL banner (https://...)" class="rounded-xl bg-slate-950 p-3">
<input type="date" wire:model="release_date" class="rounded-xl bg-slate-950 p-3"><input type="number" min="0" max="100" wire:model="metacritic_score" placeholder="Metacritic 0-100" class="rounded-xl bg-slate-950 p-3">
<select wire:model="esrb_rating" class="rounded-xl bg-slate-950 p-3" aria-label="ESRB rating">
<option value="">Pilih rating ESRB (opsional)</option>
<option value="RP">RP — Rating Pending</option>
<option value="EC">EC — Early Childhood</option>
<option value="E">E — Everyone</option>
<option value="E10+">E10+ — Everyone 10+</option>
<option value="T">T — Teen</option>
<option value="M">M — Mature 17+</option>
<option value="AO">AO — Adults Only 18+</option>
</select>
<fieldset class="rounded-xl border border-white/10 p-3 md:col-span-2"><legend class="px-2 text-sm text-slate-400">Kategori / genre game</legend><div class="flex flex-wrap gap-3">@forelse($categories as $category)<label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="categoryIds" value="{{ $category->id }}" class="rounded border-white/20 bg-slate-950"> {{ $category->name }}</label>@empty<span class="text-sm text-slate-500">Belum ada kategori. Tambahkan melalui menu Kategori.</span>@endforelse</div></fieldset>
<div class="flex gap-2 md:col-span-2"><button class="rounded-xl bg-violet-500 px-5 py-3 font-semibold">Simpan</button>@if($editingId)<button type="button" wire:click="resetForm" class="rounded-xl border border-white/10 px-5">Batal</button>@endif</div>
@foreach($errors->all() as $error)<p class="text-sm text-red-300 md:col-span-2">{{$error}}</p>@endforeach
</form>
<input wire:model.live.debounce.300ms="search" placeholder="Cari judul game..." class="w-full rounded-xl border border-white/10 bg-slate-900 p-3">
<div class="overflow-hidden rounded-2xl border border-white/10"><table class="w-full text-left text-sm"><thead class="bg-slate-900 text-slate-400"><tr><th class="p-3">Game</th><th class="p-3">Rilis</th><th class="p-3">Metacritic</th><th class="p-3">Aksi</th></tr></thead><tbody>@forelse($games as $game)<tr class="border-t border-white/10"><td class="p-3 font-medium">{{$game->title}}</td><td class="p-3">{{$game->release_date?->format('d M Y') ?: '—'}}</td><td class="p-3">{{$game->metacritic_score ?? '—'}}</td><td class="space-x-3 p-3"><button wire:click="edit({{$game->id}})" class="text-cyan-300">Edit</button><button wire:click="delete({{$game->id}})" wire:confirm="Hapus game ini dari katalog?" class="text-red-300">Hapus</button></td></tr>@empty<tr><td colspan="4" class="p-5 text-center text-slate-500">Belum ada game.</td></tr>@endforelse</tbody></table></div>{{$games->links()}}
</div>