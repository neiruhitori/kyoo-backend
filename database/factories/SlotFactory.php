<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Slot;

class SlotFactory extends Factory
{
    protected $model = Slot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startTime = $this->faker->date('H:i');
        $endTime = Carbon::parse($startTime)->add(3, 'hours')->format('H:i');
        $day = strtolower($this->faker->date('l'));

        return [
            'service_id' => $this->faker->randomDigit(),
            'max_slots' => $this->faker->randomDigit(),
            'day' => $day,
            'start_time' => $startTime,
            'end_time' => $endTime
        ];
    }
}
