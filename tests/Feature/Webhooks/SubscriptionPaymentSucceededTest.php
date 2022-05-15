<?php

namespace Tests\Unit\Services;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\Receipt;
use App\Models\Subscription;
use App\Models\User;
use App\Services\RenewLicenceKey;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class SubscriptionPaymentSucceededTest extends TestCase
{
    /** @test */
    public function it_mocks_validating_an_existing_key(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create([
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
        $licenceKey = LicenceKey::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $payload = $this->get_payload($user->id, $subscription->paddle_id);

        $this->mock(RenewLicenceKey::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('execute')
                ->once()
                ->withArgs(function (User $billable, Receipt $receipt, array $payload) use ($user) {
                    $this->assertEquals($user->id, $billable->id);
                    $this->assertEquals($user->id, $receipt->billable_id);

                    return true;
                });
        });

        $this->post('/paddle/webhook', $payload['data']);
    }

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
        $receipt = Receipt::factory()->create([
            'billable_id' => $user->id,
            'paddle_subscription_id' => $subscription->paddle_id,
        ]);
        $licenceKey = LicenceKey::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        $payload = $this->get_payload($user->id, $subscription->paddle_id);

        $response = $this->post('/paddle/webhook', $payload['data']);
        $response->assertOk();

        $this->assertDatabaseHas('licence_keys', [
            'id' => $licenceKey->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_state' => 'subscription_payment_succeeded',
            'valid_until_at' => Carbon::create('2022-04-02'),
        ]);
    }

    private function get_payload(int $userId, int $subscriptionId): array
    {
        $file = file_get_contents(base_path('tests/Fixtures/Paddle/subscription_payment_succeeded.json'));
        $file = Str::of($file)->replace('%USER_ID%', $userId)
            ->replace('%SUBSCRIPTION_ID%', $subscriptionId);

        return json_decode($file, true);
    }
}
