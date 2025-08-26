<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
    // Authenticate a user because the '/' route is protected by 'auth' middleware
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
    }
}
