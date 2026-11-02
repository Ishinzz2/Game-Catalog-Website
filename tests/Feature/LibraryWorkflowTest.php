<?php

namespace Tests\Feature;

use App\Livewire\PlayActivityManager;
use App\Livewire\PlatformAccountManager;
use App\Livewire\WishlistManager;
use App\Models\Category;
use App\Models\Game;
use App\Models\GamePlaySession;
use App\Models\Platform;
use App\Models\User;
use App\Models\UserGameInventory;
use App\Models\UserPlatformAccount;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LibraryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function platformAccount(User $user): UserPlatformAccount
    {
        $platform = Platform::create(['name' => 'Steam', 'code' => 'steam']);
        return UserPlatformAccount::create([
            'user_id' => $user->id,
            'platform_id' => $platform->id,
            'account_username' => 'player-one',
        ]);
    }

    private function inventory(User $user, UserPlatformAccount $account, Game $game): UserGameInventory
    {
        return UserGameInventory::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'user_platform_account_id' => $account->id,
            'format' => 'Digital',
            'acquisition_type' => 'Purchased',
            'is_installed' => false,
            'backlog_priority' => 'Medium',
        ]);
    }

    public function test_platform_account_cannot_be_deleted_while_inventory_uses_it(): void
    {
        $user = User::factory()->create();
        $account = $this->platformAccount($user);
        $game = Game::create(['title' => 'Example Game', 'slug' => 'example-game']);
        $this->inventory($user, $account, $game);

        Livewire::actingAs($user)
            ->test(PlatformAccountManager::class)
            ->call('delete', $account->id)
            ->assertHasErrors(['delete-'.$account->id]);

        $this->assertDatabaseHas('user_platform_accounts', [
            'id' => $account->id,
            'deleted_at' => null,
        ]);
    }

    public function test_recorded_play_sessions_are_added_to_manual_playtime_and_recalculated_on_delete(): void
    {
        $fixedNow = now()->setTime(12, 0);
        \Illuminate\Support\Carbon::setTestNow($fixedNow);

        $user = User::factory()->create();
        $account = $this->platformAccount($user);
        $game = Game::create(['title' => 'Session Game', 'slug' => 'session-game']);
        $inventory = $this->inventory($user, $account, $game);
        $summary = GamePlaySession::create([
            'user_game_inventory_id' => $inventory->id,
            'manual_playtime_minutes' => 30,
            'playtime_minutes' => 30,
            'completion_status' => 'Playing',
        ]);

        Livewire::actingAs($user)
            ->test(PlayActivityManager::class)
            ->set('inventory_id', $inventory->id)
            ->set('started_at', $fixedNow->copy()->subMinutes(60)->format('Y-m-d\TH:i'))
            ->set('ended_at', $fixedNow->copy()->format('Y-m-d\TH:i'))
            ->call('saveSession')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('game_play_sessions', [
            'id' => $summary->id,
            'manual_playtime_minutes' => 30,
            'playtime_minutes' => 90,
        ]);

        $sessionId = $inventory->activitySessions()->value('id');
        Livewire::actingAs($user)
            ->test(PlayActivityManager::class)
            ->call('deleteSession', $sessionId);

        $this->assertDatabaseHas('game_play_sessions', [
            'id' => $summary->id,
            'manual_playtime_minutes' => 30,
            'playtime_minutes' => 30,
        ]);

        \Illuminate\Support\Carbon::setTestNow();
    }

    public function test_wishlist_search_is_scoped_to_the_authenticated_user_and_paginated(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $game = Game::create(['title' => 'Wanted Game', 'slug' => 'wanted-game']);
        $otherGame = Game::create(['title' => 'Private Other Game', 'slug' => 'private-other-game']);
        Wishlist::create(['user_id' => $user->id, 'game_id' => $game->id, 'priority' => 'High']);
        Wishlist::create(['user_id' => $otherUser->id, 'game_id' => $otherGame->id, 'priority' => 'High']);

        Livewire::actingAs($user)
            ->test(WishlistManager::class)
            ->set('search', 'Wanted')
            ->assertViewHas('items', fn ($items) => $items->total() === 1 && $items->first()->game_id === $game->id);
    }

    public function test_recommendations_exclude_owned_games_and_rank_matching_genres(): void
    {
        $user = User::factory()->create();
        $account = $this->platformAccount($user);
        $genre = Category::create(['name' => 'RPG', 'slug' => 'rpg']);
        $owned = Game::create(['title' => 'Owned RPG', 'slug' => 'owned-rpg']);
        $recommended = Game::create(['title' => 'Recommended RPG', 'slug' => 'recommended-rpg']);
        $unrelated = Game::create(['title' => 'Unrelated Game', 'slug' => 'unrelated-game']);
        $owned->categories()->attach($genre->id);
        $recommended->categories()->attach($genre->id);
        $this->inventory($user, $account, $owned);

        $this->actingAs($user)
            ->get(route('recommendations.index'))
            ->assertOk()
            ->assertSee('Recommended RPG')
            ->assertDontSee('Owned RPG')
            ->assertDontSee('Unrelated Game');
    }
}
