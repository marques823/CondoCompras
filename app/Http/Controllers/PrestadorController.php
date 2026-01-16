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
            ->with('tags')
            ->orderBy('nome_razao_social')
            ->paginate(15);

        return view('prestadores.index', compact('prestadores'));
    }

    public function create()
    {
        $tags = \App\Models\Tag::daEmpresa(Auth::user()->empresa_id)
            ->porTipo('prestador')
            ->ativas()
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        return view('prestadores.create', compact('tags'));
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
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'areas_atuacao' => 'nullable|string',
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

        $prestador = Prestador::create($validated);

        // Associa tags
        if (!empty($tags)) {
            $prestador->tags()->sync($tags);
        }

        return redirect()->route('prestadores.index')
            ->with('success', 'Prestador cadastrado com sucesso!');
    }

    public function show($id)
    {
        $prestador = Prestador::daEmpresa(Auth::user()->empresa_id)
            ->with('tags')
            ->findOrFail($id);

        return view('prestadores.show', compact('prestador'));
    }

    public function edit($id)
    {
        $prestador = Prestador::daEmpresa(Auth::user()->empresa_id)
            ->findOrFail($id);

        $tags = \App\Models\Tag::daEmpresa(Auth::user()->empresa_id)
            ->porTipo('prestador')
            ->ativas()
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        $prestador->load('tags');

        return view('prestadores.edit', compact('prestador', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $prestador = Prestador::daEmpresa(Auth::user()->empresa_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'nome_razao_social' => 'required|string|max:255',
            'tipo' => 'required|in:fisica,juridica',
            'cpf_cnpj' => 'nullable|string|max:18',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'areas_atuacao' => 'nullable|string',
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

        $prestador->update($validated);

        // Atualiza tags
        $prestador->tags()->sync($tags);

        return redirect()->route('prestadores.index')
            ->with('success', 'Prestador atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $prestador = Prestador::daEmpresa(Auth::user()->empresa_id)
            ->findOrFail($id);

        $prestador->delete();

        return redirect()->route('prestadores.index')
            ->with('success', 'Prestador removido com sucesso!');
    }
}
