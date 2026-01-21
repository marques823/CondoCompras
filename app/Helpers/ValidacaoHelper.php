<?php

namespace App\Helpers;

class ValidacaoHelper
{
    /**
     * Valida CPF
     */
    public static function validarCPF(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Validação dos dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Valida CNPJ
     */
    public static function validarCNPJ(string $cnpj): bool
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        
        // Validação dos dígitos verificadores
        $tamanho = strlen($cnpj) - 2;
        $numeros = substr($cnpj, 0, $tamanho);
        $digitos = substr($cnpj, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }
        
        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
        if ($resultado != $digitos[0]) {
            return false;
        }
        
        $tamanho = $tamanho + 1;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }
        
        $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
        if ($resultado != $digitos[1]) {
            return false;
        }
        
        return true;
    }

    /**
     * Valida CPF ou CNPJ
     */
    public static function validarCPFouCNPJ(string $documento): bool
    {
        $documento = preg_replace('/\D/', '', $documento);
        
        if (strlen($documento) == 11) {
            return self::validarCPF($documento);
        } elseif (strlen($documento) == 14) {
            return self::validarCNPJ($documento);
        }
        
        return false;
    }

    /**
     * Identifica se é CPF ou CNPJ
     */
    public static function identificarTipo(string $documento): ?string
    {
        $documento = preg_replace('/\D/', '', $documento);
        
        if (strlen($documento) == 11) {
            return 'cpf';
        } elseif (strlen($documento) == 14) {
            return 'cnpj';
        }
        
        return null;
    }
}
