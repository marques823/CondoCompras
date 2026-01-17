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
        
        if (!$user->isZelador() || !$user->condominio_id) {
            abort(403, 'Acesso negado. Você não é um zelador.');
        }

        $condominio = $user->condominio;
        
        // Estatísticas
        $totalDemandas = $condominio->demandas()->count();
        $demandasAbertas = $condominio->demandas()->where('status', 'aberta')->count();
        $demandasEmAndamento = $condominio->demandas()->where('status', 'em_andamento')->count();
        $demandasConcluidas = $condominio->demandas()->where('status', 'concluida')->count();

        // Demandas recentes
        $demandasRecentes = $condominio->demandas()
            ->with('categoriaServico')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('zelador.dashboard', compact('condominio', 'totalDemandas', 'demandasAbertas', 'demandasEmAndamento', 'demandasConcluidas', 'demandasRecentes'));
    }
}
