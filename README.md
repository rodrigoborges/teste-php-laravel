![Logo AI Solutions](http://aisolutions.tec.br/wp-content/uploads/sites/2/2019/04/logo.png)

# AI Solutions

## Teste para novos candidatos (PHP/Laravel)

### Introdução

Este teste utiliza PHP 8.1, Laravel 10 e um banco de dados SQLite simples.

1. Faça o clone desse repositório;
1. Execute o `composer install`;
1. Crie e ajuste o `.env` conforme necessário
1. Execute as migrations e os seeders;

### Primeira Tarefa:

Crítica das Migrations e Seeders: Aponte problemas, se houver, e solucione; Implemente melhorias;

### Segunda Tarefa:

Crie a estrutura completa de uma tela que permita adicionar a importação do arquivo `storage/data/2023-03-28.json`, para a tabela `documents`. onde cada registro representado neste arquivo seja adicionado a uma fila para importação.

Feito isso crie uma tela com um botão simples que dispara o processamento desta fila.

Utilize os padrões que preferir para as tarefas.

### Terceira Tarefa:

Crie um test unitário que valide o tamanho máximo do campo conteúdo.

Crie um test unitário que valide a seguinte regra:

Se a categoria for "Remessa" o título do registro deve conter a palavra "semestre", caso contrário deve emitir um erro de registro inválido.
Se a caterogia for "Remessa Parcial", o titulo deve conter o nome de um mês(Janeiro, Fevereiro, etc), caso contrário deve emitir um erro de registro inválido.


Boa sorte!

### Instruções para validar o teste:

1. Após criar o .env.example, rodar o comando `php artisan key:generate` para gerar a chave da aplicação.
2. Caso opte por utilizar o QUEUE_CONNECTION como database, bastar rodar o comando `php artisan queue:work --queue=process-documents` para processar a fila.
3. Para rodar os testes isolados, basta rodar os comandos:
   1. `php artisan test --filter=check_max_length_field_contents_is_invalid`
   2. `php artisan test --filter=check_max_length_field_contents_is_valid`
   3. `php artisan test --filter=check_contents_documents_from_category_remessa_is_valid`
   4. `php artisan test --filter=check_contents_documents_from_category_remessa_is_invalid`
   5. `php artisan test --filter=check_contents_documents_from_category_remessa_parcial_is_valid`
   6. `php artisan test --filter=check_contents_documents_from_category_remessa_parcial_is_invalid`
6. A rota para acessar a tela de importação é a `/document/import` 
7. Para atender a teste unitário que valida o tamanho máximo do campo conteúdo, foi utilizado o limite de **2000** caracteres.
8. **O arquivo fornecido para importação não está no critério de validação do teste, ao ser importado pela aplicação o processamento irá informar Registro Inválido.**

