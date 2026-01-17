<x-layouts.public-layout title="Criar Demanda">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            Nova Demanda - {{ $condominio->nome }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Preencha os dados abaixo para criar uma nova demanda de serviço
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('publico.store-demanda', ['token' => $link->token]) }}">
                        @csrf

                        <div class="space-y-6">
                            <!-- Tipo de Serviço -->
                            <div>
                                <label for="categoria_servico_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tipo de Serviço <span class="text-red-500">*</span>
                                </label>
                                <select id="categoria_servico_id" name="categoria_servico_id" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Selecione o tipo de serviço</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categoria_servico_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Selecione o tipo de serviço desejado</p>
                                <x-input-error :messages="$errors->get('categoria_servico_id')" class="mt-2" />
                            </div>

                            <!-- Título -->
                            <div>
                                <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Título da Demanda <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}" required class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Ex: Manutenção do sistema elétrico">
                                <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                            </div>

                            <!-- Descrição -->
                            <div>
                                <label for="descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Descrição Detalhada <span class="text-red-500">*</span>
                                </label>
                                <textarea id="descricao" name="descricao" rows="6" required class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Descreva detalhadamente o serviço necessário...">{{ old('descricao') }}</textarea>
                                <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                            </div>

                            <!-- Prazo Limite -->
                            <div>
                                <label for="prazo_limite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Prazo Limite
                                </label>
                                <input type="date" id="prazo_limite" name="prazo_limite" value="{{ old('prazo_limite') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Data limite para realização do serviço (opcional)</p>
                                <x-input-error :messages="$errors->get('prazo_limite')" class="mt-2" />
                            </div>

                            <!-- Dados do Solicitante -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Dados do Solicitante (opcional)</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Nome -->
                                    <div>
                                        <label for="nome_solicitante" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Nome
                                        </label>
                                        <input type="text" id="nome_solicitante" name="nome_solicitante" value="{{ old('nome_solicitante') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <x-input-error :messages="$errors->get('nome_solicitante')" class="mt-2" />
                                    </div>

                                    <!-- Telefone -->
                                    <div>
                                        <label for="telefone_solicitante" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Telefone
                                        </label>
                                        <input type="text" id="telefone_solicitante" name="telefone_solicitante" value="{{ old('telefone_solicitante') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="(00) 00000-0000">
                                        <x-input-error :messages="$errors->get('telefone_solicitante')" class="mt-2" />
                                    </div>

                                    <!-- Email -->
                                    <div class="md:col-span-2">
                                        <label for="email_solicitante" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            E-mail
                                        </label>
                                        <input type="email" id="email_solicitante" name="email_solicitante" value="{{ old('email_solicitante') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="email@exemplo.com">
                                        <x-input-error :messages="$errors->get('email_solicitante')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Enviar Demanda
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public-layout>
