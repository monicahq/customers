<?php

namespace Tests\Unit\Services;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\CreateLicenceKey;
use Carbon\Carbon;
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
        $subscription = Subscription::factory()->create([
            'billable_id' => $user->id,
            'paddle_plan' => $plan->plan_id_on_paddle,
        ]);

        $licenceKey = (new CreateLicenceKey)->execute($user, $subscription, [
            'next_bill_date' => '2022-04-02',
            'cancel_url' => 'fake',
            'update_url' => 'fake',
        ]);

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
}
