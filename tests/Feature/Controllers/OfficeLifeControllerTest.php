<?php

namespace Tests\Feature\Controllers;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OfficeLifeControllerTest extends TestCase
{
    static $iteration;

    /** @test */
    public function it_displays_list_of_plans(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create([
            'friendly_name' => 'OfficeLifePlan',
            'plan_id_on_paddle' => 1,
        ]);

        Http::fake([
            "https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}" => Http::response([
                'success' => true,
                'response' => [
                    'customer_country' => 'US',
                    'products' => [
                        [
                            'product_id' => $plan->plan_id_on_paddle,
                            'currency' => 'USD',
                            'price' => [
                                'gross' => 10,
                            ],
                            'subscription' => [
                                'interval' => 'month',
                                'frequency' => 1,
                            ],
                        ]
                    ]
                ],
            ], 200),
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->get('/officelife');

        static::$iteration=0;
        Http::assertSent(function ($request) use ($user, $plan) {
            switch (static::$iteration++)
            {
                case 0:
                    $this->assertEquals("https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}", $request->url());
                    $this->assertEquals('GET', $request->method());

                    return true;
                            break;
                case 1:
                    $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link', $request->url());
                    $this->assertEquals('POST', $request->method());
                    $this->assertStringContainsString('"product_id":"1"', $request->body());
                    $this->assertStringContainsString('\"billable_id\":'.$user->id, $request->body());

                    return true;
                    break;
                default:
                    return false;
            }
        });

        $response->assertStatus(200);
        $response->assertSee('OfficeLifePlan');
        $response->assertSee('https:\\/\\/sandbox-vendors.paddle.com\\/example');
    }

    /** @test */
    public function it_does_not_displays_monica_plans(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create([
            'friendly_name' => 'OfficeLifePlan',
            'plan_id_on_paddle' => 1,
        ]);
        Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
            'plan_id_on_paddle' => 2,
        ]);

        Http::fake([
            "https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}" => Http::response([
                'success' => true,
                'response' => [
                    'customer_country' => 'US',
                    'products' => [
                        [
                            'product_id' => $plan->plan_id_on_paddle,
                            'currency' => 'USD',
                            'price' => [
                                'gross' => 10,
                            ],
                            'subscription' => [
                                'interval' => 'month',
                                'frequency' => 1,
                            ],
                        ]
                    ]
                ],
            ], 200),
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->get('/officelife');

        static::$iteration=0;
        Http::assertSent(function ($request) use ($user, $plan) {
            switch (static::$iteration++)
            {
                case 0:
                    $this->assertEquals("https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}", $request->url());
                    $this->assertEquals('GET', $request->method());

                    return true;
                            break;
                case 1:
                    $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link', $request->url());
                    $this->assertEquals('POST', $request->method());
                    $this->assertStringContainsString('"product_id":"1"', $request->body());
                    $this->assertStringContainsString('\"billable_id\":'.$user->id, $request->body());

                    return true;
                    break;
                default:
                    return false;
            }
        });

        $response->assertStatus(200);
        $response->assertSee('OfficeLifePlan');
        $response->assertSee('https:\\/\\/sandbox-vendors.paddle.com\\/example');

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

        Http::fake([
            "https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}" => Http::response([
                'success' => true,
                'response' => [
                    'customer_country' => 'US',
                    'products' => [
                        [
                            'product_id' => $plan->plan_id_on_paddle,
                            'currency' => 'USD',
                            'price' => [
                                'gross' => 10,
                            ],
                            'subscription' => [
                                'interval' => 'month',
                                'frequency' => 1,
                            ],
                        ]
                    ]
                ],
            ], 200),
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->get('/officelife');

        $response->assertStatus(200);
        $response->assertSee('abc123');
    }

    /** @test */
    public function it_does_not_list_other_users_licencekeys(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create([
            'friendly_name' => 'OfficeLifePlan',
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
            "https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user2->country}&product_ids={$plan->plan_id_on_paddle}" => Http::response([
                'success' => true,
                'response' => [
                    'customer_country' => 'US',
                    'products' => [
                        [
                            'product_id' => $plan->plan_id_on_paddle,
                            'currency' => 'USD',
                            'price' => [
                                'gross' => 10,
                            ],
                            'subscription' => [
                                'interval' => 'month',
                                'frequency' => 1,
                            ],
                        ]
                    ]
                ],
            ], 200),
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user2)->get('/officelife');

        $response->assertStatus(200);
        $response->assertDontSee('abc123');
    }

    /** @test */
    public function it_gets_new_price(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->officelife()->create([
        ]);

        Http::fake([
            "https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}" => Http::response([
                'success' => true,
                'response' => [
                    'customer_country' => 'US',
                    'products' => [
                        [
                            'product_id' => $plan->plan_id_on_paddle,
                            'currency' => 'USD',
                            'price' => [
                                'gross' => 10,
                            ],
                            'subscription' => [
                                'interval' => 'month',
                                'frequency' => 1,
                            ],
                        ]
                    ]
                ],
            ], 200),
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post("/officelife/{$plan->id}/price", [
            'quantity' => 2,
        ]);

        static::$iteration=0;
        Http::assertSent(function ($request) use ($user, $plan) {
            switch (static::$iteration++)
            {
                case 0:
                    $this->assertEquals("https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}", $request->url());
                    $this->assertEquals('GET', $request->method());

                    return true;
                            break;
                case 1:
                    $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link', $request->url());
                    $this->assertEquals('POST', $request->method());
                    $this->assertStringContainsString('"product_id":"'.$plan->plan_id_on_paddle.'"', $request->body());
                    $this->assertStringContainsString('"quantity":2', $request->body());
                    $this->assertStringContainsString('\"billable_id\":'.$user->id, $request->body());

                    return true;
                            break;
                default:
                    return false;
            }
        });

        $response->assertStatus(200);
        $response->assertJson([
            'price' => '$20.00',
            'pay_link' => 'https://sandbox-vendors.paddle.com/example',
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
}
