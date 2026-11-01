<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (! Schema::hasTable('users')) Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable(); $table->string('password');
            $table->string('theme_preference')->default('dark'); $table->string('default_currency',3)->default('IDR');
            $table->rememberToken(); $table->timestamps(); $table->softDeletes();
        });
        if (! Schema::hasTable('publishers')) Schema::create('publishers', function(Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique();
            $table->string('website')->nullable(); $table->string('country',100)->nullable();
            $table->timestamps(); $table->softDeletes();
        });
        if (! Schema::hasTable('developers')) Schema::create('developers', function(Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique();
            $table->string('website')->nullable(); $table->string('country',100)->nullable();
            $table->timestamps(); $table->softDeletes();
        });
        if (! Schema::hasTable('categories')) Schema::create('categories', function(Blueprint $table) {
            $table->id(); $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name'); $table->string('slug')->unique(); $table->text('description')->nullable();
            $table->timestamps(); $table->index(['parent_id','name']);
        });
        if (! Schema::hasTable('tags')) Schema::create('tags', function(Blueprint $table) {
            $table->id(); $table->string('name')->unique(); $table->string('slug')->unique();
            $table->text('description')->nullable(); $table->timestamps();
        });
        if (! Schema::hasTable('platforms')) Schema::create('platforms', function(Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('code')->unique();
            $table->string('icon_path')->nullable(); $table->string('company_name')->nullable();
            $table->timestamps();
        });
        if (! Schema::hasTable('user_platform_accounts')) Schema::create('user_platform_accounts', function(Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('platform_id')->constrained()->restrictOnDelete();
            $table->string('account_username')->nullable(); $table->string('account_id_external')->nullable();
            $table->boolean('is_connected')->default(false); $table->timestamp('last_synced_at')->nullable();
            $table->timestamps(); $table->softDeletes();
            $table->index(['user_id','platform_id']); $table->unique(['user_id','platform_id','account_username']);
        });
        if (! Schema::hasTable('games')) Schema::create('games', function(Blueprint $table) {
            $table->id(); $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('developer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title'); $table->string('slug')->unique(); $table->longText('description')->nullable();
            $table->string('cover_url')->nullable(); $table->string('banner_url')->nullable();
            $table->date('release_date')->nullable(); $table->unsignedTinyInteger('metacritic_score')->nullable();
            $table->string('esrb_rating',20)->nullable(); $table->timestamps(); $table->softDeletes();
            $table->index(['title']); $table->index(['release_date','metacritic_score']);
        });
        if (! Schema::hasTable('category_game')) Schema::create('category_game', function(Blueprint $table) {
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['game_id','category_id']);
        });
        if (! Schema::hasTable('game_tag')) Schema::create('game_tag', function(Blueprint $table) {
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['game_id','tag_id']);
        });
        if (! Schema::hasTable('user_game_inventories')) Schema::create('user_game_inventories', function(Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_platform_account_id')->constrained()->cascadeOnDelete();
            $table->enum('format',['Digital','Physical Disc','Physical Cartridge'])->default('Digital');
            $table->enum('acquisition_type',['Purchased','Subscription/Game Pass/PS Plus','Gift','Free-to-Play'])->default('Purchased');
            $table->decimal('purchase_price',12,2)->nullable(); $table->date('purchase_date')->nullable();
            $table->string('store_region',80)->nullable(); $table->boolean('is_installed')->default(false);
            $table->string('physical_location_notes')->nullable(); $table->timestamps(); $table->softDeletes();
            $table->index(['user_id','game_id']); $table->index(['user_id','user_platform_account_id']);
        });
        if (! Schema::hasTable('game_play_sessions')) Schema::create('game_play_sessions', function(Blueprint $table) {
            $table->id(); $table->foreignId('user_game_inventory_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('playtime_minutes')->default(0);
            $table->enum('completion_status',['Unplayed','Backlog','Playing','Completed','100% Platinum','Dropped'])->default('Backlog');
            $table->unsignedTinyInteger('rating_user')->nullable(); $table->text('review_notes')->nullable();
            $table->timestamp('last_played_at')->nullable(); $table->timestamps(); $table->softDeletes();
            $table->index(['completion_status','last_played_at']);
        });
        if (! Schema::hasTable('wishlists')) Schema::create('wishlists', function(Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('target_platform_id')->nullable()->constrained('platforms')->nullOnDelete();
            $table->decimal('desired_max_price',12,2)->nullable();
            $table->enum('priority',['Low','Medium','High'])->default('Medium');
            $table->text('notes')->nullable(); $table->timestamps(); $table->softDeletes();
            $table->unique(['user_id','game_id','target_platform_id']); $table->index(['user_id','priority']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('wishlists'); Schema::dropIfExists('game_play_sessions');
        Schema::dropIfExists('user_game_inventories'); Schema::dropIfExists('game_tag');
        Schema::dropIfExists('category_game'); Schema::dropIfExists('games');
        Schema::dropIfExists('user_platform_accounts'); Schema::dropIfExists('platforms');
        Schema::dropIfExists('tags'); Schema::dropIfExists('categories');
        Schema::dropIfExists('developers'); Schema::dropIfExists('publishers');
    }
};
