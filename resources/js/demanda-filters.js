// Filtros e busca para criação de demandas
document.addEventListener('DOMContentLoaded', function() {
    const condominioSelect = document.getElementById('condominio_id');
    const prestadoresContainer = document.getElementById('prestadores-container');
    
    if (!condominioSelect || !prestadoresContainer) {
        return;
    }

    // Busca de Condomínios
    const condominioSearch = document.createElement('input');
    condominioSearch.type = 'text';
    condominioSearch.placeholder = 'Buscar condomínio...';
    condominioSearch.className = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm mb-2';
    condominioSelect.parentElement.insertBefore(condominioSearch, condominioSelect);

    condominioSearch.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const options = condominioSelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            const text = option.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });

    // Busca de Prestadores
    const prestadoresSearch = document.createElement('input');
    prestadoresSearch.type = 'text';
    prestadoresSearch.placeholder = 'Buscar prestadores...';
    prestadoresSearch.className = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm mb-3';
    prestadoresContainer.parentElement.insertBefore(prestadoresSearch, prestadoresContainer);

    prestadoresSearch.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const checkboxes = prestadoresContainer.querySelectorAll('label');
        
        checkboxes.forEach(label => {
            const text = label.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                label.style.display = 'flex';
            } else {
                label.style.display = 'none';
            }
        });

        // Mostra mensagem se não houver resultados
        const visibleLabels = Array.from(checkboxes).filter(label => label.style.display !== 'none');
        let noResultsMsg = prestadoresContainer.querySelector('.no-results');
        
        if (visibleLabels.length === 0 && searchTerm) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('p');
                noResultsMsg.className = 'no-results text-sm text-gray-500 text-center py-4';
                noResultsMsg.textContent = 'Nenhum prestador encontrado com esse termo.';
                prestadoresContainer.appendChild(noResultsMsg);
            }
        } else if (noResultsMsg) {
            noResultsMsg.remove();
        }
    });

    // Contador de prestadores selecionados
    const selectedCount = document.createElement('div');
    selectedCount.className = 'text-sm text-gray-600 mt-2 mb-2';
    selectedCount.id = 'prestadores-count';
    prestadoresContainer.parentElement.insertBefore(selectedCount, prestadoresContainer);

    function updateSelectedCount() {
        const checked = prestadoresContainer.querySelectorAll('input[type="checkbox"]:checked').length;
        const total = prestadoresContainer.querySelectorAll('input[type="checkbox"]').length;
        selectedCount.textContent = `${checked} de ${total} prestadores selecionados`;
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
    quickActions.className = 'flex gap-2 mb-2';
    
    const selectAllBtn = document.createElement('button');
    selectAllBtn.type = 'button';
    selectAllBtn.className = 'text-xs text-blue-600 hover:text-blue-800';
    selectAllBtn.textContent = 'Selecionar todos';
    selectAllBtn.addEventListener('click', function() {
        const visibleCheckboxes = Array.from(prestadoresContainer.querySelectorAll('input[type="checkbox"]'))
            .filter(cb => {
                const label = cb.closest('label');
                return label && label.style.display !== 'none';
            });
        visibleCheckboxes.forEach(cb => cb.checked = true);
        updateSelectedCount();
    });

    const deselectAllBtn = document.createElement('button');
    deselectAllBtn.type = 'button';
    deselectAllBtn.className = 'text-xs text-gray-600 hover:text-gray-800';
    deselectAllBtn.textContent = 'Desmarcar todos';
    deselectAllBtn.addEventListener('click', function() {
        prestadoresContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        updateSelectedCount();
    });

    quickActions.appendChild(selectAllBtn);
    quickActions.appendChild(deselectAllBtn);
    prestadoresContainer.parentElement.insertBefore(quickActions, prestadoresContainer);
});
