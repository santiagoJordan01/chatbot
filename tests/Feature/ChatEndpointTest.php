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

        $response = $this->postJson('/api/v1/chat/groq', [
            'message' => 'Hello',
            'use_rag' => false,
        ]);

        $response->assertStatus(200)->assertJson(['ok' => true]);
    }

    public function test_rag_sends_clinic_context_and_history()
    {
        config([
            'embeddings.base_url' => 'http://embeddings.test/v1',
            'embeddings.model' => 'nomic-embed-text',
            'embeddings.api_key' => 'ollama',
        ]);

        Embedding::query()->create([
            'source_type' => 'clinic',
            'source_id' => 'limpieza-dental',
            'embedding' => [1, 0, 0],
            'metadata' => ['text' => 'Perro mediano: 240.000 pesos'],
        ]);

        Http::fake(function ($request) {
            if (str_contains($request->url(), '/embeddings')) {
                return Http::response(['data' => [['embedding' => [1, 0, 0]]]], 200);
            }

            return Http::response([
                'choices' => [['message' => ['content' => '240.000 pesos']]],
            ], 200);
        });

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/chat/groq', [
            'message' => 'Tiene 4 años',
            'use_rag' => true,
            'history' => [
                ['role' => 'user', 'content' => 'Quiero cotizar la limpieza de mi perro'],
            ],
        ]);

        $response->assertOk()->assertJsonPath('data.choices.0.message.content', '240.000 pesos');

        Http::assertSent(function ($request) {
            if (! str_ends_with($request->url(), '/chat/completions')) {
                return false;
            }

            $contents = collect($request['messages'])->pluck('content')->implode("\n");

            return str_contains($contents, '240.000 pesos')
                && str_contains($contents, 'Quiero cotizar la limpieza de mi perro')
                && str_contains($contents, 'Tiene 4 años');
        });
    }
}
