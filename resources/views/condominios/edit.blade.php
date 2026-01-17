<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Condomínio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('condominios.update', $condominio) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div>
                                <x-input-label for="nome" :value="__('Nome do Condomínio')" />
                                <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $condominio->nome)" required autofocus />
                                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                            </div>

                            <!-- CNPJ -->
                            <div>
                                <x-input-label for="cnpj" :value="__('CNPJ')" />
                                <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj', $condominio->cnpj)" placeholder="00.000.000/0000-00" maxlength="18" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Digite o CNPJ para preenchimento automático</p>
                                <x-input-error :messages="$errors->get('cnpj')" class="mt-2" />
                            </div>

                            <!-- Endereço -->
                            <div class="md:col-span-2">
                                <x-input-label for="endereco" :value="__('Endereço')" />
                                <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco', $condominio->endereco)" required />
                                <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                            </div>

                            <!-- Número -->
                            <div>
                                <x-input-label for="numero" :value="__('Número')" />
                                <x-text-input id="numero" class="block mt-1 w-full" type="text" name="numero" :value="old('numero', $condominio->numero)" />
                                <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                            </div>

                            <!-- Complemento -->
                            <div>
                                <x-input-label for="complemento" :value="__('Complemento')" />
                                <x-text-input id="complemento" class="block mt-1 w-full" type="text" name="complemento" :value="old('complemento', $condominio->complemento)" />
                                <x-input-error :messages="$errors->get('complemento')" class="mt-2" />
                            </div>

                            <!-- Bairro -->
                            <div>
                                <x-input-label for="bairro" :value="__('Bairro')" />
                                <x-text-input id="bairro" class="block mt-1 w-full" type="text" name="bairro" :value="old('bairro', $condominio->bairro)" />
                                <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                            </div>

                            <!-- Cidade -->
                            <div>
                                <x-input-label for="cidade" :value="__('Cidade')" />
                                <x-text-input id="cidade" class="block mt-1 w-full" type="text" name="cidade" :value="old('cidade', $condominio->cidade)" />
                                <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                            </div>

                            <!-- Estado -->
                            <div>
                                <x-input-label for="estado" :value="__('Estado (UF)')" />
                                <x-text-input id="estado" class="block mt-1 w-full" type="text" name="estado" maxlength="2" :value="old('estado', $condominio->estado)" />
                                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                            </div>

                            <!-- CEP -->
                            <div>
                                <x-input-label for="cep" :value="__('CEP')" />
                                <x-text-input id="cep" class="block mt-1 w-full" type="text" name="cep" :value="old('cep', $condominio->cep)" />
                                <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                            </div>

                            <!-- Síndico -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium mb-4">Dados do Síndico</h3>
                            </div>

                            <div>
                                <x-input-label for="sindico_nome" :value="__('Nome do Síndico')" />
                                <x-text-input id="sindico_nome" class="block mt-1 w-full" type="text" name="sindico_nome" :value="old('sindico_nome', $condominio->sindico_nome)" />
                                <x-input-error :messages="$errors->get('sindico_nome')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sindico_telefone" :value="__('Telefone do Síndico')" />
                                <x-text-input id="sindico_telefone" class="block mt-1 w-full" type="text" name="sindico_telefone" :value="old('sindico_telefone', $condominio->sindico_telefone)" />
                                <x-input-error :messages="$errors->get('sindico_telefone')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sindico_email" :value="__('E-mail do Síndico')" />
                                <x-text-input id="sindico_email" class="block mt-1 w-full" type="email" name="sindico_email" :value="old('sindico_email', $condominio->sindico_email)" />
                                <x-input-error :messages="$errors->get('sindico_email')" class="mt-2" />
                            </div>

                            <!-- Ativo -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="ativo" value="1" {{ old('ativo', $condominio->ativo) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Ativo</span>
                                </label>
                            </div>

                            <!-- Tags -->
                            <div class="md:col-span-2">
                                <x-input-label for="tags" :value="__('Tags / Marcadores')" />
                                <div class="mt-2 space-y-2">
                                    @if($tags->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($tags as $tag)
                                                <label class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium cursor-pointer hover:opacity-80 transition-opacity" style="background-color: {{ $tag->cor }}20; color: {{ $tag->cor }}; border: 1px solid {{ $tag->cor }}40;">
                                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only peer" {{ in_array($tag->id, old('tags', $condominio->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                                <textarea id="observacoes" name="observacoes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('observacoes', $condominio->observacoes) }}</textarea>
                                <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                            </div>

                            <!-- Seção Zelador (Opcional) -->
                            <div class="md:col-span-2">
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                                    <h3 class="text-lg font-medium mb-4">Usuário Zelador</h3>
                                    @php
                                        $zelador = \App\Models\User::where('condominio_id', $condominio->id)
                                            ->where('perfil', 'zelador')
                                            ->first();
                                    @endphp
                                    
                                    @if($zelador)
                                        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Zelador atual:</p>
                                            <p class="text-sm text-blue-800 dark:text-blue-200"><strong>Nome:</strong> {{ $zelador->name }}</p>
                                            <p class="text-sm text-blue-800 dark:text-blue-200"><strong>Telefone:</strong> {{ $zelador->telefone ?? 'Não informado' }}</p>
                                            <p class="text-xs text-blue-600 dark:text-blue-300 mt-2">Para alterar, preencha os campos abaixo com novos dados.</p>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Nenhum zelador cadastrado. Preencha os campos abaixo para criar um usuário zelador.</p>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Nome do Zelador -->
                                        <div>
                                            <x-input-label for="zelador_nome" :value="__('Nome do Zelador')" />
                                            <x-text-input id="zelador_nome" class="block mt-1 w-full" type="text" name="zelador_nome" :value="old('zelador_nome', $zelador->name ?? '')" />
                                            <x-input-error :messages="$errors->get('zelador_nome')" class="mt-2" />
                                        </div>

                                        <!-- Telefone do Zelador -->
                                        <div>
                                            <x-input-label for="zelador_telefone" :value="__('Telefone/Celular do Zelador')" />
                                            <x-text-input id="zelador_telefone" class="block mt-1 w-full" type="tel" name="zelador_telefone" :value="old('zelador_telefone', $zelador->telefone ?? '')" placeholder="(00) 00000-0000" />
                                            <x-input-error :messages="$errors->get('zelador_telefone')" class="mt-2" />
                                        </div>

                                        <!-- Senha do Zelador -->
                                        <div>
                                            <x-input-label for="zelador_password" :value="__('Senha')" />
                                            <x-text-input id="zelador_password" class="block mt-1 w-full" type="password" name="zelador_password" />
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $zelador ? 'Deixe em branco para manter a senha atual' : 'Mínimo de 8 caracteres' }}</p>
                                            <x-input-error :messages="$errors->get('zelador_password')" class="mt-2" />
                                        </div>

                                        <!-- Confirmar Senha -->
                                        <div>
                                            <x-input-label for="zelador_password_confirmation" :value="__('Confirmar Senha')" />
                                            <x-text-input id="zelador_password_confirmation" class="block mt-1 w-full" type="password" name="zelador_password_confirmation" />
                                            <x-input-error :messages="$errors->get('zelador_password_confirmation')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('condominios.index') }}" class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
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
