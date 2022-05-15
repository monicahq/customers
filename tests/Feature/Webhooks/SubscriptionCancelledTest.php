<?php

namespace Tests\Unit\Services;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\DestroyLicenceKey;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class SubscriptionCancelledTest extends TestCase
{
    /** @test */
    public function it_mock_destroying_an_existing_key(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create([
            'plan_id_on_paddle' => 1,
        ]);
        $subscription = Subscription::factory()->create([
            'billable_id' => $user->id,
            'paddle_plan' => $plan->plan_id_on_paddle,
        ]);

        $payload = $this->get_payload($user->id, $subscription->paddle_id);

        $this->mock(DestroyLicenceKey::class, function (MockInterface $mock) use ($user, $plan) {
            $mock->shouldReceive('execute')
                ->once()
                ->withArgs(function (Subscription $subscription, array $payload) use ($user, $plan) {
                    $this->assertEquals($user->id, $subscription->billable_id);
                    $this->assertEquals($plan->plan_id_on_paddle, $subscription->paddle_plan);
                    return true;
                });
        });

        $this->post('/paddle/webhook', $payload['data']);
    }

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

        $payload = $this->get_payload($user->id, $subscription->paddle_id);

        $response = $this->post('/paddle/webhook', $payload['data']);
        $response->assertOk();

        $this->assertDatabaseHas('licence_keys', [
            'id' => $licenceKey->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_state' => 'subscription_cancelled',
            'valid_until_at' => Carbon::create('2022-04-02'),
        ]);
    }

    private function get_payload(int $userId, int $subscriptionId): array
    {
        $file = file_get_contents(base_path('tests/Fixtures/Paddle/subscription_cancelled.json'));
        $file = Str::of($file)->replace('%USER_ID%', $userId)
            ->replace('%SUBSCRIPTION_ID%', $subscriptionId);

        return json_decode($file, true);
    }
}
