<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportDocumentRequest;
use App\Libraries\DocumentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ImportDocumentController extends Controller
{
    private DocumentManager $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function store(ImportDocumentRequest $request) : JsonResponse
    {
        $jsonFile = $request->file('json_file');

        if (!$jsonFile->isValid()) {
            return response()->json(['message' => 'Arquivo inválido.'], Response::HTTP_BAD_REQUEST);
        }

        $data = $this->documentManager->getJsonContent($jsonFile);

        $statusProcess = $this->documentManager->processDocuments($data);

        if (!$statusProcess) return response()->json(['message' => 'Arquivo para importação não possui documentos.'], Response::HTTP_NOT_FOUND);

        return response()->json(['message' => 'Importação iniciada com sucesso.']);
    }
}
