<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable {
    use HasFactory, Notifiable, SoftDeletes;
    protected $fillable = ['name','email','password','theme_preference','default_currency'];
    protected $hidden = ['password','remember_token'];
    protected function casts(): array { return ['email_verified_at'=>'datetime','password'=>'hashed']; }
    public function platformAccounts() { return $this->hasMany(UserPlatformAccount::class); }
    public function gameInventories() { return $this->hasMany(UserGameInventory::class); }
    public function wishlists() { return $this->hasMany(Wishlist::class); }
}
