<div class="mx-auto max-w-md rounded-3xl border border-white/10 bg-slate-900 p-8">
<h1 class="text-2xl font-bold">Buat akun GameVault</h1><p class="mt-2 text-sm text-slate-400">Daftar untuk mengelola library game milikmu.</p>
<form wire:submit="register" class="mt-6 space-y-4">
<div><label class="text-sm">Nama</label><input wire:model="name" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950 p-3">@error('name')<p class="text-sm text-red-400">{{$message}}</p>@enderror</div>
<div><label class="text-sm">Email</label><input type="email" wire:model="email" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950 p-3">@error('email')<p class="text-sm text-red-400">{{$message}}</p>@enderror</div>
<div><label class="text-sm">Password</label><input type="password" wire:model="password" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950 p-3">@error('password')<p class="text-sm text-red-400">{{$message}}</p>@enderror</div>
<div><label class="text-sm">Konfirmasi password</label><input type="password" wire:model="password_confirmation" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950 p-3"></div>
<button class="w-full rounded-xl bg-violet-500 p-3 font-semibold">Daftar</button></form><a href="{{route('login')}}" class="mt-5 block text-center text-sm text-violet-300">Sudah punya akun? Masuk</a></div>