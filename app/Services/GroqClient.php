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
            'input' => $input,
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
     * Convenience method for embeddings (if supported by Groq endpoint).
     */
    public function embeddings(string $text, array $options = []): array
    {
        $payload = array_merge([
            'model' => $this->model,
            'input' => $text,
            'type' => 'embedding',
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
}
