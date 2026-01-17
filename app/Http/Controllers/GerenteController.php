<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Condominio;
use App\Models\Demanda;
use App\Models\User;

class GerenteController extends Controller
{
    /**
     * Dashboard do gerente
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Condomínios da administradora (filtrados pelo Global Scope)
        $condominios = Condominio::all();
        $condominioIds = $condominios->pluck('id');

        // Estatísticas
        $stats = [
            'total_condominios' => $condominios->count(),
            'total_zeladores' => User::whereIn('condominio_id', $condominioIds)->whereHas('roles', fn($q) => $q->where('name', 'zelador'))->count(),
            'total_demandas' => Demanda::whereIn('condominio_id', $condominioIds)->count(),
            'demandas_recentes' => Demanda::whereIn('condominio_id', $condominioIds)->with(['condominio', 'usuario'])->orderBy('created_at', 'desc')->limit(10)->get(),
        ];

        return view('gerente.dashboard', compact('stats'));
    }
}
