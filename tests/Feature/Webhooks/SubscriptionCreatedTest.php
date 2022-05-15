<?php

namespace Tests\Feature\Webhooks;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\CreateLicenceKey;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class SubscriptionCreatedTest extends TestCase
{
    /** @test */
    public function it_mocks_creating_an_instance_key(): void
    {
        $user = User::factory()->create([
            'email' => 'regis@monicahq.com',
        ]);
        $plan = Plan::factory()->create([
            'frequency' => Plan::TYPE_MONTHLY,
            'plan_id_on_paddle' => 1,
        ]);

        $payload = $this->get_payload($user->id);

        $this->mock(CreateLicenceKey::class, function (MockInterface $mock) use ($user, $plan) {
            $mock->shouldReceive('execute')
                ->once()
                ->withArgs(function (User $billable, Subscription $subscription, array $payload) use ($user, $plan) {
                    $this->assertEquals($user->id, $billable->id);
                    $this->assertEquals($user->id, $subscription->billable_id);
                    $this->assertEquals($plan->plan_id_on_paddle, $subscription->paddle_plan);
                    return true;
                });
        });

        $this->post('/paddle/webhook', $payload['data']);
    }

    /** @test */
    public function it_creates_an_instance_key(): void
    {
        $user = User::factory()->create([
            'email' => 'regis@monicahq.com',
        ]);
        $plan = Plan::factory()->create([
            'frequency' => Plan::TYPE_MONTHLY,
            'plan_id_on_paddle' => 1,
        ]);

        $payload = $this->get_payload($user->id);

        $response = $this->post('/paddle/webhook', $payload['data']);
        $response->assertOk();

        $this->assertDatabaseHas('licence_keys', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'valid_until_at' => '2022-04-02 00:00:00',
            'paddle_cancel_url' => $payload['data']['cancel_url'],
            'paddle_update_url' => $payload['data']['update_url'],
        ]);
        $this->assertDatabaseHas('subscriptions', [
            'billable_id' => $user->id,
            'paddle_id' => '218112',
        ]);
    }

    private function get_payload(int $userId): array
    {
        $file = file_get_contents(base_path('tests/Fixtures/Paddle/subscription_created.json'));
        $file = Str::of($file)->replace('%USER_ID%', $userId);

        return json_decode($file, true);
    }
}
