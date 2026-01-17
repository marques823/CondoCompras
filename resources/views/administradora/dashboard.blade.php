<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }} - {{ $empresa->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards de Estatísticas Gerais -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Condomínios</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalCondominios }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Prestadores</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalPrestadores }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Demandas</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalDemandas }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Orçamentos</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalOrcamentos }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards de Status de Demandas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Abertas</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $demandasAbertas }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Em Andamento</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $demandasEmAndamento }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Concluídas</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $demandasConcluidas }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links Rápidos -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Ações Rápidas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('condominios.create') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="h-8 w-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <div>
                                <p class="font-medium">Novo Condomínio</p>
                                <p class="text-sm text-gray-500">Cadastrar condomínio</p>
                            </div>
                        </a>
                        <a href="{{ route('prestadores.create') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="h-8 w-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <div>
                                <p class="font-medium">Novo Prestador</p>
                                <p class="text-sm text-gray-500">Cadastrar prestador</p>
                            </div>
                        </a>
                        <a href="{{ route('demandas.create') }}" class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="h-8 w-8 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <div>
                                <p class="font-medium">Nova Demanda</p>
                                <p class="text-sm text-gray-500">Criar demanda de serviço</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Condomínios Recentes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Condomínios Recentes</h3>
                            <a href="{{ route('condominios.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Ver todos</a>
                        </div>

                        @if($condominiosRecentes->count() > 0)
                            <div class="space-y-4">
                                @foreach($condominiosRecentes as $condominio)
                                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0 last:pb-0">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-medium">{{ $condominio->nome }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $condominio->cidade ?? 'N/A' }}, {{ $condominio->estado ?? 'N/A' }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Cadastrado em {{ $condominio->created_at->format('d/m/Y') }}
                                                </p>
                                            </div>
                                            <a href="{{ route('condominios.show', $condominio->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Ver</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">Nenhum condomínio cadastrado ainda.</p>
                        @endif
                    </div>
                </div>

                <!-- Demandas Recentes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Demandas Recentes</h3>
                            <a href="{{ route('demandas.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Ver todas</a>
                        </div>

                        @if($demandasRecentes->count() > 0)
                            <div class="space-y-4">
                                @foreach($demandasRecentes as $demanda)
                                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0 last:pb-0">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="font-medium">{{ $demanda->titulo }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $demanda->condominio->nome ?? 'N/A' }}
                                                    @if($demanda->usuario)
                                                        <span class="text-xs">- por {{ $demanda->usuario->name }}</span>
                                                    @endif
                                                </p>
                                                <div class="flex items-center gap-2 mt-2">
                                                    @php
                                                        $statusColors = [
                                                            'aberta' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                            'em_andamento' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                            'aguardando_orcamento' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                                            'concluida' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                            'cancelada' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                        ];
                                                        $statusLabels = [
                                                            'aberta' => 'Aberta',
                                                            'em_andamento' => 'Em Andamento',
                                                            'aguardando_orcamento' => 'Aguardando Orçamento',
                                                            'concluida' => 'Concluída',
                                                            'cancelada' => 'Cancelada',
                                                        ];
                                                    @endphp
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$demanda->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ $statusLabels[$demanda->status] ?? $demanda->status }}
                                                    </span>
                                                    <span class="text-xs text-gray-400">{{ $demanda->created_at->format('d/m/Y') }}</span>
                                                </div>
                                            </div>
                                            <a href="{{ route('demandas.show', $demanda->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm ml-4">Ver</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">Nenhuma demanda cadastrada ainda.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
