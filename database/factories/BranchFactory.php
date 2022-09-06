<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Branch;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'industry_category_id' => $this->faker->randomDigit(),
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile_phone' => $this->faker->unique()->e164PhoneNumber(),
            'country' => 'Indonesia',
            'regency_id' => $this->faker->randomDigit(),
            'branch_type_id' => $this->faker->randomDigit(),
            'is_active' => true,
        ];
    }
}
