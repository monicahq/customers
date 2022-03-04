<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\LicenceKey;
use Illuminate\Support\Str;
use App\Services\CreateLicenceKey;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateLicenceKeyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_an_instance_key(): void
    {
        config(['customers.private_key_to_encrypt_licence_keys' => '123']);

        $user = User::factory()->create();
        $plan = Plan::factory()->create([
            'frequency' => Plan::TYPE_MONTHLY,
            'plan_id_on_paddle' => 1,
        ]);

        $payload = json_decode(file_get_contents(base_path('tests/Fixtures/Paddle/subscription_created.json')), true);

        // it's a bit dirty, but we need to set the user id on the payload to make
        // sure our tests pass
        $payload = Str::replace('"billable_id":1', '"billable_id":'.$user->id, $payload['data']);

        $licenceKey = (new CreateLicenceKey)->execute($payload);

        $this->assertInstanceOf(LicenceKey::class, $licenceKey);
        $this->assertIsString($licenceKey->key);

        $licenceKey = $licenceKey->key;
        $licenceKey = base64_decode($licenceKey);
        $licenceKey = Str::replace('123', '', $licenceKey);
        $array = json_decode($licenceKey, true);

        $this->assertArrayHasKey('frequency', $array[0]);
        $this->assertArrayHasKey('purchaser_email', $array[0]);
        $this->assertArrayHasKey('next_check_at', $array[0]);

        $this->assertEquals(
            $user->email,
            $array[0]['purchaser_email']
        );
        $this->assertEquals(
            '2022-04-02T00:00:00.000000Z',
            $array[0]['next_check_at']
        );
        $this->assertEquals(
            Plan::TYPE_MONTHLY,
            $array[0]['frequency']
        );

        $this->assertDatabaseHas('licence_keys', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'key' => base64_encode($licenceKey.'123'),
            'valid_until_at' => '2022-04-02 00:00:00',
        ]);
    }
}
