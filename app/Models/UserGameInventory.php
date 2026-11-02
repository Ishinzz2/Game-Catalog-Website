<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGameInventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'game_id', 'user_platform_account_id', 'format', 'acquisition_type',
        'purchase_price', 'purchase_date', 'store_region', 'is_installed', 'physical_location_notes',
        'backlog_priority', 'backlog_target_date', 'backlog_notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'purchase_date' => 'date',
            'is_installed' => 'boolean',
            'backlog_target_date' => 'date',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function game() { return $this->belongsTo(Game::class); }
    public function platformAccount() { return $this->belongsTo(UserPlatformAccount::class, 'user_platform_account_id'); }
    public function playSession() { return $this->hasOne(GamePlaySession::class); }
    public function playSessions() { return $this->hasMany(GamePlaySession::class); }
    public function activitySessions() { return $this->hasMany(PlayActivitySession::class); }
    public function achievements() { return $this->hasMany(GameAchievement::class); }
}
