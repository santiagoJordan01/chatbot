<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Groq API configuration
    |--------------------------------------------------------------------------
    |
    | Configure the Groq API base URL, API key and default model here.
    | Keep secrets in your environment (`.env`).
    |
    */

    'base_url' => env('GROQ_BASE_URL', 'https://api.groq.ai'),
    'endpoint' => env('GROQ_ENDPOINT', '/v1/generate'),
    'api_key' => env('GROQ_API_KEY', ''),
    'model' => env('GROQ_MODEL', 'llama-3.3-70b'),
    'timeout' => env('GROQ_TIMEOUT', 60),
];
