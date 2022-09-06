<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BranchScheduleTemplateDetail;

class BranchScheduleTemplateDetailFactory extends Factory
{
    protected $model = BranchScheduleTemplateDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'branch_id' => $this->faker->randomDigit(),
            'schedule_template_id' => $this->faker->randomDigit(),
            'schedule_template_detail_id' => $this->faker->randomDigit(),
            'name' => $this->faker->unique()->word(),
            'date' => $this->faker->date()
        ];
    }
}
