<?php
namespace Database\\Factories;
use Illuminate\\Database\\Eloquent\\Factories\\Factory;
use Illuminate\\Support\\Str;
class PlatformFactory extends Factory {
    public function definition(): array {
        $name=$this->faker->unique()->company().' Gaming';
        return ['name'=>$name,'code'=>Str::slug($name).'-'.Str::lower(Str::random(4)),'company_name'=>$this->faker->company()];
    }
}
