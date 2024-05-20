<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TravelListTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_public_user_cannot_create_travel(): void
    {
        
        $response = $this->postJson('/api/v1/admin/travels');

        $response->assertStatus(401);
    }
}
