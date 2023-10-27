<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportDocumentRequest;
use App\Libraries\DocumentManager;
use Illuminate\Support\Facades\File;
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

        $jsonData = File::get($jsonFile->path());

        $data = json_decode($jsonData, true);

        return $this->documentManager->processDocuments($data);
    }
}
