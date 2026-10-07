<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Local embeddings (Ollama)
    |--------------------------------------------------------------------------
    |
    | Chat stays on Groq. Vectors are generated locally with nomic-embed-text
    | so clinic documents can be retrieved without a paid embeddings API.
    |
    */

    'base_url' => env('EMBEDDING_BASE_URL', 'http://127.0.0.1:11434/v1'),
    'model' => env('EMBEDDING_MODEL', 'nomic-embed-text'),
    'api_key' => env('EMBEDDING_API_KEY', 'ollama'),
    'timeout' => env('EMBEDDING_TIMEOUT', 60),
];
