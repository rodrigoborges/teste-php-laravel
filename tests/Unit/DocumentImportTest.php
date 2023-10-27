<?php

namespace Tests\Unit;

use App\Http\Controllers\ImportDocumentController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DocumentImportTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function check_max_lenght_field_contents_is_correct(): void
    {
        $data = ['documentos' => [
            [
                'categoria' => $this->faker->randomElement(['Remessa', 'Remessa Parcial']),
                'titulo' => $this->faker->sentence,
                'conteúdo' => $this->faker->realText(2100)
            ]
        ]];

        // Caminho para o arquivo JSON temporário
        $jsonFilePath = storage_path('app/testing/temporary.json');

        // Cria o arquivo JSON temporário
        file_put_contents($jsonFilePath, json_encode($data));

        // Simula o envio do arquivo JSON
        $response = $this->post(route('document.import'), [
            'json_file' => new UploadedFile($jsonFilePath,
                'temporary.json',
                'application/json',
                null,
                true)
        ]);

        // Limpa o arquivo JSON temporário após o teste
        unlink($jsonFilePath);

        $this->assertStringContainsString('The contents field must not be greater than 2000 characters.',
            $response->exception->getMessage());
    }

    /** @test */
    public function check_contents_documents_from_category_is_valid(): void
    {

        $magicWords = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro', 'semestre'];

        $data = ['documentos' => [
            [
                'categoria' => $this->faker->randomElement(['Remessa', 'Remessa Parcial']),
                'titulo' => $this->faker->sentence . ' ' . $this->faker->randomElement($magicWords),
                'conteúdo' => $this->faker->text(1000)
            ]
        ]];

        // Caminho para o arquivo JSON temporário
        $jsonFilePath = storage_path('app/testing/temporary.json');

        // Cria o arquivo JSON temporário
        file_put_contents($jsonFilePath, json_encode($data));

        // Simula o envio do arquivo JSON
        $response = $this->post(route('document.import'), [
            'json_file' => new UploadedFile($jsonFilePath,
                'temporary.json',
                'application/json',
                null,
                true)
        ]);

        // Limpa o arquivo JSON temporário após o teste
        unlink($jsonFilePath);

        if ($response->exception instanceof ValidationException)
            $this->assertEquals('Registro inválido.', $response->exception->getMessage());
        else
            $this->fail('ValidationException was not thrown as expected. ' . var_export($data, true));

    }
}
