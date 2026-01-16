<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Prestador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('prestadores.update', $prestador) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome/Razão Social -->
                            <div class="md:col-span-2">
                                <x-input-label for="nome_razao_social" :value="__('Nome / Razão Social')" />
                                <x-text-input id="nome_razao_social" class="block mt-1 w-full" type="text" name="nome_razao_social" :value="old('nome_razao_social', $prestador->nome_razao_social)" required autofocus />
                                <x-input-error :messages="$errors->get('nome_razao_social')" class="mt-2" />
                            </div>

                            <!-- Tipo -->
                            <div>
                                <x-input-label for="tipo" :value="__('Tipo')" />
                                <select id="tipo" name="tipo" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="juridica" {{ old('tipo', $prestador->tipo) == 'juridica' ? 'selected' : '' }}>Pessoa Jurídica</option>
                                    <option value="fisica" {{ old('tipo', $prestador->tipo) == 'fisica' ? 'selected' : '' }}>Pessoa Física</option>
                                </select>
                                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                            </div>

                            <!-- CPF/CNPJ -->
                            <div>
                                <x-input-label for="cpf_cnpj" :value="__('CPF / CNPJ')" />
                                <x-text-input id="cpf_cnpj" class="block mt-1 w-full" type="text" name="cpf_cnpj" :value="old('cpf_cnpj', $prestador->cpf_cnpj)" placeholder="Digite o CPF ou CNPJ" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Para CNPJ, os dados serão preenchidos automaticamente</p>
                                <x-input-error :messages="$errors->get('cpf_cnpj')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('E-mail')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $prestador->email)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Telefone -->
                            <div>
                                <x-input-label for="telefone" :value="__('Telefone')" />
                                <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" :value="old('telefone', $prestador->telefone)" />
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                            </div>

                            <!-- Celular -->
                            <div>
                                <x-input-label for="celular" :value="__('Celular')" />
                                <x-text-input id="celular" class="block mt-1 w-full" type="text" name="celular" :value="old('celular', $prestador->celular)" />
                                <x-input-error :messages="$errors->get('celular')" class="mt-2" />
                            </div>

                            <!-- Endereço -->
                            <div class="md:col-span-2">
                                <x-input-label for="endereco" :value="__('Endereço')" />
                                <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco', $prestador->endereco)" />
                                <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                            </div>

                            <!-- Ativo -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="ativo" value="1" {{ old('ativo', $prestador->ativo) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Ativo</span>
                                </label>
                            </div>

                            <!-- Áreas de Atuação -->
                            <div class="md:col-span-2">
                                <x-input-label for="areas_atuacao" :value="__('Áreas de Atuação')" />
                                <textarea id="areas_atuacao" name="areas_atuacao" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Ex: Elétrica, Hidráulica, Pintura, Reformas...">{{ old('areas_atuacao', $prestador->areas_atuacao) }}</textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Descreva as áreas de atuação do prestador, separadas por vírgula ou uma por linha</p>
                                <x-input-error :messages="$errors->get('areas_atuacao')" class="mt-2" />
                            </div>

                            <!-- Tags -->
                            <div class="md:col-span-2">
                                <x-input-label for="tags" :value="__('Tags / Marcadores')" />
                                <div class="mt-2 space-y-2">
                                    @if($tags->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($tags as $tag)
                                                <label class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium cursor-pointer hover:opacity-80 transition-opacity" style="background-color: {{ $tag->cor }}20; color: {{ $tag->cor }}; border: 1px solid {{ $tag->cor }}40;">
                                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only peer" {{ in_array($tag->id, old('tags', $prestador->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                    <span class="peer-checked:font-bold">{{ $tag->nome }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma tag disponível. <a href="{{ route('tags.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Criar tag</a></p>
                                    @endif
                                </div>
                                <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                            </div>

                            <!-- Observações -->
                            <div class="md:col-span-2">
                                <x-input-label for="observacoes" :value="__('Observações')" />
                                <textarea id="observacoes" name="observacoes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('observacoes', $prestador->observacoes) }}</textarea>
                                <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('prestadores.index') }}" class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Atualizar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
