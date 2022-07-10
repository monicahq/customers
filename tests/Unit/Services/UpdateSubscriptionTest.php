<?php

namespace Tests\Unit\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\UpdateSubscription;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdateSubscriptionTest extends TestCase
{
    /** @test */
    public function it_updates_subscription_plan(): void
    {
        $user = User::factory()->create();
        $plan1 = Plan::factory()->monica()->create();
        $plan2 = Plan::factory()->monica()->create();

        $subscription = Subscription::factory()->create([
            'name' => 'name',
            'billable_id' => $user->id,
            'paddle_plan' => $plan1->plan_id_on_paddle,
        ]);

        Http::fake([
            'https://sandbox-vendors.paddle.com/api/2.0/subscription/users/update' => Http::response([
                'success' => true,
                'response' => [],
            ], 200),
        ]);

        (new UpdateSubscription)->execute([
            'user_id' => $user->id,
            'plan_id' => $plan2->id,
        ]);

        Http::assertSent(function ($request) use ($subscription, $plan2) {
            $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/subscription/users/update', $request->url());
            $this->assertEquals('POST', $request->method());
            $this->assertEquals([
                'vendor_id' => 0,
                'vendor_auth_code' => '',
                'subscription_id' => $subscription->paddle_id,
                'bill_immediately' => true,
                'plan_id' => $plan2->plan_id_on_paddle,
                'prorate' => true,
            ], $request->data());

            return true;
        });

        $this->assertDatabaseHas('subscriptions', [
            'billable_id' => $user->id,
            'billable_type' => User::class,
            'name' => 'name',
            'paddle_status' => 'active',
            'paddle_plan' => $plan2->plan_id_on_paddle,
            'quantity' => 1,
        ]);
    }

    /** @test */
    public function it_updates_subscription_quantity(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create();

        $subscription = Subscription::factory()->create([
            'name' => 'name',
            'billable_id' => $user->id,
            'paddle_plan' => $plan->plan_id_on_paddle,
        ]);

        Http::fake([
            'https://sandbox-vendors.paddle.com/api/2.0/subscription/users/update' => Http::response([
                'success' => true,
                'response' => [],
            ], 200),
        ]);

        (new UpdateSubscription)->execute([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'quantity' => 2,
        ]);

        Http::assertSent(function ($request) use ($subscription) {
            $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/subscription/users/update', $request->url());
            $this->assertEquals('POST', $request->method());
            $this->assertEquals([
                'vendor_id' => 0,
                'vendor_auth_code' => '',
                'subscription_id' => $subscription->paddle_id,
                'bill_immediately' => true,
                'prorate' => true,
                'quantity' => 2,
            ], $request->data());

            return true;
        });

        $this->assertDatabaseHas('subscriptions', [
            'billable_id' => $user->id,
            'billable_type' => User::class,
            'name' => 'name',
            'paddle_status' => 'active',
            'paddle_plan' => $plan->plan_id_on_paddle,
            'quantity' => 2,
        ]);
    }
}
