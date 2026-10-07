<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqClient
{
    protected string $baseUrl;
    protected string $endpoint;
    protected string $apiKey;
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('groq.base_url'), '/');
        $this->endpoint = '/' . ltrim(config('groq.endpoint', '/v1/generate'), '/');
        $this->apiKey = config('groq.api_key');
        $this->model = config('groq.model');
        $this->timeout = (int) config('groq.timeout', 60);
    }

    /**
     * Send a chat/generation request to Groq.
     * Returns decoded JSON array on success, throws exception on failure.
     */
    public function chat(string $input, array $options = []): array
    {
        $payload = array_merge([
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $input],
            ],
        ], $options);

        $url = $this->baseUrl . $this->endpoint;

        $response = Http::withToken($this->apiKey)
            ->acceptJson()
            ->timeout($this->timeout)
            ->post($url, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \RuntimeException('Groq API request failed: ' . $response->body(), $response->status());
    }

    /**
     * Create an embedding with the local model (Ollama).
     */
    public function embeddings(string $text, array $options = []): array
    {
        $baseUrl = rtrim((string) config('embeddings.base_url'), '/');
        $apiKey = (string) config('embeddings.api_key', '');
        $timeout = (int) config('embeddings.timeout', $this->timeout);

        $payload = array_merge([
            'model' => config('embeddings.model', 'nomic-embed-text'),
            'input' => $text,
        ], $options);

        $request = Http::acceptJson()->timeout($timeout);
        if ($apiKey !== '') {
            $request = $request->withToken($apiKey);
        }

        $response = $request->post($baseUrl.'/embeddings', $payload);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \RuntimeException('Embedding request failed: '.$response->body(), $response->status());
    }
}
