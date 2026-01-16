<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrcamentoController extends Controller
{
    public function index()
    {
        $orcamentos = Orcamento::whereHas('demanda', function($q) {
                $q->where('empresa_id', Auth::user()->empresa_id);
            })
            ->with(['demanda', 'prestador'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('orcamentos.index', compact('orcamentos'));
    }

    public function aprovar(Orcamento $orcamento)
    {
        if ($orcamento->demanda->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $orcamento->aprovar(Auth::id());

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento aprovado com sucesso!');
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
