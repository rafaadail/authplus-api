<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RefreshTokenTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_can_refresh_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $responseLogin = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $responseLogin->json('data.refresh_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/refresh');

        $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'access_token',
                'token_type',
                'refresh_token',
                'expires_in'
            ],
        ]);

        $this->assertEquals(true, $response->json('success'));
    }

        public function test_refresh_token_fails_with_invalid_token(): void
        {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token'
            ])->postJson('/api/auth/refresh');

            $response->assertStatus(401)
                ->assertJsonStructure([
                    'success',
                    'message'
                ]);

            $this->assertEquals(false, $response->json('success'));
        }
}
