<?php
namespace App\Livewire;

use App\Models\Game;
use App\Models\Platform;
use App\Models\UserGameInventory;
use App\Models\UserPlatformAccount;
use App\Models\GamePlaySession;
use App\Models\Wishlist;
use App\Models\WishlistPriceHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class WishlistManager extends Component
{
    public ?int $game_id = null;
    public ?int $target_platform_id = null;
    public ?string $desired_max_price = null;
    public string $priority = 'Medium';
    public ?string $notes = null;
    public ?int $editingId = null;
    public ?int $price_wishlist_id = null;
    public ?string $tracked_price = null;
    public string $price_currency = 'IDR';
    public string $price_source = 'Manual';
    public ?string $price_recorded_at = null;

    protected function rules(): array
    {
        return [
            'game_id' => ['required', 'integer', Rule::exists('games', 'id')],
            'target_platform_id' => ['nullable', 'integer', Rule::exists('platforms', 'id')],
            'desired_max_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        $item = $this->editingId
            ? Wishlist::where('user_id', auth()->id())->findOrFail($this->editingId)
            : new Wishlist(['user_id' => auth()->id()]);
        $item->fill($data)->save();
        $this->resetForm();
        session()->flash('status', 'Wishlist berhasil disimpan.');
    }

    public function edit(int $id): void
    {
        $item = Wishlist::where('user_id', auth()->id())->findOrFail($id);
        $this->editingId = $item->id;
        $this->game_id = $item->game_id;
        $this->target_platform_id = $item->target_platform_id;
        $this->desired_max_price = $item->desired_max_price;
        $this->priority = $item->priority;
        $this->notes = $item->notes;
    }

    public function resetForm(): void
    {
        $this->reset(['game_id', 'target_platform_id', 'desired_max_price', 'notes', 'editingId']);
        $this->priority = 'Medium';
    }

    public function delete(int $id): void
    {
        Wishlist::where('user_id', auth()->id())->findOrFail($id)->delete();
        session()->flash('status', 'Wishlist dihapus.');
    }

    public function recordPrice(int $wishlistId): void
    {
        $data = $this->validate([
            'tracked_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'price_currency' => ['required', 'string', 'size:3'],
            'price_source' => ['required', 'string', 'max:120'],
            'price_recorded_at' => ['nullable', 'date', 'before_or_equal:now'],
        ]);

        $item = Wishlist::where('user_id', auth()->id())->findOrFail($wishlistId);
        WishlistPriceHistory::create([
            'wishlist_id' => $item->id,
            'price' => $data['tracked_price'],
            'currency' => strtoupper($data['price_currency']),
            'source' => $data['price_source'],
            'recorded_at' => $data['price_recorded_at'] ? Carbon::parse($data['price_recorded_at']) : now(),
        ]);

        $this->reset(['tracked_price', 'price_recorded_at']);
        $this->price_wishlist_id = null;
        session()->flash('status', 'Catatan harga berhasil disimpan.');
    }

    public function deletePrice(int $id): void
    {
        WishlistPriceHistory::whereHas('wishlist', fn ($query) => $query->where('user_id', auth()->id()))
            ->findOrFail($id)->delete();
        session()->flash('status', 'Catatan harga dihapus.');
    }

    public function moveToInventory(int $wishlistId, int $accountId): void
    {
        $item = Wishlist::where('user_id', auth()->id())->with('game')->findOrFail($wishlistId);
        $account = UserPlatformAccount::where('user_id', auth()->id())->where('platform_id', $item->target_platform_id)->find($accountId);
        if (!$account) $account = UserPlatformAccount::where('user_id', auth()->id())->findOrFail($accountId);
        if ($item->target_platform_id && $account->platform_id !== $item->target_platform_id) {
            $this->addError('account-'.$wishlistId, 'Pilih akun dari platform tujuan yang sesuai.');
            return;
        }

        DB::transaction(function () use ($item, $account): void {
            $inventory = UserGameInventory::firstOrCreate(
                ['user_id' => auth()->id(), 'game_id' => $item->game_id, 'user_platform_account_id' => $account->id],
                ['format' => 'Digital', 'acquisition_type' => 'Purchased', 'purchase_price' => $item->desired_max_price, 'is_installed' => false]
            );
            GamePlaySession::firstOrCreate(['user_game_inventory_id' => $inventory->id], ['completion_status' => 'Backlog', 'playtime_minutes' => 0]);
            $item->delete();
        });
        session()->flash('status', 'Game dipindahkan ke inventaris.');
    }

    public function render()
    {
        return view('livewire.wishlist-manager', [
            'games' => Game::orderBy('title')->get(['id', 'title']),
            'platforms' => Platform::orderBy('name')->get(),
            'accounts' => UserPlatformAccount::where('user_id', auth()->id())->with('platform')->get(),
            'items' => Wishlist::where('user_id', auth()->id())->with(['game', 'targetPlatform', 'priceHistories' => fn ($query) => $query->latest('recorded_at')])->latest()->get(),
        ])->layout('layouts.app', ['pageHeading' => 'Wishlist Saya']);
    }
}
