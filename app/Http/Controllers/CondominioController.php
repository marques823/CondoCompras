<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\LinkCondominio;
use App\Models\User;
use App\Models\Zelador;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CondominioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Condominio::class);
        
        $condominios = Condominio::with(['tags', 'gerente'])
            ->orderBy('nome')
            ->paginate(15);

        return view('condominios.index', compact('condominios'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Condominio::class);

        $tags = \App\Models\Tag::porTipo('condominio')
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
        $this->authorize('create', Condominio::class);

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
            'tags.*' => 'exists:tags,id',
            // Campos do zelador (opcionais)
            'zelador_nome' => 'nullable|string|max:255',
            'zelador_telefone' => 'nullable|string|max:20|unique:users,telefone',
            'zelador_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $validated['administradora_id'] = $user->administradora_id;
        
        // Se quem está criando for um Gerente, ele é o gerente responsável
        if ($user->isGerente()) {
            $validated['gerente_id'] = $user->id;
        }

        $validated['ativo'] = true;

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $condominio = Condominio::create($validated);

        // Associa tags
        if (!empty($tags)) {
            $condominio->tags()->sync($tags);
        }

        // Cria usuário zelador se os campos foram preenchidos
        if ($request->filled('zelador_nome') && $request->filled('zelador_telefone') && $request->filled('zelador_password')) {
            $zeladorUser = User::create([
                'name' => $validated['zelador_nome'],
                'telefone' => $validated['zelador_telefone'],
                'email' => null,
                'password' => Hash::make($validated['zelador_password']),
                'administradora_id' => $user->administradora_id,
                'condominio_id' => $condominio->id,
                'perfil' => 'zelador',
            ]);

            // Atribui a role de zelador
            $zeladorRole = Role::where('name', 'zelador')->first();
            if ($zeladorRole) {
                $zeladorUser->roles()->attach($zeladorRole);
            }

            // Cria o registro na tabela zeladores
            Zelador::create([
                'user_id' => $zeladorUser->id,
                'condominio_id' => $condominio->id,
                'administradora_id' => $user->administradora_id,
                'ativo' => true,
            ]);
        }

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $condominio = Condominio::with(['demandas', 'documentos', 'tags', 'links', 'gerente'])
            ->findOrFail($id);

        $this->authorize('view', $condominio);

        return view('condominios.show', compact('condominio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $condominio = Condominio::findOrFail($id);
        
        $this->authorize('update', $condominio);

        $tags = \App\Models\Tag::porTipo('condominio')
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
        $condominio = Condominio::findOrFail($id);
        
        $this->authorize('update', $condominio);

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
            'tags.*' => 'exists:tags,id',
        ]);

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $condominio->update($validated);
        $condominio->tags()->sync($tags);

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $condominio = Condominio::findOrFail($id);
        
        $this->authorize('delete', $condominio);

        $condominio->delete();

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio removido com sucesso!');
    }

    public function gerarLink(Request $request, $id)
    {
        $condominio = Condominio::findOrFail($id);
        $this->authorize('update', $condominio);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
        ]);

        LinkCondominio::create([
            'condominio_id' => $condominio->id,
            'administradora_id' => $condominio->administradora_id,
            'token' => LinkCondominio::gerarToken(),
            'titulo' => $validated['titulo'],
            'ativo' => true,
        ]);

        return redirect()->back()->with('success', 'Link gerado com sucesso!');
    }

    public function desativarLink($condominioId, $linkId)
    {
        $condominio = Condominio::findOrFail($condominioId);
        $this->authorize('update', $condominio);

        $link = LinkCondominio::where('condominio_id', $condominio->id)->findOrFail($linkId);
        $link->update(['ativo' => false]);

        return redirect()->back()->with('success', 'Link desativado!');
    }
}
