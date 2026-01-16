<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Novo Condomínio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('condominios.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div>
                                <x-input-label for="nome" :value="__('Nome do Condomínio')" />
                                <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus />
                                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                            </div>

                            <!-- CNPJ -->
                            <div>
                                <x-input-label for="cnpj" :value="__('CNPJ')" />
                                <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj')" placeholder="00.000.000/0000-00" maxlength="18" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Digite o CNPJ para preenchimento automático</p>
                                <x-input-error :messages="$errors->get('cnpj')" class="mt-2" />
                            </div>

                            <!-- Endereço -->
                            <div class="md:col-span-2">
                                <x-input-label for="endereco" :value="__('Endereço')" />
                                <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco')" required />
                                <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                            </div>

                            <!-- Número -->
                            <div>
                                <x-input-label for="numero" :value="__('Número')" />
                                <x-text-input id="numero" class="block mt-1 w-full" type="text" name="numero" :value="old('numero')" />
                                <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                            </div>

                            <!-- Complemento -->
                            <div>
                                <x-input-label for="complemento" :value="__('Complemento')" />
                                <x-text-input id="complemento" class="block mt-1 w-full" type="text" name="complemento" :value="old('complemento')" />
                                <x-input-error :messages="$errors->get('complemento')" class="mt-2" />
                            </div>

                            <!-- Bairro -->
                            <div>
                                <x-input-label for="bairro" :value="__('Bairro')" />
                                <x-text-input id="bairro" class="block mt-1 w-full" type="text" name="bairro" :value="old('bairro')" />
                                <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                            </div>

                            <!-- Cidade -->
                            <div>
                                <x-input-label for="cidade" :value="__('Cidade')" />
                                <x-text-input id="cidade" class="block mt-1 w-full" type="text" name="cidade" :value="old('cidade')" />
                                <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                            </div>

                            <!-- Estado -->
                            <div>
                                <x-input-label for="estado" :value="__('Estado (UF)')" />
                                <x-text-input id="estado" class="block mt-1 w-full" type="text" name="estado" maxlength="2" :value="old('estado')" />
                                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                            </div>

                            <!-- CEP -->
                            <div>
                                <x-input-label for="cep" :value="__('CEP')" />
                                <x-text-input id="cep" class="block mt-1 w-full" type="text" name="cep" :value="old('cep')" />
                                <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                            </div>

                            <!-- Síndico -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium mb-4">Dados do Síndico</h3>
                            </div>

                            <div>
                                <x-input-label for="sindico_nome" :value="__('Nome do Síndico')" />
                                <x-text-input id="sindico_nome" class="block mt-1 w-full" type="text" name="sindico_nome" :value="old('sindico_nome')" />
                                <x-input-error :messages="$errors->get('sindico_nome')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sindico_telefone" :value="__('Telefone do Síndico')" />
                                <x-text-input id="sindico_telefone" class="block mt-1 w-full" type="text" name="sindico_telefone" :value="old('sindico_telefone')" />
                                <x-input-error :messages="$errors->get('sindico_telefone')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sindico_email" :value="__('E-mail do Síndico')" />
                                <x-text-input id="sindico_email" class="block mt-1 w-full" type="email" name="sindico_email" :value="old('sindico_email')" />
                                <x-input-error :messages="$errors->get('sindico_email')" class="mt-2" />
                            </div>

                            <!-- Observações -->
                            <div class="md:col-span-2">
                                <x-input-label for="observacoes" :value="__('Observações')" />
                                <textarea id="observacoes" name="observacoes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('observacoes') }}</textarea>
                                <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('condominios.index') }}" class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Cadastrar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
