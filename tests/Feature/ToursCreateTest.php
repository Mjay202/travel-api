<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToursCreateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_public_user_cannot_create_tours(): void
    {
        $travel = Travel::factory()->create();

        $response = $this->postJson('/api/v1/admin/travels/'.$travel->slug.'/tour');

        $response->assertStatus(401);
    }

    public function test_non_admin_cannot_create_tour(): void
    {
        $travel = Travel::factory()->create();

        $user = User::factory()->create();
        $this->seed(RoleSeeder::class);
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->slug.'/tour');

        $response->assertStatus(403);
    }

    public function test_admin_create_tours_with_invalid_input_returns_validation_errors(): void
    {
        $travel = Travel::factory()->create();

        $user = User::factory()->create();
        $this->seed(RoleSeeder::class);
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->slug.'/tour', [
            'name' => 1234,
            'starting_date' => '2024-12-11',
            'ending_date' => '2024-10-11',
            'price' => 'price',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_create_tours_returns_200_with_admin_inputing_valid_inputs(): void
    {
        $travel = Travel::factory()->create();

        $user = User::factory()->create();
        $this->seed(RoleSeeder::class);
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->slug.'/tour', [
            'name' => 'new tour',
            'starting_date' => '2024-10-11',
            'ending_date' => '2024-12-11',
            'price' => 3000,
        ]);

        $response->assertStatus(201);
        $response->assertJsonCount(1);
    }
}
