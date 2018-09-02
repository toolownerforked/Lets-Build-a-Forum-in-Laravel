<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_threads()
    {
        config(['scout.driver' => 'algolia']);

        create(\App\Thread::class, [], 2);
        create(\App\Thread::class, ['body' => 'A thread with the foobar term.'], 2);

        do {
            // Account for latency.
            sleep(.25);

            $results = $this->getJson('/threads/search?q=foobar')->json()['data'];
        } while (empty($results));

        $this->assertCount(2, $results);

        // Clean up.
        Thread::latest()->take(4)->unsearchable();
    }
}
