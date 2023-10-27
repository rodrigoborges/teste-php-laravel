<?php

namespace App\Http\Controllers;

use App\Jobs\DocumetImportJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportDocumentController extends Controller
{
    private const DOCUMENT_FILE  = '2023-03-28.json';

    public function import(Request $request) : JsonResponse
    {
        if (!Storage::disk('import')->exists(self::DOCUMENT_FILE)) {
            return response()->json(['message' => 'Arquivo para importação não encontrado.'], 404);
        }

        $jsonData = Storage::disk('import')->get(self::DOCUMENT_FILE);

        $data = json_decode($jsonData, true);

        return $this->processDocuments($data);
    }

    private function processDocuments(array $data) : JsonResponse
    {
        if (!isset($data['documentos']) || empty($data['documentos'])) {
            return response()->json(['message' => 'Arquivo para importação não possui documentos.'], 404);
        }

        foreach ($data['documentos'] as $document) {
            DocumetImportJob::dispatch($document)->onQueue('process-documents');
        }

        return response()->json(['message' => 'Importação iniciada com sucesso.']);
    }
}
