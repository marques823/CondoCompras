<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\Condominio;
use App\Models\Prestador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandaController extends Controller
{
    public function index(Request $request)
    {
        $query = Demanda::daEmpresa(Auth::user()->empresa_id)
            ->with(['condominio', 'categoriaServico', 'usuario']);

        // Filtro por pesquisa (título ou descrição)
        if ($request->filled('pesquisa')) {
            $pesquisa = $request->pesquisa;
            $query->where(function($q) use ($pesquisa) {
                $q->where('titulo', 'like', "%{$pesquisa}%")
                  ->orWhere('descricao', 'like', "%{$pesquisa}%");
            });
        }

        // Filtro por status
        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        // Filtro por condomínio
        if ($request->filled('condominio_id')) {
            $query->where('condominio_id', $request->condominio_id);
        }

        // Filtro por categoria de serviço
        if ($request->filled('categoria_servico_id')) {
            $query->where('categoria_servico_id', $request->categoria_servico_id);
        }

        // Ordenação por clique na coluna
        $ordenarColuna = $request->get('ordenar_coluna', 'created_at');
        $ordenarDirecao = $request->get('ordenar_direcao', 'desc');
        
        // Valida coluna de ordenação
        $colunasPermitidas = ['titulo', 'status', 'created_at', 'categoria_servico_id'];
        if (!in_array($ordenarColuna, $colunasPermitidas)) {
            $ordenarColuna = 'created_at';
        }
        
        // Valida direção de ordenação
        if (!in_array($ordenarDirecao, ['asc', 'desc'])) {
            $ordenarDirecao = 'desc';
        }
        
        // Aplica ordenação
        if ($ordenarColuna === 'categoria_servico_id') {
            $query->leftJoin('categorias_servicos', 'demandas.categoria_servico_id', '=', 'categorias_servicos.id')
                  ->select('demandas.*')
                  ->orderBy('categorias_servicos.nome', $ordenarDirecao);
        } else {
            $query->orderBy($ordenarColuna, $ordenarDirecao);
        }
        
        // Ordenação secundária sempre por data (mais recente primeiro) se não for por data
        if ($ordenarColuna !== 'created_at') {
            $query->orderBy('created_at', 'desc');
        }

        $demandas = $query->paginate(15)->withQueryString();

        // Carrega categorias para filtros
        $categorias = \App\Models\CategoriaServico::ativas()
            ->orderBy('nome')
            ->get();

        // Prepara dados dos condomínios para JavaScript (autocomplete)
        $condominiosData = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->ativos()
            ->select('id', 'nome', 'bairro', 'cidade')
            ->orderBy('nome')
            ->get()
            ->map(function($c) {
                return [
                    'id' => $c->id,
                    'nome' => $c->nome,
                    'bairro' => $c->bairro ?? '',
                    'cidade' => $c->cidade ?? '',
                ];
            })->values();

        return view('demandas.index', compact('demandas', 'categorias', 'condominiosData'));
    }

    public function create()
    {
        $condominios = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->ativos()
            ->with('tags')
            ->orderBy('nome')
            ->get();
            
        $prestadores = Prestador::daEmpresa(Auth::user()->empresa_id)
            ->ativos()
            ->with('tags')
            ->orderBy('nome_razao_social')
            ->get();

        $tags = \App\Models\Tag::daEmpresa(Auth::user()->empresa_id)
            ->ativas()
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        // Categorias de serviços para sugestões
        $categorias = \App\Models\CategoriaServico::ativas()
            ->orderBy('nome')
            ->get();

        // Prepara dados dos condomínios para JavaScript
        $condominiosData = $condominios->map(function($c) {
            return [
                'id' => $c->id,
                'nome' => $c->nome,
                'bairro' => $c->bairro ?? '',
                'cidade' => $c->cidade ?? '',
                'estado' => $c->estado ?? '',
                'tags' => $c->tags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'nome' => $tag->nome,
                        'cor' => $tag->cor
                    ];
                })->toArray()
            ];
        })->values();

        return view('demandas.create', compact('condominios', 'prestadores', 'tags', 'condominiosData', 'categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'prazo_limite' => 'nullable|date|after:today',
            'prestadores' => 'nullable|array',
            'prestadores.*' => 'exists:prestadores,id',
        ]);

        $validated['empresa_id'] = Auth::user()->empresa_id;
        $validated['usuario_id'] = Auth::id();
        $validated['status'] = 'aberta';

        $demanda = Demanda::create($validated);

        // Associa prestadores se fornecidos
        if ($request->has('prestadores')) {
            foreach ($request->prestadores as $prestadorId) {
                $demanda->prestadores()->attach($prestadorId, ['status' => 'convidado']);
            }

            // Gera links únicos
            \App\Http\Controllers\LinkPrestadorController::gerarLinksParaDemanda(
                $demanda,
                $request->prestadores
            );
        }

        return redirect()->route('demandas.index')
            ->with('success', 'Demanda criada com sucesso!');
    }

    public function show(Demanda $demanda)
    {
        if ($demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $demanda->load(['condominio', 'categoriaServico', 'usuario', 'prestadores', 'orcamentos' => function($query) {
            $query->with('negociacoes');
        }, 'links', 'negociacoes']);

        // Prestadores disponíveis para adicionar (excluindo os já associados)
        $prestadoresIds = $demanda->prestadores->pluck('id')->toArray();
        $prestadoresDisponiveis = Prestador::daEmpresa(Auth::user()->empresa_id)
            ->ativos()
            ->whereNotIn('id', $prestadoresIds)
            ->orderBy('nome_razao_social')
            ->get();

        return view('demandas.show', compact('demanda', 'prestadoresDisponiveis'));
    }

    public function edit(Demanda $demanda)
    {
        if ($demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $condominios = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->ativos()
            ->with('tags')
            ->orderBy('nome')
            ->get();
            
        $prestadores = Prestador::daEmpresa(Auth::user()->empresa_id)
            ->ativos()
            ->with('tags')
            ->orderBy('nome_razao_social')
            ->get();

        $tags = \App\Models\Tag::daEmpresa(Auth::user()->empresa_id)
            ->ativas()
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        $categorias = \App\Models\CategoriaServico::ativas()
            ->orderBy('nome')
            ->get();

        // Prepara dados dos condomínios para JavaScript
        $condominiosData = $condominios->map(function($c) {
            return [
                'id' => $c->id,
                'nome' => $c->nome,
                'bairro' => $c->bairro ?? '',
                'cidade' => $c->cidade ?? '',
                'estado' => $c->estado ?? '',
                'tags' => $c->tags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'nome' => $tag->nome,
                        'cor' => $tag->cor
                    ];
                })->toArray()
            ];
        })->values();

        // Prestadores já associados à demanda
        $prestadoresSelecionados = $demanda->prestadores->pluck('id')->toArray();

        return view('demandas.edit', compact('demanda', 'condominios', 'prestadores', 'tags', 'condominiosData', 'categorias', 'prestadoresSelecionados'));
    }

    public function update(Request $request, Demanda $demanda)
    {
        if ($demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'categoria_servico_id' => 'nullable|exists:categorias_servicos,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'prazo_limite' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'prestadores' => 'nullable|array',
            'prestadores.*' => 'exists:prestadores,id',
        ]);

        $demanda->update($validated);

        // Gerencia prestadores
        if ($request->has('prestadores')) {
            $prestadoresAtuais = $demanda->prestadores->pluck('id')->toArray();
            $prestadoresNovos = $request->prestadores;
            
            // Remove prestadores que não estão mais na lista
            $prestadoresParaRemover = array_diff($prestadoresAtuais, $prestadoresNovos);
            foreach ($prestadoresParaRemover as $prestadorId) {
                $demanda->prestadores()->detach($prestadorId);
            }
            
            // Adiciona novos prestadores
            $prestadoresParaAdicionar = array_diff($prestadoresNovos, $prestadoresAtuais);
            foreach ($prestadoresParaAdicionar as $prestadorId) {
                $demanda->prestadores()->attach($prestadorId, ['status' => 'convidado']);
                
                // Gera link único para o novo prestador
                \App\Http\Controllers\LinkPrestadorController::gerarLinksParaDemanda(
                    $demanda,
                    [$prestadorId]
                );
            }
        }

        return redirect()->route('demandas.show', $demanda)
            ->with('success', 'Demanda atualizada com sucesso!');
    }

    public function updateStatus(Request $request, Demanda $demanda)
    {
        if ($demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:aberta,em_andamento,aguardando_orcamento,concluida,cancelada',
        ]);

        $demanda->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'Status da demanda atualizado com sucesso!');
    }

    public function adicionarPrestadores(Request $request, Demanda $demanda)
    {
        if ($demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $validated = $request->validate([
            'prestadores' => 'required|array',
            'prestadores.*' => 'exists:prestadores,id',
        ]);

        foreach ($validated['prestadores'] as $prestadorId) {
            // Verifica se o prestador já está associado
            if (!$demanda->prestadores->contains($prestadorId)) {
                $demanda->prestadores()->attach($prestadorId, ['status' => 'convidado']);
                
                // Gera link único
                \App\Http\Controllers\LinkPrestadorController::gerarLinksParaDemanda(
                    $demanda,
                    [$prestadorId]
                );
            }
        }

        return redirect()->back()
            ->with('success', 'Prestadores adicionados com sucesso!');
    }

    public function removerPrestador(Request $request, Demanda $demanda, Prestador $prestador)
    {
        if ($demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $demanda->prestadores()->detach($prestador->id);

        return redirect()->back()
            ->with('success', 'Prestador removido com sucesso!');
    }

    public function aprovarOrcamento(Request $request, Demanda $demanda, $orcamento)
    {
        if ($demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $orcamento = \App\Models\Orcamento::where('demanda_id', $demanda->id)
            ->where('id', $orcamento)
            ->firstOrFail();

        $validated = $request->validate([
            'observacoes' => 'nullable|string',
        ]);

        $orcamento->update([
            'status' => 'aprovado',
            'aprovado_por' => Auth::id(),
            'aprovado_em' => now(),
            'observacoes' => $validated['observacoes'] ?? $orcamento->observacoes,
        ]);

        // Atualiza status da demanda
        $demanda->update(['status' => 'em_andamento']);

        return redirect()->back()
            ->with('success', 'Orçamento aprovado com sucesso!');
    }

    public function rejeitarOrcamento(Request $request, Demanda $demanda, $orcamento)
    {
        if ($demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $orcamento = \App\Models\Orcamento::where('demanda_id', $demanda->id)
            ->where('id', $orcamento)
            ->firstOrFail();

        $validated = $request->validate([
            'motivo_rejeicao' => 'required|string|max:500',
        ]);

        $orcamento->update([
            'status' => 'rejeitado',
            'motivo_rejeicao' => $validated['motivo_rejeicao'],
        ]);

        return redirect()->back()
            ->with('success', 'Orçamento rejeitado com sucesso!');
    }

    public function criarNegociacao(Request $request, Demanda $demanda, $orcamento)
    {
        if ($demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $orcamento = \App\Models\Orcamento::where('demanda_id', $demanda->id)
            ->where('id', $orcamento)
            ->firstOrFail();

        $validated = $request->validate([
            'tipo' => 'required|in:desconto,parcelamento,contraproposta',
            'valor_solicitado' => 'nullable|numeric|min:0.01|required_if:tipo,contraproposta',
            'mensagem_solicitacao' => 'nullable|string|max:1000',
        ]);

        // Validação específica por tipo
        if ($validated['tipo'] === 'contraproposta') {
            if ($validated['valor_solicitado'] >= $orcamento->valor) {
                return redirect()->back()
                    ->withErrors(['valor_solicitado' => 'O valor da contraproposta deve ser menor que o valor do orçamento.'])
                    ->withInput();
            }
        } else {
            // Para desconto e parcelamento, o valor será definido pelo prestador
            $validated['valor_solicitado'] = null;
        }

        \App\Models\Negociacao::create([
            'orcamento_id' => $orcamento->id,
            'demanda_id' => $demanda->id,
            'prestador_id' => $orcamento->prestador_id,
            'tipo' => $validated['tipo'],
            'valor_original' => $orcamento->valor,
            'valor_solicitado' => $validated['valor_solicitado'],
            'parcelas' => null, // Será definido pelo prestador
            'status' => 'pendente',
            'mensagem_solicitacao' => $validated['mensagem_solicitacao'] ?? null,
            'criado_por' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Negociação criada com sucesso! O prestador será notificado.');
    }
}
