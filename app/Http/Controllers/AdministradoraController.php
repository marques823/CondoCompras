<?php

namespace App\Http\Controllers;

use App\Models\Administradora;
use App\Models\User;
use App\Models\Condominio;
use App\Models\Demanda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdministradoraController extends Controller
{
    /**
     * Dashboard da Administradora
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $stats = [
            'total_condominios' => Condominio::count(),
            'total_gerentes' => User::whereHas('roles', fn($q) => $q->where('name', 'gerente'))->count(),
            'total_demandas' => Demanda::count(),
            'demandas_abertas' => Demanda::where('status', 'aberta')->count(),
        ];

        return view('administradora.dashboard', compact('stats'));
    }

    /**
     * CRUD de Administradoras (Apenas Super Admin)
     */
    public function index()
    {
        $this->authorize('admin');
        $administradoras = Administradora::withCount(['usuarios', 'condominios', 'prestadores'])
            ->orderBy('nome')
            ->paginate(15);
        return view('administradoras.index', compact('administradoras'));
    }

    public function create()
    {
        $this->authorize('admin');
        return view('administradoras.create');
    }

    public function store(Request $request)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:administradoras,cnpj',
            'email' => 'required|email|unique:administradoras,email',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
        ]);

        $validated['ativo'] = true;
        Administradora::create($validated);

        return redirect()->route('administradoras.index')->with('success', 'Administradora criada!');
    }

    public function show(Administradora $administradora)
    {
        $this->authorize('admin');
        $administradora->loadCount(['usuarios', 'condominios', 'prestadores', 'demandas']);
        $administradora->load(['condominios', 'usuarios']);
        
        // Passa como $empresa para manter compatibilidade com a view que usa essa variável
        $empresa = $administradora;
        return view('administradoras.show', compact('empresa'));
    }

    public function edit(Administradora $administradora)
    {
        $this->authorize('admin');
        $empresa = $administradora;
        return view('administradoras.edit', compact('empresa'));
    }

    public function update(Request $request, Administradora $administradora)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:administradoras,cnpj,' . $administradora->id,
            'email' => 'required|email|unique:administradoras,email,' . $administradora->id,
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $administradora->update($validated);

        return redirect()->route('administradoras.index')->with('success', 'Administradora atualizada!');
    }

    public function destroy(Administradora $administradora)
    {
        $this->authorize('admin');
        $administradora->delete();
        return redirect()->route('administradoras.index')->with('success', 'Administradora removida!');
    }
}
