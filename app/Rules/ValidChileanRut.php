<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidChileanRut implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return false;
        }

        // Limpiar el RUT: quitar puntos, espacios y convertir a mayúsculas
        $rut = strtoupper(str_replace(['.', ' ', '-'], '', $value));
        
        // Verificar formato básico: debe tener al menos 8 caracteres (7 números + 1 dígito verificador)
        if (strlen($rut) < 8 || strlen($rut) > 9) {
            return false;
        }

        // Separar número y dígito verificador
        $number = substr($rut, 0, -1);
        $verifier = substr($rut, -1);

        // Verificar que el número sea numérico
        if (!is_numeric($number)) {
            return false;
        }

        // Calcular dígito verificador
        $sum = 0;
        $multiplier = 2;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $sum += $number[$i] * $multiplier;
            $multiplier = $multiplier == 7 ? 2 : $multiplier + 1;
        }

        $calculatedVerifier = 11 - ($sum % 11);
        
        if ($calculatedVerifier == 11) {
            $calculatedVerifier = '0';
        } elseif ($calculatedVerifier == 10) {
            $calculatedVerifier = 'K';
        } else {
            $calculatedVerifier = (string) $calculatedVerifier;
        }

        return $verifier === $calculatedVerifier;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El RUT ingresado no es válido. Debe ser un RUT chileno válido (ej: 12345678-9).';
    }
}
