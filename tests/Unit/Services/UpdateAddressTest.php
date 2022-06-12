<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UpdateAddress;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserAddressTest extends TestCase
{
    /** @test */
    public function it_updates_user_address(): void
    {
        $user = User::factory()->create();

        app(UpdateAddress::class)->execute([
            'user_id' => $user->id,
            'address_line_1' => 'My address',
            'city' => 'Paris',
            'postal_code' => '75001',
            'country' => 'FR',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'address_line_1' => 'My address',
            'city' => 'Paris',
            'postal_code' => 75001,
            'country' => 'FR',
        ]);
    }

    /** @test */
    public function it_fails_when_wrong_params(): void
    {
        $this->expectException(ValidationException::class);

        app(UpdateAddress::class)->execute([
            'user_id' => 0,
            'address_line_1' => 'My address',
            'city' => 'Paris',
            'postal_code' => '75001',
            'country' => 'FR',
        ]);
    }
}
