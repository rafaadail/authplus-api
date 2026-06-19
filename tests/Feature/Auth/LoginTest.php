<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'access_token',
                    'token_type',
                    'refresh_token',
                    'expires_in',
                ],
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertEquals(false, $response->json('success'));
    }

    public function test_login_fails_with_missing_fields(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            // No email or password
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);

        $json = $response->json();

        $this->assertEquals(false, $json['success']);
        $this->assertContains('The email field is required.', $json['errors']);
        $this->assertContains('The password field is required.', $json['errors']);
    }

    public function test_login_fails_with_invalid_email_format(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);
    }

    public function test_login_requires_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);

        $json = $response->json();

        $this->assertEquals(false, $json['success']);

        $this->assertContains(
            'The email field is required.',
            $json['errors']
        );
    }

    public function test_login_requires_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);

        $json = $response->json();

        $this->assertEquals(false, $json['success']);

        $this->assertContains(
            'The password field is required.',
            $json['errors']
        );
    }

    public function test_login_requires_valid_email_format(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'email-invalido',
            'password' => 'password',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);

        $json = $response->json();

        $this->assertEquals(false, $json['success']);

        $this->assertContains(
            'The email field must be a valid email address.',
            $json['errors']
        );
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 6; $i++) {

            $response = $this->postJson('/api/auth/login', [
                'email' => 'invalid@example.com',
                'password' => 'invalid-password',
            ]);
        }

        $response
            ->assertStatus(429)
            ->assertJson([
                'success' => false,
            ]);
    }
}
