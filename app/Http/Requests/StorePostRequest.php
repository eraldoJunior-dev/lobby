<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:2000'],
            'game_id' => ['nullable', 'required_with:rating', 'integer', 'exists:games,id'],
            'rating' => ['nullable', 'numeric', 'between:0.5,5', 'multiple_of:0.5'],
            'contains_spoilers' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'body.required' => __('Escreva alguma coisa antes de publicar.'),
            'body.max' => __('A publicação pode ter no máximo 2000 caracteres.'),
            'game_id.required_with' => __('Escolha o jogo para dar uma nota.'),
            'game_id.exists' => __('Esse jogo não existe.'),
            'rating.between' => __('A nota vai de 0,5 a 5 estrelas, em intervalos de meia estrela.'),
        ];
    }
}
