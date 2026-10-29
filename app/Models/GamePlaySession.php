<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\SoftDeletes;
class GamePlaySession extends Model {
    use SoftDeletes;
    protected $fillable = ['user_game_inventory_id','playtime_minutes','completion_status','rating_user','review_notes','last_played_at'];
    protected function casts(): array { return ['last_played_at'=>'datetime','playtime_minutes'=>'integer','rating_user'=>'integer']; }
    public function inventory() { return $this->belongsTo(UserGameInventory::class,'user_game_inventory_id'); }
}
