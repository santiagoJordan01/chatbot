<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Embedding;

class ChatEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_chat()
    {
        Http::fake([
            '*' => Http::response(['data' => [['embedding' => [0.1,0.2,0.3]]]], 200),
        ]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/chat/groq', ['message' => 'Hello']);

        $response->assertStatus(200)->assertJson(['ok' => true]);
    }
}
