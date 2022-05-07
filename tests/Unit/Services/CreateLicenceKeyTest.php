<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\LicenceKey;
use Illuminate\Support\Str;
use App\Services\CreateLicenceKey;

class CreateLicenceKeyTest extends TestCase
{
    private const PRIVATE_KEY = 'PRIVATE_KEY';

    /** @test */
    public function it_creates_an_instance_key(): void
    {
        config(['customers.private_key_to_encrypt_licence_keys' => static::PRIVATE_KEY]);

        $user = User::factory()->create([
            'email' => 'regis@monicahq.com',
        ]);
        $plan = Plan::factory()->create([
            'frequency' => Plan::TYPE_MONTHLY,
            'plan_id_on_paddle' => 1,
        ]);

        $payload = $this->get_payload($user->id);

        $licenceKey = (new CreateLicenceKey)->execute($payload['data']);

        $this->assertInstanceOf(LicenceKey::class, $licenceKey);
        $this->assertIsString($licenceKey->key);

        $licenceKey = Str::of($licenceKey->key);
        $licenceKey = base64_decode($licenceKey);
        $licenceKey = Str::replace(static::PRIVATE_KEY, '', $licenceKey);
        $array = json_decode($licenceKey, true);

        $this->assertArrayHasKey('frequency', $array);
        $this->assertArrayHasKey('purchaser_email', $array);
        $this->assertArrayHasKey('next_check_at', $array);

        $this->assertEquals(
            'regis@monicahq.com',
            $array['purchaser_email']
        );
        $this->assertEquals(
            '2022-04-02T00:00:00.000000Z',
            $array['next_check_at']
        );
        $this->assertEquals(
            Plan::TYPE_MONTHLY,
            $array['frequency']
        );

        $this->assertDatabaseHas('licence_keys', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'key' => base64_encode($licenceKey . static::PRIVATE_KEY),
            'valid_until_at' => '2022-04-02 00:00:00',
        ]);
    }

    private function get_payload(int $userId): array
    {
        $file = file_get_contents(base_path('tests/Fixtures/Paddle/subscription_created.json'));
        $file = Str::of($file)->replace('%USER_ID%', $userId);

        return json_decode($file, true);
    }
}
