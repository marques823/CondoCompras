<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Tag') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('tags.update', $tag) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div class="md:col-span-2">
                                <x-input-label for="nome" :value="__('Nome da Tag')" />
                                <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $tag->nome)" required autofocus />
                                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                            </div>

                            <!-- Cor -->
                            <div>
                                <x-input-label for="cor" :value="__('Cor')" />
                                <div class="mt-1 flex items-center gap-4">
                                    <input type="color" id="cor" name="cor" value="{{ old('cor', $tag->cor) }}" class="h-10 w-20 rounded border-gray-300 dark:border-gray-700 cursor-pointer" required>
                                    <x-text-input type="text" id="cor_hex" class="block w-full" value="{{ old('cor', $tag->cor) }}" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#3B82F6" />
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Selecione uma cor ou digite o código hexadecimal</p>
                                <x-input-error :messages="$errors->get('cor')" class="mt-2" />
                            </div>

                            <!-- Tipo -->
                            <div>
                                <x-input-label for="tipo" :value="__('Tipo')" />
                                <select id="tipo" name="tipo" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="ambos" {{ old('tipo', $tag->tipo) == 'ambos' ? 'selected' : '' }}>Ambos (Prestador e Condomínio)</option>
                                    <option value="prestador" {{ old('tipo', $tag->tipo) == 'prestador' ? 'selected' : '' }}>Apenas Prestador</option>
                                    <option value="condominio" {{ old('tipo', $tag->tipo) == 'condominio' ? 'selected' : '' }}>Apenas Condomínio</option>
                                </select>
                                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                            </div>

                            <!-- Ordem -->
                            <div>
                                <x-input-label for="ordem" :value="__('Ordem de Exibição')" />
                                <x-text-input id="ordem" class="block mt-1 w-full" type="number" name="ordem" :value="old('ordem', $tag->ordem)" min="0" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tags com menor número aparecem primeiro</p>
                                <x-input-error :messages="$errors->get('ordem')" class="mt-2" />
                            </div>

                            <!-- Ativo -->
                            <div>
                                <label class="flex items-center mt-6">
                                    <input type="checkbox" name="ativo" value="1" {{ old('ativo', $tag->ativo) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Tag Ativa</span>
                                </label>
                            </div>

                            <!-- Descrição -->
                            <div class="md:col-span-2">
                                <x-input-label for="descricao" :value="__('Descrição (opcional)')" />
                                <textarea id="descricao" name="descricao" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('descricao', $tag->descricao) }}</textarea>
                                <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('tags.index') }}" class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Atualizar Tag') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sincroniza o seletor de cor com o campo de texto
        document.getElementById('cor').addEventListener('input', function(e) {
            document.getElementById('cor_hex').value = e.target.value.toUpperCase();
        });

        document.getElementById('cor_hex').addEventListener('input', function(e) {
            if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
                document.getElementById('cor').value = e.target.value;
            }
        });
    </script>
</x-app-layout>
