<?php
namespace Database\Factories;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class UserPlatformAccountFactory extends Factory {
    public function definition(): array {
        return ['user_id'=>User::factory(),'platform_id'=>Platform::query()->inRandomOrder()->value('id') ?? Platform::factory(),'account_username'=>$this->faker->userName(),'account_id_external'=>$this->faker->optional()->uuid(),'is_connected'=>false];
    }
}
