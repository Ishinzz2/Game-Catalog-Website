<?php
namespace Database\\Factories;
use Illuminate\\Database\\Eloquent\\Factories\\Factory;
use Illuminate\\Support\\Str;
class GameFactory extends Factory {
    public function definition(): array {
        $title=$this->faker->unique()->words(3,true);
        return ['title'=>ucwords($title),'slug'=>Str::slug($title).'-'.Str::lower(Str::random(5)),'description'=>$this->faker->paragraph(),'release_date'=>$this->faker->date(),'metacritic_score'=>$this->faker->numberBetween(40,99),'esrb_rating'=>$this->faker->randomElement(['E','E10+','T','M'])];
    }
}
