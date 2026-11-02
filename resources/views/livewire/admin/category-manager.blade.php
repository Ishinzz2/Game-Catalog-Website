<div class="space-y-6">
    <form wire:submit="save" class="grid gap-3 rounded-2xl border border-white/10 bg-slate-900 p-5 md:grid-cols-2">
        <h2 class="text-lg font-bold md:col-span-2">{{ $editingId ? 'Edit kategori' : 'Tambah kategori' }}</h2>
        <input wire:model.live="name" placeholder="Nama kategori" class="rounded-xl bg-slate-950 p-3">
        <input wire:model="slug" placeholder="Slug URL" class="rounded-xl bg-slate-950 p-3">
        <textarea wire:model="description" placeholder="Deskripsi (opsional)" class="rounded-xl bg-slate-950 p-3 md:col-span-2"></textarea>
        @foreach($errors->all() as $error)<p class="text-sm text-red-300 md:col-span-2">{{ $error }}</p>@endforeach
        <div class="flex gap-2 md:col-span-2"><button class="rounded-xl bg-violet-500 px-5 py-3 font-semibold">Simpan</button>@if($editingId)<button type="button" wire:click="resetForm" class="rounded-xl border border-white/10 px-5">Batal</button>@endif</div>
    </form>
    <input wire:model.live.debounce.300ms="search" placeholder="Cari kategori..." class="w-full rounded-xl border border-white/10 bg-slate-900 p-3">
    <div class="overflow-x-auto rounded-2xl border border-white/10"><table class="w-full text-left text-sm"><thead class="bg-slate-900 text-slate-400"><tr><th class="p-3">Kategori</th><th class="p-3">Jumlah game</th><th class="p-3">Aksi</th></tr></thead><tbody>
    @forelse($categories as $category)<tr class="border-t border-white/10"><td class="p-3"><div class="font-medium">{{ $category->name }}</div><div class="text-xs text-slate-500">{{ $category->slug }}</div></td><td class="p-3">{{ $category->games_count }}</td><td class="space-x-3 p-3"><button wire:click="edit({{ $category->id }})" class="text-cyan-300">Edit</button><button wire:click="delete({{ $category->id }})" wire:confirm="Hapus kategori ini? Hubungan kategori dengan game akan dilepas." class="text-red-300">Hapus</button></td></tr>
    @empty<tr><td colspan="3" class="p-5 text-center text-slate-500">Belum ada kategori.</td></tr>@endforelse
    </tbody></table></div>{{ $categories->links() }}
</div>
