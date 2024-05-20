<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_login_returns_token_for_validated_users(): void
    {
        $user = User::factory()->create();
        
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);
    }

    public function test_login_returns_error_for_invalid_users(): void
    {
        $user = User::factory()->create();
        
        $response = $this->postJson('/api/v1/login', [
            'email' => 'nonexistinguser@gmail.com',
            'password' => 'incorrectpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJsonMissingPath('access_token');
    }
}
