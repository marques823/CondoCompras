<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZeladorController extends Controller
{
    /**
     * Dashboard do zelador
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $condominio = $user->condominio;
        
        if (!$condominio) {
            return view('zelador.no_condo'); // Caso o zelador não tenha condomínio vinculado
        }
        
        // Estatísticas (Global Scope já garante isolamento por administradora, e aqui filtramos por condomínio)
        $stats = [
            'total_demandas' => $condominio->demandas()->count(),
            'demandas_abertas' => $condominio->demandas()->where('status', 'aberta')->count(),
            'demandas_em_andamento' => $condominio->demandas()->where('status', 'em_andamento')->count(),
            'demandas_recentes' => $condominio->demandas()->with('categoriaServico')->orderBy('created_at', 'desc')->limit(10)->get(),
        ];

        return view('zelador.dashboard', compact('condominio', 'stats'));
    }
}
