<?php

namespace Tests\Feature\Controllers;

use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\User;
use Tests\TestCase;

class OfficeLifeControllerTest extends TestCase
{
    /** @test */
    public function it_displays_list_of_plans(): void
    {
        $user = User::factory()->create();
        Plan::factory()->officelife()->create([
            'friendly_name' => 'OfficeLifePlan',
            'plan_id_on_paddle' => 1,
        ]);

        $response = $this->actingAs($user)->get('/officelife');

        $response->assertStatus(200);
        $response->assertSee('OfficeLifePlan');
    }

    /** @test */
    public function it_does_not_displays_monica_plans(): void
    {
        $user = User::factory()->create();
        Plan::factory()->officelife()->create([
            'friendly_name' => 'OfficeLifePlan',
            'plan_id_on_paddle' => 1,
        ]);
        Plan::factory()->monica()->create([
            'friendly_name' => 'MonicaPlan',
            'plan_id_on_paddle' => 2,
        ]);

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

        $response = $this->actingAs($user2)->get('/officelife');

        $response->assertStatus(200);
        $response->assertDontSee('abc123');
    }
}
