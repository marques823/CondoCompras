<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\Condominio;
use App\Models\Prestador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandaController extends Controller
{
    public function index()
    {
        $demandas = Demanda::daEmpresa(Auth::user()->empresa_id)
            ->with(['condominio', 'categoriaServico', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('demandas.index', compact('demandas'));
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

        return view('demandas.create', compact('condominios', 'prestadores', 'tags', 'condominiosData'));
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

        $demanda->load(['condominio', 'categoriaServico', 'usuario', 'prestadores', 'orcamentos', 'links']);

        return view('demandas.show', compact('demanda'));
    }
}
