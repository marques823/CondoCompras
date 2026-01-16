<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nova Demanda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('demandas.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Condomínio -->
                            <div class="md:col-span-2">
                                <x-input-label for="condominio_id" :value="__('Condomínio')" />
                                <div class="relative">
                                    <select id="condominio_id" name="condominio_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">Selecione um condomínio</option>
                                        @foreach($condominios as $condominio)
                                            <option value="{{ $condominio->id }}" {{ old('condominio_id') == $condominio->id ? 'selected' : '' }}>{{ $condominio->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Use o campo de busca acima para filtrar condomínios</p>
                                <x-input-error :messages="$errors->get('condominio_id')" class="mt-2" />
                            </div>

                            <!-- Título -->
                            <div class="md:col-span-2">
                                <x-input-label for="titulo" :value="__('Título da Demanda')" />
                                <x-text-input id="titulo" class="block mt-1 w-full" type="text" name="titulo" :value="old('titulo')" required autofocus />
                                <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                            </div>

                            <!-- Descrição -->
                            <div class="md:col-span-2">
                                <x-input-label for="descricao" :value="__('Descrição')" />
                                <textarea id="descricao" name="descricao" rows="5" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('descricao') }}</textarea>
                                <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                            </div>

                            <!-- Prazo Limite -->
                            <div>
                                <x-input-label for="prazo_limite" :value="__('Prazo Limite')" />
                                <x-text-input id="prazo_limite" class="block mt-1 w-full" type="date" name="prazo_limite" :value="old('prazo_limite')" />
                                <x-input-error :messages="$errors->get('prazo_limite')" class="mt-2" />
                            </div>

                            <!-- Prestadores -->
                            <div class="md:col-span-2">
                                <x-input-label for="prestadores" :value="__('Prestadores (opcional)')" />
                                <div id="prestadores-container" class="mt-2 space-y-2 max-h-64 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded p-3">
                                    @foreach($prestadores as $prestador)
                                        <label class="flex items-center hover:bg-gray-50 dark:hover:bg-gray-700 p-1 rounded">
                                            <input type="checkbox" name="prestadores[]" value="{{ $prestador->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array($prestador->id, old('prestadores', [])) ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $prestador->nome_razao_social }}</span>
                                            @if($prestador->email)
                                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $prestador->email }})</span>
                                            @endif
                                        </label>
                                    @endforeach
                                    @if($prestadores->isEmpty())
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum prestador cadastrado. <a href="{{ route('prestadores.create') }}" class="text-blue-500 hover:text-blue-700">Cadastre um prestador primeiro</a></p>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Use o campo de busca acima para filtrar prestadores</p>
                                <x-input-error :messages="$errors->get('prestadores')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('demandas.index') }}" class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Criar Demanda') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
