<?php

namespace App\Http\Controllers;

use App\Models\LinkPrestador;
use App\Models\Demanda;
use App\Models\Prestador;
use App\Models\Orcamento;
use App\Models\Negociacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LinkPrestadorController extends Controller
{
    /**
     * Exibe a página do prestador através do link único
     */
    public function show(string $token)
    {
        $link = LinkPrestador::where('token', $token)->firstOrFail();

        // Verifica se o link está expirado
        if ($link->expira_em && $link->expira_em->isPast()) {
            abort(404, 'Link expirado.');
        }

        // Incrementa acesso
        $link->incrementarAcesso();

        // Marca como visualizado na relação demanda_prestador
        $demanda = $link->demanda;
        $prestador = $link->prestador;

        // Só marca como visualizado se ainda não enviou orçamento
        $jaEnviouOrcamento = $demanda->orcamentos()
            ->where('prestador_id', $prestador->id)
            ->exists();

        if (!$jaEnviouOrcamento) {
            $demanda->prestadores()->updateExistingPivot($prestador->id, [
                'status' => 'visualizou',
                'visualizado_em' => now(),
            ]);
        }

        // Carrega dados necessários
        $demanda->load(['condominio', 'categoriaServico', 'orcamentos' => function ($query) use ($prestador) {
            $query->where('prestador_id', $prestador->id)->orderBy('created_at', 'desc');
        }]);

        // Carrega o zelador do condomínio
        $zelador = \App\Models\User::where('condominio_id', $demanda->condominio_id)
            ->where('perfil', 'zelador')
            ->first();

        // Verifica se já enviou orçamento
        $jaEnviouOrcamento = $demanda->orcamentos->where('prestador_id', $prestador->id)->isNotEmpty();

        // Carrega negociações pendentes para os orçamentos deste prestador
        $orcamentosIds = $demanda->orcamentos->where('prestador_id', $prestador->id)->pluck('id')->toArray();
        $negociacoes = Negociacao::whereIn('orcamento_id', $orcamentosIds)
            ->where('status', 'pendente')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('prestador.demanda', compact('link', 'demanda', 'prestador', 'jaEnviouOrcamento', 'negociacoes', 'zelador'));
    }

    /**
     * Processa o envio de orçamento pelo prestador
     */
    public function enviarOrcamento(Request $request, string $token)
    {
        $link = LinkPrestador::where('token', $token)->firstOrFail();

        // Verifica se já enviou orçamento
        $jaEnviouOrcamento = Orcamento::where('demanda_id', $link->demanda_id)
            ->where('prestador_id', $link->prestador_id)
            ->exists();

        if ($jaEnviouOrcamento) {
            return redirect()->route('prestador.link.show', $token)
                ->withErrors(['error' => 'Você já enviou um orçamento para esta demanda. Não é possível enviar novamente.']);
        }

        // Verifica se o link está expirado
        if ($link->expira_em && $link->expira_em->isPast()) {
            return redirect()->route('prestador.link.show', $token)
                ->withErrors(['error' => 'Link expirado.']);
        }

        $request->validate([
            'valor' => 'required|numeric|min:0',
            'descricao' => 'nullable|string',
            'validade' => 'nullable|date|after:today',
            'arquivo' => 'nullable|file|mimes:pdf|max:10240', // 10MB
        ]);

        $orcamento = Orcamento::create([
            'demanda_id' => $link->demanda_id,
            'prestador_id' => $link->prestador_id,
            'link_prestador_id' => $link->id,
            'valor' => $request->valor,
            'descricao' => $request->descricao,
            'validade' => $request->validade,
            'status' => 'recebido',
        ]);

        // Upload de arquivo se fornecido
        if ($request->hasFile('arquivo')) {
            $arquivo = $request->file('arquivo');
            $nomeArquivo = Str::uuid() . '.' . $arquivo->getClientOriginalExtension();
            $caminho = $arquivo->storeAs('documentos/orcamentos', $nomeArquivo, 'public');

            $orcamento->documentos()->create([
                'administradora_id' => $link->demanda->administradora_id,
                'condominio_id' => $link->demanda->condominio_id,
                'demanda_id' => $link->demanda->id,
                'prestador_id' => $link->prestador_id,
                'orcamento_id' => $orcamento->id,
                'tipo' => 'orcamento_pdf',
                'nome_original' => $arquivo->getClientOriginalName(),
                'nome_arquivo' => $nomeArquivo,
                'caminho' => $caminho,
                'mime_type' => $arquivo->getMimeType(),
                'tamanho' => $arquivo->getSize(),
            ]);
        }

        // Carrega a demanda
        $demanda = $link->demanda;

        // Atualiza status na relação demanda_prestador
        $demanda->prestadores()->updateExistingPivot($link->prestador_id, [
            'status' => 'enviou_orcamento',
        ]);

        // Marca link como usado após o primeiro envio (bloqueia novos envios)
        $link->marcarComoUsado();

        // Recarrega os dados para exibir na view
        $demanda->load(['condominio', 'categoriaServico', 'orcamentos' => function ($query) use ($link) {
            $query->where('prestador_id', $link->prestador_id);
        }]);

        return redirect()->route('prestador.link.show', $token)
            ->with('success', 'Orçamento enviado com sucesso! Você pode acompanhar o status abaixo.');
    }

    /**
     * Gera links únicos para prestadores de uma demanda
     */
    public static function gerarLinksParaDemanda(Demanda $demanda, array $prestadorIds): array
    {
        $links = [];

        foreach ($prestadorIds as $prestadorId) {
            // Verifica se já existe link válido
            $linkExistente = LinkPrestador::where('demanda_id', $demanda->id)
                ->where('prestador_id', $prestadorId)
                ->validos()
                ->first();

            if ($linkExistente) {
                $links[] = $linkExistente;
                continue;
            }

            // Cria novo link
            $link = LinkPrestador::create([
                'demanda_id' => $demanda->id,
                'prestador_id' => $prestadorId,
                'token' => LinkPrestador::gerarToken(),
                'expira_em' => now()->addDays(30), // Expira em 30 dias
                'usado' => false,
                'acessos' => 0,
            ]);

            $links[] = $link;
        }

        return $links;
    }

    /**
     * Prestador aceita uma negociação
     */
    public function aceitarNegociacao(Request $request, string $token, Negociacao $negociacao)
    {
        $link = LinkPrestador::where('token', $token)->firstOrFail();

        // Verifica se a negociação pertence ao prestador do link
        if ($negociacao->prestador_id !== $link->prestador_id || $negociacao->demanda_id !== $link->demanda_id) {
            abort(403, 'Negociação não pertence a este prestador.');
        }

        // Verifica se a negociação está pendente
        if ($negociacao->status !== 'pendente') {
            return redirect()->route('prestador.link.show', $token)
                ->withErrors(['error' => 'Esta negociação já foi respondida.']);
        }

        $validated = $request->validate([
            'mensagem_resposta' => 'nullable|string|max:1000',
            'valor_solicitado' => 'nullable|numeric|min:0.01|required_unless:tipo,contraproposta',
            'parcelas' => 'nullable|integer|min:2|required_if:tipo,parcelamento',
        ]);
        
        // Para contraproposta, o valor já vem da negociação original
        if ($negociacao->tipo === 'contraproposta') {
            $validated['valor_solicitado'] = $negociacao->valor_solicitado;
        }

        // Para desconto e parcelamento, atualiza os valores escolhidos pelo prestador
        if ($negociacao->tipo === 'desconto' || $negociacao->tipo === 'parcelamento') {
            $negociacao->update([
                'valor_solicitado' => $validated['valor_solicitado'],
                'parcelas' => $validated['parcelas'] ?? null,
            ]);
        }

        $negociacao->aceitar($validated['mensagem_resposta'] ?? null);

        return redirect()->route('prestador.link.show', $token)
            ->with('success', 'Negociação aceita com sucesso!');
    }

    /**
     * Prestador recusa uma negociação
     */
    public function recusarNegociacao(Request $request, string $token, Negociacao $negociacao)
    {
        $link = LinkPrestador::where('token', $token)->firstOrFail();

        // Verifica se a negociação pertence ao prestador do link
        if ($negociacao->prestador_id !== $link->prestador_id || $negociacao->demanda_id !== $link->demanda_id) {
            abort(403, 'Negociação não pertence a este prestador.');
        }

        // Verifica se a negociação está pendente
        if ($negociacao->status !== 'pendente') {
            return redirect()->route('prestador.link.show', $token)
                ->withErrors(['error' => 'Esta negociação já foi respondida.']);
        }

        $validated = $request->validate([
            'mensagem_resposta' => 'nullable|string|max:1000',
        ]);

        $negociacao->recusar($validated['mensagem_resposta'] ?? null);

        return redirect()->route('prestador.link.show', $token)
            ->with('success', 'Negociação recusada.');
    }
}
