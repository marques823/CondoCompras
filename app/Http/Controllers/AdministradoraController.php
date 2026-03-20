<?php

namespace App\Http\Controllers;

use App\Models\Administradora;
use App\Models\User;
use App\Models\Role;
use App\Models\Condominio;
use App\Models\Demanda;
use App\Models\Prestador;
use App\Models\Documento;
use App\Models\Orcamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

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
        
        // ===== MÉTRICAS PRINCIPAIS =====
        $totalCondominios = Condominio::where('administradora_id', $empresa->id)->count();
        $totalPrestadores = Prestador::where('administradora_id', $empresa->id)->count();
        $totalDemandas = Demanda::where('administradora_id', $empresa->id)->count();
        
        // ===== MÉTRICAS DE DEMANDAS =====
        $demandasAbertas = Demanda::where('administradora_id', $empresa->id)->where('status', 'aberta')->count();
        $demandasEmAndamento = Demanda::where('administradora_id', $empresa->id)->where('status', 'em_andamento')->count();
        $demandasAguardandoOrcamento = Demanda::where('administradora_id', $empresa->id)->where('status', 'aguardando_orcamento')->count();
        $demandasConcluidas = Demanda::where('administradora_id', $empresa->id)->where('status', 'concluida')->count();
        $demandasUrgentes = Demanda::where('administradora_id', $empresa->id)
            ->whereIn('status', ['aberta', 'em_andamento', 'aguardando_orcamento'])
            ->whereIn('urgencia', ['alta', 'critica'])
            ->count();
        
        // ===== MÉTRICAS DE ORÇAMENTOS =====
        $orcamentosPendentes = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->where('status', 'recebido')->count();
        
        $orcamentosAprovados = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->where('status', 'aprovado')->count();
        
        $totalOrcamentos = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->count();
        
        $taxaAprovacao = $totalOrcamentos > 0 ? round(($orcamentosAprovados / $totalOrcamentos) * 100, 1) : 0;
        
        // Orçamentos vencendo hoje
        $orcamentosVencendoHoje = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->whereNotNull('validade')
          ->whereDate('validade', today())
          ->where('status', 'aprovado')
          ->count();
        
        // Orçamentos vencendo esta semana
        $orcamentosVencendoSemana = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->whereNotNull('validade')
          ->whereBetween('validade', [today(), today()->addDays(7)])
          ->where('status', 'aprovado')
          ->count();
        
        // ===== MÉTRICAS DE NEGOCIAÇÕES =====
        $negociacoesPendentes = \App\Models\Negociacao::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->where('status', 'pendente')->count();
        
        // ===== MÉTRICAS DE SERVIÇOS CONCLUÍDOS =====
        $servicosConcluidos = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->where('concluido', true)->count();
        
        $servicosConcluidosHoje = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->where('concluido', true)
          ->whereDate('concluido_em', today())->count();
        
        // ===== LISTAS DE AÇÕES NECESSÁRIAS =====
        // Orçamentos que precisam de aprovação
        $orcamentosParaAprovar = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->where('status', 'recebido')
          ->with(['demanda' => function($q) {
              $q->with('condominio');
          }, 'prestador'])
          ->orderBy('created_at', 'desc')
          ->limit(5)
          ->get();
        
        // Negociações pendentes
        $negociacoesParaResponder = \App\Models\Negociacao::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->where('status', 'pendente')
          ->with(['demanda' => function($q) {
              $q->with('condominio');
          }, 'prestador', 'orcamento'])
          ->orderBy('created_at', 'desc')
          ->limit(5)
          ->get();
        
        // Demandas urgentes
        $demandasUrgentesLista = Demanda::where('administradora_id', $empresa->id)
            ->whereIn('status', ['aberta', 'em_andamento', 'aguardando_orcamento'])
            ->whereIn('urgencia', ['alta', 'critica'])
            ->with(['condominio', 'usuario'])
            ->orderByRaw("CASE WHEN urgencia = 'critica' THEN 1 WHEN urgencia = 'alta' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Serviços concluídos recentemente
        $servicosConcluidosRecentes = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->where('concluido', true)
          ->whereNotNull('concluido_em')
          ->with(['demanda' => function($q) {
              $q->with('condominio');
          }, 'prestador', 'concluidoPor'])
          ->orderBy('concluido_em', 'desc')
          ->limit(5)
          ->get();
        
        // Orçamentos vencendo
        $orcamentosVencendo = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->whereNotNull('validade')
          ->whereBetween('validade', [today(), today()->addDays(7)])
          ->where('status', 'aprovado')
          ->with(['demanda' => function($q) {
              $q->with('condominio');
          }, 'prestador'])
          ->orderBy('validade', 'asc')
          ->limit(5)
          ->get();
        
        // ===== ESTATÍSTICAS DO MÊS =====
        $demandasEsteMes = Demanda::where('administradora_id', $empresa->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $orcamentosEsteMes = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->whereMonth('created_at', now()->month)
          ->whereYear('created_at', now()->year)
          ->count();
        
        $servicosConcluidosEsteMes = Orcamento::whereHas('demanda', function($q) use ($empresa) {
            $q->where('administradora_id', $empresa->id);
        })->where('concluido', true)
          ->whereNotNull('concluido_em')
          ->whereMonth('concluido_em', now()->month)
          ->whereYear('concluido_em', now()->year)
          ->count();

        return view('administradora.dashboard', compact(
            'empresa',
            'totalCondominios',
            'totalPrestadores',
            'totalDemandas',
            'demandasAbertas',
            'demandasEmAndamento',
            'demandasAguardandoOrcamento',
            'demandasConcluidas',
            'demandasUrgentes',
            'orcamentosPendentes',
            'orcamentosAprovados',
            'totalOrcamentos',
            'taxaAprovacao',
            'orcamentosVencendoHoje',
            'orcamentosVencendoSemana',
            'negociacoesPendentes',
            'servicosConcluidos',
            'servicosConcluidosHoje',
            'orcamentosParaAprovar',
            'negociacoesParaResponder',
            'demandasUrgentesLista',
            'servicosConcluidosRecentes',
            'orcamentosVencendo',
            'demandasEsteMes',
            'orcamentosEsteMes',
            'servicosConcluidosEsteMes'
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
            // Dados da Administradora
            'nome' => 'required|string|max:255',
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:administradoras,cnpj',
            'email' => 'required|email|unique:administradoras,email',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'ativo' => 'nullable|in:0,1',
            
            // Dados do Usuário
            'usuario_name' => 'required|string|max:255',
            'usuario_email' => 'required|email|max:255|unique:users,email',
            'usuario_telefone' => 'nullable|string|max:20|unique:users,telefone',
            'usuario_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Cria a Administradora
        $administradoraData = [
            'nome' => $validated['nome'],
            'razao_social' => $validated['razao_social'] ?? null,
            'cnpj' => $validated['cnpj'] ?? null,
            'email' => $validated['email'],
            'telefone' => $validated['telefone'] ?? null,
            'endereco' => $validated['endereco'] ?? null,
            'numero' => $validated['numero'] ?? null,
            'complemento' => $validated['complemento'] ?? null,
            'bairro' => $validated['bairro'] ?? null,
            'cidade' => $validated['cidade'] ?? null,
            'estado' => $validated['estado'] ?? null,
            'cep' => $validated['cep'] ?? null,
            'ativo' => isset($validated['ativo']) ? (bool)$validated['ativo'] : true,
        ];

        $administradora = Administradora::create($administradoraData);

        // Cria o usuário Administradora associado
        $usuario = User::create([
            'name' => $validated['usuario_name'],
            'email' => $validated['usuario_email'],
            'telefone' => $validated['usuario_telefone'] ?? null,
            'password' => Hash::make($validated['usuario_password']),
            'administradora_id' => $administradora->id,
            'perfil' => 'administradora',
        ]);

        // Atribui a role de administradora
        $role = Role::where('name', 'administradora')->first();
        if ($role) {
            $usuario->roles()->attach($role);
        }

        return redirect()->route('administradoras.index')->with('success', 'Administradora e usuário de acesso criados com sucesso!');
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
            'razao_social' => 'nullable|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:administradoras,cnpj,' . $administradora->id,
            'email' => 'required|email|unique:administradoras,email,' . $administradora->id,
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'ativo' => 'nullable|boolean',
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

    /**
     * Mostra página de confirmação de exclusão com resumo de dependências
     */
    public function confirmDestroy(Administradora $administradora)
    {
        $this->authorize('admin');
        
        // Carrega contagens de todos os relacionamentos
        $administradora->loadCount([
            'usuarios',
            'condominios',
            'prestadores',
            'demandas',
            'documentos',
            'zeladores'
        ]);
        
        // Conta orçamentos relacionados
        $orcamentosCount = Orcamento::whereHas('demanda', function($q) use ($administradora) {
            $q->where('administradora_id', $administradora->id);
        })->count();
        
        // Verifica se há dados críticos
        $temDadosCriticos = $administradora->usuarios_count > 0 
            || $administradora->condominios_count > 0 
            || $administradora->demandas_count > 0;
        
        return view('administradoras.confirm-destroy', compact('administradora', 'orcamentosCount', 'temDadosCriticos'));
    }

    /**
     * Remove a administradora de forma segura
     */
    public function destroy(Request $request, Administradora $administradora)
    {
        $this->authorize('admin');
        
        // Valida confirmação explícita
        $request->validate([
            'confirmacao' => 'required|accepted',
            'confirmacao_texto' => 'required|in:EXCLUIR',
        ], [
            'confirmacao.accepted' => 'Você deve confirmar a exclusão.',
            'confirmacao_texto.in' => 'Você deve digitar EXCLUIR para confirmar.',
        ]);
        
        // Verifica dependências críticas
        $usuariosCount = $administradora->usuarios()->count();
        $condominiosCount = $administradora->condominios()->count();
        $demandasCount = $administradora->demandas()->count();
        
        // Se houver dados críticos, apenas desativa ao invés de excluir
        if ($usuariosCount > 0 || $condominiosCount > 0 || $demandasCount > 0) {
            // Usa transação para garantir atomicidade
            DB::transaction(function() use ($administradora) {
                // Desativa a administradora
                $administradora->update(['ativo' => false]);
                
                // Desativa todos os condomínios
                $administradora->condominios()->update(['ativo' => false]);
                
                // Desativa todos os prestadores
                $administradora->prestadores()->update(['ativo' => false]);
            });
            
            return redirect()->route('administradoras.index')
                ->with('success', 'Administradora desativada com sucesso! Como havia dados relacionados, ela foi desativada ao invés de excluída para preservar o histórico. Os usuários não poderão mais fazer login enquanto a administradora estiver desativada.');
        }
        
        // Se não houver dados críticos, pode excluir completamente
        DB::transaction(function() use ($administradora) {
            // Remove relacionamentos não críticos primeiro
            $administradora->prestadores()->delete();
            $administradora->zeladores()->delete();
            $administradora->documentos()->delete();
            
            // Remove a administradora (soft delete)
            $administradora->delete();
        });
        
        return redirect()->route('administradoras.index')
            ->with('success', 'Administradora excluída com sucesso!');
    }
}
