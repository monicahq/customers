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

class OfficeLifeControllerTest extends TestCase
{
    /** @test */
    public function it_displays_list_of_plans(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create([
            'translation_key' => 'OfficeLifePlan',
            'plan_id_on_paddle' => 1,
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

        $response = $this->actingAs($user)->get('/officelife');

        $response->assertStatus(200);
        $response->assertSee('OfficeLifePlan');
    }

    /** @test */
    public function it_does_not_displays_monica_plans(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create([
            'translation_key' => 'OfficeLifePlan',
            'plan_id_on_paddle' => 1,
        ]);
        Plan::factory()->monica()->create([
            'translation_key' => 'MonicaPlan',
            'plan_id_on_paddle' => 2,
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

        $response = $this->actingAs($user)->get('/officelife');

        $response->assertStatus(200);
        $response->assertSee('OfficeLifePlan');

        $response->assertDontSee('MonicaPlan');
    }

    /** @test */
    public function it_does_list_licencekeys(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create();

        LicenceKey::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'key' => 'abc123',
        ]);
        Subscription::factory()->create([
            'billable_id' => $user->id,
            'paddle_plan' => $plan->plan_id_on_paddle,
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

        $response = $this->actingAs($user)->get('/officelife');

        $response->assertStatus(200);
        $response->assertSee('abc123');
    }

    /** @test */
    public function it_does_not_list_other_users_licencekeys(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create([
            'translation_key' => 'OfficeLifePlan',
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

        $response = $this->actingAs($user2)->get('/officelife');

        $response->assertStatus(200);
        $response->assertDontSee('abc123');
    }

    /** @test */
    public function it_gets_new_price(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create();

        $this->mock(Products::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('getProductPrices')
                ->andReturn(collect([
                    [
                        'product_id' => $plan->plan_id_on_paddle,
                        'price' => '$20.00',
                        'currency' => 'USD',
                        'frequency_name' => 'month',
                    ],
                ]));
        });

        $response = $this->actingAs($user)->post("/officelife/{$plan->id}/price", [
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'price' => '$20.00',
        ]);
    }

    /** @test */
    public function it_refuses_getting_price_for_monica_plan(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create();

        $response = $this->actingAs($user)->post("/officelife/{$plan->id}/price", [
            'quantity' => 2,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_updates_subscription_plan(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create();

        $this->mock(UpdateSubscription::class, function (MockInterface $mock) use ($user, $plan) {
            $mock->shouldReceive('execute')
                ->once()
                ->withArgs(function ($data) use ($user, $plan) {
                    $this->assertEquals([
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'quantity' => 5,
                    ], $data);

                    return true;
                });
        });

        $response = $this->actingAs($user)->patch('/officelife', [
            'plan_id' => $plan->id,
            'quantity' => 5,
        ]);

        $response->assertStatus(302);
    }

    /** @test */
    public function it_subscribes_to_a_plan(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create([
            'translation_key' => 'OfficeLifePlan',
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

        $response = $this->actingAs($user)->post("/officelife/{$plan->id}", [
            'quantity' => 5,
        ]);

        Http::assertSent(function ($request) use ($user) {
            $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link', $request->url());
            $this->assertEquals('POST', $request->method());
            $this->assertStringContainsString('"product_id":1', $request->body());
            $this->assertStringContainsString('"quantity":5', $request->body());
            $this->assertStringContainsString('\"billable_id\":'.$user->id, $request->body());

            return true;
        });

        $response->assertStatus(302);
        $response->assertRedirect('https://sandbox-vendors.paddle.com/example');
    }
}
