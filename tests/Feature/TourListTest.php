<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TourListTest extends TestCase
{
    /**
     * A basic feature test example.
     */

     use RefreshDatabase;
    public function test_tours_list_by_travel_slug_returns_correct_tours(): void
    {
       $travel = Travel::factory()->create();
         Tour::factory()->create(['travel_id' => $travel->id]);
        
        $response = $this->get('/api/v1/travel/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['travel_id' => $travel->id]);
    }

    public function test_tours_price_returns_correctly(): void
    {
       $travel = Travel::factory()->create();
        Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 123,
        ]);
        
        $response = $this->get('/api/v1/travel/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => '123.00']);
    }

    public function test_tours_price_returns_pagination_correctly(): void
    {
       $travel = Travel::factory()->create();
        Tour::factory(16)->create([
            'travel_id' => $travel->id,
            'price' => 123,
        ]);
        
        $response = $this->get('/api/v1/travel/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }


}
