<?php

namespace Database\Seeders;

use App\Enums\CategoryEnum;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criei a variável abaixo para armazenar os dados que serão inseridos na tabela categories.
        // Para não utiliar código hardcode, utilizei a classe CategoryEnum para obter os valores das categorias.
        $categoriesData = [
            ['name' => CategoryEnum::REMESSA->value],
            ['name' => CategoryEnum::REMESSA_PARCIAL->value],
        ];

        // Para manter a unicidade das categorias no banco de dados, utilizei o método updateOrCreate do Eloquent.
        foreach ($categoriesData as $category) {
            Category::updateOrCreate($category);
        }
    }
}
