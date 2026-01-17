<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Administradora') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('administradoras.update', $empresa->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- CNPJ -->
                            <div>
                                <x-input-label for="cnpj" :value="__('CNPJ')" />
                                <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj', $empresa->cnpj)" placeholder="00.000.000/0000-00" maxlength="18" />
                                <x-input-error :messages="$errors->get('cnpj')" class="mt-2" />
                            </div>

                            <!-- Nome -->
                            <div>
                                <x-input-label for="nome" :value="__('Nome Fantasia')" />
                                <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $empresa->nome)" required autofocus />
                                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                            </div>

                            <!-- Razão Social -->
                            <div>
                                <x-input-label for="razao_social" :value="__('Razão Social')" />
                                <x-text-input id="razao_social" class="block mt-1 w-full" type="text" name="razao_social" :value="old('razao_social', $empresa->razao_social)" />
                                <x-input-error :messages="$errors->get('razao_social')" class="mt-2" />
                            </div>

                            <!-- E-mail -->
                            <div>
                                <x-input-label for="email" :value="__('E-mail')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $empresa->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Telefone -->
                            <div>
                                <x-input-label for="telefone" :value="__('Telefone')" />
                                <x-text-input id="telefone" class="block mt-1 w-full" type="tel" name="telefone" :value="old('telefone', $empresa->telefone)" placeholder="(00) 0000-0000" />
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                            </div>

                            <!-- Endereço -->
                            <div class="md:col-span-2">
                                <x-input-label for="endereco" :value="__('Endereço')" />
                                <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco', $empresa->endereco)" />
                                <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                            </div>

                            <!-- Bairro -->
                            <div>
                                <x-input-label for="bairro" :value="__('Bairro')" />
                                <x-text-input id="bairro" class="block mt-1 w-full" type="text" name="bairro" :value="old('bairro', $empresa->bairro)" />
                                <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                            </div>

                            <!-- Cidade -->
                            <div>
                                <x-input-label for="cidade" :value="__('Cidade')" />
                                <x-text-input id="cidade" class="block mt-1 w-full" type="text" name="cidade" :value="old('cidade', $empresa->cidade)" />
                                <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                            </div>

                            <!-- Estado -->
                            <div>
                                <x-input-label for="estado" :value="__('Estado (UF)')" />
                                <x-text-input id="estado" class="block mt-1 w-full" type="text" name="estado" maxlength="2" :value="old('estado', $empresa->estado)" />
                                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                            </div>

                            <!-- CEP -->
                            <div>
                                <x-input-label for="cep" :value="__('CEP')" />
                                <x-text-input id="cep" class="block mt-1 w-full" type="text" name="cep" :value="old('cep', $empresa->cep)" />
                                <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="ativo" :value="__('Status')" />
                                <select id="ativo" name="ativo" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="1" {{ old('ativo', $empresa->ativo) ? 'selected' : '' }}>Ativa</option>
                                    <option value="0" {{ old('ativo', $empresa->ativo) === false ? 'selected' : '' }}>Inativa</option>
                                </select>
                                <x-input-error :messages="$errors->get('ativo')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('administradoras.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Atualizar Administradora') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
