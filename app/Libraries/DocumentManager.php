<?php

namespace App\Libraries;

use App\Jobs\DocumetImportJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DocumentManager
{
    public function processDocuments(array $data) : JsonResponse
    {
        if (!isset($data['documentos']) || empty($data['documentos'])) {
            return response()->json(['message' => 'Arquivo para importação não possui documentos.'], Response::HTTP_NOT_FOUND);
        }

        foreach ($data['documentos'] as $document) {
            DocumetImportJob::dispatch($document)->onQueue('process-documents');
        }

        return response()->json(['message' => 'Importação iniciada com sucesso.']);
    }

}
