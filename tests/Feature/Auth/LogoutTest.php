<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $responseLogin = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $responseLogin->json('data.access_token')
        ])->post('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        $json = $response->json();

        $this->assertEquals(true, $json['success']);
    }

    public function test_user_cannot_logout_without_token(): void
    {
        $response = $this->post('/api/auth/logout');

        $response->assertStatus(401)
            ->assertJson([
               'success' => false,
                'message' => 'Unauthenticated.',

            ]);
    }

    public function test_user_cannot_access_me_after_logout(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $responseLogin = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $responseLogin->json('data.access_token');

        $this->withToken($token)
            ->post('/api/auth/logout')
            ->assertStatus(200);


        $responseMe = $this->withToken($token)->getJson('/api/auth/me');

        $responseMe->assertStatus(401)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        $this->assertEquals(false, $responseMe->json('success'));
    }
}
