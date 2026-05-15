<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\EmbeddingService;
use App\Models\Embedding;

class EmbeddingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_from_text_and_find_nearest()
    {
        Http::fake([
            '*' => Http::response(['data' => [['embedding' => [0.1, 0.2, 0.3]]]], 200),
        ]);

        $svc = new EmbeddingService();

        $emb = $svc->createFromText('doc', '1', 'Hello world', ['text' => 'Hello world']);

        $this->assertDatabaseCount('embeddings', 1);
        $this->assertEquals('1', $emb->source_id);

        $nearest = $svc->findNearestByVector([0.1, 0.2, 0.3], 1);
        $this->assertCount(1, $nearest);
        $this->assertGreaterThanOrEqual(0, $nearest[0]['score']);
    }
}
