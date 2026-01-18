<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nova Administradora') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('administradoras.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- CNPJ -->
                            <div>
                                <x-input-label for="cnpj" :value="__('CNPJ')" />
                                <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj', request('cnpj'))" placeholder="00.000.000/0000-00" maxlength="18" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Digite o CNPJ para preenchimento automático</p>
                                <x-input-error :messages="$errors->get('cnpj')" class="mt-2" />
                            </div>

                            <!-- Nome -->
                            <div>
                                <x-input-label for="nome" :value="__('Nome Fantasia')" />
                                <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', request('nome'))" required autofocus />
                                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                            </div>

                            <!-- Razão Social -->
                            <div>
                                <x-input-label for="razao_social" :value="__('Razão Social')" />
                                <x-text-input id="razao_social" class="block mt-1 w-full" type="text" name="razao_social" :value="old('razao_social')" />
                                <x-input-error :messages="$errors->get('razao_social')" class="mt-2" />
                            </div>

                            <!-- E-mail -->
                            <div>
                                <x-input-label for="email" :value="__('E-mail')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Telefone -->
                            <div>
                                <x-input-label for="telefone" :value="__('Telefone')" />
                                <x-text-input id="telefone" class="block mt-1 w-full" type="tel" name="telefone" :value="old('telefone')" placeholder="(00) 0000-0000" />
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                            </div>

                            <!-- Endereço -->
                            <div class="md:col-span-2">
                                <x-input-label for="endereco" :value="__('Endereço')" />
                                <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco', request('logradouro'))" />
                                <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                            </div>

                            <!-- Número -->
                            <div>
                                <x-input-label for="numero" :value="__('Número')" />
                                <x-text-input id="numero" class="block mt-1 w-full" type="text" name="numero" :value="old('numero', request('numero'))" />
                                <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                            </div>

                            <!-- Complemento -->
                            <div>
                                <x-input-label for="complemento" :value="__('Complemento')" />
                                <x-text-input id="complemento" class="block mt-1 w-full" type="text" name="complemento" :value="old('complemento', request('complemento'))" />
                                <x-input-error :messages="$errors->get('complemento')" class="mt-2" />
                            </div>

                            <!-- Bairro -->
                            <div>
                                <x-input-label for="bairro" :value="__('Bairro')" />
                                <x-text-input id="bairro" class="block mt-1 w-full" type="text" name="bairro" :value="old('bairro', request('bairro'))" />
                                <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                            </div>

                            <!-- Cidade -->
                            <div>
                                <x-input-label for="cidade" :value="__('Cidade')" />
                                <x-text-input id="cidade" class="block mt-1 w-full" type="text" name="cidade" :value="old('cidade', request('municipio'))" />
                                <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                            </div>

                            <!-- Estado -->
                            <div>
                                <x-input-label for="estado" :value="__('Estado (UF)')" />
                                <x-text-input id="estado" class="block mt-1 w-full" type="text" name="estado" maxlength="2" :value="old('estado', request('uf'))" />
                                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                            </div>

                            <!-- CEP -->
                            <div>
                                <x-input-label for="cep" :value="__('CEP')" />
                                <x-text-input id="cep" class="block mt-1 w-full" type="text" name="cep" :value="old('cep', request('cep'))" />
                                <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="ativo" :value="__('Status')" />
                                <select id="ativo" name="ativo" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="1" {{ old('ativo', true) ? 'selected' : '' }}>Ativa</option>
                                    <option value="0" {{ old('ativo') === false ? 'selected' : '' }}>Inativa</option>
                                </select>
                                <x-input-error :messages="$errors->get('ativo')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Separador -->
                        <div class="border-t border-gray-200 dark:border-gray-700 my-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-6 mb-4">
                                Usuário de Acesso
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Crie o usuário que terá acesso como Administradora desta empresa
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome do Usuário -->
                            <div>
                                <x-input-label for="usuario_name" :value="__('Nome do Usuário')" />
                                <x-text-input id="usuario_name" class="block mt-1 w-full" type="text" name="usuario_name" :value="old('usuario_name')" required />
                                <x-input-error :messages="$errors->get('usuario_name')" class="mt-2" />
                            </div>

                            <!-- E-mail do Usuário -->
                            <div>
                                <x-input-label for="usuario_email" :value="__('E-mail do Usuário')" />
                                <x-text-input id="usuario_email" class="block mt-1 w-full" type="email" name="usuario_email" :value="old('usuario_email')" required />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Será usado para login no sistema</p>
                                <x-input-error :messages="$errors->get('usuario_email')" class="mt-2" />
                            </div>

                            <!-- Telefone do Usuário -->
                            <div>
                                <x-input-label for="usuario_telefone" :value="__('Telefone do Usuário')" />
                                <x-text-input id="usuario_telefone" class="block mt-1 w-full" type="tel" name="usuario_telefone" :value="old('usuario_telefone')" placeholder="(00) 00000-0000" />
                                <x-input-error :messages="$errors->get('usuario_telefone')" class="mt-2" />
                            </div>

                            <!-- Senha -->
                            <div>
                                <x-input-label for="usuario_password" :value="__('Senha')" />
                                <x-text-input id="usuario_password" class="block mt-1 w-full" type="password" name="usuario_password" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('usuario_password')" class="mt-2" />
                            </div>

                            <!-- Confirmar Senha -->
                            <div>
                                <x-input-label for="usuario_password_confirmation" :value="__('Confirmar Senha')" />
                                <x-text-input id="usuario_password_confirmation" class="block mt-1 w-full" type="password" name="usuario_password_confirmation" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('usuario_password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('administradoras.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Criar Administradora') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/cnpj-cpf-autocomplete.js') }}"></script>
    <script>
        // Preencher empresa com dados do CNPJ
        function preencherAdministradora(data) {
            // Nome fantasia
            if (data.nome) {
                document.getElementById('nome').value = data.nome;
            }
            
            // Razão social
            if (data.razao_social) {
                document.getElementById('razao_social').value = data.razao_social;
            }
            
            // Endereço
            if (data.logradouro) {
                document.getElementById('endereco').value = data.logradouro;
            }
            
            // Número
            if (data.numero) {
                const numeroField = document.getElementById('numero');
                if (numeroField) {
                    numeroField.value = data.numero;
                }
            }
            
            // Complemento
            if (data.complemento) {
                const complementoField = document.getElementById('complemento');
                if (complementoField) {
                    complementoField.value = data.complemento;
                }
            }
            
            // Bairro
            if (data.bairro) {
                document.getElementById('bairro').value = data.bairro;
            }
            
            // Cidade
            if (data.municipio) {
                document.getElementById('cidade').value = data.municipio;
            }
            
            // Estado
            if (data.uf) {
                document.getElementById('estado').value = data.uf;
            }
            
            // CEP
            if (data.cep) {
                const cep = data.cep.replace(/\D/g, '');
                document.getElementById('cep').value = cep.replace(/^(\d{5})(\d{3})$/, '$1-$2');
            }
            
            // Telefone
            if (data.telefone) {
                document.getElementById('telefone').value = data.telefone;
            }
            
            // Email
            if (data.email) {
                document.getElementById('email').value = data.email;
            }
        }

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
                        const loading = document.createElement('div');
                        loading.id = 'cnpj-loading';
                        loading.className = 'text-blue-500 text-sm mt-1';
                        loading.textContent = 'Buscando dados...';
                        cnpjInput.parentElement.appendChild(loading);
                        
                        const data = await buscarCNPJ(cnpj);
                        
                        const loadingEl = document.getElementById('cnpj-loading');
                        if (loadingEl) {
                            loadingEl.remove();
                        }
                        
                        if (data) {
                            preencherAdministradora(data);
                            
                            const success = document.createElement('div');
                            success.className = 'text-green-500 text-sm mt-1';
                            success.textContent = '✓ Dados preenchidos automaticamente';
                            cnpjInput.parentElement.appendChild(success);
                            setTimeout(() => success.remove(), 3000);
                        } else {
                            const error = document.createElement('div');
                            error.className = 'text-red-500 text-sm mt-1';
                            error.textContent = 'CNPJ não encontrado ou inválido';
                            cnpjInput.parentElement.appendChild(error);
                            setTimeout(() => error.remove(), 3000);
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
