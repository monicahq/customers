<?php

namespace Tests\Feature\Controllers\Account;

use App\Models\User;
use Tests\TestCase;

class UserAddressControllerTest extends TestCase
{
    /** @test */
    public function it_updates_address(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/user/address', [
            'address_line_1' => 'My address',
            'city' => 'Paris',
            'postal_code' => '75001',
            'country' => 'FR',
        ]);

        $response->assertStatus(303);
        $response->assertRedirect('');
        $response->assertSessionHas('status', 'user-address-updated');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'address_line_1' => 'My address',
            'city' => 'Paris',
            'postal_code' => 75001,
            'country' => 'FR',
        ]);
    }

    /** @test */
    public function it_fails_if_missing_postal_code(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/user/address', [
            'address_line_1' => 'My address',
            'city' => 'Paris',
            'country' => 'FR',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'address_line_1' => 'My address',
            'city' => 'Paris',
            'country' => 'FR',
        ]);
    }

    /** @test */
    public function it_succeed_if_missing_postal_code(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/user/address', [
            'address_line_1' => 'My address',
            'city' => 'Bruxelles',
            'country' => 'BE',
        ]);

        $response->assertStatus(303);
        $response->assertRedirect('');
        $response->assertSessionHas('status', 'user-address-updated');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'address_line_1' => 'My address',
            'city' => 'Bruxelles',
            'country' => 'BE',
        ]);
    }
}
