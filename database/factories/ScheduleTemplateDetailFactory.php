<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\ScheduleTemplateDetail;

class ScheduleTemplateDetailFactory extends Factory
{
    protected $model = ScheduleTemplateDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'schedule_template_id' => $this->faker->randomDigit(),
            'description' => $this->faker->words(2, true)
        ];
    }
}
