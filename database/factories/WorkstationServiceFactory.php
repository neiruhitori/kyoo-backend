<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\WorkstationService;

class WorkstationServiceFactory extends Factory
{
    protected $model = WorkstationService::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'workstation_id' => $this->faker->randomDigit(),
            'service_id' => $this->faker->randomDigit()
        ];
    }
}
