<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Appointment;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'branch_id' => $this->faker->randomDigit(),
            'slot_id' => $this->faker->randomDigit(),
            'booking_code' => $this->faker->regexify('[0-9A-Za-z]{5}'),
            'date' => $this->faker->date(),
            'name' => $this->faker->unique()->name(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'number' => $this->faker->randomDigit(),
            'service_id' => $this->faker->randomDigit(),
            'waiting_duration' => $this->faker->randomNumber(5, true),
            'serving_duration' => $this->faker->randomNumber(5, true)
        ];
    }
}
