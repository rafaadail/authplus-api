<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $responseLogin = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $responseLogin = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $responseLogin->json('data.access_token');

        $response = $this->withToken($token)
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertEquals(true, $response->json('success'));
        $this->assertEquals($user->email, $response->json('data.email'));
    }

    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertEquals(false, $response->json('success'));
    }

    public function test_user_cannot_get_profile_with_invalid_token(): void
    {
        $response = $this->withToken('invalid-token')
            ->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertEquals(false, $response->json('success'));
    }
}
