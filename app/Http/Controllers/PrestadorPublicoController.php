<?php

namespace App\Http\Controllers;

use App\Models\Administradora;
use App\Models\Prestador;
use Illuminate\Http\Request;

class PrestadorPublicoController extends Controller
{
    /**
     * Exibe o formulário público de cadastro de prestadores
     */
    public function cadastro(Request $request)
    {
        $empresa = null;
        
        // Se houver token na URL, carrega a empresa
        if ($request->has('token')) {
            $empresa = Administradora::where('token_cadastro', $request->token)
                ->where('ativo', true)
                ->first();
        }

        return view('publico.cadastro-prestador', compact('empresa'));
    }

    /**
     * Processa o cadastro público de prestadores
     */
    public function store(Request $request)
    {
        // Validação do token de empresa (obrigatório para cadastro público)
        $empresa = Administradora::where('token_cadastro', $request->token_empresa)
            ->where('ativo', true)
            ->first();

        if (!$empresa) {
            return redirect()->back()
                ->withErrors(['token_empresa' => 'Código de cadastro inválido ou empresa inativa.'])
                ->withInput();
        }

        $validated = $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'tipo' => 'required|in:fisica,juridica',
            'cpf_cnpj' => 'nullable|string|max:18',
            'email' => 'required|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'areas_atuacao' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'token_empresa' => 'required|string',
        ]);

        // Verifica se já existe prestador com mesmo CPF/CNPJ na empresa
        if ($validated['cpf_cnpj']) {
            $cpfCnpjLimpo = preg_replace('/[^0-9]/', '', $validated['cpf_cnpj']);
            $prestadorExistente = Prestador::daAdministradora($empresa->id)
                ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(cpf_cnpj, '.', ''), '/', ''), '-', ''), ' ', '') = ?", [$cpfCnpjLimpo])
                ->first();

            if ($prestadorExistente) {
                return redirect()->back()
                    ->withErrors(['cpf_cnpj' => 'Já existe um prestador cadastrado com este CPF/CNPJ nesta empresa.'])
                    ->withInput();
            }
        }

        // Verifica se já existe prestador com mesmo email na empresa
        if ($validated['email']) {
            $prestadorExistente = Prestador::daAdministradora($empresa->id)
                ->where('email', $validated['email'])
                ->first();

            if ($prestadorExistente) {
                return redirect()->back()
                    ->withErrors(['email' => 'Já existe um prestador cadastrado com este email nesta empresa.'])
                    ->withInput();
            }
        }

        // Cria o prestador
        $validated['administradora_id'] = $empresa->id;
        $validated['ativo'] = true; // Prestadores cadastrados publicamente começam ativos

        unset($validated['token_empresa']);

        $prestador = Prestador::create($validated);

        return view('publico.prestador-cadastrado', compact('prestador', 'empresa'))
            ->with('success', 'Cadastro realizado com sucesso! Nossa equipe entrará em contato em breve.');
    }
}
