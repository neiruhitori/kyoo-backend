<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\ScheduleTemplate;

class ScheduleTemplateFactory extends Factory
{
    protected $model = ScheduleTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'filename' => $this->faker->file('docs', 'schedule_templates', true)
        ];
    }
}
