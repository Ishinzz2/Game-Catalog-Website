<div class="space-y-5">
    <p class="text-sm text-slate-400">Admin dapat mengatur role dan menonaktifkan akun. Akun yang sedang dipakai dan administrator aktif terakhir dilindungi.</p>
    <input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email pengguna..." class="w-full rounded-xl border border-white/10 bg-slate-900 p-3">
    <div class="overflow-x-auto rounded-2xl border border-white/10"><table class="w-full text-left text-sm"><thead class="bg-slate-900 text-slate-400"><tr><th class="p-3">Pengguna</th><th class="p-3">Role</th><th class="p-3">Terdaftar</th><th class="p-3">Aksi</th></tr></thead><tbody>
    @forelse($users as $user)<tr class="border-t border-white/10"><td class="p-3"><div class="font-medium">{{ $user->name }}</div><div class="text-xs text-slate-500">{{ $user->email }}</div>@if($user->trashed())<div class="mt-1 text-xs text-red-300">Nonaktif</div>@endif</td><td class="p-3"><span class="rounded-full px-2 py-1 text-xs {{ $user->role === 'admin' ? 'bg-violet-400/10 text-violet-300' : 'bg-slate-700 text-slate-300' }}">{{ ucfirst($user->role) }}</span></td><td class="p-3 text-slate-400">{{ $user->created_at?->format('d M Y') }}</td><td class="p-3">
        @if(!$user->is(auth()->user()))
            <div class="flex flex-wrap gap-2">
                @if($user->trashed())
                    <button wire:click="restore({{ $user->id }})" wire:confirm="Aktifkan kembali akun ini?" class="text-emerald-300">Aktifkan kembali</button>
                @else
                    @if($user->role === 'user')<button wire:click="updateRole({{ $user->id }}, 'admin')" wire:confirm="Jadikan pengguna ini administrator?" class="text-cyan-300">Jadikan Admin</button>@else<button wire:click="updateRole({{ $user->id }}, 'user')" wire:confirm="Turunkan role administrator ini menjadi User?" class="text-amber-300">Jadikan User</button>@endif
                    <button wire:click="delete({{ $user->id }})" wire:confirm="Nonaktifkan akun pengguna ini? Data akan tetap tersimpan." class="text-red-300">Nonaktifkan</button>
                @endif
            </div>
        @else<span class="text-xs text-slate-500">Akun Anda</span>@endif
    </td></tr>
    @empty<tr><td colspan="4" class="p-5 text-center text-slate-500">Pengguna tidak ditemukan.</td></tr>@endforelse
    </tbody></table></div>{{ $users->links() }}
</div>
