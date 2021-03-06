<?php

namespace Tests\Unit\Services;

use App\Helpers\Products;
use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\CreateLicenceKey;
use Mockery\MockInterface;
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
            'plan_id_on_paddle' => 1,
        ]);
        $subscription = Subscription::factory()->create([
            'billable_id' => $user->id,
            'paddle_plan' => $plan->plan_id_on_paddle,
        ]);

        $this->mock(Products::class, function (MockInterface $mock) {
            $mock->shouldReceive('getProductPrices')
                ->andReturn(collect([
                    [
                        'product_id' => 1,
                        'price' => '$10.00',
                        'currency' => 'USD',
                        'frequency' => 'month',
                    ],
                ]));
        });

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

        $this->assertEquals(
            'regis@monicahq.com',
            $array['purchaser_email']
        );
        $this->assertEquals(
            'month',
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
