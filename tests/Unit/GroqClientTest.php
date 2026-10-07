<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\GroqClient;

class GroqClientTest extends TestCase
{
    public function test_chat_returns_json()
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    ['message' => ['role' => 'assistant', 'content' => 'hello']],
                ],
            ], 200),
        ]);

        $client = new GroqClient();
        $resp = $client->chat('hi');

        $this->assertIsArray($resp);
        $this->assertEquals('hello', $resp['choices'][0]['message']['content']);

        Http::assertSent(function ($request) {
            return str_ends_with($request->url(), '/chat/completions')
                && $request['messages'][0]['content'] === 'hi';
        });
    }

    public function test_embeddings_use_the_local_model()
    {
        config([
            'embeddings.base_url' => 'http://embeddings.test/v1',
            'embeddings.model' => 'nomic-embed-text',
            'embeddings.api_key' => 'ollama',
        ]);

        Http::fake([
            'http://embeddings.test/*' => Http::response([
                'data' => [['embedding' => [0.1, 0.2]]],
            ], 200),
        ]);

        $resp = (new GroqClient())->embeddings('hola');

        $this->assertEquals([0.1, 0.2], $resp['data'][0]['embedding']);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://embeddings.test/v1/embeddings'
                && $request['model'] === 'nomic-embed-text'
                && $request['input'] === 'hola';
        });
    }
}
