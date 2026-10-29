<?php
use App\\Livewire\\Auth\\Login;
use App\\Livewire\\InventoryManager;
use App\\Models\\Game;
use App\\Models\\UserGameInventory;
use Illuminate\\Support\\Facades\\Auth;
use Illuminate\\Support\\Facades\\Route;
Route::get('/login', Login::class)->name('login');
Route::post('/logout', function () { Auth::logout(); request()->session()->invalidate(); request()->session()->regenerateToken(); return redirect()->route('login'); })->middleware('auth')->name('logout');
Route::get('/', function () {
    $user = auth()->user();
    $inventories = $user
        ? UserGameInventory::query()->where('user_id', $user->id)
            ->with(['game.publisher','game.developer','game.categories','platformAccount.platform','playSession'])
            ->latest()->take(8)->get()
        : collect();
    $stats = [
        'games' => $user ? UserGameInventory::where('user_id',$user->id)->count() : 0,
        'minutes' => $user ? (int) $user->gameInventories()->withSum('playSession','playtime_minutes')->get()->sum('play_session_sum_playtime_minutes') : 0,
        'value' => $user ? (float) UserGameInventory::where('user_id',$user->id)->sum('purchase_price') : 0,
        'completed' => $user ? $user->gameInventories()->whereHas('playSession', fn($q) => $q->whereIn('completion_status',['Completed','100% Platinum']))->count() : 0,
    ];
    return view('dashboard', compact('inventories','stats'));
})->name('dashboard');
Route::get('/games/{game:slug}', function (Game $game) {
    $game->load(['publisher','developer','categories','tags','inventories.platformAccount.platform','inventories.playSession']);
    $ownedElsewhere = auth()->check()
        ? UserGameInventory::where('user_id',auth()->id())->where('game_id',$game->id)->count() > 1
        : false;
    return view('games.show', compact('game','ownedElsewhere'));
})->name('games.show');
Route::middleware('auth')->group(function () {
    Route::get('/inventory/manage', InventoryManager::class)->name('inventory.manage');
});
