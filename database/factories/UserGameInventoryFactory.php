<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use App\Models\UserPlatformAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserGameInventoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'game_id' => Game::factory(),
            'user_platform_account_id' => UserPlatformAccount::factory(),
            'format' => $this->faker->randomElement(['Digital', 'Physical Disc', 'Physical Cartridge']),
            'acquisition_type' => $this->faker->randomElement([
                'Purchased',
                'Subscription/Game Pass/PS Plus',
                'Gift',
                'Free-to-Play',
            ]),
            'purchase_price' => $this->faker->optional()->randomFloat(2, 0, 1500000),
            'purchase_date' => $this->faker->optional()->date(),
            'is_installed' => $this->faker->boolean(),
        ];
    }
}
