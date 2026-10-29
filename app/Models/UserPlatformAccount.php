<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\SoftDeletes;
class UserPlatformAccount extends Model {
 use HasFactory, SoftDeletes;
 protected $fillable=['user_id','platform_id','account_username','account_id_external','is_connected','last_synced_at'];
 protected function casts(): array{return ['is_connected'=>'boolean','last_synced_at'=>'datetime'];}
 public function user(){return $this->belongsTo(User::class);}
 public function platform(){return $this->belongsTo(Platform::class);}
 public function inventories(){return $this->hasMany(UserGameInventory::class);}
}