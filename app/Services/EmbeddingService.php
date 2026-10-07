<?php

namespace App\Services;

use App\Models\Embedding;

class EmbeddingService
{
    protected GroqClient $groq;

    public function __construct()
    {
        $this->groq = new GroqClient();
    }

    public function createFromText(string $sourceType, string $sourceId, string $text, array $metadata = []): Embedding
    {
        $resp = $this->groq->embeddings('search_document: '.$text);
        $vector = $this->extractVector($resp);

        return Embedding::create([
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'embedding' => $vector,
            'metadata' => $metadata,
        ]);
    }

    public function findNearestByVector(array $vector, int $k = 5): array
    {
        $rows = Embedding::all();
        $scores = [];

        foreach ($rows as $row) {
            $other = $row->embedding ?? [];
            if (!is_array($other) || count($other) === 0) continue;
            $scores[] = [
                'row' => $row,
                'score' => $this->cosineSimilarity($vector, $other),
            ];
        }

        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($scores, 0, $k);
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

    protected function cosineSimilarity(array $a, array $b): float
    {
        $len = min(count($a), count($b));
        if ($len === 0) return 0.0;

        $dot = 0.0;
        $na = 0.0;
        $nb = 0.0;

        for ($i = 0; $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $na += $a[$i] * $a[$i];
            $nb += $b[$i] * $b[$i];
        }

        if ($na <= 0 || $nb <= 0) return 0.0;

        return $dot / (sqrt($na) * sqrt($nb));
    }
}
