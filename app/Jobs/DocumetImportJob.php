<?php

namespace App\Jobs;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        try {
            $dataDocument = [
                'title' => $this->document['titulo'],
                'contents' => $this->document['conteúdo'],
            ];

            $validator = Validator::make($dataDocument, [
                'title' => 'required',
                'contents' => 'required',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $nameCategory = $this->document['categoria'];

            // Tente encontrar a categoria existente ou crie uma nova
            $category = Category::firstOrNew(['name' => $nameCategory]);

            if (!$category->exists) {
                $category->save();
            }

            $category->documents()->create($dataDocument);

        } catch (ValidationException $e) {
            Log::channel('import_document')->error('[DOCUMENT_IMPORT]ValidationException: ' . $e->getMessage());
        } catch (QueryException $e) {
            Log::channel('import_document')->error('[DOCUMENT_IMPORT]QueryException: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::channel('import_document')->error('[DOCUMENT_IMPORT]Exception: ' . $e->getMessage());
        }
    }
}
