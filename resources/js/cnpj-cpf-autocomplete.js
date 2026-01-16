// Função para remover caracteres especiais
function limparDocumento(doc) {
    return doc.replace(/\D/g, '');
}

// Função para formatar CNPJ
function formatarCNPJ(cnpj) {
    cnpj = limparDocumento(cnpj);
    return cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
}

// Função para formatar CPF
function formatarCPF(cpf) {
    cpf = limparDocumento(cpf);
    return cpf.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
}

// Buscar dados do CNPJ via API do Laravel (backend)
async function buscarCNPJ(cnpj) {
    cnpj = limparDocumento(cnpj);
    
    if (cnpj.length !== 14) {
        return null;
    }

    try {
        const response = await fetch(`/api/buscar-cnpj?cnpj=${cnpj}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Erro ao buscar CNPJ');
        }
        
        return result.data;
    } catch (error) {
        console.error('Erro ao buscar CNPJ:', error);
        return null;
    }
}

// Buscar dados do CPF (usando API alternativa - BrasilAPI)
async function buscarCPF(cpf) {
    cpf = limparDocumento(cpf);
    
    if (cpf.length !== 11) {
        return null;
    }

    // Nota: APIs públicas de CPF são limitadas. Vamos apenas validar o formato.
    // Para dados completos, seria necessário uma API paga.
    return null;
}

// Preencher formulário de Condomínio
function preencherCondominio(data) {
    if (data.nome) {
        document.getElementById('nome').value = data.nome;
    }
    
    if (data.logradouro) {
        document.getElementById('endereco').value = data.logradouro;
    }
    
    if (data.numero) {
        document.getElementById('numero').value = data.numero;
    }
    
    if (data.complemento) {
        document.getElementById('complemento').value = data.complemento;
    }
    
    if (data.bairro) {
        document.getElementById('bairro').value = data.bairro;
    }
    
    if (data.municipio) {
        document.getElementById('cidade').value = data.municipio;
    }
    
    if (data.uf) {
        document.getElementById('estado').value = data.uf;
    }
    
    if (data.cep) {
        document.getElementById('cep').value = data.cep.replace(/\D/g, '').replace(/^(\d{5})(\d{3})$/, '$1-$2');
    }
}

// Preencher formulário de Prestador
function preencherPrestador(data, tipo) {
    if (tipo === 'juridica') {
        if (data.nome) {
            document.getElementById('nome_razao_social').value = data.nome;
        }
        
        if (data.logradouro) {
            document.getElementById('endereco').value = data.logradouro;
        }
        
        if (data.numero) {
            // Não temos campo número no prestador, mas podemos adicionar ao endereço
            const endereco = document.getElementById('endereco');
            if (endereco.value && !endereco.value.includes(data.numero)) {
                endereco.value += ', ' + data.numero;
            }
        }
        
        if (data.municipio) {
            const endereco = document.getElementById('endereco');
            if (endereco.value && !endereco.value.includes(data.municipio)) {
                endereco.value += ' - ' + data.municipio + '/' + (data.uf || '');
            }
        }
        
        if (data.email) {
            document.getElementById('email').value = data.email;
        }
        
        if (data.telefone) {
            document.getElementById('telefone').value = data.telefone;
        }
    }
}

// Inicializar autocomplete para CNPJ (Condomínios)
document.addEventListener('DOMContentLoaded', function() {
    const cnpjInput = document.getElementById('cnpj');
    
    if (cnpjInput) {
        // Aplicar máscara
        cnpjInput.addEventListener('input', function(e) {
            e.target.value = formatarCNPJ(e.target.value);
        });
        
        // Buscar dados quando o CNPJ estiver completo
        cnpjInput.addEventListener('blur', async function(e) {
            const cnpj = limparDocumento(e.target.value);
            
            if (cnpj.length === 14) {
                // Mostrar loading
                const loading = document.createElement('div');
                loading.id = 'cnpj-loading';
                loading.className = 'text-blue-500 text-sm mt-1';
                loading.textContent = 'Buscando dados...';
                cnpjInput.parentElement.appendChild(loading);
                
                const data = await buscarCNPJ(cnpj);
                
                // Remover loading
                const loadingEl = document.getElementById('cnpj-loading');
                if (loadingEl) {
                    loadingEl.remove();
                }
                
                if (data) {
                    preencherCondominio(data);
                    
                    // Mostrar sucesso
                    const success = document.createElement('div');
                    success.className = 'text-green-500 text-sm mt-1';
                    success.textContent = '✓ Dados preenchidos automaticamente';
                    cnpjInput.parentElement.appendChild(success);
                    
                    setTimeout(() => success.remove(), 3000);
                } else {
                    // Mostrar erro
                    const error = document.createElement('div');
                    error.className = 'text-red-500 text-sm mt-1';
                    error.textContent = 'CNPJ não encontrado ou inválido';
                    cnpjInput.parentElement.appendChild(error);
                    
                    setTimeout(() => error.remove(), 3000);
                }
            }
        });
    }
    
    // Inicializar autocomplete para CPF/CNPJ (Prestadores)
    const cpfCnpjInput = document.getElementById('cpf_cnpj');
    const tipoSelect = document.getElementById('tipo');
    
    if (cpfCnpjInput && tipoSelect) {
        // Aplicar máscara baseado no tipo
        cpfCnpjInput.addEventListener('input', function(e) {
            const tipo = tipoSelect.value;
            if (tipo === 'juridica') {
                e.target.value = formatarCNPJ(e.target.value);
            } else {
                e.target.value = formatarCPF(e.target.value);
            }
        });
        
        // Atualizar máscara quando o tipo mudar
        tipoSelect.addEventListener('change', function() {
            const valor = limparDocumento(cpfCnpjInput.value);
            if (this.value === 'juridica') {
                cpfCnpjInput.value = formatarCNPJ(valor);
            } else {
                cpfCnpjInput.value = formatarCPF(valor);
            }
        });
        
        // Buscar dados quando o documento estiver completo
        cpfCnpjInput.addEventListener('blur', async function(e) {
            const tipo = tipoSelect.value;
            const doc = limparDocumento(e.target.value);
            
            if (tipo === 'juridica' && doc.length === 14) {
                // Mostrar loading
                const loading = document.createElement('div');
                loading.id = 'cnpj-loading';
                loading.className = 'text-blue-500 text-sm mt-1';
                loading.textContent = 'Buscando dados...';
                cpfCnpjInput.parentElement.appendChild(loading);
                
                const data = await buscarCNPJ(doc);
                
                // Remover loading
                const loadingEl = document.getElementById('cnpj-loading');
                if (loadingEl) {
                    loadingEl.remove();
                }
                
                if (data) {
                    preencherPrestador(data, 'juridica');
                    
                    // Mostrar sucesso
                    const success = document.createElement('div');
                    success.className = 'text-green-500 text-sm mt-1';
                    success.textContent = '✓ Dados preenchidos automaticamente';
                    cpfCnpjInput.parentElement.appendChild(success);
                    
                    setTimeout(() => success.remove(), 3000);
                } else {
                    // Mostrar erro
                    const error = document.createElement('div');
                    error.className = 'text-red-500 text-sm mt-1';
                    error.textContent = 'CNPJ não encontrado ou inválido';
                    cpfCnpjInput.parentElement.appendChild(error);
                    
                    setTimeout(() => error.remove(), 3000);
                }
            }
        });
    }
});
