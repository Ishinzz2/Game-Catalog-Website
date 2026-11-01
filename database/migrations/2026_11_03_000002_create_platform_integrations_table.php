<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{Schema::create('platform_integrations',function(Blueprint $table){$table->id();$table->string('provider_type',40);$table->string('name');$table->string('api_base_url',2048)->nullable();$table->text('api_key')->nullable();$table->boolean('is_enabled')->default(false);$table->text('notes')->nullable();$table->timestamps();$table->index(['provider_type','is_enabled']);});}
 public function down():void{Schema::dropIfExists('platform_integrations');}
};