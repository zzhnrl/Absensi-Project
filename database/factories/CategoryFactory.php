<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_uuid' => null,
            'name' => $this->faker->name(),
            'code' => Str::random(6),
            'description' => Str::random(100),
            'product_type' => 'menu',
        ];
    }
}
