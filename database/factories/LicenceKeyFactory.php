<?php

namespace Database\Factories;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LicenceKeyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = LicenceKey::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'plan_id' => Plan::factory(),
            'user_id' => User::factory(),
            'key' => app('license.encrypter')->encrypt([$this->faker->sentence()]),
            'valid_until_at' => $this->faker->dateTimeThisCentury(),
            'subscription_state' => 'subscribed',
        ];
    }
}
