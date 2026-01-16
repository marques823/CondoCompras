<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CondominioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $condominios = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->orderBy('nome')
            ->paginate(15);

        return view('condominios.index', compact('condominios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('condominios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'endereco' => 'required|string',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'sindico_nome' => 'nullable|string|max:255',
            'sindico_telefone' => 'nullable|string|max:20',
            'sindico_email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $validated['empresa_id'] = Auth::user()->empresa_id;
        $validated['ativo'] = true;

        Condominio::create($validated);

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Condominio $condominio)
    {
        // Verifica se pertence à empresa do usuário
        if ($condominio->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $condominio->load(['demandas', 'documentos']);

        return view('condominios.show', compact('condominio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Condominio $condominio)
    {
        // Verifica se pertence à empresa do usuário
        if ($condominio->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        return view('condominios.edit', compact('condominio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Condominio $condominio)
    {
        // Verifica se pertence à empresa do usuário
        if ($condominio->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'endereco' => 'required|string',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'sindico_nome' => 'nullable|string|max:255',
            'sindico_telefone' => 'nullable|string|max:20',
            'sindico_email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $condominio->update($validated);

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Condominio $condominio)
    {
        // Verifica se pertence à empresa do usuário
        if ($condominio->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $condominio->delete();

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio removido com sucesso!');
    }
}
