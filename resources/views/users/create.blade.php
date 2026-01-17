<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Novo Usuário') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div>
                                <x-input-label for="name" :value="__('Nome Completo')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Perfil -->
                            <div>
                                <x-input-label for="perfil" :value="__('Perfil')" />
                                <select id="perfil" name="perfil" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Selecione um perfil</option>
                                    @foreach($perfis as $key => $label)
                                        <option value="{{ $key }}" {{ old('perfil') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('perfil')" class="mt-2" />
                            </div>

                            <!-- E-mail -->
                            <div id="email-field">
                                <x-input-label for="email" :value="__('E-mail')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Obrigatório para todos os perfis exceto Zelador</p>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Telefone -->
                            <div id="telefone-field">
                                <x-input-label for="telefone" :value="__('Telefone')" />
                                <x-text-input id="telefone" class="block mt-1 w-full" type="tel" name="telefone" :value="old('telefone')" placeholder="(00) 00000-0000" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Obrigatório para Zelador</p>
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                            </div>

                            <!-- Administradora (apenas admin) -->
                            @if(Auth::user()->isAdmin())
                            <div id="empresa-cnpj-field">
                                <x-input-label for="empresa_cnpj" :value="__('CNPJ da Administradora')" />
                                <x-text-input id="empresa_cnpj" class="block mt-1 w-full" type="text" name="empresa_cnpj" :value="old('empresa_cnpj')" placeholder="00.000.000/0000-00" maxlength="18" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Digite o CNPJ para buscar e associar a empresa</p>
                                <x-input-error :messages="$errors->get('empresa_cnpj')" class="mt-2" />
                            </div>
                            <div id="empresa-select-field" style="display: none;">
                                <x-input-label for="administradora_id" :value="__('Administradora')" />
                                <select id="administradora_id" name="administradora_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Selecione uma empresa</option>
                                    @foreach($administradoras as $empresa)
                                        <option value="{{ $empresa->id }}" {{ old('administradora_id') == $empresa->id ? 'selected' : '' }}>{{ $empresa->nome }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="document.getElementById('empresa-select-field').style.display='none'; document.getElementById('empresa-cnpj-field').style.display='block'; document.getElementById('empresa_cnpj').value=''; document.getElementById('administradora_id').value='';" class="mt-2 text-sm text-blue-600 hover:text-blue-800">Buscar por CNPJ</button>
                                <x-input-error :messages="$errors->get('administradora_id')" class="mt-2" />
                            </div>
                            @endif

                            <!-- Condomínio -->
                            <div id="condominio-field">
                                <x-input-label for="condominio_id" :value="__('Condomínio')" />
                                <select id="condominio_id" name="condominio_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Selecione um condomínio</option>
                                    @foreach($condominios as $condominio)
                                        <option value="{{ $condominio->id }}" {{ old('condominio_id') == $condominio->id ? 'selected' : '' }}>{{ $condominio->nome }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Obrigatório apenas para Zelador</p>
                                <x-input-error :messages="$errors->get('condominio_id')" class="mt-2" />
                            </div>

                            <!-- Senha -->
                            <div>
                                <x-input-label for="password" :value="__('Senha')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirmar Senha -->
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('users.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Criar Usuário') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Função para remover caracteres especiais
        function limparDocumento(doc) {
            return doc.replace(/\D/g, '');
        }

        // Função para formatar CNPJ
        function formatarCNPJ(cnpj) {
            cnpj = limparDocumento(cnpj);
            return cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
        }

        // Buscar empresa por CNPJ
        async function buscarAdministradoraPorCNPJ(cnpj) {
            cnpj = limparDocumento(cnpj);
            
            if (cnpj.length !== 14) {
                return null;
            }

            try {
                // Primeiro buscar dados do CNPJ
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

        // Inicializar autocomplete de CNPJ para empresa (apenas admin)
        @if(Auth::user()->isAdmin())
        document.addEventListener('DOMContentLoaded', function() {
            const empresaCnpjInput = document.getElementById('empresa_cnpj');
            
            if (empresaCnpjInput) {
                // Aplicar máscara
                empresaCnpjInput.addEventListener('input', function(e) {
                    e.target.value = formatarCNPJ(e.target.value);
                });
                
                // Buscar empresa quando o CNPJ estiver completo
                empresaCnpjInput.addEventListener('blur', async function(e) {
                    const cnpj = limparDocumento(e.target.value);
                    
                    if (cnpj.length === 14) {
                        // Mostrar loading
                        const loading = document.createElement('div');
                        loading.id = 'empresa-cnpj-loading';
                        loading.className = 'text-blue-500 text-sm mt-1';
                        loading.textContent = 'Buscando empresa...';
                        empresaCnpjInput.parentElement.appendChild(loading);
                        
                        const data = await buscarAdministradoraPorCNPJ(cnpj);
                        
                        // Remover loading
                        const loadingEl = document.getElementById('empresa-cnpj-loading');
                        if (loadingEl) {
                            loadingEl.remove();
                        }
                        
                        if (data && data.nome) {
                            // Buscar empresa no banco pelo CNPJ via API
                            try {
                                const administradorasResponse = await fetch('/administradoras?cnpj=' + cnpj, {
                                    method: 'GET',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    }
                                });
                                
                                // Tentar buscar na lista de administradoras disponíveis
                                const administradoras = @json($administradoras);
                                const empresaEncontrada = administradoras.find(emp => {
                                    if (!emp.cnpj) return false;
                                    const empCnpj = limparDocumento(emp.cnpj);
                                    return empCnpj === cnpj;
                                });
                                
                                if (empresaEncontrada) {
                                    // Administradora já existe, selecionar
                                    document.getElementById('administradora_id').value = empresaEncontrada.id;
                                    document.getElementById('empresa-select-field').style.display = 'block';
                                    document.getElementById('empresa-cnpj-field').style.display = 'none';
                                    
                                    const success = document.createElement('div');
                                    success.className = 'text-green-500 text-sm mt-1';
                                    success.textContent = '✓ Administradora encontrada: ' + empresaEncontrada.nome;
                                    document.getElementById('empresa-select-field').appendChild(success);
                                    setTimeout(() => success.remove(), 3000);
                                } else {
                                    // Administradora não existe, sugerir criar
                                    const criar = confirm('Administradora não encontrada no sistema.\n\nNome: ' + data.nome + '\n\nDeseja criar uma nova empresa?');
                                    if (criar) {
                                        // Redirecionar para criar empresa com dados pré-preenchidos
                                        const params = new URLSearchParams({
                                            cnpj: cnpj,
                                            nome: data.nome || '',
                                            logradouro: data.logradouro || '',
                                            numero: data.numero || '',
                                            complemento: data.complemento || '',
                                            bairro: data.bairro || '',
                                            municipio: data.municipio || '',
                                            uf: data.uf || '',
                                            cep: data.cep || '',
                                        });
                                        window.location.href = '{{ route("administradoras.create") }}?' + params.toString();
                                    }
                                }
                            } catch (error) {
                                console.error('Erro ao buscar empresa:', error);
                            }
                        } else {
                            const error = document.createElement('div');
                            error.className = 'text-red-500 text-sm mt-1';
                            error.textContent = 'CNPJ não encontrado ou inválido';
                            empresaCnpjInput.parentElement.appendChild(error);
                            setTimeout(() => error.remove(), 3000);
                        }
                    }
                });
            }
        });
        @endif

        // Mostrar/ocultar campos baseado no perfil selecionado
        document.getElementById('perfil').addEventListener('change', function() {
            const perfil = this.value;
            const emailField = document.getElementById('email-field');
            const telefoneField = document.getElementById('telefone-field');
            const condominioField = document.getElementById('condominio-field');
            const emailInput = document.getElementById('email');
            const telefoneInput = document.getElementById('telefone');

            if (perfil === 'zelador') {
                // Zelador: telefone obrigatório, email não usado
                emailField.style.display = 'none';
                telefoneField.style.display = 'block';
                condominioField.style.display = 'block';
                emailInput.removeAttribute('required');
                telefoneInput.setAttribute('required', 'required');
                document.getElementById('condominio_id').setAttribute('required', 'required');
            } else {
                // Outros perfis: email obrigatório, telefone opcional
                emailField.style.display = 'block';
                telefoneField.style.display = 'block';
                condominioField.style.display = 'block';
                emailInput.setAttribute('required', 'required');
                telefoneInput.removeAttribute('required');
                document.getElementById('condominio_id').removeAttribute('required');
            }
        });

        // Trigger no carregamento se houver valor antigo
        if (document.getElementById('perfil').value) {
            document.getElementById('perfil').dispatchEvent(new Event('change'));
        }
    </script>
</x-app-layout>
