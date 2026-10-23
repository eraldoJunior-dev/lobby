<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Closure;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cpf' => ['required', 'digits:11', 'unique:users,cpf', function (string $attribute, mixed $value, Closure $fail): void {
                if (! self::validCpf((string) $value)) {
                    $fail('Informe um CPF válido.');
                }
            }],
            'birth_date' => ['required', 'date_format:d/m/Y', function (string $attribute, mixed $value, Closure $fail): void {
                $date = \DateTimeImmutable::createFromFormat('!d/m/Y', (string) $value);
                if (! $date || $date->format('d/m/Y') !== $value || $date > new \DateTimeImmutable('today')) {
                    $fail('Informe uma data de nascimento válida.');
                }
            }],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Informe seu nome.'),
            'name.max' => __('O nome pode ter no máximo 255 caracteres.'),
            'email.required' => __('Informe seu e-mail.'),
            'email.email' => __('Informe um e-mail válido.'),
            'email.max' => __('O e-mail pode ter no máximo 255 caracteres.'),
            'email.unique' => __('Este e-mail já está cadastrado.'),
            'password.required' => __('Crie uma senha.'),
            'password.min' => __('A senha precisa ter pelo menos 8 caracteres.'),
            'password.confirmed' => __('A confirmação da senha não confere.'),
            'cpf.required' => 'Informe seu CPF.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'birth_date.required' => 'Informe sua data de nascimento.',
            'birth_date.date_format' => 'Use o formato DD/MM/AAAA.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        if (is_array($data) && isset($data['birth_date'])) {
            $data['birth_date'] = \DateTimeImmutable::createFromFormat('!d/m/Y', $data['birth_date'])->format('Y-m-d');
        }
        return $data;
    }

    private static function validCpf(string $cpf): bool
    {
        if (! preg_match('/^\d{11}$/', $cpf) || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        for ($length = 9; $length <= 10; $length++) {
            $sum = 0;
            for ($i = 0; $i < $length; $i++) {
                $sum += (int) $cpf[$i] * ($length + 1 - $i);
            }
            $digit = ($sum * 10) % 11;
            if ($digit === 10) $digit = 0;
            if ((int) $cpf[$length] !== $digit) return false;
        }
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('cpf'))) {
            $this->merge(['cpf' => preg_replace('/\D/', '', $this->input('cpf'))]);
        }
        if (is_string($this->email)) {
            $this->merge(['email' => mb_strtolower(trim($this->email))]);
        }
    }
}
