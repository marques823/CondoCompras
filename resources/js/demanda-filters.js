// Filtros e busca avançada para criação de demandas
document.addEventListener('DOMContentLoaded', function() {
    const condominioSearch = document.getElementById('condominio_search');
    const condominioId = document.getElementById('condominio_id');
    const condominioAutocomplete = document.getElementById('condominio-autocomplete');
    const prestadoresContainer = document.getElementById('prestadores-container');
    const prestadoresSearch = document.getElementById('prestadores-search');
    
    // Dados dos condomínios vindo do backend
    const condominios = window.condominiosData || [];
    
    // Função para normalizar strings (remover acentos e converter para minúsculas)
    function normalize(str) {
        return (str || '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    }

    // ========== AUTOCOMPLETE DE CONDOMÍNIOS ==========
    if (condominioSearch && condominioId && condominioAutocomplete) {
        let selectedCondominio = null;

        // Se já existe um condomínio selecionado (via old input), encontra e define
        const oldCondominioId = condominioId.value;
        if (oldCondominioId) {
            selectedCondominio = condominios.find(c => c.id == oldCondominioId);
            if (selectedCondominio) {
                condominioSearch.value = selectedCondominio.nome + 
                    (selectedCondominio.bairro || selectedCondominio.cidade ? 
                        ' - ' + [selectedCondominio.bairro, selectedCondominio.cidade].filter(Boolean).join(', ') : '');
            }
        }

        function renderCondominioResults(searchTerm) {
            if (!searchTerm || searchTerm.length < 1) {
                condominioAutocomplete.classList.add('hidden');
                return;
            }

            const term = normalize(searchTerm.trim());
            const filtered = condominios.filter(c => {
                const nome = normalize(c.nome);
                const bairro = normalize(c.bairro);
                const cidade = normalize(c.cidade);
                return nome.includes(term) || bairro.includes(term) || cidade.includes(term);
            });

            if (filtered.length === 0) {
                condominioAutocomplete.innerHTML = '<div class="p-3 text-sm text-gray-500 dark:text-gray-400 text-center">Nenhum condomínio encontrado</div>';
                condominioAutocomplete.classList.remove('hidden');
                return;
            }

            condominioAutocomplete.innerHTML = filtered.map(c => {
                const displayName = c.nome + 
                    (c.bairro || c.cidade ? 
                        ' - ' + [c.bairro, c.cidade].filter(Boolean).join(', ') : '');
                const tagsHtml = (c.tags && c.tags.length > 0) ? 
                    '<div class="mt-1 flex flex-wrap gap-1">' + 
                    c.tags.map(tag => 
                        `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium" style="background-color: ${tag.cor}20; color: ${tag.cor}; border: 1px solid ${tag.cor}40;">${tag.nome}</span>`
                    ).join('') + 
                    '</div>' : '';

                return `
                    <div class="condominio-option p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0" 
                         data-id="${c.id}" 
                         data-nome="${c.nome}">
                        <div class="font-medium text-gray-900 dark:text-gray-100">${c.nome}</div>
                        ${c.bairro || c.cidade ? 
                            `<div class="text-xs text-gray-500 dark:text-gray-400 mt-1">${[c.bairro, c.cidade].filter(Boolean).join(', ')}${c.estado ? ' - ' + c.estado : ''}</div>` : 
                            ''}
                        ${tagsHtml}
                    </div>
                `;
            }).join('');

            // Adiciona event listeners aos resultados
            condominioAutocomplete.querySelectorAll('.condominio-option').forEach(option => {
                option.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nome = this.getAttribute('data-nome');
                    const condominio = condominios.find(c => c.id == id);
                    
                    selectedCondominio = condominio;
                    condominioId.value = id;
                    
                    if (condominio) {
                        condominioSearch.value = condominio.nome + 
                            (condominio.bairro || condominio.cidade ? 
                                ' - ' + [condominio.bairro, condominio.cidade].filter(Boolean).join(', ') : '');
                    } else {
                        condominioSearch.value = nome;
                    }
                    
                    condominioAutocomplete.classList.add('hidden');
                });
            });

            condominioAutocomplete.classList.remove('hidden');
        }

        condominioSearch.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Se o campo foi limpo, limpa também o ID selecionado
            if (!value.trim()) {
                condominioId.value = '';
                selectedCondominio = null;
                condominioAutocomplete.classList.add('hidden');
                return;
            }

            renderCondominioResults(value);
        });

        // Fecha o autocomplete ao clicar fora
        document.addEventListener('click', function(e) {
            if (!condominioSearch.contains(e.target) && !condominioAutocomplete.contains(e.target)) {
                condominioAutocomplete.classList.add('hidden');
            }
        });

        // Fecha o autocomplete ao pressionar Enter
        condominioSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstOption = condominioAutocomplete.querySelector('.condominio-option');
                if (firstOption) {
                    firstOption.click();
                }
            } else if (e.key === 'Escape') {
                condominioAutocomplete.classList.add('hidden');
            }
        });
    } // Fim do bloco de condomínios

    // ========== BUSCA DE PRESTADORES ==========
    if (!prestadoresContainer || !prestadoresSearch) {
        return;
    }

    // Armazena tags selecionadas para filtro
    let selectedTags = [];

    // Função para filtrar prestadores
    function filterPrestadores() {
        const searchTermRaw = prestadoresSearch.value.trim();
        const searchTerm = normalize(searchTermRaw);
        const cleanSearchTerm = searchTermRaw.replace(/\D/g, ''); // Apenas números para busca de CNPJ
        const prestadorItems = prestadoresContainer.querySelectorAll('.prestador-item');
        let visibleCount = 0;

        prestadorItems.forEach(item => {
            const nome = normalize(item.getAttribute('data-nome'));
            const cnpj = (item.getAttribute('data-cnpj') || '').replace(/\D/g, '');
            const bairro = normalize(item.getAttribute('data-bairro'));
            const cidade = normalize(item.getAttribute('data-cidade'));
            const areasAtuacao = normalize(item.getAttribute('data-areas-atuacao'));
            const tags = item.getAttribute('data-tags') || '';
            const tagIds = tags ? tags.split(',').map(id => parseInt(id)) : [];

            // Verifica busca por texto
            let textMatches = !searchTerm;
            
            if (searchTerm) {
                // Busca no nome, bairro, cidade ou áreas de atuação (normalizado)
                const basicMatches = nome.includes(searchTerm) || 
                                   bairro.includes(searchTerm) || 
                                   cidade.includes(searchTerm) ||
                                   areasAtuacao.includes(searchTerm);
                
                // Busca no CNPJ (apenas se o termo de busca contiver números)
                const cnpjMatches = cleanSearchTerm !== '' && cnpj.includes(cleanSearchTerm);
                
                textMatches = basicMatches || cnpjMatches;
            }

            // Verifica filtro por tags
            const tagMatches = selectedTags.length === 0 || 
                selectedTags.some(tagId => tagIds.includes(parseInt(tagId)));

            if (textMatches && tagMatches) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Mostra mensagem se não houver resultados
        let noResultsMsg = prestadoresContainer.querySelector('.prestadores-no-results');
        if (visibleCount === 0 && (searchTerm || selectedTags.length > 0)) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('p');
                noResultsMsg.className = 'prestadores-no-results text-sm text-gray-500 dark:text-gray-400 text-center py-4 w-full';
                noResultsMsg.textContent = 'Nenhum prestador encontrado com os filtros aplicados.';
                prestadoresContainer.appendChild(noResultsMsg);
            }
        } else if (noResultsMsg) {
            noResultsMsg.remove();
        }

        updateSelectedCount();
    }

    // Event listeners para busca de prestadores
    prestadoresSearch.addEventListener('input', filterPrestadores);

    // Filtros por tags
    const tagFilters = document.querySelectorAll('.prestador-tag-filter');
    tagFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            const label = this.closest('label');
            if (this.checked) {
                selectedTags.push(this.getAttribute('data-tag-id'));
                label.classList.add('ring-2', 'ring-indigo-500', 'ring-offset-2');
            } else {
                selectedTags = selectedTags.filter(id => id !== this.getAttribute('data-tag-id'));
                label.classList.remove('ring-2', 'ring-indigo-500', 'ring-offset-2');
            }
            filterPrestadores();
        });
    });

    // Contador de prestadores selecionados
    const selectedCount = document.createElement('div');
    selectedCount.className = 'text-sm text-gray-600 dark:text-gray-400 mt-2 mb-2';
    selectedCount.id = 'prestadores-count';
    prestadoresContainer.parentElement.insertBefore(selectedCount, prestadoresContainer);

    function updateSelectedCount() {
        const allItems = Array.from(prestadoresContainer.querySelectorAll('.prestador-item'));
        const checked = allItems.filter(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            return checkbox && checkbox.checked;
        }).length;
        const visibleTotal = allItems.filter(item => item.style.display !== 'none').length;
        
        selectedCount.textContent = `${checked} prestador(es) selecionado(s) | ${visibleTotal} visível(is)`;
    }

    prestadoresContainer.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox') {
            updateSelectedCount();
        }
    });

    // Inicializa contador
    updateSelectedCount();

    // Botões de seleção rápida
    const quickActions = document.createElement('div');
    quickActions.className = 'flex flex-wrap gap-2 mb-2';
    
    const selectAllBtn = document.createElement('button');
    selectAllBtn.type = 'button';
    selectAllBtn.className = 'text-xs px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded transition-colors';
    selectAllBtn.textContent = 'Selecionar todos visíveis';
    selectAllBtn.addEventListener('click', function() {
        const visibleItems = Array.from(prestadoresContainer.querySelectorAll('.prestador-item'))
            .filter(item => item.style.display !== 'none');
        visibleItems.forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox) checkbox.checked = true;
        });
        updateSelectedCount();
    });

    const deselectAllBtn = document.createElement('button');
    deselectAllBtn.type = 'button';
    deselectAllBtn.className = 'text-xs px-2 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded transition-colors';
    deselectAllBtn.textContent = 'Desmarcar todos';
    deselectAllBtn.addEventListener('click', function() {
        prestadoresContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        updateSelectedCount();
    });

    const clearFiltersBtn = document.createElement('button');
    clearFiltersBtn.type = 'button';
    clearFiltersBtn.className = 'text-xs px-2 py-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded transition-colors ml-auto';
    clearFiltersBtn.textContent = 'Limpar busca';
    clearFiltersBtn.addEventListener('click', function() {
        prestadoresSearch.value = '';
        selectedTags = [];
        tagFilters.forEach(filter => {
            filter.checked = false;
            filter.closest('label').classList.remove('ring-2', 'ring-indigo-500', 'ring-offset-2');
        });
        filterPrestadores();
    });

    quickActions.appendChild(selectAllBtn);
    quickActions.appendChild(deselectAllBtn);
    quickActions.appendChild(clearFiltersBtn);
    prestadoresContainer.parentElement.insertBefore(quickActions, prestadoresContainer);
});
