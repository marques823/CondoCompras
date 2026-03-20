<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('Dashboard do Gerente') }} - {{ $empresa->nome }}
                    </h2>
                    <span class="px-2 py-1 text-xs font-bold bg-indigo-500 text-white rounded-full">NOVO</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ now()->format('d/m/Y H:i') }} - Informações atualizadas em tempo real
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- DASHBOARD REDESENHADO - Versão 2.0 - {{ now() }} -->
            <!-- ALERTAS E URGÊNCIAS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                @if($demandasUrgentes > 0)
                <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-500 rounded-lg p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-full p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 0v2m0 0h2m-2 0h-2m2-6h2m-2 0H8m2 0V5m0 4v2m0-2h2m-2 0H8"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-800 dark:text-red-200">Demandas Urgentes</p>
                                <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ $demandasUrgentes }}</p>
                            </div>
                        </div>
                        <a href="{{ route('demandas.index', ['urgencia' => 'alta']) }}" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                            Ver →
                        </a>
                    </div>
                </div>
                @endif

                @if($orcamentosPendentes > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-500 rounded-lg p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-full p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Orçamentos Pendentes</p>
                                <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $orcamentosPendentes }}</p>
                            </div>
                        </div>
                        <a href="{{ route('demandas.index', ['status' => 'aguardando_orcamento']) }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-semibold">
                            Ver →
                        </a>
                    </div>
                </div>
                @endif

                @if($negociacoesPendentes > 0)
                <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-500 rounded-lg p-4 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-full p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Negociações Pendentes</p>
                                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $negociacoesPendentes }}</p>
                            </div>
                        </div>
                        <a href="{{ route('demandas.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                            Ver →
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- MÉTRICAS PRINCIPAIS -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <a href="{{ route('condominios.index') }}" class="text-blue-100 hover:text-white text-sm">Ver todos</a>
                    </div>
                    <p class="text-blue-100 text-sm mb-1">Condomínios</p>
                    <p class="text-4xl font-bold">{{ $totalCondominios }}</p>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="text-green-100 text-xs">Zeladores</span>
                    </div>
                    <p class="text-green-100 text-sm mb-1">Zeladores</p>
                    <p class="text-4xl font-bold">{{ $totalZeladores }}</p>
                </div>

                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <a href="{{ route('demandas.index') }}" class="text-yellow-100 hover:text-white text-sm">Ver todas</a>
                    </div>
                    <p class="text-yellow-100 text-sm mb-1">Demandas</p>
                    <p class="text-4xl font-bold">{{ $totalDemandas }}</p>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-purple-100 text-xs">Taxa: {{ $taxaAprovacao }}%</span>
                    </div>
                    <p class="text-purple-100 text-sm mb-1">Orçamentos</p>
                    <p class="text-4xl font-bold">{{ $totalOrcamentos }}</p>
                    <p class="text-purple-100 text-xs mt-2">{{ $orcamentosAprovados }} aprovados</p>
                </div>
            </div>

            <!-- ESTATÍSTICAS DO MÊS -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📊 Estatísticas do Mês ({{ now()->format('F Y') }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                        <p class="text-sm text-blue-700 dark:text-blue-300 mb-1">Novas Demandas</p>
                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $demandasEsteMes }}</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                        <p class="text-sm text-purple-700 dark:text-purple-300 mb-1">Orçamentos Recebidos</p>
                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $orcamentosEsteMes }}</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                        <p class="text-sm text-green-700 dark:text-green-300 mb-1">Serviços Concluídos</p>
                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $servicosConcluidosEsteMes }}</p>
                    </div>
                </div>
            </div>

            <!-- AÇÕES NECESSÁRIAS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Orçamentos para Aprovar -->
                @if($orcamentosParaAprovar->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-yellow-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">⚠️ Orçamentos Pendentes de Aprovação</h3>
                            <span class="bg-yellow-600 text-white text-sm font-bold px-3 py-1 rounded-full">{{ $orcamentosPendentes }}</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($orcamentosParaAprovar as $orcamento)
                                @if($orcamento->demanda && $orcamento->prestador)
                                <a href="{{ route('demandas.show', $orcamento->demanda->id) }}" class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $orcamento->demanda->titulo }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $orcamento->prestador->nome_razao_social }} • {{ $orcamento->demanda->condominio->nome ?? 'N/A' }}
                                            </p>
                                            <div class="flex items-center gap-3 mt-2">
                                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                                    R$ {{ number_format($orcamento->valor, 2, ',', '.') }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $orcamento->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                                @endif
                            @endforeach
                        </div>
                        <a href="{{ route('demandas.index', ['status' => 'aguardando_orcamento']) }}" class="block text-center mt-4 text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 font-semibold">
                            Ver todos os orçamentos pendentes →
                        </a>
                    </div>
                </div>
                @endif

                <!-- Negociações Pendentes -->
                @if($negociacoesParaResponder->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-blue-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">💬 Negociações Aguardando Resposta</h3>
                            <span class="bg-blue-600 text-white text-sm font-bold px-3 py-1 rounded-full">{{ $negociacoesPendentes }}</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($negociacoesParaResponder as $negociacao)
                                @if($negociacao->demanda && $negociacao->prestador)
                                <a href="{{ route('demandas.show', $negociacao->demanda->id) }}" class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    @if($negociacao->tipo === 'desconto') Desconto
                                                    @elseif($negociacao->tipo === 'parcelamento') Parcelamento
                                                    @else Contraproposta
                                                    @endif
                                                </span>
                                            </div>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $negociacao->demanda->titulo }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $negociacao->prestador->nome_razao_social }} • {{ $negociacao->demanda->condominio->nome ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-2">{{ $negociacao->created_at->diffForHumans() }}</p>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                                @endif
                            @endforeach
                        </div>
                        <a href="{{ route('demandas.index') }}" class="block text-center mt-4 text-blue-600 hover:text-blue-800 dark:text-blue-400 font-semibold">
                            Ver todas as negociações →
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- DEMANDAS URGENTES E ORÇAMENTOS VENCENDO -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Demandas Urgentes -->
                @if($demandasUrgentesLista->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-red-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">🚨 Demandas Urgentes</h3>
                            <span class="bg-red-600 text-white text-sm font-bold px-3 py-1 rounded-full">{{ $demandasUrgentes }}</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($demandasUrgentesLista as $demanda)
                                <a href="{{ route('demandas.show', $demanda->id) }}" class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($demanda->urgencia === 'critica') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                    @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                    @endif">
                                                    {{ ucfirst($demanda->urgencia) }}
                                                </span>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    {{ ucfirst(str_replace('_', ' ', $demanda->status)) }}
                                                </span>
                                            </div>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $demanda->titulo }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $demanda->condominio->nome ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('demandas.index', ['urgencia' => 'alta']) }}" class="block text-center mt-4 text-red-600 hover:text-red-800 dark:text-red-400 font-semibold">
                            Ver todas as demandas urgentes →
                        </a>
                    </div>
                </div>
                @endif

                <!-- Orçamentos Vencendo -->
                @if($orcamentosVencendo->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-orange-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">⏰ Orçamentos Vencendo</h3>
                            <span class="bg-orange-600 text-white text-sm font-bold px-3 py-1 rounded-full">{{ $orcamentosVencendoSemana }}</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($orcamentosVencendo as $orcamento)
                                @if($orcamento->demanda && $orcamento->prestador && $orcamento->validade)
                                <a href="{{ route('demandas.show', $orcamento->demanda->id) }}" class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $orcamento->demanda->titulo }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $orcamento->prestador->nome_razao_social }} • {{ $orcamento->demanda->condominio->nome ?? 'N/A' }}
                                            </p>
                                            <div class="flex items-center gap-3 mt-2">
                                                <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">
                                                    Vence: {{ $orcamento->validade->format('d/m/Y') }}
                                                </span>
                                                @if($orcamento->validade->isToday())
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        Hoje!
                                                    </span>
                                                @elseif($orcamento->validade->isTomorrow())
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                        Amanhã
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- STATUS DAS DEMANDAS E SERVIÇOS CONCLUÍDOS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Status das Demandas -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">📈 Status das Demandas</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Abertas</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $demandasAbertas }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Em Andamento</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $demandasEmAndamento }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aguardando Orçamento</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $demandasAguardandoOrcamento }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Concluídas</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $demandasConcluidas }}</span>
                        </div>
                    </div>
                </div>

                <!-- Serviços Concluídos Recentemente -->
                @if($servicosConcluidosRecentes->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-green-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">✅ Serviços Concluídos Recentemente</h3>
                            <span class="bg-green-600 text-white text-sm font-bold px-3 py-1 rounded-full">{{ $servicosConcluidosHoje }} hoje</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($servicosConcluidosRecentes as $orcamento)
                                @if($orcamento->demanda && $orcamento->prestador && $orcamento->concluido_em)
                                <a href="{{ route('demandas.show', $orcamento->demanda->id) }}" class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $orcamento->demanda->titulo }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $orcamento->prestador->nome_razao_social }} • {{ $orcamento->demanda->condominio->nome ?? 'N/A' }}
                                            </p>
                                            <div class="flex items-center gap-3 mt-2">
                                                <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                    R$ {{ number_format($orcamento->valor, 2, ',', '.') }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $orcamento->concluido_em->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- AÇÕES RÁPIDAS -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">⚡ Ações Rápidas</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('demandas.create') }}" class="flex items-center p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border-2 border-yellow-300 dark:border-yellow-700 rounded-lg hover:from-yellow-100 hover:to-yellow-200 dark:hover:from-yellow-800/30 dark:hover:to-yellow-700/30 transition">
                        <svg class="h-10 w-10 text-yellow-600 dark:text-yellow-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-gray-100">Nova Demanda</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Criar demanda de serviço</p>
                        </div>
                    </a>
                    <a href="{{ route('condominios.index') }}" class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-2 border-blue-300 dark:border-blue-700 rounded-lg hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-800/30 dark:hover:to-blue-700/30 transition">
                        <svg class="h-10 w-10 text-blue-600 dark:text-blue-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-gray-100">Ver Condomínios</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Gerenciar condomínios</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
