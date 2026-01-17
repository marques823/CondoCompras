<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Orçamentos') }}
        </h2>
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
                    <form method="GET" action="{{ route('orcamentos.index') }}" class="mb-6 space-y-4">
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
                                       placeholder="Demanda ou prestador..."
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
                                    <option value="recebido" {{ request('status') === 'recebido' ? 'selected' : '' }}>Recebido</option>
                                    <option value="aprovado" {{ request('status') === 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                                    <option value="rejeitado" {{ request('status') === 'rejeitado' ? 'selected' : '' }}>Rejeitado</option>
                                </select>
                            </div>

                            <!-- Filtro por Condomínio (Autocomplete) -->
                            <div class="relative">
                                <label for="condominio_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Condomínio
                                </label>
                                <input type="text" 
                                       id="condominio_search" 
                                       placeholder="Buscar condomínio..."
                                       autocomplete="off"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <input type="hidden" id="condominio_id" name="condominio_id" value="{{ request('condominio_id') }}">
                                <div id="condominio-autocomplete" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto"></div>
                            </div>

                            <!-- Filtro por Prestador -->
                            <div>
                                <label for="prestador_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Prestador
                                </label>
                                <select id="prestador_id" 
                                        name="prestador_id" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Todos</option>
                                    @foreach($prestadores ?? [] as $prestador)
                                        <option value="{{ $prestador->id }}" {{ request('prestador_id') == $prestador->id ? 'selected' : '' }}>
                                            {{ $prestador->nome_razao_social }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro por Valor Mínimo -->
                            <div>
                                <label for="valor_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Valor Mínimo (R$)
                                </label>
                                <input type="number" 
                                       id="valor_min" 
                                       name="valor_min" 
                                       step="0.01"
                                       value="{{ request('valor_min') }}"
                                       placeholder="0.00"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Filtro por Valor Máximo -->
                            <div>
                                <label for="valor_max" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Valor Máximo (R$)
                                </label>
                                <input type="number" 
                                       id="valor_max" 
                                       name="valor_max" 
                                       step="0.01"
                                       value="{{ request('valor_max') }}"
                                       placeholder="0.00"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                Filtrar
                            </button>
                            <a href="{{ route('orcamentos.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                Limpar
                            </a>
                        </div>
                    </form>

                    @if($orcamentos->count() > 0)
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
                                                return route('orcamentos.index', $params);
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Demanda</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condomínio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <a href="{{ getSortUrl('valor', $ordenarDirecao, $ordenarColuna) }}" class="flex items-center">
                                                Valor
                                                {!! getSortIcon('valor', $ordenarColuna, $ordenarDirecao) !!}
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
                                    @foreach($orcamentos as $orcamento)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $orcamento->demanda->titulo }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $orcamento->demanda->condominio->nome }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                R$ {{ number_format($orcamento->valor, 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'recebido' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                        'aprovado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                        'rejeitado' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    ];
                                                    $statusLabels = [
                                                        'recebido' => 'Recebido',
                                                        'aprovado' => 'Aprovado',
                                                        'rejeitado' => 'Rejeitado',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$orcamento->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $statusLabels[$orcamento->status] ?? ucfirst($orcamento->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $orcamento->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('orcamentos.show', $orcamento) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Ver</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $orcamentos->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">Nenhum orçamento encontrado com os filtros aplicados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dados dos condomínios para JavaScript -->
    <div id="condominios-filter-data" data-condominios='@json($condominiosData ?? [])' style="display: none;"></div>

    <script>
        // Autocomplete de Condomínios para Filtro (mesmo código da view de demandas)
        document.addEventListener('DOMContentLoaded', function() {
            const condominioSearch = document.getElementById('condominio_search');
            const condominioId = document.getElementById('condominio_id');
            const condominioAutocomplete = document.getElementById('condominio-autocomplete');
            
            if (!condominioSearch || !condominioId || !condominioAutocomplete) return;
            
            const condominiosDataEl = document.getElementById('condominios-filter-data');
            let condominios = [];
            try {
                condominios = JSON.parse(condominiosDataEl.getAttribute('data-condominios') || '[]');
            } catch (e) {
                console.error('Erro ao processar dados de condomínios:', e);
            }
            
            function normalize(str) {
                return (str || '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            }
            
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
            
            condominioSearch.addEventListener('input', function() {
                if (!this.value) {
                    condominioId.value = '';
                }
                renderResults(this.value);
            });
            
            condominioSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    condominioAutocomplete.classList.add('hidden');
                }
            });
            
            document.addEventListener('click', function(e) {
                if (!condominioAutocomplete.contains(e.target) && e.target !== condominioSearch) {
                    condominioAutocomplete.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
