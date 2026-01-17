<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prestador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrestadorApiController extends Controller
{
    /**
     * Lista todos os prestadores da empresa
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Prestador::daAdministradora($user->administradora_id)
            ->ativos()
            ->with(['categorias', 'regioes']);

        // Filtros
        if ($request->has('categoria_id')) {
            $query->whereHas('categorias', function ($q) use ($request) {
                $q->where('categorias_servicos.id', $request->categoria_id);
            });
        }

        if ($request->has('regiao_id')) {
            $query->whereHas('regioes', function ($q) use ($request) {
                $q->where('regioes.id', $request->regiao_id);
            });
        }

        $prestadores = $query->paginate($request->get('per_page', 15));

        return response()->json($prestadores);
    }

    /**
     * Exibe um prestador específico
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $prestador = Prestador::daAdministradora($user->administradora_id)
            ->with(['categorias', 'regioes', 'empresa'])
            ->findOrFail($id);

        return response()->json($prestador);
    }
}
