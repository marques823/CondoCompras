<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Condominio;
use App\Models\Prestador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Documento::daEmpresa(Auth::user()->empresa_id)
            ->with(['condominio', 'demanda.categoriaServico', 'demanda.condominio', 'orcamento', 'prestador']);

        // Filtro por pesquisa (nome do arquivo, condomínio)
        if ($request->filled('pesquisa')) {
            $pesquisa = $request->pesquisa;
            $query->where(function($q) use ($pesquisa) {
                $q->where('nome_original', 'like', "%{$pesquisa}%")
                  ->orWhereHas('condominio', function($qCondo) use ($pesquisa) {
                      $qCondo->where('nome', 'like', "%{$pesquisa}%");
                  })
                  ->orWhereHas('demanda.condominio', function($qCondo) use ($pesquisa) {
                      $qCondo->where('nome', 'like', "%{$pesquisa}%");
                  });
            });
        }

        // Filtro por condomínio
        if ($request->filled('condominio_id')) {
            $query->where('condominio_id', $request->condominio_id);
        }

        // Filtro por tipo de serviço (através da demanda)
        if ($request->filled('categoria_servico_id')) {
            $query->whereHas('demanda', function($q) use ($request) {
                $q->where('categoria_servico_id', $request->categoria_servico_id);
            });
        }

        // Filtro por prestador
        if ($request->filled('prestador_id')) {
            $query->where('prestador_id', $request->prestador_id);
        }

        // Filtro por tipo de documento
        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por data de validade (data_documento)
        if ($request->filled('data_inicio')) {
            $query->where('data_documento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('data_documento', '<=', $request->data_fim);
        }

        // Filtro por status (através da demanda ou orçamento relacionado)
        if ($request->filled('status')) {
            if ($request->status === 'com_demanda_ativa') {
                $query->whereHas('demanda', function($q) {
                    $q->whereIn('status', ['aberta', 'em_andamento', 'aguardando_orcamento']);
                });
            } elseif ($request->status === 'com_orcamento_aprovado') {
                $query->whereHas('orcamento', function($q) {
                    $q->where('status', 'aprovado');
                });
            } elseif ($request->status === 'sem_relacao') {
                $query->whereNull('demanda_id')->whereNull('orcamento_id');
            }
        }

        // Ordenação
        $ordenarColuna = $request->get('ordenar_coluna', 'created_at');
        $ordenarDirecao = $request->get('ordenar_direcao', 'desc');
        
        $colunasPermitidas = ['nome_original', 'tipo', 'data_documento', 'created_at'];
        if (!in_array($ordenarColuna, $colunasPermitidas)) {
            $ordenarColuna = 'created_at';
        }
        
        if (!in_array($ordenarDirecao, ['asc', 'desc'])) {
            $ordenarDirecao = 'desc';
        }
        
        $query->orderBy($ordenarColuna, $ordenarDirecao);
        
        // Ordenação secundária
        if ($ordenarColuna !== 'created_at') {
            $query->orderBy('created_at', 'desc');
        }

        $documentos = $query->paginate(15)->withQueryString();

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

        $categorias = \App\Models\CategoriaServico::ativas()
            ->orderBy('nome')
            ->get();

        return view('documentos.index', compact('documentos', 'condominiosData', 'prestadores', 'categorias'));
    }

    /**
     * Visualiza o documento em nova aba
     */
    public function visualizar(Documento $documento)
    {
        // Verifica se o documento pertence à empresa do usuário
        if ($documento->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        // Verifica se o arquivo existe
        if (!Storage::disk('public')->exists($documento->caminho)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $caminhoCompleto = Storage::disk('public')->path($documento->caminho);

        return response()->file($caminhoCompleto, [
            'Content-Type' => $documento->mime_type,
            'Content-Disposition' => 'inline; filename="' . $documento->nome_original . '"',
        ]);
    }

    /**
     * Faz o download do documento
     */
    public function download(Documento $documento)
    {
        // Verifica se o documento pertence à empresa do usuário
        if ($documento->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        // Verifica se o arquivo existe
        if (!Storage::disk('public')->exists($documento->caminho)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk('public')->download($documento->caminho, $documento->nome_original);
    }
}
