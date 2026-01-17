<?php

namespace App\Http\Controllers;

use App\Models\LinkCondominio;
use App\Models\CategoriaServico;
use Illuminate\Http\Request;

class CondominioPublicoController extends Controller
{
    /**
     * Exibe o formulário público para criar demanda via link único
     */
    public function criarDemanda(string $token)
    {
        $link = LinkCondominio::where('token', $token)->firstOrFail();

        // Verifica se o link está válido
        if (!$link->isValido()) {
            abort(404, 'Link expirado ou inativo.');
        }

        $condominio = $link->condominio;
        
        // Carrega categorias de serviços
        $categorias = CategoriaServico::ativas()
            ->orderBy('nome')
            ->get();

        return view('publico.criar-demanda', compact('link', 'condominio', 'categorias'));
    }

    /**
     * Processa a criação de demanda via link público
     */
    public function storeDemanda(Request $request, string $token)
    {
        $link = LinkCondominio::where('token', $token)->firstOrFail();

        // Verifica se o link está válido
        if (!$link->isValido()) {
            return redirect()->back()
                ->withErrors(['error' => 'Link expirado ou inativo.'])
                ->withInput();
        }

        $condominio = $link->condominio;

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'categoria_servico_id' => 'nullable|exists:categorias_servicos,id',
            'prazo_limite' => 'nullable|date|after:today',
            'nome_solicitante' => 'nullable|string|max:255',
            'telefone_solicitante' => 'nullable|string|max:20',
            'email_solicitante' => 'nullable|email|max:255',
        ]);

        // Cria a demanda vinculada ao condomínio e empresa do link
        $demanda = \App\Models\Demanda::create([
            'administradora_id' => $link->administradora_id,
            'condominio_id' => $condominio->id,
            'categoria_servico_id' => $validated['categoria_servico_id'] ?? null,
            'usuario_id' => null, // Demanda pública não tem usuário específico
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'],
            'prazo_limite' => $validated['prazo_limite'] ?? null,
            'status' => 'aberta',
            'observacoes' => $validated['nome_solicitante'] || $validated['telefone_solicitante'] || $validated['email_solicitante'] 
                ? "Solicitante: " . ($validated['nome_solicitante'] ?? 'Não informado') . "\n" .
                  "Telefone: " . ($validated['telefone_solicitante'] ?? 'Não informado') . "\n" .
                  "Email: " . ($validated['email_solicitante'] ?? 'Não informado')
                : null,
        ]);

        // Incrementa contador de usos do link
        $link->incrementarUso();

        return view('publico.demanda-criada', compact('demanda', 'condominio'))
            ->with('success', 'Demanda criada com sucesso! Nossa equipe entrará em contato em breve.');
    }
}
