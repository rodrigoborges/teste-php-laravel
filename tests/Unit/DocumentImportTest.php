<?php

namespace Tests\Unit;

use App\Http\Controllers\ImportDocumentController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $controller = new ImportDocumentController();

        $method = new \ReflectionMethod($controller, 'processDocuments');
        $method->setAccessible(true);

        try {
            $method->invoke($controller, $data);
        } catch (ValidationException $e) {
            $this->assertStringContainsString('The contents field must not be greater than 2000 characters.', $e->getMessage());
            return;
        }

        $this->fail('ValidationException was not thrown as expected.');
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

        $controller = new ImportDocumentController();

        $method = new \ReflectionMethod($controller, 'processDocuments');
        $method->setAccessible(true);

        try {
            $method->invoke($controller, $data);
        } catch (ValidationException $e) {
            $this->assertEquals('Registro inválido.', $e->getMessage());
            return;
        }

        $this->fail('ValidationException was not thrown as expected. ' . var_export($data, true));
    }
}
