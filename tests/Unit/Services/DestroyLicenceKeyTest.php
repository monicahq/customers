<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\LicenceKey;
use Illuminate\Support\Str;
use App\Services\DestroyLicenceKey;

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

        $payload = $this->get_payload($user->id);

        $response = (new DestroyLicenceKey)->execute($payload['data']);

        $this->assertTrue($response);
        $this->assertDatabaseHas('licence_keys', [
            'id' => $licenceKey->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'subscription_state' => 'subscription_cancelled',
            'valid_until_at' => Carbon::create('2022-04-02'),
        ]);
    }

    private function get_payload(int $userId): array
    {
        $file = file_get_contents(base_path('tests/Fixtures/Paddle/subscription_cancelled.json'));
        $file = Str::of($file)->replace('%USER_ID%', $userId);

        return json_decode($file, true);
    }
}
