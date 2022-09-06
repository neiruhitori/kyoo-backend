<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use App\BranchType;

class BranchTypeFactory extends Factory
{
    protected $model = BranchType::class;

    private $queueTypes = [
        'appointment' => [
            'is_appointment' => true,
            'is_direct_queue' => false,
            'is_exhibition' => false
        ],
        'onsite' => [
            'is_appointment' => false,
            'is_direct_queue' => true,
            'is_exhibition' => false
        ],
        'exhibition' => [
            'is_appointment' => false,
            'is_direct_queue' => false,
            'is_exhibition' => true
        ]
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $queueType = Arr::random(['appointment', 'onsite', 'exhibition']);

        return [
            'code' => $this->faker->unique()->regexify('[A-Z]{3}'),
            'name' => $this->faker->word(3, true),
            'is_premium' => $this->faker->boolean(),
            'is_appointment' => $this->queueTypes[$queueType]['is_appointment'],
            'is_direct_queue' => $this->queueTypes[$queueType]['is_direct_queue'],
            'is_exhibition' => $this->queueTypes[$queueType]['is_exhibition']
        ];
    }

    public function free()
    {
        return $this->state(function (array $attributes) {
            return ['is_premium' => false];
        });
    }

    public function appointment()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_appointment' => true,
                'is_direct_queue' => false,
                'is_exhibition' => false
            ];
        });
    }

    public function onsite()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_appointment' => false,
                'is_direct_queue' => true,
                'is_exhibition' => false
            ];
        });
    }

    public function exhibition()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_appointment' => false,
                'is_direct_queue' => false,
                'is_exhibition' => true
            ];
        });
    }
}
