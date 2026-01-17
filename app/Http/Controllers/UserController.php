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
        // O Global Scope cuida da filtragem por administradora
        $users = User::with(['administradora', 'condominio', 'roles'])
            ->orderBy('name')
            ->paginate(15);

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
        $userModel = User::with(['administradora', 'condominio', 'roles'])->findOrFail($id);
        return view('users.show', compact('userModel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $userModel = User::findOrFail($id);
        
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
        $userModel = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'telefone' => 'nullable|string|max:20|unique:users,telefone,' . $id,
            'password' => 'nullable|confirmed|min:8',
            'perfil' => 'required|string',
            'administradora_id' => auth()->user()->isAdmin() ? 'nullable|exists:administradoras,id' : 'nullable',
            'condominio_id' => 'nullable|exists:condominios,id',
        ]);

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
        if (auth()->id() == $id) {
            return redirect()->back()->with('error', 'Não pode excluir a si mesmo.');
        }

        User::findOrFail($id)->delete();
        return redirect()->route('users.index')->with('success', 'Usuário excluído!');
    }
}
