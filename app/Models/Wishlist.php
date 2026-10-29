<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Wishlist extends Model {
    use SoftDeletes;
    protected $fillable = ['user_id','game_id','target_platform_id','desired_max_price','priority','notes'];
    protected function casts(): array { return ['desired_max_price'=>'decimal:2']; }
    public function user() { return $this->belongsTo(User::class); }
    public function game() { return $this->belongsTo(Game::class); }
    public function targetPlatform() { return $this->belongsTo(Platform::class,'target_platform_id'); }
}
