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
        if (!Schema::hasTable('documents')) {
            Schema::create('documents' , function (Blueprint $table) {
                $table->id();
                $table->string('title' , 60);
                $table->text('contents');
                $table->bigInteger('category_id')->unsigned(); // Adicionei o 'unsigned' para garantir que seja sempre positivo;
                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('documents');
    }
};
