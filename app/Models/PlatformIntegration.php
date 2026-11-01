<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PlatformIntegration extends Model {
 protected $fillable=['provider_type','name','api_base_url','api_key','is_enabled','notes'];
 protected $hidden=['api_key'];
 protected function casts():array{return ['api_key'=>'encrypted','is_enabled'=>'boolean'];}
}