<?php

namespace App\Http\Controllers;

use App\Models\LinkDemandaPublico;
use App\Models\Demanda;
use App\Models\Orcamento;
use App\Models\Prestador;
use App\Models\Negociacao;
use App\Helpers\ValidacaoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemandaPublicaController extends Controller
{
    /**
     * Exibe a página de login para acesso ao link
     */
    public function login(string $token)
    {
        $link = LinkDemandaPublico::where('token', $token)->firstOrFail();

        // Verifica se o link está válido
        if (!$link->isValido()) {
            abort(404, 'Link expirado ou inativo.');
        }

        // Se já está autenticado, redireciona para a demanda
        if ($link->isAutenticado()) {
            return redirect()->route('publico.demanda.show', $token);
        }

        return view('publico.login-link', compact('link'));
    }

    /**
     * Processa o login do link
     */
    public function processarLogin(Request $request, string $token)
    {
        $link = LinkDemandaPublico::where('token', $token)->firstOrFail();

        // Verifica se o link está válido
        if (!$link->isValido()) {
            return view('publico.link-inativo', compact('link'));
        }

        $validated = $request->validate([
            'cpf_cnpj' => 'required|string|max:18',
            'token_acesso' => 'required|string|size:5',
        ]);

        // Valida CPF/CNPJ
        if (!ValidacaoHelper::validarCPFouCNPJ($validated['cpf_cnpj'])) {
            return redirect()->back()
                ->withErrors(['cpf_cnpj' => 'CPF ou CNPJ inválido.'])
                ->withInput();
        }

        // Tenta autenticar
        if (!$link->autenticar($validated['cpf_cnpj'], $validated['token_acesso'])) {
            return redirect()->back()
                ->withErrors(['error' => 'CPF/CNPJ ou token de acesso inválido.'])
                ->withInput();
        }

        return redirect()->route('publico.demanda.show', $token)
            ->with('success', 'Autenticação realizada com sucesso!');
    }

    /**
     * Exibe a página pública da demanda através do link único
     */
    public function show(string $token)
    {
        $link = LinkDemandaPublico::where('token', $token)->firstOrFail();

        // Verifica se o link está válido
        if (!$link->isValido()) {
            return view('publico.link-inativo', compact('link'));
        }

        // Verifica se precisa de autenticação e se está autenticado
        // Se o link tem token_acesso, exige autenticação
        if ($link->token_acesso) {
            if (!$link->isAutenticado()) {
                return redirect()->route('publico.demanda.login', $token);
            }
        }

        // Incrementa acesso
        $link->incrementarAcesso();

        // Carrega dados necessários
        $demanda = $link->demanda;
        $demanda->load(['condominio', 'categoriaServico', 'orcamentos' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        // Carrega o zelador do condomínio
        $zelador = \App\Models\User::where('condominio_id', $demanda->condominio_id)
            ->where('perfil', 'zelador')
            ->first();

        // Verifica se já existe orçamento enviado por este prestador (usando CPF/CNPJ do link)
        $jaEnviouOrcamento = false;
        $orcamentoEnviado = null;
        $negociacoes = collect();
        
        if ($link->cpf_cnpj_autorizado) {
            $prestador = \App\Models\Prestador::where('cpf_cnpj', $link->cpf_cnpj_autorizado)
                ->where('administradora_id', $demanda->administradora_id)
                ->first();
            
            if ($prestador) {
                $orcamentoEnviado = \App\Models\Orcamento::where('demanda_id', $demanda->id)
                    ->where('prestador_id', $prestador->id)
                    ->with(['negociacoes' => function($query) {
                        $query->orderBy('created_at', 'desc');
                    }])
                    ->first();
                
                $jaEnviouOrcamento = $orcamentoEnviado !== null;
                
                // Carrega negociações do orçamento
                if ($orcamentoEnviado && $orcamentoEnviado->negociacoes) {
                    $negociacoes = $orcamentoEnviado->negociacoes;
                }
            }
        }

        return view('publico.demanda-prestador', compact('link', 'demanda', 'zelador', 'jaEnviouOrcamento', 'orcamentoEnviado', 'negociacoes'));
    }

    /**
     * Processa o envio de orçamento pelo prestador não cadastrado
     */
    public function enviarOrcamento(Request $request, string $token)
    {
        $link = LinkDemandaPublico::where('token', $token)->firstOrFail();

        // Verifica se o link está válido
        if (!$link->isValido()) {
            return view('publico.link-inativo', compact('link'));
        }

        // Verifica se precisa de autenticação e se está autenticado
        // Se o link tem token_acesso, exige autenticação
        if ($link->token_acesso) {
            if (!$link->isAutenticado()) {
                return redirect()->route('publico.demanda.login', $token)
                    ->withErrors(['error' => 'É necessário fazer login para enviar orçamento.']);
            }
        }

        $demanda = $link->demanda;

        $validated = $request->validate([
            'valor' => 'required|numeric|min:0',
            'descricao' => 'nullable|string',
            'validade_dias' => 'nullable|integer|min:1|max:365',
            'arquivo' => 'nullable|file|mimes:pdf|max:10240', // 10MB
        ]);

        // Busca ou cria prestador usando o CPF/CNPJ autorizado no link
        $cpfCnpj = $link->cpf_cnpj_autorizado;
        
        if (!$cpfCnpj) {
            return redirect()->back()
                ->withErrors(['error' => 'Link não possui CPF/CNPJ autorizado.'])
                ->withInput();
        }

        // Busca prestador pelo CPF/CNPJ
        $prestador = Prestador::where('cpf_cnpj', $cpfCnpj)
            ->where('administradora_id', $demanda->administradora_id)
            ->first();

        // Se não encontrou, cria um prestador usando o nome do link
        if (!$prestador) {
            $nomePrestador = $link->nome_prestador ?? 'Prestador - ' . substr($cpfCnpj, -4);
            
            $prestador = Prestador::create([
                'administradora_id' => $demanda->administradora_id,
                'cpf_cnpj' => $cpfCnpj,
                'nome_razao_social' => $nomePrestador,
                'email' => 'prestador_' . $cpfCnpj . '@temp.com', // Email temporário
                'telefone' => null,
                'ativo' => true,
            ]);
        } else {
            // Atualiza o nome se o link tiver um nome definido e o prestador não tiver nome completo
            if ($link->nome_prestador && (!$prestador->nome_razao_social || $prestador->nome_razao_social === 'Prestador - ' . substr($cpfCnpj, -4))) {
                $prestador->update([
                    'nome_razao_social' => $link->nome_prestador,
                ]);
            }
        }

        // Verifica se já enviou orçamento para esta demanda
        $jaEnviouOrcamento = Orcamento::where('demanda_id', $demanda->id)
            ->where('prestador_id', $prestador->id)
            ->exists();

        if ($jaEnviouOrcamento) {
            return redirect()->route('publico.demanda.show', $token)
                ->withErrors(['error' => 'Você já enviou um orçamento para esta demanda.']);
        }

        // Calcula a data de validade baseada nos dias
        $validade = null;
        if (isset($validated['validade_dias']) && $validated['validade_dias']) {
            $validade = now()->addDays((int) $validated['validade_dias']);
        }

        // Cria o orçamento
        $orcamento = Orcamento::create([
            'demanda_id' => $demanda->id,
            'prestador_id' => $prestador->id,
            'link_prestador_id' => null, // Não usa link de prestador cadastrado
            'valor' => $validated['valor'],
            'descricao' => $validated['descricao'],
            'validade' => $validade,
            'status' => 'recebido',
        ]);

        // Upload de arquivo se fornecido
        if ($request->hasFile('arquivo')) {
            $arquivo = $request->file('arquivo');
            $nomeArquivo = Str::uuid() . '.' . $arquivo->getClientOriginalExtension();
            $caminho = $arquivo->storeAs('documentos/orcamentos', $nomeArquivo, 'public');

            $orcamento->documentos()->create([
                'administradora_id' => $demanda->administradora_id,
                'condominio_id' => $demanda->condominio_id,
                'demanda_id' => $demanda->id,
                'prestador_id' => $prestador->id,
                'orcamento_id' => $orcamento->id,
                'tipo' => 'orcamento_pdf',
                'nome_original' => $arquivo->getClientOriginalName(),
                'nome_arquivo' => $nomeArquivo,
                'caminho' => $caminho,
                'mime_type' => $arquivo->getMimeType(),
                'tamanho' => $arquivo->getSize(),
            ]);
        }

        // Associa prestador à demanda se ainda não estiver associado
        $demanda->prestadores()->syncWithoutDetaching([$prestador->id]);

        // Recarrega os dados para exibir na view
        $demanda->load(['condominio', 'categoriaServico', 'orcamentos' => function ($query) use ($prestador) {
            $query->where('prestador_id', $prestador->id)->orderBy('created_at', 'desc');
        }]);

        return redirect()->route('publico.demanda.show', $token)
            ->with('success', 'Orçamento enviado com sucesso! Nossa equipe entrará em contato em breve.');
    }

    /**
     * Prestador aceita uma negociação
     */
    public function aceitarNegociacao(Request $request, string $token, Negociacao $negociacao)
    {
        $link = LinkDemandaPublico::where('token', $token)->firstOrFail();

        // Verifica se o link está válido
        if (!$link->isValido()) {
            return view('publico.link-inativo', compact('link'));
        }

        // Verifica se precisa de autenticação e se está autenticado
        if ($link->token_acesso) {
            if (!$link->isAutenticado()) {
                return redirect()->route('publico.demanda.login', $token);
            }
        }

        // Busca o prestador pelo CPF/CNPJ do link
        $prestador = null;
        if ($link->cpf_cnpj_autorizado) {
            $prestador = Prestador::where('cpf_cnpj', $link->cpf_cnpj_autorizado)
                ->where('administradora_id', $link->demanda->administradora_id)
                ->first();
        }

        // Verifica se a negociação pertence ao prestador do link
        if (!$prestador || $negociacao->prestador_id !== $prestador->id || $negociacao->demanda_id !== $link->demanda_id) {
            abort(403, 'Negociação não pertence a este prestador.');
        }

        // Verifica se a negociação está pendente
        if ($negociacao->status !== 'pendente') {
            return redirect()->route('publico.demanda.show', $token)
                ->withErrors(['error' => 'Esta negociação já foi respondida.']);
        }

        // Validação baseada no tipo de negociação
        $rules = [
            'mensagem_resposta' => 'nullable|string|max:1000',
        ];
        
        if ($negociacao->tipo === 'desconto') {
            $rules['valor_solicitado'] = 'required|numeric|min:0.01|max:' . $negociacao->valor_original;
            $rules['porcentagem_desconto'] = 'nullable|numeric|min:0|max:100';
        } elseif ($negociacao->tipo === 'parcelamento') {
            $rules['parcelas'] = 'required|integer|min:2|max:12';
        }
        
        $validated = $request->validate($rules);

        // Para desconto, atualiza o valor com desconto escolhido pelo prestador
        if ($negociacao->tipo === 'desconto' && isset($validated['valor_solicitado'])) {
            // Se foi informada porcentagem, calcula o valor baseado nela
            if (isset($validated['porcentagem_desconto']) && $validated['porcentagem_desconto'] > 0) {
                $valorComDesconto = $negociacao->valor_original * (1 - $validated['porcentagem_desconto'] / 100);
                $validated['valor_solicitado'] = round($valorComDesconto, 2);
            }
            
            $negociacao->update([
                'valor_solicitado' => $validated['valor_solicitado'],
            ]);
        }
        
        // Para parcelamento, atualiza o número de parcelas
        if ($negociacao->tipo === 'parcelamento' && isset($validated['parcelas'])) {
            $negociacao->update([
                'parcelas' => $validated['parcelas'],
            ]);
        }
        
        // Para contraproposta, o valor já foi definido pela administradora, não precisa atualizar

        $negociacao->aceitar($validated['mensagem_resposta'] ?? null);

        return redirect()->route('publico.demanda.show', $token)
            ->with('success', 'Negociação aceita com sucesso!');
    }

    /**
     * Prestador recusa uma negociação
     */
    public function recusarNegociacao(Request $request, string $token, Negociacao $negociacao)
    {
        $link = LinkDemandaPublico::where('token', $token)->firstOrFail();

        // Verifica se o link está válido
        if (!$link->isValido()) {
            return view('publico.link-inativo', compact('link'));
        }

        // Verifica se precisa de autenticação e se está autenticado
        if ($link->token_acesso) {
            if (!$link->isAutenticado()) {
                return redirect()->route('publico.demanda.login', $token);
            }
        }

        // Busca o prestador pelo CPF/CNPJ do link
        $prestador = null;
        if ($link->cpf_cnpj_autorizado) {
            $prestador = Prestador::where('cpf_cnpj', $link->cpf_cnpj_autorizado)
                ->where('administradora_id', $link->demanda->administradora_id)
                ->first();
        }

        // Verifica se a negociação pertence ao prestador do link
        if (!$prestador || $negociacao->prestador_id !== $prestador->id || $negociacao->demanda_id !== $link->demanda_id) {
            abort(403, 'Negociação não pertence a este prestador.');
        }

        // Verifica se a negociação está pendente
        if ($negociacao->status !== 'pendente') {
            return redirect()->route('publico.demanda.show', $token)
                ->withErrors(['error' => 'Esta negociação já foi respondida.']);
        }

        $validated = $request->validate([
            'mensagem_resposta' => 'nullable|string|max:1000',
        ]);

        $negociacao->recusar($validated['mensagem_resposta'] ?? null);

        return redirect()->route('publico.demanda.show', $token)
            ->with('success', 'Negociação recusada.');
    }

    /**
     * Processa a conclusão do serviço pelo prestador
     */
    public function concluirServico(Request $request, string $token)
    {
        $link = LinkDemandaPublico::where('token', $token)->firstOrFail();

        // Verifica se o link está válido
        if (!$link->isValido()) {
            return view('publico.link-inativo', compact('link'));
        }

        // Verifica se precisa de autenticação e se está autenticado
        if ($link->token_acesso) {
            if (!$link->isAutenticado()) {
                return redirect()->route('publico.demanda.login', $token);
            }
        }

        $validated = $request->validate([
            'orcamento_id' => 'required|exists:orcamentos,id',
            'concluido' => 'required|accepted',
            'observacoes_conclusao' => 'nullable|string|max:2000',
            'dados_bancarios' => 'nullable|string|max:2000',
            'nota_fiscal' => 'required|file|mimes:pdf|max:10240', // 10MB
            'boleto' => 'required|file|mimes:pdf|max:10240', // 10MB
        ]);

        // Busca o orçamento
        $orcamento = Orcamento::findOrFail($validated['orcamento_id']);

        // Verifica se o orçamento pertence ao prestador do link
        $prestador = null;
        if ($link->cpf_cnpj_autorizado) {
            $prestador = Prestador::where('cpf_cnpj', $link->cpf_cnpj_autorizado)
                ->where('administradora_id', $orcamento->demanda->administradora_id)
                ->first();
        }

        if (!$prestador || $orcamento->prestador_id !== $prestador->id) {
            abort(403, 'Orçamento não pertence a este prestador.');
        }

        // Verifica se o orçamento está aprovado
        if ($orcamento->status !== 'aprovado') {
            return redirect()->route('publico.demanda.show', $token)
                ->withErrors(['error' => 'Apenas orçamentos aprovados podem ser concluídos.']);
        }

        // Verifica se já foi concluído
        if ($orcamento->concluido) {
            return redirect()->route('publico.demanda.show', $token)
                ->withErrors(['error' => 'Este serviço já foi marcado como concluído.']);
        }

        // Atualiza o orçamento com os dados de conclusão
        $orcamento->update([
            'concluido' => true,
            'concluido_em' => now(),
            'concluido_por' => $prestador->id,
            'observacoes_conclusao' => $validated['observacoes_conclusao'] ?? null,
            'dados_bancarios' => $validated['dados_bancarios'] ?? null,
        ]);

        // Atualiza o status da demanda para concluída
        $orcamento->demanda->update([
            'status' => 'concluida',
        ]);

        // Upload da Nota Fiscal
        if ($request->hasFile('nota_fiscal')) {
            $arquivo = $request->file('nota_fiscal');
            $nomeArquivo = Str::uuid() . '.' . $arquivo->getClientOriginalExtension();
            $caminho = $arquivo->storeAs('documentos/orcamentos', $nomeArquivo, 'public');

            $orcamento->documentos()->create([
                'administradora_id' => $orcamento->demanda->administradora_id,
                'condominio_id' => $orcamento->demanda->condominio_id,
                'demanda_id' => $orcamento->demanda_id,
                'prestador_id' => $orcamento->prestador_id,
                'orcamento_id' => $orcamento->id,
                'tipo' => 'nota_fiscal',
                'nome_original' => $arquivo->getClientOriginalName(),
                'nome_arquivo' => $nomeArquivo,
                'caminho' => $caminho,
                'mime_type' => $arquivo->getMimeType(),
                'tamanho' => $arquivo->getSize(),
            ]);
        }

        // Upload do Boleto
        if ($request->hasFile('boleto')) {
            $arquivo = $request->file('boleto');
            $nomeArquivo = Str::uuid() . '.' . $arquivo->getClientOriginalExtension();
            $caminho = $arquivo->storeAs('documentos/orcamentos', $nomeArquivo, 'public');

            $orcamento->documentos()->create([
                'administradora_id' => $orcamento->demanda->administradora_id,
                'condominio_id' => $orcamento->demanda->condominio_id,
                'demanda_id' => $orcamento->demanda_id,
                'prestador_id' => $orcamento->prestador_id,
                'orcamento_id' => $orcamento->id,
                'tipo' => 'boleto',
                'nome_original' => $arquivo->getClientOriginalName(),
                'nome_arquivo' => $nomeArquivo,
                'caminho' => $caminho,
                'mime_type' => $arquivo->getMimeType(),
                'tamanho' => $arquivo->getSize(),
            ]);
        }

        return redirect()->route('publico.demanda.show', $token)
            ->with('success', 'Serviço marcado como concluído com sucesso!');
    }
}
