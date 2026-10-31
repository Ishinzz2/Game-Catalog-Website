<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Game extends Model {
 use HasFactory, SoftDeletes;
 protected $fillable=['publisher_id','developer_id','title','slug','description','cover_url','banner_url','release_date','metacritic_score','esrb_rating'];
 protected function casts(): array { return ['release_date'=>'date','metacritic_score'=>'integer']; }
 public function publisher(){return $this->belongsTo(Publisher::class);}
 public function developer(){return $this->belongsTo(Developer::class);}
 public function categories(){return $this->belongsToMany(Category::class, 'game_category');}
 public function tags(){return $this->belongsToMany(Tag::class);}
 public function inventories(){return $this->hasMany(UserGameInventory::class);}
 public function wishlists(){return $this->hasMany(Wishlist::class);}
}