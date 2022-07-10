<?php

namespace Tests\Feature\Webhooks;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\CreateLicenceKey;
use Mockery\MockInterface;
use Tests\TestCase;

class WebhookReceivedListenerTest extends TestCase
{
    /** @test */
    public function it_mocks_creating_an_instance_key(): void
    {
        $plan = Plan::factory()->create([
            'plan_id_on_paddle' => 1,
        ]);

        $payload = $this->get_payload();

        $this->mock(CreateLicenceKey::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('execute')
                ->once()
                ->withArgs(function (User $billable, Subscription $subscription, array $payload) use ($plan) {
                    $this->assertEquals($plan->plan_id_on_paddle, $subscription->paddle_plan);

                    return true;
                });
        });

        $this->post('/paddle/webhook', $payload['data']);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@admin.com',
            'name' => 'admin@admin.com',
        ]);
        $user = User::firstWhere(['email' => 'admin@admin.com']);
        $this->assertDatabaseHas('customers', [
            'billable_id' => $user->id,
            'billable_type' => User::class,
        ]);
    }

    private function get_payload(): array
    {
        $file = file_get_contents(base_path('tests/Fixtures/Paddle/subscription_created_migration.json'));

        return json_decode($file, true);
    }
}
