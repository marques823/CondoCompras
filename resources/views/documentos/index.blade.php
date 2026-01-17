<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Documentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Filtros e Pesquisa -->
                    <form method="GET" action="{{ route('documentos.index') }}" class="mb-6 space-y-4">
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
                                       placeholder="Nome do arquivo..."
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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

                            <!-- Filtro por Tipo de Serviço -->
                            <div>
                                <label for="categoria_servico_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tipo de Serviço
                                </label>
                                <select id="categoria_servico_id" 
                                        name="categoria_servico_id" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Todos</option>
                                    @foreach($categorias ?? [] as $categoria)
                                        <option value="{{ $categoria->id }}" {{ request('categoria_servico_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nome }}
                                        </option>
                                    @endforeach
                                </select>
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

                            <!-- Filtro por Tipo de Documento -->
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tipo de Documento
                                </label>
                                <select id="tipo" 
                                        name="tipo" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="todos" {{ request('tipo') === 'todos' || !request('tipo') ? 'selected' : '' }}>Todos</option>
                                    <option value="nota_fiscal" {{ request('tipo') === 'nota_fiscal' ? 'selected' : '' }}>Nota Fiscal</option>
                                    <option value="boleto" {{ request('tipo') === 'boleto' ? 'selected' : '' }}>Boleto</option>
                                    <option value="comprovante" {{ request('tipo') === 'comprovante' ? 'selected' : '' }}>Comprovante</option>
                                    <option value="orcamento_pdf" {{ request('tipo') === 'orcamento_pdf' ? 'selected' : '' }}>Orçamento PDF</option>
                                    <option value="outro" {{ request('tipo') === 'outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                            </div>

                            <!-- Filtro por Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Status
                                </label>
                                <select id="status" 
                                        name="status" 
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Todos</option>
                                    <option value="com_demanda_ativa" {{ request('status') === 'com_demanda_ativa' ? 'selected' : '' }}>Com Demanda Ativa</option>
                                    <option value="com_orcamento_aprovado" {{ request('status') === 'com_orcamento_aprovado' ? 'selected' : '' }}>Com Orçamento Aprovado</option>
                                    <option value="sem_relacao" {{ request('status') === 'sem_relacao' ? 'selected' : '' }}>Sem Relação</option>
                                </select>
                            </div>

                            <!-- Filtro por Data Início -->
                            <div>
                                <label for="data_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data Início
                                </label>
                                <input type="date" 
                                       id="data_inicio" 
                                       name="data_inicio" 
                                       value="{{ request('data_inicio') }}"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <!-- Filtro por Data Fim -->
                            <div>
                                <label for="data_fim" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data Fim
                                </label>
                                <input type="date" 
                                       id="data_fim" 
                                       name="data_fim" 
                                       value="{{ request('data_fim') }}"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                Filtrar
                            </button>
                            <a href="{{ route('documentos.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                Limpar
                            </a>
                        </div>
                    </form>

                    @if($documentos->count() > 0)
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
                                                return route('documentos.index', $params);
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
                                            <a href="{{ getSortUrl('nome_original', $ordenarDirecao, $ordenarColuna) }}" class="flex items-center">
                                                Nome do Arquivo
                                                {!! getSortIcon('nome_original', $ordenarColuna, $ordenarDirecao) !!}
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <a href="{{ getSortUrl('tipo', $ordenarDirecao, $ordenarColuna) }}" class="flex items-center">
                                                Tipo
                                                {!! getSortIcon('tipo', $ordenarColuna, $ordenarDirecao) !!}
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condomínio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo de Serviço</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Prestador</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tamanho</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <a href="{{ getSortUrl('data_documento', $ordenarDirecao, $ordenarColuna) }}" class="flex items-center">
                                                Data de Validade
                                                {!! getSortIcon('data_documento', $ordenarColuna, $ordenarDirecao) !!}
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                            <a href="{{ getSortUrl('created_at', $ordenarDirecao, $ordenarColuna) }}" class="flex items-center">
                                                Data de Upload
                                                {!! getSortIcon('created_at', $ordenarColuna, $ordenarDirecao) !!}
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($documentos as $documento)
                                        @php
                                            // Determina status baseado nas relações
                                            $status = '-';
                                            $statusColor = 'bg-gray-100 text-gray-800';
                                            if ($documento->demanda) {
                                                $statusDemanda = $documento->demanda->status;
                                                if (in_array($statusDemanda, ['aberta', 'em_andamento', 'aguardando_orcamento'])) {
                                                    $status = 'Demanda Ativa';
                                                    $statusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                                } elseif ($statusDemanda === 'concluida') {
                                                    $status = 'Concluída';
                                                    $statusColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                                } elseif ($statusDemanda === 'cancelada') {
                                                    $status = 'Cancelada';
                                                    $statusColor = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                                }
                                            }
                                            if ($documento->orcamento && $documento->orcamento->status === 'aprovado') {
                                                $status = 'Orçamento Aprovado';
                                                $statusColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $documento->nome_original }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ ucfirst(str_replace('_', ' ', $documento->tipo)) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $documento->condominio->nome ?? ($documento->demanda->condominio->nome ?? '-') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $documento->demanda->categoriaServico->nome ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $documento->prestador->nome_razao_social ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $documento->tamanho_formatado }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $documento->data_documento ? $documento->data_documento->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $documento->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex gap-2">
                                                    <a href="{{ route('documentos.visualizar', $documento) }}" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                       title="Visualizar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('documentos.download', $documento) }}" 
                                                       class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                       title="Download">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $documentos->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">Nenhum documento encontrado com os filtros aplicados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dados dos condomínios para JavaScript -->
    <div id="condominios-filter-data" data-condominios='@json($condominiosData ?? [])' style="display: none;"></div>

    <script>
        // Autocomplete de Condomínios para Filtro
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
