<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use Dotenv\Dotenv;

$root = dirname(__DIR__);
if (file_exists($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

$apiKey = getenv('GROQ_API_KEY') ?: null;
$base = getenv('GROQ_BASE_URL') ?: 'https://api.groq.ai';
$endpoint = getenv('GROQ_ENDPOINT') ?: '/v1/generate';
$model = getenv('GROQ_MODEL') ?: 'llama-3.3-70b';

if (empty($apiKey)) {
    echo "GROQ_API_KEY is not set. Please add it to your .env file and retry.\n";
    exit(1);
}

$client = new Client(['base_uri' => rtrim($base, '/')]);

try {
    $resp = $client->post($endpoint, [
        'headers' => [
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ],
        'json' => [
            'model' => $model,
            'input' => 'Hello from integration test',
        ],
        'timeout' => 10,
    ]);

    $body = json_decode((string) $resp->getBody(), true);
    echo "Groq API responded (status: " . $resp->getStatusCode() . "):\n";
    echo json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    exit(0);
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo "Request failed: " . $e->getMessage() . "\n";
    if ($e->hasResponse()) {
        echo (string) $e->getResponse()->getBody() . "\n";
    }
    exit(2);
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(3);
}
