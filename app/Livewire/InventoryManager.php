<?php
namespace App\Livewire;

use App\Models\Game;
use App\Models\GamePlaySession;
use App\Models\UserGameInventory;
use App\Models\UserPlatformAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterPlatform = '';
    public string $filterStatus = '';
    public string $filterFormat = '';
    public string $filterGenre = '';
    public string $filterBacklogPriority = '';
    public string $filterInstalled = '';
    public string $filterAcquisition = '';
    public string $sortBy = 'recent';
    public string $viewMode = 'grid';

    public ?int $game_id = null;
    public ?int $user_platform_account_id = null;
    public string $format = 'Digital';
    public string $acquisition_type = 'Purchased';
    public ?string $purchase_price = null;
    public ?string $purchase_date = null;
    public string $completion_status = 'Backlog';
    public int $playtime_minutes = 0;
    public bool $is_installed = false;
    public ?int $editingId = null;
    public ?int $rating_user = null;
    public ?string $review_notes = null;
    public ?string $last_played_at = null;
    public string $backlog_priority = 'Medium';
    public ?string $backlog_target_date = null;
    public ?string $backlog_notes = null;

    protected function rules(): array
    {
        return [
            'game_id' => ['required', 'integer', Rule::exists('games', 'id')],
            'user_platform_account_id' => ['required', 'integer', Rule::exists('user_platform_accounts', 'id')->where('user_id', auth()->id())],
            'format' => ['required', Rule::in(['Digital', 'Physical Disc', 'Physical Cartridge'])],
            'acquisition_type' => ['required', Rule::in(['Purchased', 'Subscription/Game Pass/PS Plus', 'Gift', 'Free-to-Play'])],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:today'],
            'completion_status' => ['required', Rule::in(['Unplayed', 'Backlog', 'Playing', 'Completed', '100% Platinum', 'Dropped'])],
            'playtime_minutes' => ['required', 'integer', 'min:0', 'max:10000000'],
            'is_installed' => ['boolean'],
            'rating_user' => ['nullable', 'integer', 'between:1,10'],
            'review_notes' => ['nullable', 'string', 'max:3000'],
            'last_played_at' => ['nullable', 'date', 'before_or_equal:now'],
            'backlog_priority' => ['required', Rule::in(['Low', 'Medium', 'High'])],
            'backlog_target_date' => ['nullable', 'date'],
            'backlog_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();
        $user = auth()->user();
        $account = UserPlatformAccount::where('user_id', $user->id)->findOrFail($data['user_platform_account_id']);

        DB::transaction(function () use ($data, $user, $account): void {
            $item = $this->editingId
                ? UserGameInventory::where('user_id', $user->id)->findOrFail($this->editingId)
                : new UserGameInventory();

            $item->fill([
                'user_id' => $user->id,
                'game_id' => $data['game_id'],
                'user_platform_account_id' => $account->id,
                'format' => $data['format'],
                'acquisition_type' => $data['acquisition_type'],
                'purchase_price' => $data['purchase_price'] ?? null,
                'purchase_date' => $data['purchase_date'] ?? null,
                'is_installed' => $data['is_installed'],
                'backlog_priority' => $data['backlog_priority'],
                'backlog_target_date' => $data['backlog_target_date'] ?? null,
                'backlog_notes' => $data['backlog_notes'] ?? null,
            ])->save();

            GamePlaySession::updateOrCreate(
                ['user_game_inventory_id' => $item->id],
                [
                    'completion_status' => $data['completion_status'],
                    'manual_playtime_minutes' => $data['playtime_minutes'],
                    'playtime_minutes' => (int) $data['playtime_minutes'] + (int) $item->activitySessions()->sum('duration_minutes'),
                    'rating_user' => $data['rating_user'] ?? null,
                    'review_notes' => $data['review_notes'] ?? null,
                    'last_played_at' => $data['last_played_at'] ? \Illuminate\Support\Carbon::parse($data['last_played_at']) : null,
                ]
            );
        });

        $this->resetForm();
        session()->flash('status', 'Inventaris berhasil disimpan.');
    }

    public function edit(int $id): void
    {
        $item = UserGameInventory::where('user_id', auth()->id())->with('playSession')->findOrFail($id);
        $this->editingId = $item->id;
        $this->game_id = $item->game_id;
        $this->user_platform_account_id = $item->user_platform_account_id;
        $this->format = $item->format;
        $this->acquisition_type = $item->acquisition_type;
        $this->purchase_price = $item->purchase_price;
        $this->purchase_date = $item->purchase_date?->format('Y-m-d');
        $this->is_installed = $item->is_installed;
        $this->completion_status = $item->playSession?->completion_status ?? 'Backlog';
        $this->playtime_minutes = $item->playSession?->manual_playtime_minutes ?? 0;
        $this->rating_user = $item->playSession?->rating_user;
        $this->review_notes = $item->playSession?->review_notes;
        $this->last_played_at = $item->playSession?->last_played_at?->format('Y-m-d\\TH:i');
        $this->backlog_priority = $item->backlog_priority ?? 'Medium';
        $this->backlog_target_date = $item->backlog_target_date?->format('Y-m-d');
        $this->backlog_notes = $item->backlog_notes;
    }

    public function resetForm(): void
    {
        $this->reset([
            'game_id', 'user_platform_account_id', 'purchase_price', 'purchase_date', 'editingId',
            'playtime_minutes', 'is_installed', 'rating_user', 'review_notes', 'last_played_at',
            'backlog_target_date', 'backlog_notes',
        ]);
        $this->format = 'Digital';
        $this->acquisition_type = 'Purchased';
        $this->completion_status = 'Backlog';
        $this->backlog_priority = 'Medium';
    }

    public function delete(int $id): void
    {
        UserGameInventory::where('user_id', auth()->id())->findOrFail($id)->delete();
        session()->flash('status', 'Item inventaris dihapus.');
    }

    public function render()
    {
        $inventories = UserGameInventory::where('user_id', auth()->id())
            ->with(['game.categories', 'platformAccount.platform', 'playSession'])
            ->when($this->search, fn ($q) => $q->whereHas('game', fn ($g) => $g->where('title', 'like', '%'.$this->search.'%')))
            ->when($this->filterPlatform, fn ($q) => $q->where('user_platform_account_id', $this->filterPlatform))
            ->when($this->filterFormat, fn ($q) => $q->where('format', $this->filterFormat))
            ->when($this->filterStatus, fn ($q) => $q->whereHas('playSession', fn ($s) => $s->where('completion_status', $this->filterStatus)))
            ->when($this->filterGenre, fn ($q) => $q->whereHas('game.categories', fn ($c) => $c->where('categories.id', $this->filterGenre)))
            ->when($this->filterBacklogPriority, fn ($q) => $q->where('backlog_priority', $this->filterBacklogPriority)
                ->whereHas('playSession', fn ($s) => $s->whereIn('completion_status', ['Unplayed', 'Backlog'])))
            ->when($this->filterInstalled !== '', fn ($q) => $q->where('is_installed', $this->filterInstalled === '1'))
            ->when($this->filterAcquisition, fn ($q) => $q->where('acquisition_type', $this->filterAcquisition))
            ->when($this->sortBy === 'title', fn ($q) => $q->join('games as sort_games', 'sort_games.id', '=', 'user_game_inventories.game_id')->orderBy('sort_games.title')->select('user_game_inventories.*'))
            ->when($this->sortBy === 'playtime', fn ($q) => $q->orderBy(GamePlaySession::select('playtime_minutes')->whereColumn('user_game_inventory_id', 'user_game_inventories.id')->limit(1), 'desc'))
            ->when($this->sortBy === 'purchase_price', fn ($q) => $q->orderByDesc('purchase_price'))
            ->when($this->sortBy === 'recent', fn ($q) => $q->orderByDesc('created_at'))
            ->paginate(12);

        return view('livewire.inventory-manager', [
            'games' => Game::orderBy('title')->get(['id', 'title']),
            'accounts' => UserPlatformAccount::where('user_id', auth()->id())->with('platform')->get(),
            'categories' => \App\Models\Category::orderBy('name')->get(['id', 'name']),
            'inventories' => $inventories,
        ])->layout('layouts.app', ['pageHeading' => 'Inventaris Saya']);
    }
}
