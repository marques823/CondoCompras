<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Administradora;
use App\Models\Condominio;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        $query = User::with(['administradora', 'condominio', 'roles'])->orderBy('name');

        if ($user->isAdministradora()) {
            // Administradora só vê Gerentes da sua empresa
            $query->whereHas('roles', function($q) {
                $q->where('name', 'gerente');
            });
        } elseif ($user->isGerente()) {
            // Gerente só vê Zeladores da sua empresa
            $query->whereHas('roles', function($q) {
                $q->where('name', 'zelador');
            });
        } elseif ($user->isAdmin()) {
            // Super Admin vê usuários Administradora para gerenciar empresas
            $query->whereHas('roles', function($q) {
                $q->where('name', 'administradora');
            });
        } else {
            // Outros perfis (Zelador) não devem listar usuários
            abort(403, 'Acesso negado.');
        }

        $users = $query->paginate(15);

        return view('users.index', compact('users'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        $administradoras = $user->isAdmin() 
            ? Administradora::orderBy('nome')->get() 
            : Administradora::where('id', $user->administradora_id)->get();

        $condominios = Condominio::orderBy('nome')->get();

        $perfis = [];
        if ($user->isAdmin()) {
            $perfis = ['admin' => 'Super Admin', 'administradora' => 'Administradora'];
        } elseif ($user->isAdministradora()) {
            $perfis = ['gerente' => 'Gerente'];
        } elseif ($user->isGerente()) {
            $perfis = ['zelador' => 'Zelador'];
        }

        return view('users.create', compact('administradoras', 'condominios', 'perfis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'telefone' => 'nullable|string|max:20|unique:users,telefone',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'perfil' => 'required|string',
            'administradora_id' => $user->isAdmin() ? 'nullable|exists:administradoras,id' : 'nullable',
            'condominio_id' => 'nullable|exists:condominios,id',
        ]);

        // Validação: Administradora só pode criar Gerentes
        if ($user->isAdministradora() && $validated['perfil'] !== 'gerente') {
            return back()->withErrors(['perfil' => 'Administradoras só podem criar usuários do tipo Gerente.'])->withInput();
        }

        // Validação: Gerente só pode criar Zeladores
        if ($user->isGerente() && $validated['perfil'] !== 'zelador') {
            return back()->withErrors(['perfil' => 'Gerentes só podem criar usuários do tipo Zelador.'])->withInput();
        }

        $validated['administradora_id'] = $user->isAdmin() ? $validated['administradora_id'] : $user->administradora_id;
        
        if ($validated['perfil'] === 'zelador' && empty($validated['email'])) {
            $validated['email'] = null;
        }

        $validated['password'] = Hash::make($validated['password']);

        $newUser = User::create($validated);

        // Atribui a Role
        $role = Role::where('name', $validated['perfil'])->first();
        if ($role) {
            $newUser->roles()->attach($role);
        }

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $userModel = User::with(['administradora', 'condominio', 'roles'])->findOrFail($id);

        // Bloqueio de visualização
        if ($user->isAdministradora() && !$userModel->hasRole('gerente')) {
            abort(403);
        }
        if ($user->isGerente() && !$userModel->hasRole('zelador')) {
            abort(403);
        }
        if ($user->isAdmin() && !$userModel->hasRole('administradora')) {
            // Super Admin na área de usuários vê apenas donos de Administradora
            // Os outros usuários ele vê através dos dashboards das empresas
            abort(403);
        }

        return view('users.show', compact('userModel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $userModel = User::findOrFail($id);
        
        // Bloqueio de edição
        if ($user->isAdministradora()) {
            if ($userModel->administradora_id !== $user->administradora_id || !$userModel->hasRole('gerente')) {
                abort(403);
            }
        } elseif ($user->isGerente()) {
            if ($userModel->administradora_id !== $user->administradora_id || !$userModel->hasRole('zelador')) {
                abort(403);
            }
        } elseif (!$user->isAdmin()) {
            abort(403);
        }

        $administradoras = $user->isAdmin() 
            ? Administradora::orderBy('nome')->get() 
            : Administradora::where('id', $user->administradora_id)->get();

        $condominios = Condominio::orderBy('nome')->get();

        $perfis = [];
        if ($user->isAdmin()) {
            $perfis = ['admin' => 'Super Admin', 'administradora' => 'Administradora'];
        } elseif ($user->isAdministradora()) {
            $perfis = ['gerente' => 'Gerente'];
        } elseif ($user->isGerente()) {
            $perfis = ['zelador' => 'Zelador'];
        }

        return view('users.edit', compact('userModel', 'administradoras', 'condominios', 'perfis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $userModel = User::findOrFail($id);
        
        // Bloqueio de segurança baseado em Role
        if ($user->isAdministradora()) {
            if ($userModel->administradora_id !== $user->administradora_id || !$userModel->hasRole('gerente')) {
                abort(403, 'Acesso negado. Administradoras só gerenciam Gerentes.');
            }
        } elseif ($user->isGerente()) {
            if ($userModel->administradora_id !== $user->administradora_id || !$userModel->hasRole('zelador')) {
                abort(403, 'Acesso negado. Gerentes só gerenciam Zeladores.');
            }
        } elseif (!$user->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'telefone' => 'nullable|string|max:20|unique:users,telefone,' . $id,
            'password' => 'nullable|confirmed|min:8',
            'perfil' => 'required|string',
            'administradora_id' => $user->isAdmin() ? 'nullable|exists:administradoras,id' : 'nullable',
            'condominio_id' => 'nullable|exists:condominios,id',
        ]);

        // Garante que o perfil não seja alterado para um nível superior
        if ($user->isAdministradora() && $validated['perfil'] !== 'gerente') {
            return back()->withErrors(['perfil' => 'Administradoras só podem manter usuários como Gerente.'])->withInput();
        }
        if ($user->isGerente() && $validated['perfil'] !== 'zelador') {
            return back()->withErrors(['perfil' => 'Gerentes só podem manter usuários como Zelador.'])->withInput();
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $userModel->update($validated);

        // Atualiza a Role
        $role = Role::where('name', $validated['perfil'])->first();
        if ($role) {
            $userModel->roles()->sync([$role->id]);
        }

        return redirect()->route('users.index')->with('success', 'Usuário atualizado!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if ($user->id == $id) {
            return redirect()->back()->with('error', 'Não pode excluir a si mesmo.');
        }

        $userModel = User::findOrFail($id);
        
        // Bloqueio de segurança baseado em Role
        if ($user->isAdministradora()) {
            if ($userModel->administradora_id !== $user->administradora_id || !$userModel->hasRole('gerente')) {
                abort(403, 'Acesso negado. Administradoras só excluem Gerentes.');
            }
        } elseif ($user->isGerente()) {
            if ($userModel->administradora_id !== $user->administradora_id || !$userModel->hasRole('zelador')) {
                abort(403, 'Acesso negado. Gerentes só excluem Zeladores.');
            }
        } elseif (!$user->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $userModel->delete();
        return redirect()->route('users.index')->with('success', 'Usuário excluído!');
    }


}
