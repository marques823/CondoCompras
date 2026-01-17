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
        
        // Carrega a administradora do usuário
        $empresa = $user->administradora;
        
        if (!$empresa) {
            return redirect()->route('dashboard')->with('error', 'Usuário sem administradora vinculada.');
        }
        
        // Estatísticas filtradas pela administradora
        $stats = [
            'total_condominios' => Condominio::where('administradora_id', $empresa->id)->count(),
            'total_gerentes' => User::where('administradora_id', $empresa->id)
                ->whereHas('roles', fn($q) => $q->where('name', 'gerente'))
                ->count(),
            'total_demandas' => Demanda::where('administradora_id', $empresa->id)->count(),
            'demandas_abertas' => Demanda::where('administradora_id', $empresa->id)
                ->where('status', 'aberta')
                ->count(),
            'demandas_em_andamento' => Demanda::where('administradora_id', $empresa->id)
                ->where('status', 'em_andamento')
                ->count(),
            'demandas_concluidas' => Demanda::where('administradora_id', $empresa->id)
                ->where('status', 'concluida')
                ->count(),
        ];
        
        // Variáveis para compatibilidade com a view
        $totalCondominios = $stats['total_condominios'];
        $totalPrestadores = \App\Models\Prestador::where('administradora_id', $empresa->id)->count();
        $totalDemandas = $stats['total_demandas'];
        $totalOrcamentos = \App\Models\Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->count();
        $demandasAbertas = $stats['demandas_abertas'];
        $demandasEmAndamento = $stats['demandas_em_andamento'];
        $demandasConcluidas = $stats['demandas_concluidas'];
        
        // Condomínios recentes
        $condominiosRecentes = Condominio::where('administradora_id', $empresa->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Demandas recentes
        $demandasRecentes = Demanda::where('administradora_id', $empresa->id)
            ->with(['condominio', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('administradora.dashboard', compact(
            'empresa',
            'totalCondominios',
            'totalPrestadores',
            'totalDemandas',
            'totalOrcamentos',
            'demandasAbertas',
            'demandasEmAndamento',
            'demandasConcluidas',
            'condominiosRecentes',
            'demandasRecentes'
        ));
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

    /**
     * Mostra formulário para editar configurações da própria administradora
     */
    public function editConfig()
    {
        $user = Auth::user();
        
        if (!$user->isAdministradora() || !$user->administradora_id) {
            abort(403, 'Acesso negado.');
        }
        
        $empresa = $user->administradora;
        return view('administradora.config', compact('empresa'));
    }

    /**
     * Atualiza configurações da própria administradora
     */
    public function updateConfig(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdministradora() || !$user->administradora_id) {
            abort(403, 'Acesso negado.');
        }
        
        $empresa = $user->administradora;

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:administradoras,cnpj,' . $empresa->id,
            'email' => 'required|email|unique:administradoras,email,' . $empresa->id,
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
        ]);

        $empresa->update($validated);

        return redirect()->route('administradora.config')->with('success', 'Configurações atualizadas com sucesso!');
    }

    public function destroy(Administradora $administradora)
    {
        $this->authorize('admin');
        $administradora->delete();
        return redirect()->route('administradoras.index')->with('success', 'Administradora removida!');
    }
}
