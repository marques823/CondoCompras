<?php

namespace App\Http\Controllers;

use App\Models\LinkPrestador;
use App\Models\Demanda;
use App\Models\Prestador;
use App\Models\Orcamento;
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

        if (!$link->isValido()) {
            abort(404, 'Link expirado ou já utilizado.');
        }

        // Incrementa acesso
        $link->incrementarAcesso();

        // Marca como visualizado na relação demanda_prestador
        $demanda = $link->demanda;
        $prestador = $link->prestador;

        $demanda->prestadores()->updateExistingPivot($prestador->id, [
            'status' => 'visualizou',
            'visualizado_em' => now(),
        ]);

        // Carrega dados necessários
        $demanda->load(['condominio', 'categoriaServico', 'orcamentos' => function ($query) use ($prestador) {
            $query->where('prestador_id', $prestador->id);
        }]);

        return view('prestador.demanda', compact('link', 'demanda', 'prestador'));
    }

    /**
     * Processa o envio de orçamento pelo prestador
     */
    public function enviarOrcamento(Request $request, string $token)
    {
        $link = LinkPrestador::where('token', $token)->firstOrFail();

        if (!$link->isValido()) {
            return response()->json([
                'message' => 'Link expirado ou já utilizado.'
            ], 403);
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
                'empresa_id' => $link->demanda->empresa_id,
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

        // Atualiza status na relação demanda_prestador
        $link->demanda->prestadores()->updateExistingPivot($link->prestador_id, [
            'status' => 'enviou_orcamento',
        ]);

        // Marca link como usado
        $link->marcarComoUsado();

        return response()->json([
            'message' => 'Orçamento enviado com sucesso!',
            'orcamento' => $orcamento,
        ]);
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
}
