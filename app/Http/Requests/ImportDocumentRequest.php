<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
           'json_file' => ['required', 'file', 'mimetypes:application/json'],
        ];
    }

    public function messages()
    {
        return [
            'json_file.required' => 'O arquivo JSON é obrigatório.',
            'json_file.mimetypes' => 'O arquivo deve ser do tipo JSON.',
        ];
    }
}
