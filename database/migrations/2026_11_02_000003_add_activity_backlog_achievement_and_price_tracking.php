<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('game_play_sessions', function (Blueprint $table): void {
            $table->dropUnique('game_play_sessions_inventory_unique');
        });

        Schema::table('user_game_inventories', function (Blueprint $table): void {
            $table->string('backlog_priority', 20)->default('Medium');
            $table->date('backlog_target_date')->nullable();
            $table->text('backlog_notes')->nullable();
        });

        Schema::create('play_activity_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_game_inventory_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at');
            $table->unsignedInteger('duration_minutes');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_game_inventory_id', 'started_at']);
        });

        Schema::create('game_achievements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_game_inventory_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();
            $table->index(['user_game_inventory_id', 'unlocked_at']);
        });

        Schema::create('wishlist_price_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('wishlist_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('IDR');
            $table->string('source', 120)->default('Manual');
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['wishlist_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_price_histories');
        Schema::dropIfExists('game_achievements');
        Schema::dropIfExists('play_activity_sessions');

        Schema::table('user_game_inventories', function (Blueprint $table): void {
            $table->dropColumn(['backlog_priority', 'backlog_target_date', 'backlog_notes']);
        });

        // Restore the original one-summary-row-per-inventory constraint only
        // after duplicate summary rows have been removed by the application.
        Schema::table('game_play_sessions', function (Blueprint $table): void {
            $table->unique('user_game_inventory_id', 'game_play_sessions_inventory_unique');
        });
    }
};
