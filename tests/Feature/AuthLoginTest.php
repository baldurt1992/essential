<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_login_via_api_endpoint(): void
    {
        $this->seed();

        $response = $this->postJson('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertNoContent();

        $this->assertAuthenticated();

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonFragment([
                'email' => 'admin@example.com',
            ]);
    }
}
