<?php

namespace Tests\Feature\Controllers;

use App\Models\Plan;
use App\Models\Receipt;
use App\Models\Subscription;
use App\Models\User;
use Tests\TestCase;

class DashoardControllerTest extends TestCase
{
    /** @test */
    public function it_displays_list_of_receipts(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
            'plan_id_on_paddle' => 1,
        ]);
        $subscription = Subscription::factory()->create([
            'billable_id' => $user->id,
            'paddle_plan' => $plan->plan_id_on_paddle,
        ]);
        $receipt = Receipt::factory()->create([
            'billable_id' => $user->id,
            'paddle_subscription_id' => $subscription->paddle_id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('"receipts":{"data":[{"id":'.$receipt->id);
    }
}
