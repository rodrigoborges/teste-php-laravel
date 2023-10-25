<?php

namespace Database\Seeders;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

// Criei a variável abaixo para armazenar os dados que serão inseridos na tabela categories.
        $categoriesData = [
            ['name' => 'Remessa Parcial'],
            ['name' => 'Remessa'],
        ];

// Priorizo a utilização do Eloquent Model (Category) para interagir com a tabela associada no banco de dados.
// O insert é mais performático que o create, pois não dispara os eventos do Eloquent Model, ou seja, o created_at e o
// updated_at não são preenchidos automaticamente.
// Poderia ter adicionado o created_at e o updated_at no array $categoriesData, mas preferi utiliizar o array_map para
// adicionar os campos created_at e updated_at no array $categoriesData, dessa forma, fica mais fácil acrescenter mais
// dados no futuro.
        Category::insert(array_map(function ($category) use ($now) {
            return array_merge($category, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $categoriesData));
    }
}
