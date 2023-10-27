<?php

namespace App\Rules\Documents;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class CheckTitleFromCategoryRule implements ValidationRule
{
    private $category;

    public function __construct(string $category)
    {
        $this->category = $category;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->category === 'Remessa') {
            if (!Str::contains($value, 'semestre')) {
                $fail($this->message());
            }
        }

        if ($this->category === 'Remessa Parcial') {

            if (!Str::contains($value, [
                'Janeiro',
                'Fevereiro',
                'Março',
                'Abril',
                'Maio',
                'Junho',
                'Julho',
                'Agosto',
                'Setembro',
                'Outubro',
                'Novembro',
                'Dezembro',
            ])) {
                $fail($this->message());
            }
        }
    }
    public function message()
    {
        return 'Registro inválido.';
    }
}
