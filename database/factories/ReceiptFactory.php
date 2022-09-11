<?php

namespace Database\Factories;

use App\Models\Receipt;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceiptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Receipt::class;

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
            'paddle_subscription_id' => function () {
                return Subscription::factory()->create()->paddle_id;
            },
            'quantity' => 1,
            'checkout_id' => $this->faker->numberBetween(1, 100),
            'order_id' => $this->faker->numberBetween(1, 100),
            'amount' => $this->faker->randomNumber(),
            'tax' => $this->faker->randomNumber(),
            'currency' => 'USD',
            'receipt_url' => $this->faker->url,
            'paid_at' => now(),
        ];
    }
}
