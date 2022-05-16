<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'billable_id' => User::factory(),
            'billable_type' => User::class,
            'name' => $this->faker->word,
            'paddle_id' => $this->faker->numberBetween(1, 100),
            'paddle_status' => 'active',
            'paddle_plan' => $this->faker->numberBetween(1, 100),
            'quantity' => 1,
        ];
    }
}
