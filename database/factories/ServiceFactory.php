<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Service;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'branch_id' => $this->faker->randomDigit(),
            'department_id' => $this->faker->randomDigit(),
            'name' => $this->faker->unique()->company()
        ];
    }
}
