<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /**
     * Executa a regra de validação.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('A verificação anti-robô é obrigatória.');
            return;
        }

        // Faz a chamada POST interna para a API de verificação do Google
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret') ?? env('RECAPTCHA_SECRET_KEY'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $resultado = $response->json();

        // Se o Google responder que o token é inválido ou falso, a validação falha
        if (!$response->successful() || !($resultado['success'] ?? false)) {
            $fail('Falha na verificação do reCAPTCHA. Tente novamente.');
        }
    }
}
