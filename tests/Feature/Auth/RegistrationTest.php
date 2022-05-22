<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_registration_screen_can_be_rendered()
    {
        if (! Features::enabled(Features::registration())) {
            return $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    // public function test_registration_screen_cannot_be_rendered_if_support_is_disabled()
    // {
    //     if (Features::enabled(Features::registration())) {
    //         return $this->markTestSkipped('Registration support is enabled.');
    //     }

    //     $response = $this->get('/register');

    //     $response->assertStatus(404);
    // }

    public function test_new_users_can_register()
    {
        if (! Features::enabled(Features::registration())) {
            return $this->markTestSkipped('Registration support is not enabled.');
        }

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password$123',
            'password_confirmation' => 'Password$123',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
