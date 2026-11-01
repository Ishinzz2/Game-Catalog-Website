<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    /**
     * Align the legacy pivot table name with Laravel's belongsToMany convention.
     *
     * Older installations created "game_category", while Game::categories()
     * expects "category_game". Fresh installs already use the corrected name.
     */
    public function up(): void
    {
        if (Schema::hasTable('game_category') && ! Schema::hasTable('category_game')) {
            Schema::rename('game_category', 'category_game');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('category_game') && ! Schema::hasTable('game_category')) {
            Schema::rename('category_game', 'game_category');
        }
    }
};
