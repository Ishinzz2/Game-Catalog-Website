<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
class Platform extends Model {
    protected $fillable = ['name','code','icon_path','company_name'];
    public function userAccounts() { return $this->hasMany(UserPlatformAccount::class); }
    public function wishlists() { return $this->hasMany(Wishlist::class,'target_platform_id'); }
}
