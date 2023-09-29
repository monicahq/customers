<?php

namespace Tests\Unit\Services;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\DestroyLicenceKey;
use Tests\TestCase;

class DestroyLicenceKeyTest extends TestCase
{
    /** @test */
    public function it_destroys_an_existing_key(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create([
            'plan_id_on_paddle' => 1,
        ]);
        $licenceKey = LicenceKey::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);
        $subscription = Subscription::factory()->create([
            'billable_id' => $user->id,
            'paddle_plan' => $plan->plan_id_on_paddle,
        ]);

        $response = (new DestroyLicenceKey)->execute($subscription, [
            'cancellation_effective_date' => '2022-04-02',
        ]);

        $this->assertTrue($response);
        $this->assertDatabaseHas('licence_keys', [
            'id' => $licenceKey->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_state' => 'subscription_cancelled',
            'valid_until_at' => '2022-04-02',
        ]);
    }
}
