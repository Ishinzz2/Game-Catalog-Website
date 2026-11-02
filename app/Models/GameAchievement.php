<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameAchievement extends Model
{
    protected $fillable = ['user_game_inventory_id', 'name', 'description', 'unlocked_at'];

    protected function casts(): array
    {
        return ['unlocked_at' => 'datetime'];
    }

    public function inventory()
    {
        return $this->belongsTo(UserGameInventory::class, 'user_game_inventory_id');
    }
}
