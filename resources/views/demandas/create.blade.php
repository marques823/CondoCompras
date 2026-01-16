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
                                <x-input-label for="condominio_search" :value="__('Condomínio')" />
                                <div class="relative">
                                    <input type="hidden" id="condominio_id" name="condominio_id" value="{{ old('condominio_id') }}" required>
                                    <input type="text" 
                                           id="condominio_search" 
                                           class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                           placeholder="Digite para buscar condomínio por nome, bairro ou cidade..."
                                           autocomplete="off"
                                           value="{{ old('condominio_id') ? $condominios->firstWhere('id', old('condominio_id'))?->nome : '' }}">
                                    <div id="condominio-autocomplete" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto hidden">
                                        <!-- Os resultados serão inseridos aqui pelo JavaScript -->
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Digite para buscar e selecionar um condomínio</p>
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
                                
                                <!-- Campo de Busca de Prestadores -->
                                <input type="text" 
                                       id="prestadores-search" 
                                       class="block w-full mt-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm mb-3" 
                                       placeholder="Buscar prestadores por nome, CNPJ, bairro, cidade ou áreas de atuação...">
                                
                                <!-- Filtros de Tags -->
                                @php
                                    $tagsPrestador = $tags->filter(function($tag) {
                                        return in_array($tag->tipo, ['prestador', 'ambos']);
                                    });
                                @endphp
                                @if($tagsPrestador->count() > 0)
                                    <div class="mt-2 mb-3">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Filtrar por Tags:</label>
                                        <div class="flex flex-wrap gap-2" id="prestadores-tags-filter">
                                            @foreach($tagsPrestador as $tag)
                                                <label class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium cursor-pointer hover:opacity-80 transition-opacity border" style="background-color: {{ $tag->cor }}20; color: {{ $tag->cor }}; border-color: {{ $tag->cor }}40;">
                                                    <input type="checkbox" value="{{ $tag->id }}" class="sr-only prestador-tag-filter" data-tag-id="{{ $tag->id }}">
                                                    <span>{{ $tag->nome }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Container de Prestadores -->
                                <div id="prestadores-container" class="mt-2 space-y-2 max-h-80 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded p-3">
                                    @foreach($prestadores as $prestador)
                                        <label class="prestador-item flex items-start hover:bg-gray-50 dark:hover:bg-gray-700 p-2 rounded cursor-pointer border border-transparent hover:border-gray-300 dark:hover:border-gray-600" 
                                               data-nome="{{ strtolower($prestador->nome_razao_social) }}"
                                               data-cnpj="{{ strtolower($prestador->cpf_cnpj ?? '') }}"
                                               data-bairro="{{ strtolower($prestador->bairro ?? '') }}"
                                               data-cidade="{{ strtolower($prestador->cidade ?? '') }}"
                                               data-areas-atuacao="{{ strtolower($prestador->areas_atuacao ?? '') }}"
                                               data-tags="{{ $prestador->tags->pluck('id')->implode(',') }}">
                                            <input type="checkbox" name="prestadores[]" value="{{ $prestador->id }}" class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array($prestador->id, old('prestadores', [])) ? 'checked' : '' }}>
                                            <div class="ml-2 flex-1">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $prestador->nome_razao_social }}</div>
                                                <div class="mt-1 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                    @if($prestador->cpf_cnpj)
                                                        <span>CNPJ: {{ $prestador->cpf_cnpj }}</span>
                                                    @endif
                                                    @if($prestador->bairro)
                                                        <span>• {{ $prestador->bairro }}</span>
                                                    @endif
                                                    @if($prestador->cidade)
                                                        <span>• {{ $prestador->cidade }}{{ $prestador->estado ? ' - ' . $prestador->estado : '' }}</span>
                                                    @endif
                                                </div>
                                                @if($prestador->areas_atuacao)
                                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        <span class="font-medium">Áreas: </span>{{ $prestador->areas_atuacao }}
                                                    </div>
                                                @endif
                                                @if($prestador->tags->count() > 0)
                                                    <div class="mt-1 flex flex-wrap gap-1">
                                                        @foreach($prestador->tags as $tag)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium" style="background-color: {{ $tag->cor }}20; color: {{ $tag->cor }}; border: 1px solid {{ $tag->cor }}40;">
                                                                {{ $tag->nome }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                    @if($prestadores->isEmpty())
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum prestador cadastrado. <a href="{{ route('prestadores.create') }}" class="text-blue-500 hover:text-blue-700">Cadastre um prestador primeiro</a></p>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Use o campo de busca acima para filtrar por nome, CNPJ, bairro, cidade ou áreas de atuação</p>
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

    <div id="condominios-data" data-condominios='@json($condominiosData)' style="display: none;"></div>

    <script>
        const condominiosDataEl = document.getElementById('condominios-data');
        if (condominiosDataEl) {
            window.condominiosData = JSON.parse(condominiosDataEl.getAttribute('data-condominios') || '[]');
        } else {
            window.condominiosData = [];
        }
    </script>
</x-app-layout>
