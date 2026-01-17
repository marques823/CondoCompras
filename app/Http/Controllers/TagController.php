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
        $this->authorize('viewAny', Tag::class);
        
        $tags = Tag::orderBy('ordem')
            ->orderBy('nome')
            ->paginate(20);

        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Tag::class);
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Tag::class);
        
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:prestador,condominio,ambos',
            'ordem' => 'nullable|integer|min:0',
        ]);

        $validated['administradora_id'] = Auth::user()->administradora_id;
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
        $this->authorize('view', $tag);

        $tag->load(['prestadores', 'condominios']);

        return view('tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        $this->authorize('update', $tag);

        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $this->authorize('update', $tag);

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
        $this->authorize('delete', $tag);

        $tag->delete();

        return redirect()->route('tags.index')
            ->with('success', 'Tag removida com sucesso!');
    }
}
