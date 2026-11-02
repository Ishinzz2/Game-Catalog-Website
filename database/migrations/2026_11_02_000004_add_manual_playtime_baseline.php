<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('game_play_sessions', function (Blueprint $table): void {
            $table->unsignedInteger('manual_playtime_minutes')->default(0)->after('playtime_minutes');
        });

        DB::table('game_play_sessions')->orderBy('id')->chunkById(200, function ($summaries): void {
            foreach ($summaries as $summary) {
                $recordedMinutes = (int) DB::table('play_activity_sessions')
                    ->where('user_game_inventory_id', $summary->user_game_inventory_id)
                    ->sum('duration_minutes');

                DB::table('game_play_sessions')->where('id', $summary->id)->update([
                    'manual_playtime_minutes' => max(0, (int) $summary->playtime_minutes - $recordedMinutes),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('game_play_sessions', function (Blueprint $table): void {
            $table->dropColumn('manual_playtime_minutes');
        });
    }
};
