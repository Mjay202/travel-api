<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TravelListTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_public_user_cannot_create_travels(): void
    {
        
        $response = $this->postJson('/api/v1/admin/travels');

        $response->assertStatus(401);
    }
    
    public function test_non_admin_users_cannot_create_travels(): void
    {
        $user = User::factory()->create();
        $this->seed(RoleSeeder::class);
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_travels(): void
    {
        $user = User::factory()->create();
        $this->seed(RoleSeeder::class);
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels');

        $response->assertStatus(422);
    }

    public function test_admin_can_create_travels_successfully_with_valid_inputs(): void
    {
        $user = User::factory()->create();
        $this->seed(RoleSeeder::class);
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'new travel',
            'description' => 'newTravel Description',
            'number_of_days' => 12,
        ]);

        $response->assertStatus(201);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['slug' => 'new-travel']);
    }
}
