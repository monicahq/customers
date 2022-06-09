<?php

namespace Tests\Feature\Controllers;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\User;
use App\Services\ProductPrices;
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
        $this->mock(ProductPrices::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('execute')
                ->andReturn(collect([
                    [
                        'product_id' => $plan->plan_id_on_paddle,
                        'price' => '$10.00',
                        'currency' => 'USD',
                        'frequency_name' => 'month',
                    ]])
                );
        });

        $response = $this->actingAs($user)->get('/monica');

        Http::assertSent(function ($request) use ($user, $plan) {
            $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link', $request->url());
            $this->assertEquals('POST', $request->method());
            $this->assertStringContainsString('"product_id":"1"', $request->body());
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
        $this->mock(ProductPrices::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('execute')
                ->andReturn(collect([
                    [
                        'product_id' => $plan->plan_id_on_paddle,
                        'price' => '$10.00',
                        'currency' => 'USD',
                        'frequency_name' => 'month',
                    ]])
                );
        });

        $response = $this->actingAs($user)->get('/monica');

        Http::assertSent(function ($request) use ($user, $plan) {
            $this->assertEquals('https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link', $request->url());
            $this->assertEquals('POST', $request->method());
            $this->assertStringContainsString('"product_id":"1"', $request->body());
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

        Http::fake([
            'https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link' => Http::response([
                'success' => true,
                'response' => [
                    'url' => 'https://sandbox-vendors.paddle.com/example',
                ],
            ], 200),
        ]);
        $this->mock(ProductPrices::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('execute')
                ->andReturn(collect([
                    [
                        'product_id' => $plan->plan_id_on_paddle,
                        'price' => '$10.00',
                        'currency' => 'USD',
                        'frequency_name' => 'month',
                    ]])
                );
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
        $this->mock(ProductPrices::class, function (MockInterface $mock) use ($plan) {
            $mock->shouldReceive('execute')
                ->andReturn(collect([
                    [
                        'product_id' => $plan->plan_id_on_paddle,
                        'price' => '$10.00',
                        'currency' => 'USD',
                        'frequency_name' => 'month',
                    ]])
                );
        });

        $response = $this->actingAs($user2)->get('/monica');

        $response->assertStatus(200);
        $response->assertDontSee('abc123');
    }
}
