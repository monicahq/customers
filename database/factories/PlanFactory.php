<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product' => $this->faker->name(),
            'friendly_name' => $this->faker->name(),
            'description' => $this->faker->name(),
            'plan_name' => $this->faker->name(),
            'plan_id_on_paddle' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomNumber(),
            'frequency' => Plan::TYPE_MONTHLY,
        ];
    }

    /**
     * Monica's plan.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function monica()
    {
        return $this->state(function () {
            return [
                'product' => 'Monica',
            ];
        });
    }

    /**
     * OfficeLife's plan.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function officelife()
    {
        return $this->state(function () {
            return [
                'product' => 'OfficeLife',
            ];
        });
    }
}
