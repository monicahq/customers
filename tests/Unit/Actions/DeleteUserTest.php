<?php

namespace Tests\Unit\Actions;

use App\Actions\Jetstream\DeleteUser;
use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    /** @test */
    public function it_cancel_subscriptions(): void
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

        Http::fake([
            'https://sandbox-vendors.paddle.com/api/2.0/subscription/users_cancel' => Http::response([
                'success' => true,
            ], 200),
        ]);

        app(DeleteUser::class)->delete($user);

        Http::assertSent(function ($request) use ($subscription) {
            $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/subscription/users_cancel', $request->url());
            $this->assertEquals('POST', $request->method());
            $this->assertEquals('{"vendor_id":0,"vendor_auth_code":"","subscription_id":'.$subscription->paddle_id.'}', $request->body());

            return true;
        });

        $this->assertNull($user->fresh());
        //$this->assertNull($licenceKey->fresh());
        $this->assertEquals('deleted', $subscription->fresh()->paddle_status);
    }
}
