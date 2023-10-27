<?php

namespace App\Libraries;

use App\Jobs\DocumetImportJob;
use App\Models\Category;
use App\Models\Document;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

class DocumentManager
{
    public function processDocuments(array $data) : bool
    {
        if (!isset($data['documentos']) || empty($data['documentos'])) {
            return false;
        }

        foreach ($data['documentos'] as $document) {
            DocumetImportJob::dispatch($document)->onQueue('process-documents');
        }

        return true;
    }

    public function getJsonContent(UploadedFile $jsonFile) : array
    {
        $jsonData = File::get($jsonFile->path());

        $data = json_decode($jsonData, true);

        return $data;
    }

    public function createDocument(string $nameCategory, array $dataDocument) : Document
    {
        // Tente encontrar a categoria existente ou crie uma nova
        $category = Category::firstOrCreate(['name' => $nameCategory]);

        return $category->documents()->create($dataDocument);
    }


}
