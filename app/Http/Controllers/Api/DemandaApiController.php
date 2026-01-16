<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Demanda;
use App\Models\LinkPrestador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DemandaApiController extends Controller
{
    /**
     * Lista todas as demandas da empresa do usuário autenticado
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Demanda::daEmpresa($user->empresa_id)
            ->with(['condominio', 'categoriaServico', 'usuario', 'prestadores']);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('condominio_id')) {
            $query->where('condominio_id', $request->condominio_id);
        }

        $demandas = $query->paginate($request->get('per_page', 15));

        return response()->json($demandas);
    }

    /**
     * Cria uma nova demanda
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'condominio_id' => 'required|exists:condominios,id',
            'categoria_servico_id' => 'nullable|exists:categorias_servicos,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'prazo_limite' => 'nullable|date|after:today',
            'prestadores' => 'nullable|array',
            'prestadores.*' => 'exists:prestadores,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Verifica se o condomínio pertence à empresa do usuário
        $condominio = \App\Models\Condominio::where('id', $request->condominio_id)
            ->where('empresa_id', $user->empresa_id)
            ->firstOrFail();

        $demanda = Demanda::create([
            'empresa_id' => $user->empresa_id,
            'condominio_id' => $request->condominio_id,
            'categoria_servico_id' => $request->categoria_servico_id,
            'usuario_id' => $user->id,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'prazo_limite' => $request->prazo_limite,
            'status' => 'aberta',
        ]);

        // Associa prestadores se fornecidos
        if ($request->has('prestadores') && is_array($request->prestadores)) {
            $prestadoresIds = array_filter($request->prestadores, function ($id) use ($user) {
                return \App\Models\Prestador::where('id', $id)
                    ->where('empresa_id', $user->empresa_id)
                    ->exists();
            });

            if (!empty($prestadoresIds)) {
                // Cria relações na tabela pivot
                foreach ($prestadoresIds as $prestadorId) {
                    $demanda->prestadores()->attach($prestadorId, [
                        'status' => 'convidado',
                    ]);
                }

                // Gera links únicos para cada prestador
                \App\Http\Controllers\LinkPrestadorController::gerarLinksParaDemanda(
                    $demanda,
                    $prestadoresIds
                );
            }
        }

        $demanda->load(['condominio', 'categoriaServico', 'usuario', 'prestadores', 'links']);

        return response()->json([
            'message' => 'Demanda criada com sucesso',
            'data' => $demanda
        ], 201);
    }

    /**
     * Exibe uma demanda específica
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $demanda = Demanda::daEmpresa($user->empresa_id)
            ->with(['condominio', 'categoriaServico', 'usuario', 'prestadores', 'orcamentos', 'documentos', 'links'])
            ->findOrFail($id);

        return response()->json($demanda);
    }

    /**
     * Atualiza uma demanda
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        $demanda = Demanda::daEmpresa($user->empresa_id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|string|max:255',
            'descricao' => 'sometimes|string',
            'status' => 'sometimes|in:aberta,em_andamento,aguardando_orcamento,concluida,cancelada',
            'prazo_limite' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $demanda->update($request->only(['titulo', 'descricao', 'status', 'prazo_limite', 'observacoes']));

        $demanda->load(['condominio', 'categoriaServico', 'usuario', 'prestadores']);

        return response()->json([
            'message' => 'Demanda atualizada com sucesso',
            'data' => $demanda
        ]);
    }

    /**
     * Remove uma demanda
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        $demanda = Demanda::daEmpresa($user->empresa_id)->findOrFail($id);
        $demanda->delete();

        return response()->json([
            'message' => 'Demanda removida com sucesso'
        ]);
    }
}
