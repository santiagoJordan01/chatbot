<?php

namespace App\Services;

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
     */
    public function answer(string $query, int $k = 3): array
    {
        $resp = $this->groq->embeddings($query);
        $vector = $this->extractVector($resp);

        if (empty($vector)) {
            // fallback to direct generation
            return $this->groq->chat($query);
        }

        $nearest = $this->embeddings->findNearestByVector($vector, $k);

        $contexts = [];
        foreach ($nearest as $item) {
            $row = $item['row'];
            $meta = $row->metadata ?? [];
            if (is_array($meta) && isset($meta['text'])) {
                $contexts[] = $meta['text'];
            } else {
                $contexts[] = ($meta['title'] ?? $row->source_id ?? $row->id);
            }
        }

        $prompt = "Use the following context to answer the question:\n" . implode("\n---\n", $contexts) . "\n\nQuestion: " . $query;

        return $this->groq->chat($prompt);
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
