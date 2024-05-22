<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

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

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

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

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

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

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');

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

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours?sortBy=price&sortOrder=asc');

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

        $endpoint = '/api/v1/travels/'.$travel->slug.'/tours?';
        // Sort by priceFrom
        $response = $this->get($endpoint.'priceFrom=888');
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $expensiveEarlierTour->id);
        $response->assertJsonPath('data.1.id', $expensiveLaterTour->id);

        // Sort by priceTo
        $response = $this->get($endpoint.'priceTo=150');
        // $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id);
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);

    }

    public function test_tours_list_filters_date_correctly(): void
    {
        $travel = Travel::factory()->create();
        $latestTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 111,
            'starting_date' => now()->addYear(1),
        ]);

        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 888,
            'starting_date' => now()->addMonth(1),
        ]);

        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 111,
            'starting_date' => now()->addWeeks(2),
        ]);

        $earliestTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 888,
            'starting_date' => now(),
        ]);

        $endpoint = '/api/v1/travels/'.$travel->slug.'/tours?';
        // Sort by dateFrom
        $response = $this->get($endpoint.'dateFrom='.now()->addWeeks(2));
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonPath('data.0.id', $earlierTour->id);
        $response->assertJsonPath('data.1.id', $laterTour->id);
        $response->assertJsonMissing(['id', $earliestTour->id]);

        // Sort by dateTo
        $response = $this->get($endpoint.'dateTo='.now()->addMonth(1));
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonPath('data.0.id', $earliestTour->id);
        $response->assertJsonPath('data.1.id', $earlierTour->id);
        $response->assertJsonMissing(['id', $latestTour->id]);

    }

    public function test_tours_list_throws_exception_error_status(): void
    {

        $travel = Travel::factory()->create();
        Tour::factory()->create([
            'travel_id' => $travel->id,
        ]);

        $response = $this->getJson('api/v1/travels/'.$travel->slug.'/tours?sortBy=slug&sortOrder=abcd');
        $response->assertStatus(422);
    }
}
