<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    /**
     * Busca dados do CNPJ
     */
    public function buscarCNPJ(Request $request)
    {
        try {
            $request->validate([
                'cnpj' => 'required|string|min:10|max:18',
            ]);

            $cnpj = preg_replace('/\D/', '', $request->cnpj);

            if (strlen($cnpj) !== 14) {
                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ inválido. Deve conter 14 dígitos.'
                ], 400);
            }

            // Validação básica do CNPJ (opcional - pode comentar se quiser buscar mesmo CNPJs inválidos)
            // if (!$this->validarCNPJ($cnpj)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'CNPJ inválido.'
            //     ], 400);
            // }

        // Tenta buscar do cache primeiro (cache de 1 hora)
        $cacheKey = 'cnpj_' . $cnpj;
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            return response()->json([
                'success' => true,
                'data' => $cached
            ]);
        }

        try {
            // Tenta a API da ReceitaWS primeiro
            $response = Http::timeout(10)->get("https://www.receitaws.com.br/v1/{$cnpj}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Verifica se há erro na resposta
                if (isset($data['status']) && $data['status'] === 'ERROR') {
                    // Tenta API alternativa (BrasilAPI)
                    return $this->buscarCNPJBrasilAPI($cnpj);
                }
                
                // Verifica se tem dados válidos
                if (empty($data['nome']) && empty($data['fantasia'])) {
                    // Tenta API alternativa
                    return $this->buscarCNPJBrasilAPI($cnpj);
                }
                
                // Formata os dados
                $formatted = [
                    'nome' => $data['fantasia'] ?? $data['nome'] ?? '',
                    'razao_social' => $data['nome'] ?? $data['fantasia'] ?? '',
                    'cnpj' => preg_replace('/\D/', '', $data['cnpj'] ?? $cnpj),
                    'logradouro' => $data['logradouro'] ?? '',
                    'numero' => $data['numero'] ?? '',
                    'complemento' => $data['complemento'] ?? '',
                    'bairro' => $data['bairro'] ?? '',
                    'municipio' => $data['municipio'] ?? '',
                    'uf' => $data['uf'] ?? '',
                    'cep' => preg_replace('/\D/', '', $data['cep'] ?? ''),
                    'telefone' => $data['telefone'] ?? '',
                    'email' => $data['email'] ?? '',
                ];
                
                // Salva no cache
                Cache::put($cacheKey, $formatted, now()->addHours(1));
                
                return response()->json([
                    'success' => true,
                    'data' => $formatted
                ]);
            } else {
                // Se não foi bem-sucedido, tenta BrasilAPI
                return $this->buscarCNPJBrasilAPI($cnpj);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar CNPJ na ReceitaWS: ' . $e->getMessage());
            // Se falhar, tenta BrasilAPI
            return $this->buscarCNPJBrasilAPI($cnpj);
        }
        } catch (\Exception $e) {
            \Log::error('Erro geral ao buscar CNPJ: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a requisição: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca CNPJ usando BrasilAPI (alternativa)
     */
    private function buscarCNPJBrasilAPI($cnpj)
    {
        try {
            $response = Http::timeout(10)->get("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                $formatted = [
                    'nome' => $data['razao_social'] ?? '',
                    'razao_social' => $data['razao_social'] ?? '',
                    'cnpj' => $data['cnpj'] ?? $cnpj,
                    'logradouro' => $data['logradouro'] ?? '',
                    'numero' => $data['numero'] ?? '',
                    'complemento' => $data['complemento'] ?? '',
                    'bairro' => $data['bairro'] ?? '',
                    'municipio' => $data['municipio'] ?? '',
                    'uf' => $data['uf'] ?? '',
                    'cep' => preg_replace('/\D/', '', $data['cep'] ?? ''),
                    'telefone' => '',
                    'email' => '',
                ];
                
                // Salva no cache
                $cacheKey = 'cnpj_' . $cnpj;
                Cache::put($cacheKey, $formatted, now()->addHours(1));
                
                return response()->json([
                    'success' => true,
                    'data' => $formatted
                ]);
            }
        } catch (\Exception $e) {
            // Ignora erro
        }

        return response()->json([
            'success' => false,
            'message' => 'CNPJ não encontrado nas bases de dados disponíveis.'
        ], 404);
    }

    /**
     * Valida CNPJ
     */
    private function validarCNPJ($cnpj)
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
}
