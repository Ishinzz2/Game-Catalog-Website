<div class="space-y-6">
    <form wire:submit="save" class="grid gap-3 rounded-2xl border border-white/10 bg-slate-900 p-5 md:grid-cols-2">
        <h2 class="text-lg font-bold md:col-span-2">{{ $editingId ? 'Edit platform' : 'Tambah platform' }}</h2>
        <input wire:model.live="name" placeholder="Nama platform" class="rounded-xl bg-slate-950 p-3">
        <input wire:model="code" placeholder="Kode unik (contoh: steam)" class="rounded-xl bg-slate-950 p-3">
        <input wire:model="company_name" placeholder="Perusahaan (opsional)" class="rounded-xl bg-slate-950 p-3">
        <input wire:model="icon_path" placeholder="Path / URL ikon (opsional)" class="rounded-xl bg-slate-950 p-3">
        @foreach($errors->all() as $error)<p class="text-sm text-red-300 md:col-span-2">{{ $error }}</p>@endforeach
        <div class="flex gap-2 md:col-span-2"><button class="rounded-xl bg-violet-500 px-5 py-3 font-semibold">Simpan</button>@if($editingId)<button type="button" wire:click="resetForm" class="rounded-xl border border-white/10 px-5">Batal</button>@endif</div>
    </form>
    <input wire:model.live.debounce.300ms="search" placeholder="Cari platform..." class="w-full rounded-xl border border-white/10 bg-slate-900 p-3">
    <div class="overflow-x-auto rounded-2xl border border-white/10"><table class="w-full text-left text-sm"><thead class="bg-slate-900 text-slate-400"><tr><th class="p-3">Platform</th><th class="p-3">Akun terhubung/terdaftar</th><th class="p-3">Aksi</th></tr></thead><tbody>
    @forelse($platforms as $platform)<tr class="border-t border-white/10"><td class="p-3"><div class="font-medium">{{ $platform->name }}</div><div class="text-xs text-slate-500">{{ $platform->code }} · {{ $platform->company_name ?: 'Perusahaan tidak dicantumkan' }}</div></td><td class="p-3">{{ $platform->user_accounts_count }}</td><td class="space-x-3 p-3"><button wire:click="edit({{ $platform->id }})" class="text-cyan-300">Edit</button><button wire:click="delete({{ $platform->id }})" wire:confirm="Hapus platform ini?" class="text-red-300">Hapus</button></td></tr>
    @empty<tr><td colspan="3" class="p-5 text-center text-slate-500">Belum ada platform.</td></tr>@endforelse
    </tbody></table></div>{{ $platforms->links() }}
</div>
