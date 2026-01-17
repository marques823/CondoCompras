<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\LinkCondominio;
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
            // Campos do zelador (opcionais)
            'zelador_nome' => 'nullable|string|max:255',
            'zelador_email' => 'nullable|email|max:255|unique:users,email',
            'zelador_password' => 'nullable|string|min:8|confirmed',
        ], [
            'zelador_email.unique' => 'Este e-mail já está cadastrado no sistema.',
            'zelador_password.confirmed' => 'A confirmação da senha não confere.',
            'zelador_password.min' => 'A senha deve ter no mínimo 8 caracteres.',
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

        // Cria usuário zelador se os campos foram preenchidos
        if ($request->filled('zelador_nome') && $request->filled('zelador_email') && $request->filled('zelador_password')) {
            \App\Models\User::create([
                'name' => $validated['zelador_nome'],
                'email' => $validated['zelador_email'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['zelador_password']),
                'empresa_id' => Auth::user()->empresa_id,
                'condominio_id' => $condominio->id,
                'perfil' => 'zelador',
            ]);
        }

        return redirect()->route('condominios.index')
            ->with('success', 'Condomínio cadastrado com sucesso!' . ($request->filled('zelador_nome') ? ' Usuário zelador criado com sucesso!' : ''));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $condominio = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->with(['demandas', 'documentos', 'tags', 'links'])
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

        // Verifica se já existe zelador para este condomínio
        $zeladorExistente = \App\Models\User::where('condominio_id', $condominio->id)
            ->where('perfil', 'zelador')
            ->first();

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
            // Campos do zelador (opcionais)
            'zelador_nome' => 'nullable|string|max:255',
            'zelador_email' => [
                'nullable',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($zeladorExistente) {
                    if ($value && $zeladorExistente && $zeladorExistente->email !== $value) {
                        // Se está editando e mudou o email, verifica se o novo email já existe
                        if (\App\Models\User::where('email', $value)->where('id', '!=', $zeladorExistente->id)->exists()) {
                            $fail('Este e-mail já está cadastrado no sistema.');
                        }
                    } elseif ($value && !$zeladorExistente) {
                        // Se está criando novo, verifica se o email já existe
                        if (\App\Models\User::where('email', $value)->exists()) {
                            $fail('Este e-mail já está cadastrado no sistema.');
                        }
                    }
                },
            ],
            'zelador_password' => [
                'nullable',
                'string',
                'min:8',
                function ($attribute, $value, $fail) use ($request, $zeladorExistente) {
                    // Se está criando novo zelador, senha é obrigatória
                    if (!$zeladorExistente && $request->filled('zelador_nome') && $request->filled('zelador_email') && !$request->filled('zelador_password')) {
                        $fail('A senha é obrigatória ao criar um novo zelador.');
                    }
                    // Se preencheu senha, confirmação é obrigatória
                    if ($request->filled('zelador_password') && !$request->filled('zelador_password_confirmation')) {
                        $fail('A confirmação da senha é obrigatória.');
                    }
                    // Verifica se senha e confirmação conferem
                    if ($request->filled('zelador_password') && $request->input('zelador_password') !== $request->input('zelador_password_confirmation')) {
                        $fail('A confirmação da senha não confere.');
                    }
                },
            ],
        ], [
            'zelador_password.confirmed' => 'A confirmação da senha não confere.',
            'zelador_password.min' => 'A senha deve ter no mínimo 8 caracteres.',
        ]);

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $condominio->update($validated);

        // Atualiza tags
        $condominio->tags()->sync($tags);

        // Gerencia usuário zelador
        $zeladorExistente = \App\Models\User::where('condominio_id', $condominio->id)
            ->where('perfil', 'zelador')
            ->first();

        if ($request->filled('zelador_nome') && $request->filled('zelador_email')) {
            if ($zeladorExistente) {
                // Atualiza zelador existente
                $updateData = [
                    'name' => $validated['zelador_nome'],
                    'email' => $validated['zelador_email'],
                ];
                
                if ($request->filled('zelador_password')) {
                    $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validated['zelador_password']);
                }
                
                $zeladorExistente->update($updateData);
            } else {
                // Cria novo zelador (senha é obrigatória)
                if ($request->filled('zelador_password')) {
                    \App\Models\User::create([
                        'name' => $validated['zelador_nome'],
                        'email' => $validated['zelador_email'],
                        'password' => \Illuminate\Support\Facades\Hash::make($validated['zelador_password']),
                        'empresa_id' => Auth::user()->empresa_id,
                        'condominio_id' => $condominio->id,
                        'perfil' => 'zelador',
                    ]);
                }
            }
        }

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

    /**
     * Gera um link único para o condomínio criar demandas
     */
    public function gerarLink(Request $request, $id)
    {
        $condominio = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'expira_em' => 'nullable|date|after:today',
        ]);

        $token = LinkCondominio::gerarToken();

        $link = LinkCondominio::create([
            'condominio_id' => $condominio->id,
            'empresa_id' => Auth::user()->empresa_id,
            'token' => $token,
            'titulo' => $validated['titulo'] ?? "Link para {$condominio->nome}",
            'ativo' => true,
            'expira_em' => $validated['expira_em'] ?? null,
        ]);

        $urlCompleta = route('publico.criar-demanda', ['token' => $token]);

        return redirect()->route('condominios.show', $condominio)
            ->with('success', 'Link gerado com sucesso!')
            ->with('link_gerado', $urlCompleta);
    }

    /**
     * Lista todos os links do condomínio
     */
    public function links($id)
    {
        $condominio = Condominio::daEmpresa(Auth::user()->empresa_id)
            ->findOrFail($id);

        $links = LinkCondominio::where('condominio_id', $condominio->id)
            ->where('empresa_id', Auth::user()->empresa_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('condominios.links', compact('condominio', 'links'));
    }

    /**
     * Desativa um link
     */
    public function desativarLink(Request $request, $condominioId, $linkId)
    {
        $link = LinkCondominio::where('condominio_id', $condominioId)
            ->where('empresa_id', Auth::user()->empresa_id)
            ->findOrFail($linkId);

        $link->update(['ativo' => false]);

        return redirect()->back()
            ->with('success', 'Link desativado com sucesso!');
    }
}
