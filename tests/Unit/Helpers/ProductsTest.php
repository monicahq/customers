<?php

namespace Tests\Unit\Helpers;

use App\Helpers\Products;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    /** @test */
    public function it_gets_list_of_prices(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
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
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Products::class)->getProductPrices(collect([1]), $user);

        Http::assertSent(function ($request) use ($user, $plan) {
            $this->assertEquals("https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}", $request->url());
            $this->assertEquals('GET', $request->method());

            return true;
        });

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals([
            'product_id' => 1,
            'currency' => 'USD',
            'price' => '$10.00',
            'price_value' => 10,
            'frequency' => 'month',
            'frequency_name' => 'month',
        ], $result->first());

        Cache::has('en|US|1');
    }

    /** @test */
    public function it_gets_list_of_prices_with_quantity(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
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
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Products::class)->getProductPrices(collect([1]), $user, 3);

        Http::assertSent(function ($request) use ($user, $plan) {
            $this->assertEquals("https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}", $request->url());
            $this->assertEquals('GET', $request->method());

            return true;
        });

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals([
            'product_id' => 1,
            'currency' => 'USD',
            'price' => '$30.00',
            'price_value' => 30,
            'frequency' => 'month',
            'frequency_name' => 'month',
        ], $result->first());
    }

    /** @test */
    public function it_gets_list_of_prices_without_user(): void
    {
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
            'plan_id_on_paddle' => 1,
        ]);

        Http::fake([
            "https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country=US&product_ids={$plan->plan_id_on_paddle}" => Http::response([
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
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Products::class)->getProductPrices(collect([1]));

        Http::assertSent(function ($request) use ($plan) {
            $this->assertEquals("https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country=US&product_ids={$plan->plan_id_on_paddle}", $request->url());
            $this->assertEquals('GET', $request->method());

            return true;
        });

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals([
            'product_id' => 1,
            'currency' => 'USD',
            'price' => '$10.00',
            'price_value' => 10,
            'frequency' => 'month',
            'frequency_name' => 'month',
        ], $result->first());
    }

    /** @test */
    public function it_gets_list_of_prices_daily(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
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
                                'interval' => 'day',
                                'frequency' => 1,
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Products::class)->getProductPrices(collect([1]), $user);

        Http::assertSent(function ($request) use ($user, $plan) {
            $this->assertEquals("https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}", $request->url());
            $this->assertEquals('GET', $request->method());

            return true;
        });

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals([
            'product_id' => 1,
            'currency' => 'USD',
            'price' => '$10.00',
            'price_value' => 10,
            'frequency' => 'day',
            'frequency_name' => 'day',
        ], $result->first());
    }

    /** @test */
    public function it_gets_list_of_prices_yearly(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
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
                                'gross' => 100,
                            ],
                            'subscription' => [
                                'interval' => 'year',
                                'frequency' => 1,
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Products::class)->getProductPrices(collect([1]), $user);

        Http::assertSent(function ($request) use ($user, $plan) {
            $this->assertEquals("https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}", $request->url());
            $this->assertEquals('GET', $request->method());

            return true;
        });

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals([
            'product_id' => 1,
            'currency' => 'USD',
            'price' => '$100.00',
            'price_value' => 100,
            'frequency' => 'year',
            'frequency_name' => 'year',
        ], $result->first());
    }

    /** @test */
    public function it_gets_list_of_prices_3months(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
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
                                'frequency' => 3,
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Products::class)->getProductPrices(collect([1]), $user);

        Http::assertSent(function ($request) use ($user, $plan) {
            $this->assertEquals("https://sandbox-checkout.paddle.com/api/2.0/prices?customer_country={$user->country}&product_ids={$plan->plan_id_on_paddle}", $request->url());
            $this->assertEquals('GET', $request->method());

            return true;
        });

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals([
            'product_id' => 1,
            'currency' => 'USD',
            'price' => '$10.00',
            'price_value' => 10,
            'frequency' => 'month',
            'frequency_name' => '3 months',
        ], $result->first());
    }
}
