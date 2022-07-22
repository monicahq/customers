<?php

namespace Tests\Feature\Controllers\Auth;

use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    /** @test */
    public function it_get_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    /** @test */
    public function it_get_login_without_webauthn(): void
    {
        $user = $this->user();

        $response = $this->get('/login', [
            'Cookie' => "webauthn_remember={$user->id}",
            'X-Inertia' => true,
            'X-Inertia-Version' => md5_file(public_path('build/manifest.json')),
        ]);

        $response->assertStatus(200);
        $response->assertSee('Login');
        $this->assertArrayNotHasKey('publicKey', $response->json()['props']);
    }

    /** @test */
    public function it_get_login_with_webauthn(): void
    {
        $user = $this->user();
        $key = $user->webauthnKeys()->create([
            'name' => 'name',
            'counter' => 0,
            'credentialId' => \ParagonIE\ConstantTime\Base64UrlSafe::encode($user->id),
            'type' => 'public-key',
            'transports' => [],
            'attestationType' => 'none',
            'trustPath' => new \Webauthn\TrustPath\EmptyTrustPath,
            'aaguid' => \Symfony\Component\Uid\Uuid::fromString('38195f59-0e5b-4ebf-be46-75664177eeee'),
            'credentialPublicKey' => 'oWNrZXlldmFsdWU=',

        ]);

        $response = $this->withCookie('webauthn_remember', $user->id)
            ->get('/login', [
                'X-Inertia' => true,
                'X-Inertia-Version' => md5_file(public_path('build/manifest.json')),
            ]);

        $response->assertStatus(200);
        $response->assertSee('Login');
        $this->assertArrayHasKey('publicKey', $response->json()['props']);
    }
}
