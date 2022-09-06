<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\IndustryCategory;

class IndustryCategoryFactory extends Factory
{
    protected $model = IndustryCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(1, true),
            'icon' => $this->faker->words(1, true)
        ];
    }
}
