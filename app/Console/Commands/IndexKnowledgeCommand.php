<?php

namespace App\Console\Commands;

use App\Models\Embedding;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;

class IndexKnowledgeCommand extends Command
{
    protected $signature = 'knowledge:index';

    protected $description = 'Index clinic documents with the local embedding model';

    public function handle(EmbeddingService $embeddings): int
    {
        $docs = require database_path('knowledge/clinic.php');

        Embedding::query()->where('source_type', 'clinic')->delete();

        foreach ($docs as $doc) {
            $embeddings->createFromText('clinic', $doc['id'], $doc['text'], [
                'title' => $doc['title'],
                'text' => $doc['text'],
            ]);
            $this->line('Indexed '.$doc['id']);
        }

        $this->info('Indexed '.count($docs).' clinic documents.');

        return self::SUCCESS;
    }
}
