<?php

namespace Tests\Feature;

use App\Enums\CategoryEnum;
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

    const TABLE_NAME = 'documents';

    const TABLE_CATEGORY_NAME = 'categories';

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setlocale('pt_BR');
    }

    /** @test */
    public function check_max_length_field_contents_is_invalid(): void
    {
        $data = $this->createDataStructure(
            CategoryEnum::REMESSA,
            $this->faker->sentence . '- semestre',
            $this->faker->realText(2100)
        );

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['contents']);

        $this->assertStringContainsString('The contents field must not be greater than 2000 characters.',
            $response->exception->getMessage());

        $this->assertDatabaseMissing(self::TABLE_NAME, [
            'title' => $data['documentos'][0]['titulo'],
            'contents' => $data['documentos'][0]['conteúdo'],
        ]);

        $this->assertDatabaseMissing(self::TABLE_CATEGORY_NAME, [
            'name' => $data['documentos'][0]['categoria'],
        ]);
    }

    /** @test */
    public function check_max_length_field_contents_is_valid(): void
    {
        $data = $this->createDataStructure(
            CategoryEnum::REMESSA,
            $this->faker->sentence . '- semestre',
            $this->faker->realText(1000)
        );

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertOk();

        $this->assertDatabaseHas(self::TABLE_NAME, [
            'title' => $data['documentos'][0]['titulo'],
            'contents' => $data['documentos'][0]['conteúdo'],
        ]);

        $this->assertDatabaseHas(self::TABLE_CATEGORY_NAME, [
            'name' => $data['documentos'][0]['categoria'],
        ]);
    }

    /** @test */
    public function check_contents_documents_from_category_remessa_is_valid(): void
    {
        $data = $this->createDataStructure(
            CategoryEnum::REMESSA,
            $this->faker->sentence . ' - semestre',
            $this->faker->text(1000)
        );

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertOk();

        $this->assertDatabaseHas(self::TABLE_NAME, [
            'title' => $data['documentos'][0]['titulo'],
            'contents' => $data['documentos'][0]['conteúdo'],
        ]);

        $this->assertDatabaseHas(self::TABLE_CATEGORY_NAME, [
            'name' => $data['documentos'][0]['categoria'],
        ]);
    }

    /** @test */
    public function check_contents_documents_from_category_remessa_is_invalid(): void
    {
        $data = $this->createDataStructure(
            CategoryEnum::REMESSA,
            $this->faker->sentence,
            $this->faker->text(1000)
        );

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertEquals('Registro inválido.', $response->exception->getMessage());

        $this->assertDatabaseMissing(self::TABLE_NAME, [
            'title' => $data['documentos'][0]['titulo'],
            'contents' => $data['documentos'][0]['conteúdo'],
        ]);

        $this->assertDatabaseMissing(self::TABLE_CATEGORY_NAME, [
            'name' => $data['documentos'][0]['categoria'],
        ]);
    }

    /** @test */
    public function check_contents_documents_from_category_remessa_parcial_is_valid(): void
    {
        $data = $this->createDataStructure(
            CategoryEnum::REMESSA_PARCIAL,
            $this->faker->sentence . ' - ' . Str::title(Carbon::now()->translatedFormat('F')),
            $this->faker->text(1000)
        );

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertOk();

        $this->assertDatabaseHas(self::TABLE_NAME, [
            'title' => $data['documentos'][0]['titulo'],
            'contents' => $data['documentos'][0]['conteúdo'],
        ]);

        $this->assertDatabaseHas(self::TABLE_CATEGORY_NAME, [
            'name' => $data['documentos'][0]['categoria'],
        ]);
    }

    /** @test */
    public function check_contents_documents_from_category_remessa_parcial_is_invalid(): void
    {
        $data = $this->createDataStructure(
            CategoryEnum::REMESSA_PARCIAL,
            $this->faker->sentence,
            $this->faker->text(1000)
        );

        $this->mockUploadDocumentJson($data);

        $response = $this->postJson(route('document.import'), [
            'json_file' => $this->getFakeJsonFile()
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertEquals('Registro inválido.', $response->exception->getMessage());

        $this->assertDatabaseMissing(self::TABLE_NAME, [
            'title' => $data['documentos'][0]['titulo'],
            'contents' => $data['documentos'][0]['conteúdo'],
        ]);

        $this->assertDatabaseMissing(self::TABLE_CATEGORY_NAME, [
            'name' => $data['documentos'][0]['categoria'],
        ]);
    }

    private function getFakeJsonFile($fileName = 'test_document'): UploadedFile
    {
        Storage::fake($fileName);
        return UploadedFile::fake()->create($fileName . '.json', 1000, 'application/json');
    }

    private function mockUploadDocumentJson(array $data): void
    {
        $this->partialMock(DocumentManager::class, function (MockInterface $mock) use ($data) {
            $mock->shouldReceive('getJsonContent')
                ->andReturn($data);
        });
    }

    private function createDataStructure(CategoryEnum $category, string $title, string $content): array
    {
        return ['documentos' => [
            [
                'categoria' => $category->value,
                'titulo' => $title,
                'conteúdo' => $content,
            ]
        ]];
    }
}
