<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class BrowserSessionsTest extends TestCase
{
    public function test_other_browser_sessions_can_be_logged_out()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->delete('/user/other-browser-sessions', [
            'password' => 'password',
        ]);

        $response->assertSessionHasNoErrors();
    }
}
