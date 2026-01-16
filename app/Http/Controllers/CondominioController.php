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
            ->with('tags')
            ->orderBy('nome')
            ->paginate(15);

        return view('condominios.index', compact('condominios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tags = \App\Models\Tag::daEmpresa(Auth::user()->empresa_id)
            ->porTipo('condominio')
            ->ativas()
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        return view('condominios.create', compact('tags'));
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
            'tags' => 'nullable|array',
            'tags.*' => [
                'exists:tags,id',
                function ($attribute, $value, $fail) {
                    $tag = \App\Models\Tag::find($value);
                    if ($tag && $tag->empresa_id !== Auth::user()->empresa_id) {
                        $fail('A tag selecionada não pertence à sua empresa.');
                    }
                },
            ],
        ]);

        $validated['empresa_id'] = Auth::user()->empresa_id;
        $validated['ativo'] = true;

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $condominio = Condominio::create($validated);

        // Associa tags
        if (!empty($tags)) {
            $condominio->tags()->sync($tags);
        }

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $condominio = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->with(['demandas', 'documentos', 'tags'])
            ->findOrFail($id);

        return view('condominios.show', compact('condominio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $condominio = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->findOrFail($id);

        $tags = \App\Models\Tag::daEmpresa(Auth::user()->empresa_id)
            ->porTipo('condominio')
            ->ativas()
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        $condominio->load('tags');

        return view('condominios.edit', compact('condominio', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $condominio = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->findOrFail($id);

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
            'tags' => 'nullable|array',
            'tags.*' => [
                'exists:tags,id',
                function ($attribute, $value, $fail) {
                    $tag = \App\Models\Tag::find($value);
                    if ($tag && $tag->empresa_id !== Auth::user()->empresa_id) {
                        $fail('A tag selecionada não pertence à sua empresa.');
                    }
                },
            ],
        ]);

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $condominio->update($validated);

        // Atualiza tags
        $condominio->tags()->sync($tags);

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $condominio = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->findOrFail($id);

        $condominio->delete();

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio removido com sucesso!');
    }
}
