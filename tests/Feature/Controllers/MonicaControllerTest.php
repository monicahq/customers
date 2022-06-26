<?php

namespace Tests\Feature\Controllers;

use App\Helpers\Products;
use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\UpdateSubscription;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Tests\TestCase;

class MonicaControllerTest extends TestCase
{
    /** @test */
    public function it_displays_list_of_plans(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
            'plan_id_on_paddle' => 1,
        ]);

        Http::fake([
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);
        $this->mock(Products::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('getProductPrices')
                ->andReturn(collect([
                    [
                        'product_id' => $plan->plan_id_on_paddle,
                        'price' => '$10.00',
                        'currency' => 'USD',
                        'frequency_name' => 'month',
                    ],
                ]));
        });

        $response = $this->actingAs($user)->get('/monica');

        Http::assertSent(function ($request) use ($user) {
            $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link', $request->url());
            $this->assertEquals('POST', $request->method());
            $this->assertStringContainsString('"product_id":1', $request->body());
            $this->assertStringContainsString('\"billable_id\":'.$user->id, $request->body());

            return true;
        });

        $response->assertStatus(200);
        $response->assertSee('MonicaPlan');
        $response->assertSee('https:\\/\\/sandbox-vendors.paddle.com\\/example');
    }

    /** @test */
    public function it_does_not_displays_officelife_plans(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
            'plan_id_on_paddle' => 1,
        ]);
        Plan::factory()->officelife()->create([
            'friendly_name' => 'OfficeLifePlan',
            'plan_id_on_paddle' => 2,
        ]);

        Http::fake([
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);
        $this->mock(Products::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('getProductPrices')
                ->andReturn(collect([
                    [
                        'product_id' => $plan->plan_id_on_paddle,
                        'price' => '$10.00',
                        'currency' => 'USD',
                        'frequency_name' => 'month',
                    ],
                ]));
        });

        $response = $this->actingAs($user)->get('/monica');

        Http::assertSent(function ($request) use ($user) {
            $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link', $request->url());
            $this->assertEquals('POST', $request->method());
            $this->assertStringContainsString('"product_id":1', $request->body());
            $this->assertStringContainsString('\"billable_id\":'.$user->id, $request->body());

            return true;
        });

        $response->assertStatus(200);
        $response->assertSee('MonicaPlan');
        $response->assertSee('https:\\/\\/sandbox-vendors.paddle.com\\/example');
        $response->assertDontSee('OfficeLifePlan');
    }

    /** @test */
    public function it_does_list_licencekeys(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create();

        LicenceKey::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'key' => 'abc123',
        ]);
        Subscription::create([
            'billable_id' => $user->id,
            'billable_type' => User::class,
            'name' => 'name',
            'paddle_id' => random_int(0, 100),
            'paddle_status' => 'active',
            'paddle_plan' => $plan->plan_id_on_paddle,
            'quantity' => 1,
        ]);

        Http::fake([
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);
        $this->mock(Products::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('getProductPrices')
                ->andReturn(collect([
                    [
                        'product_id' => $plan->plan_id_on_paddle,
                        'price' => '$10.00',
                        'currency' => 'USD',
                        'frequency_name' => 'month',
                    ],
                ]));
        });

        $response = $this->actingAs($user)->get('/monica');

        $response->assertStatus(200);
        $response->assertSee('abc123');
    }

    /** @test */
    public function it_does_not_list_other_users_licencekeys(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
        ]);

        LicenceKey::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'key' => 'abc123',
        ]);

        $user2 = User::factory()->create();
        LicenceKey::factory()->create([
            'user_id' => $user2->id,
            'plan_id' => $plan->id,
        ]);

        Http::fake([
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);
        $this->mock(Products::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('getProductPrices')
                ->andReturn(collect([
                    [
                        'product_id' => $plan->plan_id_on_paddle,
                        'price' => '$10.00',
                        'currency' => 'USD',
                        'frequency_name' => 'month',
                    ],
                ]));
        });

        $response = $this->actingAs($user2)->get('/monica');

        $response->assertStatus(200);
        $response->assertDontSee('abc123');
    }

    /** @test */
    public function it_updates_subscription_plan(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create();

        $this->mock(UpdateSubscription::class, function (MockInterface $mock) use ($user, $plan) {
            $mock->shouldReceive('execute')
                ->once()
                ->withArgs(function ($data) use ($user, $plan) {
                    $this->assertEquals([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                    ], $data);

                    return true;
                });
        });

        $response = $this->actingAs($user)->patch('/monica', [
            'plan_id' => $plan->id,
        ]);

        $response->assertStatus(302);
    }
}
