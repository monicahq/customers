<?php

namespace Tests\Unit\Services;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\Receipt;
use App\Models\Subscription;
use App\Models\User;
use App\Services\RenewLicenceKey;
use Tests\TestCase;

class RenewLicenceKeyTest extends TestCase
{
    /** @test */
    public function it_validates_an_existing_key(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create([
            'plan_id_on_paddle' => 1,
        ]);
        $subscription = Subscription::factory()->create([
            'billable_id' => $user->id,
            'paddle_plan' => $plan->plan_id_on_paddle,
        ]);
        $licenceKey = LicenceKey::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $response = (new RenewLicenceKey)->execute($user, $subscription, [
            'next_bill_date' => '2022-04-02',
        ]);

        $this->assertTrue($response);
        $this->assertDatabaseHas('licence_keys', [
            'id' => $licenceKey->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_state' => 'subscription_payment_succeeded',
            'valid_until_at' => '2022-04-02 00:00:00',
        ]);
    }
}
