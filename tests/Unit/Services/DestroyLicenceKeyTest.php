<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\LicenceKey;
use App\Models\Plan;
use Illuminate\Support\Str;
use App\Models\User;
use App\Services\DestroyLicenceKey;
use App\Services\RenewLicenceKey;
use App\Services\ValidateLicenceKey;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DestroyLicenceKeyTest extends TestCase
{
    use DatabaseTransactions;

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

        $payload = json_decode(file_get_contents(base_path('tests/Fixtures/Paddle/subscription_cancelled.json')), true);

        // it's a bit dirty, but we need to set the user id on the payload to make
        // sure our tests pass
        $payload = Str::replace('"billable_id":1', '"billable_id":'.$user->id, $payload['data']);

        $response = (new DestroyLicenceKey)->execute($payload);

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
