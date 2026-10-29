<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\SoftDeletes;
class Developer extends Model {
    use SoftDeletes;
    protected $fillable = ['name','slug','website','country'];
    public function games() { return $this->hasMany(Game::class); }
}
