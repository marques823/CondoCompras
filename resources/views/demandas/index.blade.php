<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Demandas') }}
            </h2>
            <a href="{{ route('demandas.create') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
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
                    <!-- Filtros e Pesquisa -->
                    <form method="GET" action="{{ route('demandas.index') }}" class="mb-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Campo de Pesquisa -->
                            <div>
                                <label for="pesquisa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Pesquisar
                                </label>
                                <input type="text" 
                                       id="pesquisa" 
                                       name="pesquisa" 
                                       value="{{ request('pesquisa') }}"
                                       placeholder="Título ou descrição..."
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Filtro por Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Status
                                </label>
                                <select id="status" 
                                        name="status" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="todos" {{ request('status') === 'todos' || !request('status') ? 'selected' : '' }}>Todos</option>
                                    <option value="aberta" {{ request('status') === 'aberta' ? 'selected' : '' }}>Aberta</option>
                                    <option value="em_andamento" {{ request('status') === 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                                    <option value="aguardando_orcamento" {{ request('status') === 'aguardando_orcamento' ? 'selected' : '' }}>Aguardando Orçamento</option>
                                    <option value="concluida" {{ request('status') === 'concluida' ? 'selected' : '' }}>Concluída</option>
                                    <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>

                            <!-- Filtro por Condomínio (Autocomplete) -->
                            <div class="relative">
                                <label for="condominio_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Condomínio
                                </label>
                                <input type="hidden" id="condominio_id" name="condominio_id" value="{{ request('condominio_id') }}">
                                <input type="text" 
                                       id="condominio_search" 
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                       placeholder="Digite para buscar condomínio por nome, bairro ou cidade..."
                                       autocomplete="off"
                                       value="{{ request('condominio_id') ? ($condominiosData->firstWhere('id', request('condominio_id'))?->nome ?? '') : '' }}">
                                <div id="condominio-autocomplete" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto hidden">
                                    <!-- Resultados serão inseridos aqui pelo JavaScript -->
                                </div>
                            </div>

                            <!-- Filtro por Urgência -->
                            <div>
                                <label for="urgencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Urgência
                                </label>
                                <select id="urgencia" 
                                        name="urgencia" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="todos" {{ request('urgencia') === 'todos' || !request('urgencia') ? 'selected' : '' }}>Todas</option>
                                    <option value="baixa" {{ request('urgencia') === 'baixa' ? 'selected' : '' }}>Baixa</option>
                                    <option value="media" {{ request('urgencia') === 'media' ? 'selected' : '' }}>Média</option>
                                    <option value="alta" {{ request('urgencia') === 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="critica" {{ request('urgencia') === 'critica' ? 'selected' : '' }}>Crítica</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                Filtrar
                            </button>
                            <a href="{{ route('demandas.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                Limpar
                            </a>
                        </div>
                    </form>

                    @if($demandas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        @php
                                            $ordenarColuna = request('ordenar_coluna', 'created_at');
                                            $ordenarDirecao = request('ordenar_direcao', 'desc');
                                            
                                            function getSortUrl($coluna, $direcaoAtual, $colunaAtual) {
                                                $params = request()->all();
                                                if ($coluna === $colunaAtual && $direcaoAtual === 'desc') {
                                                    $params['ordenar_direcao'] = 'asc';
                                                } else {
                                                    $params['ordenar_direcao'] = 'desc';
                                                }
                                                $params['ordenar_coluna'] = $coluna;
                                                return route('demandas.index', $params);
                                            }
                                            
                                            function getSortIcon($coluna, $ordenarColuna, $ordenarDirecao) {
                                                if ($coluna !== $ordenarColuna) {
                                                    return '<svg class="w-4 h-4 inline ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>';
                                                }
                                                if ($ordenarDirecao === 'asc') {
                                                    return '<svg class="w-4 h-4 inline ml-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>';
                                                }
                                                return '<svg class="w-4 h-4 inline ml-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                                            }
                                        @endphp
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <a href="{{ getSortUrl('titulo', $ordenarDirecao, $ordenarColuna) }}" class="flex items-center">
                                                Título
                                                {!! getSortIcon('titulo', $ordenarColuna, $ordenarDirecao) !!}
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condomínio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Criado por</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <a href="{{ getSortUrl('urgencia', $ordenarDirecao, $ordenarColuna) }}" class="flex items-center">
                                                Urgência
                                                {!! getSortIcon('urgencia', $ordenarColuna, $ordenarDirecao) !!}
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <a href="{{ getSortUrl('status', $ordenarDirecao, $ordenarColuna) }}" class="flex items-center">
                                                Status
                                                {!! getSortIcon('status', $ordenarColuna, $ordenarDirecao) !!}
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <a href="{{ getSortUrl('created_at', $ordenarDirecao, $ordenarColuna) }}" class="flex items-center">
                                                Data
                                                {!! getSortIcon('created_at', $ordenarColuna, $ordenarDirecao) !!}
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($demandas as $demanda)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $demanda->titulo }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $demanda->condominio->nome }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @if($demanda->usuario)
                                                    <div>
                                                        <span class="font-medium">{{ $demanda->usuario->name }}</span>
                                                        @if($demanda->usuario->isZelador())
                                                            <span class="text-xs text-blue-600">(Zelador)</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
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
                                                        'aberta' => 'bg-blue-100 text-blue-800',
                                                        'em_andamento' => 'bg-yellow-100 text-yellow-800',
                                                        'aguardando_orcamento' => 'bg-orange-100 text-orange-800',
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
                                                    {{ $statusLabels[$demanda->status] ?? ucfirst($demanda->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $demanda->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex gap-2">
                                                    <a href="{{ route('demandas.show', $demanda) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                                    <a href="{{ route('demandas.edit', $demanda) }}" class="text-green-600 hover:text-green-900">Editar</a>
                                                </div>
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
                            <a href="{{ route('demandas.create') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Criar Primeira Demanda
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dados dos condomínios para JavaScript -->
    <div id="condominios-filter-data" data-condominios='@json($condominiosData)' style="display: none;"></div>

    <script>
        // Autocomplete de Condomínios para Filtro
        document.addEventListener('DOMContentLoaded', function() {
            const condominioSearch = document.getElementById('condominio_search');
            const condominioId = document.getElementById('condominio_id');
            const condominioAutocomplete = document.getElementById('condominio-autocomplete');
            
            if (!condominioSearch || !condominioId || !condominioAutocomplete) return;
            
            // Carrega dados dos condomínios
            const condominiosDataEl = document.getElementById('condominios-filter-data');
            let condominios = [];
            try {
                condominios = JSON.parse(condominiosDataEl.getAttribute('data-condominios') || '[]');
            } catch (e) {
                console.error('Erro ao processar dados de condomínios:', e);
            }
            
            // Função para normalizar strings (remover acentos)
            function normalize(str) {
                return (str || '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            }
            
            // Preenche o campo de busca se já houver um condomínio selecionado (do filtro anterior)
            const selectedId = condominioId.value;
            if (selectedId) {
                const selected = condominios.find(c => String(c.id) === String(selectedId));
                if (selected) {
                    const location = [selected.bairro, selected.cidade].filter(Boolean).join(', ');
                    condominioSearch.value = selected.nome + (location ? ' - ' + location : '');
                }
            }
            
            function renderResults(searchTerm) {
                if (!searchTerm || searchTerm.length < 1) {
                    condominioAutocomplete.classList.add('hidden');
                    return;
                }
                
                const term = normalize(searchTerm.trim());
                const filtered = condominios.filter(c => {
                    const nome = normalize(c.nome || '');
                    const bairro = normalize(c.bairro || '');
                    const cidade = normalize(c.cidade || '');
                    return nome.includes(term) || bairro.includes(term) || cidade.includes(term);
                });
                
                if (filtered.length === 0) {
                    condominioAutocomplete.innerHTML = '<div class="p-3 text-sm text-gray-500 dark:text-gray-400 text-center">Nenhum condomínio encontrado</div>';
                    condominioAutocomplete.classList.remove('hidden');
                    return;
                }
                
                condominioAutocomplete.innerHTML = filtered.map(c => {
                    const location = [c.bairro, c.cidade].filter(Boolean).join(', ');
                    return `
                        <div class="condominio-option p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0" 
                             data-id="${c.id}" data-nome="${c.nome}" data-location="${location}">
                            <div class="font-medium text-gray-900 dark:text-gray-100">${c.nome}</div>
                            ${location ? '<div class="text-xs text-gray-500 dark:text-gray-400">' + location + '</div>' : ''}
                        </div>
                    `;
                }).join('');
                
                // Event listeners para as opções
                condominioAutocomplete.querySelectorAll('.condominio-option').forEach(option => {
                    option.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const nome = this.getAttribute('data-nome');
                        const location = this.getAttribute('data-location');
                        
                        condominioId.value = id;
                        condominioSearch.value = nome + (location ? ' - ' + location : '');
                        condominioAutocomplete.classList.add('hidden');
                    });
                });
                
                condominioAutocomplete.classList.remove('hidden');
            }
            
            condominioSearch.addEventListener('input', function(e) {
                if (e.target.value === '') {
                    condominioId.value = '';
                }
                renderResults(e.target.value);
            });

            // Mostra resultados ao clicar no campo se já tiver texto
            condominioSearch.addEventListener('click', function(e) {
                if (this.value.length >= 1) {
                    renderResults(this.value);
                }
            });
            
            // Fecha autocomplete ao clicar fora
            document.addEventListener('click', function(e) {
                if (!condominioSearch.contains(e.target) && !condominioAutocomplete.contains(e.target)) {
                    condominioAutocomplete.classList.add('hidden');
                }
            });
            
            // Atalhos de teclado
            condominioSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && condominioSearch.value === '') {
                    condominioId.value = '';
                } else if (e.key === 'Enter') {
                    const firstOption = condominioAutocomplete.querySelector('.condominio-option');
                    if (!condominioAutocomplete.classList.contains('hidden') && firstOption) {
                        e.preventDefault();
                        firstOption.click();
                    }
                } else if (e.key === 'Escape') {
                    condominioAutocomplete.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
