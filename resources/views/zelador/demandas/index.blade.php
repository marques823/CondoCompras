<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Demandas') }} - {{ $condominio->nome }}
            </h2>
            <a href="{{ route('zelador.demandas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Nova Demanda
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($demandas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Título</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Urgência</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($demandas as $demanda)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $demanda->titulo }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $urgenciaColors = [
                                                        'baixa' => 'bg-green-100 text-green-800',
                                                        'media' => 'bg-yellow-100 text-yellow-800',
                                                        'alta' => 'bg-orange-100 text-orange-800',
                                                        'critica' => 'bg-red-100 text-red-800',
                                                    ];
                                                    $urgenciaLabels = [
                                                        'baixa' => 'Baixa',
                                                        'media' => 'Média',
                                                        'alta' => 'Alta',
                                                        'critica' => 'Crítica',
                                                    ];
                                                @endphp
                                                @if($demanda->urgencia)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $urgenciaColors[$demanda->urgencia] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ $urgenciaLabels[$demanda->urgencia] ?? ucfirst($demanda->urgencia) }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $demanda->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('zelador.demandas.show', $demanda->id) }}" class="text-indigo-600 hover:text-indigo-900">Ver Detalhes</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $demandas->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Nenhuma demanda cadastrada ainda.</p>
                            <a href="{{ route('zelador.demandas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Criar Primeira Demanda
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
