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

    'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
    'endpoint' => env('GROQ_ENDPOINT', '/chat/completions'),
    'api_key' => env('GROQ_API_KEY', ''),
    'model' => env('GROQ_MODEL', 'openai/gpt-oss-20b'),
    'timeout' => env('GROQ_TIMEOUT', 60),
    'system_prompt' => 'Eres el asistente de Pulse, clínica veterinaria. Responde en español, en texto plano y en pocas frases, sin asteriscos. Usa solo el contexto de la clínica. No inventes cifras. No digas que una hora está libre ni que la cita quedó reservada: el equipo confirma el cupo. Si piden un turno y no dicen el servicio, pregunta cuál es. Consulta, estética y control van de lunes a viernes de 8:00 a 18:00 y sábados de 8:00 a 13:00. La limpieza dental solo va de lunes a viernes de 8:00 a 14:00. De 10 a 25 kg, incluido 25 kg, la limpieza cuesta 240.000 pesos. Más de 25 kg cuesta 320.000. El ayuno de 8 horas es no comer antes de la cita, no una hora de llegada.',
];
