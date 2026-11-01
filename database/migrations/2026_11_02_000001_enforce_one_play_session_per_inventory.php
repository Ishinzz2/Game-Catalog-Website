<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep the newest session for each inventory before enforcing the
        // one-to-one relationship represented by UserGameInventory::playSession().
        $duplicates = DB::table('game_play_sessions')
            ->select('user_game_inventory_id')
            ->groupBy('user_game_inventory_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_game_inventory_id');

        foreach ($duplicates as $inventoryId) {
            $keepId = DB::table('game_play_sessions')
                ->where('user_game_inventory_id', $inventoryId)
                ->orderByDesc('id')
                ->value('id');

            DB::table('game_play_sessions')
                ->where('user_game_inventory_id', $inventoryId)
                ->where('id', '<>', $keepId)
                ->delete();
        }

        Schema::table('game_play_sessions', function (Blueprint $table): void {
            $table->unique('user_game_inventory_id', 'game_play_sessions_inventory_unique');
        });
    }

    public function down(): void
    {
        Schema::table('game_play_sessions', function (Blueprint $table): void {
            $table->dropUnique('game_play_sessions_inventory_unique');
        });
    }
};
