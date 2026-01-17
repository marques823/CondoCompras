<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }} - {{ $condominio->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards de Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Demandas</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalDemandas }}</p>
                            </div>
                        </div>
                    </div>
                </div>

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

            <!-- Demandas Recentes -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Demandas Recentes</h3>
                        <a href="{{ route('zelador.demandas.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Ver todas</a>
                    </div>

                    @if($demandasRecentes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Título</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($demandasRecentes as $demanda)
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $demanda->titulo }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $demanda->categoriaServico->nome ?? '-' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'aberta' => 'bg-yellow-100 text-yellow-800',
                                                        'em_andamento' => 'bg-blue-100 text-blue-800',
                                                        'aguardando_orcamento' => 'bg-purple-100 text-purple-800',
                                                        'concluida' => 'bg-green-100 text-green-800',
                                                        'cancelada' => 'bg-red-100 text-red-800',
                                                    ];
                                                    $statusLabels = [
                                                        'aberta' => 'Aberta',
                                                        'em_andamento' => 'Em Andamento',
                                                        'aguardando_orcamento' => 'Aguardando Orçamento',
                                                        'concluida' => 'Concluída',
                                                        'cancelada' => 'Cancelada',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$demanda->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $statusLabels[$demanda->status] ?? $demanda->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $demanda->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('zelador.demandas.show', $demanda->id) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Nenhuma demanda cadastrada ainda.</p>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('zelador.demandas.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Nova Demanda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
