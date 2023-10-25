<?php

namespace App\Jobs;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DocumetImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $document;

    /**
     * Create a new job instance.
     */
    public function __construct(array $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $nameCategory = $this->document['categoria'];
        $category = Category::firstOrNew(['name' => $nameCategory]);

        if (!$category->exists) {
            $category->save();
        }

        $dataDocument = [
            'title' => $this->document['titulo'],
            'contents' => $this->document['conteúdo'],
        ];

        $category->documents()->create($dataDocument);
    }
}
