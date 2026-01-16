<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::daEmpresa(Auth::user()->empresa_id)
            ->orderBy('ordem')
            ->orderBy('nome')
            ->paginate(20);

        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:prestador,condominio,ambos',
            'ordem' => 'nullable|integer|min:0',
        ]);

        $validated['empresa_id'] = Auth::user()->empresa_id;
        $validated['ativo'] = true;

        Tag::create($validated);

        return redirect()->route('tags.index')
            ->with('success', 'Tag criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        if ($tag->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $tag->load(['prestadores', 'condominios']);

        return view('tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        if ($tag->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        if ($tag->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:prestador,condominio,ambos',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'boolean',
        ]);

        $tag->update($validated);

        return redirect()->route('tags.index')
            ->with('success', 'Tag atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        if ($tag->empresa_id !== Auth::user()->empresa_id) {
            abort(403);
        }

        $tag->delete();

        return redirect()->route('tags.index')
            ->with('success', 'Tag removida com sucesso!');
    }
}
