<?php

namespace App\Http\Controllers;

use App\Models\Prestador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrestadorController extends Controller
{
    public function index()
    {
        $prestadores = Prestador::daEmpresa(Auth::user()->empresa_id)
            ->orderBy('nome_razao_social')
            ->paginate(15);

        return view('prestadores.index', compact('prestadores'));
    }

    public function create()
    {
        return view('prestadores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'tipo' => 'required|in:fisica,juridica',
            'cpf_cnpj' => 'nullable|string|max:18',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ]);

        $validated['empresa_id'] = Auth::user()->empresa_id;
        $validated['ativo'] = true;

        Prestador::create($validated);

        return redirect()->route('prestadores.index')
            ->with('success', 'Prestador cadastrado com sucesso!');
    }

    public function show(Prestador $prestador)
    {
        if ($prestador->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        return view('prestadores.show', compact('prestador'));
    }

    public function edit(Prestador $prestador)
    {
        if ($prestador->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        return view('prestadores.edit', compact('prestador'));
    }

    public function update(Request $request, Prestador $prestador)
    {
        if ($prestador->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $validated = $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'tipo' => 'required|in:fisica,juridica',
            'cpf_cnpj' => 'nullable|string|max:18',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $prestador->update($validated);

        return redirect()->route('prestadores.index')
            ->with('success', 'Prestador atualizado com sucesso!');
    }

    public function destroy(Prestador $prestador)
    {
        if ($prestador->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $prestador->delete();

        return redirect()->route('prestadores.index')
            ->with('success', 'Prestador removido com sucesso!');
    }
}
