<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Listing;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Mail\NewApplicationNotification;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_apply_to_a_listing()
    {
        Mail::fake();

        $user = User::factory()->create();
        $listing = Listing::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson("/api/listings/{$listing->id}/apply");

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Application submitted']);

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'listing_id' => $listing->id,
        ]);

        Mail::assertQueued(NewApplicationNotification::class);
    }

    /** @test */
    public function user_cannot_apply_twice_to_same_listing()
    {
        $user = User::factory()->create();
        $listing = Listing::factory()->create();

        Application::create([
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user, 'api')->postJson("/api/listings/{$listing->id}/apply");

        $response->assertStatus(410)
                 ->assertJson(['message' => 'Already applied']);
    }
}
