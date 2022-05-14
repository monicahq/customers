<?php

namespace Tests\Unit\Services;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\User;
use App\Services\CreateLicenceKey;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateLicenceKeyTest extends TestCase
{
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

        $licenceKey = (new CreateLicenceKey)->execute($payload['data']);

        $this->assertInstanceOf(LicenceKey::class, $licenceKey);
        $this->assertIsString($licenceKey->key);

        $licenceKey = $licenceKey->key;

        $encrypter = app('license.encrypter');
        $array = $encrypter->decrypt($licenceKey);

        $this->assertArrayHasKey('frequency', $array);
        $this->assertArrayHasKey('purchaser_email', $array);
        $this->assertArrayHasKey('next_check_at', $array);

        $this->assertEquals(
            'regis@monicahq.com',
            $array['purchaser_email']
        );
        $this->assertEquals(
            Carbon::parse('2022-04-02'),
            $array['next_check_at']
        );
        $this->assertEquals(
            Plan::TYPE_MONTHLY,
            $array['frequency']
        );

        $this->assertDatabaseHas('licence_keys', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'key' => $licenceKey,
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
