<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ErrorResponseTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_returns_not_for_invalid_route(): void
    {
        $response = $this->getJson('/api/invalid-route');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }

    public function test_returns_method_not_allowed(): void
    {
        $response = $this->getJson('/api/auth/login'); // GET is expected, not POST

        $response->assertStatus(405)
            ->assertJson([
                'success' => false
            ]);
    }

    public function test_refresh_is_rate_limited(): void
    {

        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $responseLogin = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $responseLogin->json('data.access_token');

        for ($i = 0; $i < 10; $i++) {
            $this->withToken($token)->postJson('/api/auth/refresh', []);
        }

        $response = $this->withToken($token)->postJson('/api/auth/refresh', []);

        $response
            ->assertStatus(429)
            ->assertJson([
                'success' => false,
            ]);
    }
}
