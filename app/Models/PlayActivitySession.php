<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class PlayActivitySession extends Model
{
    protected $fillable = ['user_game_inventory_id', 'started_at', 'ended_at', 'duration_minutes', 'notes'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime', 'duration_minutes' => 'integer'];
    }

    public function inventory()
    {
        return $this->belongsTo(UserGameInventory::class, 'user_game_inventory_id');
    }
}
