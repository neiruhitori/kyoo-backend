<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'branch_id' => $this->faker->randomDigit(),
            'day' => strtolower($this->faker->date('l')),
            'start_time' => '08:00',
            'end_time' => '17:00',
            'status' => Arr::random(['closed', 'open'])
        ];
    }
}
