<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use App\Models\Condominio;
use App\Models\Prestador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrcamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Orcamento::whereHas('demanda', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->with(['demanda.condominio', 'demanda.categoriaServico', 'prestador']);

        // Filtro por pesquisa (demanda, prestador, condomínio)
        if ($request->filled('pesquisa')) {
            $pesquisa = $request->pesquisa;
            $query->where(function($q) use ($pesquisa) {
                $q->whereHas('demanda', function($query) use ($pesquisa) {
                    $query->where('titulo', 'like', "%{$pesquisa}%")
                          ->orWhere('descricao', 'like', "%{$pesquisa}%")
                          ->orWhereHas('condominio', function($qCondo) use ($pesquisa) {
                              $qCondo->where('nome', 'like', "%{$pesquisa}%");
                          });
                })->orWhereHas('prestador', function($query) use ($pesquisa) {
                    $query->where('nome_razao_social', 'like', "%{$pesquisa}%");
                });
            });
        }

        // Filtro por status
        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        // Filtro por condomínio
        if ($request->filled('condominio_id')) {
            $query->whereHas('demanda', function($q) use ($request) {
                $q->where('condominio_id', $request->condominio_id);
            });
        }

        // Filtro por prestador
        if ($request->filled('prestador_id')) {
            $query->where('prestador_id', $request->prestador_id);
        }

        // Filtro por valor mínimo
        if ($request->filled('valor_min')) {
            $query->where('valor', '>=', $request->valor_min);
        }

        // Filtro por valor máximo
        if ($request->filled('valor_max')) {
            $query->where('valor', '<=', $request->valor_max);
        }

        // Ordenação por clique na coluna
        $ordenarColuna = $request->get('ordenar_coluna', 'created_at');
        $ordenarDirecao = $request->get('ordenar_direcao', 'desc');
        
        // Valida coluna de ordenação
        $colunasPermitidas = ['valor', 'status', 'created_at', 'prestador_id'];
        if (!in_array($ordenarColuna, $colunasPermitidas)) {
            $ordenarColuna = 'created_at';
        }
        
        // Valida direção de ordenação
        if (!in_array($ordenarDirecao, ['asc', 'desc'])) {
            $ordenarDirecao = 'desc';
        }
        
        // Aplica ordenação
        if ($ordenarColuna === 'prestador_id') {
            $query->join('prestadores', 'orcamentos.prestador_id', '=', 'prestadores.id')
                  ->select('orcamentos.*')
                  ->orderBy('prestadores.nome_razao_social', $ordenarDirecao);
        } else {
            $query->orderBy($ordenarColuna, $ordenarDirecao);
        }
        
        // Ordenação secundária sempre por data (mais recente primeiro) se não for por data
        if ($ordenarColuna !== 'created_at') {
            $query->orderBy('created_at', 'desc');
        }

        $orcamentos = $query->paginate(15)->withQueryString();

        // Carrega dados para filtros
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

        $prestadores = Prestador::daEmpresa(Auth::user()->empresa_id)
            ->ativos()
            ->select('id', 'nome_razao_social')
            ->orderBy('nome_razao_social')
            ->get();

        return view('orcamentos.index', compact('orcamentos', 'condominiosData', 'prestadores'));
    }

    public function show(Orcamento $orcamento)
    {
        if ($orcamento->demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $orcamento->load([
            'demanda.condominio',
            'demanda.categoriaServico',
            'demanda.orcamentos', // Carrega todos os orçamentos da demanda para verificar se já existe um aprovado
            'prestador',
            'documentos',
            'negociacoes' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        return view('orcamentos.show', compact('orcamento'));
    }

    public function aprovar(Orcamento $orcamento)
    {
        if ($orcamento->demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        // Usa transação para garantir consistência
        DB::transaction(function() use ($orcamento) {
            // Rejeita automaticamente todos os outros orçamentos da demanda
            \App\Models\Orcamento::where('demanda_id', $orcamento->demanda_id)
                ->where('id', '!=', $orcamento->id)
                ->where('status', '!=', 'rejeitado')
                ->update([
                    'status' => 'rejeitado',
                    'motivo_rejeicao' => 'Outro orçamento foi aprovado para esta demanda.',
                ]);

            // Aprova o orçamento selecionado
            $orcamento->aprovar(Auth::id());

            // Atualiza status da demanda
            $orcamento->demanda->update(['status' => 'em_andamento']);
        });

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento aprovado com sucesso! Os demais orçamentos foram automaticamente rejeitados.');
    }

    public function rejeitar(Request $request, Orcamento $orcamento)
    {
        if ($orcamento->demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $request->validate([
            'motivo' => 'required|string',
        ]);

        $orcamento->rejeitar($request->motivo);

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento rejeitado.');
    }
}
