<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
// A tabela categories já existe na base e por esse motivo ao executar a migration ocorre o erro.
// Para resolver o problema, basta verificar se a tabela existe antes de criar a mesma.
// No caso de uma nova instalação, a tabela não existirá e a migration será executada.
// Caso a tabela já exista, a migration não será executada.
        if (!Schema::hasTable('categories')) {
            Schema::create('categories' , function (Blueprint $table) {
                $table->id();
// Com base no arquivo a ser importado, sugiro que o campo name seja único.
                $table->string('name' , 20)->unique();
                $table->timestamps();
// Alterei a posição do timestamp para que os campos created_at e updated_at fossem criados no final da tabela.
// Essa é uma prática comum nos projetos Laravel, tanto nos quais já trabalhei quanto naqueles que acompanho na comunidade.
// Os campos created_at e updated_at são informações relacionadas à auditoria dos registros e não diretamente relacionadas
// aos dados principais da tabela.
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
