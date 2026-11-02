<?php

namespace App\Livewire;

use App\Models\GameAchievement;
use App\Models\PlayActivitySession;
use App\Models\UserGameInventory;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PlayActivityManager extends Component
{
    public ?int $inventory_id = null;
    public ?string $started_at = null;
    public ?string $ended_at = null;
    public ?string $session_notes = null;

    public ?int $achievement_inventory_id = null;
    public string $achievement_name = '';
    public ?string $achievement_description = null;
    public ?string $achievement_unlocked_at = null;

    protected function rules(): array
    {
        return [
            'inventory_id' => ['required', 'integer', Rule::exists('user_game_inventories', 'id')->where('user_id', auth()->id())],
            'started_at' => ['required', 'date', 'before_or_equal:now'],
            'ended_at' => ['required', 'date', 'after:started_at', 'before_or_equal:now'],
            'session_notes' => ['nullable', 'string', 'max:2000'],
            'achievement_inventory_id' => ['required', 'integer', Rule::exists('user_game_inventories', 'id')->where('user_id', auth()->id())],
            'achievement_name' => ['required', 'string', 'max:160'],
            'achievement_description' => ['nullable', 'string', 'max:1000'],
            'achievement_unlocked_at' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    public function saveSession(): void
    {
        $data = $this->validate([
            'inventory_id' => $this->rules()['inventory_id'],
            'started_at' => $this->rules()['started_at'],
            'ended_at' => $this->rules()['ended_at'],
            'session_notes' => $this->rules()['session_notes'],
        ]);

        $start = Carbon::parse($data['started_at']);
        $end = Carbon::parse($data['ended_at']);
        $minutes = $start->diffInMinutes($end);

        if ($minutes < 1) {
            $this->addError('ended_at', 'Durasi sesi minimal satu menit.');
            return;
        }

        $inventory = UserGameInventory::where('user_id', auth()->id())->findOrFail($data['inventory_id']);
        DB::transaction(function () use ($inventory, $start, $end, $minutes, $data): void {
            $lockedInventory = UserGameInventory::whereKey($inventory->id)->lockForUpdate()->firstOrFail();
            PlayActivitySession::create([
                'user_game_inventory_id' => $lockedInventory->id,
                'started_at' => $start,
                'ended_at' => $end,
                'duration_minutes' => $minutes,
                'notes' => $data['session_notes'] ?? null,
            ]);

            $summary = $lockedInventory->playSession()->lockForUpdate()->first();
            if (!$summary) {
                $summary = $lockedInventory->playSession()->create([
                    'completion_status' => 'Backlog',
                    'manual_playtime_minutes' => 0,
                ]);
            }
            $sessionMinutes = $lockedInventory->activitySessions()->sum('duration_minutes');
            $summary->update([
                'playtime_minutes' => (int) $summary->manual_playtime_minutes + (int) $sessionMinutes,
                'last_played_at' => $end,
            ]);
        });

        $this->reset(['started_at', 'ended_at', 'session_notes']);
        session()->flash('status', 'Riwayat sesi bermain berhasil ditambahkan.');
    }

    public function saveAchievement(): void
    {
        $data = $this->validate([
            'achievement_inventory_id' => $this->rules()['achievement_inventory_id'],
            'achievement_name' => $this->rules()['achievement_name'],
            'achievement_description' => $this->rules()['achievement_description'],
            'achievement_unlocked_at' => $this->rules()['achievement_unlocked_at'],
        ]);

        $inventory = UserGameInventory::where('user_id', auth()->id())->findOrFail($data['achievement_inventory_id']);
        GameAchievement::create([
            'user_game_inventory_id' => $inventory->id,
            'name' => $data['achievement_name'],
            'description' => $data['achievement_description'] ?? null,
            'unlocked_at' => $data['achievement_unlocked_at'] ? Carbon::parse($data['achievement_unlocked_at']) : null,
        ]);

        $this->reset(['achievement_name', 'achievement_description', 'achievement_unlocked_at']);
        session()->flash('status', 'Achievement berhasil dicatat.');
    }

    public function deleteSession(int $id): void
    {
        $session = PlayActivitySession::whereHas('inventory', fn ($query) => $query->where('user_id', auth()->id()))
            ->with('inventory.playSession')->findOrFail($id);
        DB::transaction(function () use ($session): void {
            $inventory = UserGameInventory::whereKey($session->inventory->id)->lockForUpdate()->firstOrFail();
            $session->delete();
            $summary = $inventory->playSession()->lockForUpdate()->first();
            if ($summary) {
                $summary->update([
                    'playtime_minutes' => (int) $summary->manual_playtime_minutes + (int) $inventory->activitySessions()->sum('duration_minutes'),
                    'last_played_at' => $inventory->activitySessions()->max('ended_at'),
                ]);
            }
        });

        session()->flash('status', 'Riwayat sesi dihapus dan total playtime diperbarui.');
    }

    public function deleteAchievement(int $id): void
    {
        GameAchievement::whereHas('inventory', fn ($query) => $query->where('user_id', auth()->id()))
            ->findOrFail($id)->delete();
        session()->flash('status', 'Achievement dihapus.');
    }

    public function render()
    {
        $inventories = UserGameInventory::where('user_id', auth()->id())
            ->with('game')->orderBy('id')->get();

        $sessions = PlayActivitySession::whereHas('inventory', fn ($query) => $query->where('user_id', auth()->id()))
            ->with('inventory.game')->latest('started_at')->take(30)->get();

        $achievements = GameAchievement::whereHas('inventory', fn ($query) => $query->where('user_id', auth()->id()))
            ->with('inventory.game')->latest()->get();

        $daily = PlayActivitySession::query()
            ->whereHas('inventory', fn ($query) => $query->where('user_id', auth()->id()))
            ->where('started_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(started_at) as play_date, SUM(duration_minutes) as minutes')
            ->groupBy('play_date')->orderBy('play_date')->get()->keyBy('play_date');

        $chart = collect(range(6, 0))->map(function (int $daysAgo) use ($daily) {
            $date = now()->subDays($daysAgo)->toDateString();
            return ['label' => Carbon::parse($date)->format('d/m'), 'minutes' => (int) ($daily[$date]->minutes ?? 0)];
        });

        return view('livewire.play-activity-manager', compact('inventories', 'sessions', 'achievements', 'chart'))
            ->layout('layouts.app', ['pageHeading' => 'Aktivitas & Achievement']);
    }
}
