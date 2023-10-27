<?php

namespace Tests\Unit;

use App\Libraries\DocumentManager;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class DocumentImportTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setlocale('pt_BR');
    }

    /** @test */
    public function check_max_length_field_contents_is_invalid(): void
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
        $response = $this->postJson(route('document.import'), [
            'json_file' => new UploadedFile($jsonFilePath,
                'temporary.json',
                'application/json',
                null,
                true)
        ]);

        // Limpa o arquivo JSON temporário após o teste
        unlink($jsonFilePath);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['contents']);

        $this->assertStringContainsString('The contents field must not be greater than 2000 characters.',
            $response->exception->getMessage());
    }

    /** @test */
    public function check_max_length_field_contents_is_valid(): void
    {
        $data = ['documentos' => [
            [
                'categoria' => 'Remessa',
                'titulo' => $this->faker->sentence . '- semestre',
                'conteúdo' => $this->faker->realText(1000),
            ]
        ]];

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertOk();
    }

    /** @test */
    public function check_contents_documents_from_category_remessa_is_valid(): void
    {
        $data = ['documentos' => [
            [
                'categoria' => 'Remessa',
                'titulo' => $this->faker->sentence . ' - semestre',
                'conteúdo' => $this->faker->text(1000),
            ]
        ]];

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertOk();
    }

    /** @test */
    public function check_contents_documents_from_category_remessa_is_invalid(): void
    {
        $data = ['documentos' => [
            [
                'categoria' => 'Remessa',
                'titulo' => $this->faker->sentence,
                'conteúdo' => $this->faker->text(1000),
            ]
        ]];

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertEquals('Registro inválido.', $response->exception->getMessage());
    }

    /** @test */
    public function check_contents_documents_from_category_remessa_parcial_is_valid(): void
    {
        $data = ['documentos' => [
            [
                'categoria' => 'Remessa Parcial',
                'titulo' => $this->faker->sentence . ' - ' . Str::title(Carbon::now()->translatedFormat('F')),
                'conteúdo' => $this->faker->text(1000),
            ]
        ]];

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertOk();
    }

    /** @test */
    public function check_contents_documents_from_category_remessa_parcial_is_invalid(): void
    {
        $data = ['documentos' => [
            [
                'categoria' => 'Remessa Parcial',
                'titulo' => $this->faker->sentence,
                'conteúdo' => $this->faker->text(1000),
            ]
        ]];

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertEquals('Registro inválido.', $response->exception->getMessage());
    }

    private function getFakeJsonFile($fileName = 'test_document') : UploadedFile
    {
        Storage::fake($fileName);
        return UploadedFile::fake()->create($fileName . '.json', 1000, 'application/json');
    }

    private function mockUploadDocumentJson(array $data) : void
    {
        $this->partialMock(DocumentManager::class, function (MockInterface $mock) use ($data) {
            $mock->shouldReceive('getJsonContent')
                ->andReturn($data);
        });
    }
}
