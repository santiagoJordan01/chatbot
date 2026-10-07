<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class RAGService
{
    protected GroqClient $groq;
    protected EmbeddingService $embeddings;

    public function __construct()
    {
        $this->groq = new GroqClient();
        $this->embeddings = new EmbeddingService();
    }

    /**
     * Answer a query using retrieval-augmented generation.
     *
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function answer(string $query, int $k = 3, array $history = []): array
    {
        $contexts = [];

        try {
            $resp = $this->groq->embeddings('search_query: '.$query);
            $vector = $this->extractVector($resp);

            if ($vector !== []) {
                foreach ($this->embeddings->findNearestByVector($vector, $k) as $item) {
                    $meta = $item['row']->metadata ?? [];
                    if (is_array($meta) && ! empty($meta['text'])) {
                        $contexts[] = $meta['text'];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo recuperar contexto de la clínica', [
                'error' => $e->getMessage(),
            ]);
        }

        $contextBlock = $contexts === []
            ? 'No hay información relevante cargada.'
            : implode("\n---\n", $contexts);

        $messages = array_merge(
            [['role' => 'system', 'content' => (string) config('groq.system_prompt')]],
            $history,
            [[
                'role' => 'user',
                'content' => "Contexto de la clínica:\n{$contextBlock}\n\nReglas: no asumas que el turno es una limpieza. De 10 a 25 kg, incluido 25 kg, la limpieza cuesta 240.000 pesos. No digas que una hora está libre. El ayuno de 8 horas no es una hora de llegada.\n\nPregunta: {$query}",
            ]],
        );

        return $this->groq->chat($query, ['messages' => $messages]);
    }

    protected function extractVector(array $resp): array
    {
        if (isset($resp['embedding']) && is_array($resp['embedding'])) {
            return $resp['embedding'];
        }

        if (isset($resp['data']) && is_array($resp['data'])) {
            $first = $resp['data'][0] ?? null;
            if (is_array($first) && isset($first['embedding'])) {
                return $first['embedding'];
            }
            if (is_array($first) && isset($first['vector'])) {
                return $first['vector'];
            }
        }

        if (isset($resp['vectors']) && is_array($resp['vectors'])) {
            return $resp['vectors'];
        }

        return [];
    }
}
