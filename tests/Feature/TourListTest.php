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

    public function test_tours_list_is_sorted_by_starting_date(): void
    {
       $travel = Travel::factory()->create();
        
       $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(5),
        ]);
        
       
       $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
        ]);

        $response = $this->get('/api/v1/travel/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $earlierTour->id);
        $response->assertJsonPath('data.1.id', $laterTour->id);
        $response->assertJsonCount(2, 'data');
    }

     public function test_tours_list_sorts_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $cheapLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 111,
            'starting_date' => now()->addDays(5),
        ]);
        
       $expensiveEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 888,
            'starting_date' => now(),
        ]);

       $cheapEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 111,
            'starting_date' => now(),
        ]);

       $expensiveLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 888,
            'starting_date' => now()->addDays(5),
        ]);
        
        $response = $this->get('/api/v1/travel/'.$travel->slug.'/tours?sortBy=price&sortOrder=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id);
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);
        $response->assertJsonPath('data.2.id', $expensiveEarlierTour->id);
        $response->assertJsonPath('data.3.id', $expensiveLaterTour->id);
       
    }

     public function test_tours_list_filters_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $cheapLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 111,
            'starting_date' => now()->addDays(5),
        ]);
        
       $expensiveEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 888,
            'starting_date' => now(),
        ]);

       $cheapEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 111,
            'starting_date' => now(),
        ]);

       $expensiveLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 888,
            'starting_date' => now()->addDays(5),
        ]);
        
        $response = $this->get('/api/v1/travel/'.$travel->slug.'/tours?sortBy=price&sortOrder=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id);
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);
        $response->assertJsonPath('data.2.id', $expensiveEarlierTour->id);
        $response->assertJsonPath('data.3.id', $expensiveLaterTour->id);
       
    }
}
