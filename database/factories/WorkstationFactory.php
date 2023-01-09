<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Workstation;

class WorkstationFactory extends Factory
{
    protected $model = Workstation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $workstationName = $this->faker->words(1, true);

        return [
            'department_id' => $this->faker->randomDigit(),
            'name' => $workstationName,
            'label' => $workstationName,
            'display_id' => $workstationName
        ];
    }
}
