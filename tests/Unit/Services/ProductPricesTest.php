<?php

namespace Tests\Unit\Services;

use App\Models\Plan;
use App\Models\User;
use App\Services\ProductPrices;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductPricesTest extends TestCase
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
                        ]
                    ]
                ],
            ], 200),
        ]);

        $result = app(ProductPrices::class)->execute(collect([1]), $user);

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
            'frequency' => 'month',
        ], $result->first());
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
                        ]
                    ]
                ],
            ], 200),
        ]);

        $result = app(ProductPrices::class)->execute(collect([1]), $user, 3);

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
            'frequency' => 'month',
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
                        ]
                    ]
                ],
            ], 200),
        ]);

        $result = app(ProductPrices::class)->execute(collect([1]));

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
            'frequency' => 'month',
        ], $result->first());
    }
}
