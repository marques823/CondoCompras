<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Condominio;
use App\Models\Demanda;
use App\Models\User;
use App\Models\Orcamento;
use App\Models\Prestador;

class GerenteController extends Controller
{
    /**
     * Dashboard do gerente
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Carrega a administradora do usuário
        $empresa = $user->administradora;
        
        if (!$empresa) {
            return redirect()->route('dashboard')->with('error', 'Usuário sem administradora vinculada.');
        }
        
        // Condomínios da administradora (filtrados pelo Global Scope)
        $condominios = Condominio::all();
        $condominioIds = $condominios->pluck('id');

        // ===== MÉTRICAS PRINCIPAIS =====
        $totalCondominios = $condominios->count();
        $totalZeladores = User::whereIn('condominio_id', $condominioIds)
            ->whereHas('roles', fn($q) => $q->where('name', 'zelador'))
            ->count();
        $totalDemandas = Demanda::whereIn('condominio_id', $condominioIds)->count();
        $totalPrestadores = Prestador::where('administradora_id', $empresa->id)->count();
        
        // ===== MÉTRICAS DE DEMANDAS =====
        $demandasAbertas = Demanda::whereIn('condominio_id', $condominioIds)->where('status', 'aberta')->count();
        $demandasEmAndamento = Demanda::whereIn('condominio_id', $condominioIds)->where('status', 'em_andamento')->count();
        $demandasAguardandoOrcamento = Demanda::whereIn('condominio_id', $condominioIds)->where('status', 'aguardando_orcamento')->count();
        $demandasConcluidas = Demanda::whereIn('condominio_id', $condominioIds)->where('status', 'concluida')->count();
        $demandasUrgentes = Demanda::whereIn('condominio_id', $condominioIds)
            ->whereIn('status', ['aberta', 'em_andamento', 'aguardando_orcamento'])
            ->whereIn('urgencia', ['alta', 'critica'])
            ->count();
        
        // ===== MÉTRICAS DE ORÇAMENTOS =====
        $orcamentosPendentes = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->where('status', 'recebido')->count();
        
        $orcamentosAprovados = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->where('status', 'aprovado')->count();
        
        $totalOrcamentos = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->count();
        
        $taxaAprovacao = $totalOrcamentos > 0 ? round(($orcamentosAprovados / $totalOrcamentos) * 100, 1) : 0;
        
        // Orçamentos vencendo hoje
        $orcamentosVencendoHoje = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->whereNotNull('validade')
          ->whereDate('validade', today())
          ->where('status', 'aprovado')
          ->count();
        
        // Orçamentos vencendo esta semana
        $orcamentosVencendoSemana = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->whereNotNull('validade')
          ->whereBetween('validade', [today(), today()->addDays(7)])
          ->where('status', 'aprovado')
          ->count();
        
        // ===== MÉTRICAS DE NEGOCIAÇÕES =====
        $negociacoesPendentes = \App\Models\Negociacao::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->where('status', 'pendente')->count();
        
        // ===== MÉTRICAS DE SERVIÇOS CONCLUÍDOS =====
        $servicosConcluidos = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->where('concluido', true)->count();
        
        $servicosConcluidosHoje = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->where('concluido', true)
          ->whereNotNull('concluido_em')
          ->whereDate('concluido_em', today())->count();
        
        // ===== LISTAS DE AÇÕES NECESSÁRIAS =====
        // Orçamentos que precisam de aprovação
        $orcamentosParaAprovar = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->where('status', 'recebido')
          ->with(['demanda' => function($q) {
              $q->with('condominio');
          }, 'prestador'])
          ->orderBy('created_at', 'desc')
          ->limit(5)
          ->get();
        
        // Negociações pendentes
        $negociacoesParaResponder = \App\Models\Negociacao::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->where('status', 'pendente')
          ->with(['demanda' => function($q) {
              $q->with('condominio');
          }, 'prestador', 'orcamento'])
          ->orderBy('created_at', 'desc')
          ->limit(5)
          ->get();
        
        // Demandas urgentes
        $demandasUrgentesLista = Demanda::whereIn('condominio_id', $condominioIds)
            ->whereIn('status', ['aberta', 'em_andamento', 'aguardando_orcamento'])
            ->whereIn('urgencia', ['alta', 'critica'])
            ->with(['condominio', 'usuario'])
            ->orderByRaw("CASE WHEN urgencia = 'critica' THEN 1 WHEN urgencia = 'alta' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Serviços concluídos recentemente
        $servicosConcluidosRecentes = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->where('concluido', true)
          ->whereNotNull('concluido_em')
          ->with(['demanda' => function($q) {
              $q->with('condominio');
          }, 'prestador', 'concluidoPor'])
          ->orderBy('concluido_em', 'desc')
          ->limit(5)
          ->get();
        
        // Orçamentos vencendo
        $orcamentosVencendo = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
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
        $demandasEsteMes = Demanda::whereIn('condominio_id', $condominioIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $orcamentosEsteMes = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->whereMonth('created_at', now()->month)
          ->whereYear('created_at', now()->year)
          ->count();
        
        $servicosConcluidosEsteMes = Orcamento::whereHas('demanda', function($q) use ($condominioIds) {
            $q->whereIn('condominio_id', $condominioIds);
        })->where('concluido', true)
          ->whereNotNull('concluido_em')
          ->whereMonth('concluido_em', now()->month)
          ->whereYear('concluido_em', now()->year)
          ->count();
        
        // Listas recentes
        $condominiosRecentes = Condominio::orderBy('created_at', 'desc')->limit(5)->get();
        $demandasRecentes = Demanda::whereIn('condominio_id', $condominioIds)
            ->with(['condominio', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('gerente.dashboard', compact(
            'empresa',
            'totalCondominios',
            'totalZeladores',
            'totalDemandas',
            'totalPrestadores',
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
            'servicosConcluidosEsteMes',
            'condominiosRecentes',
            'demandasRecentes'
        ));
    }

}
