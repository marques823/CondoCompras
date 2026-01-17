<x-layouts.public-layout title="Cadastro de Prestador">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            Cadastro de Prestador de Serviço
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            @if($empresa)
                                Cadastre-se para trabalhar com <strong>{{ $empresa->nome }}</strong>
                            @else
                                Preencha os dados abaixo para se cadastrar como prestador de serviço
                            @endif
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

                    <form method="POST" action="{{ route('publico.store-prestador') }}">
                        @csrf

                        @if($empresa)
                            <input type="hidden" name="token_empresa" value="{{ $empresa->token_cadastro }}">
                        @else
                            <!-- Código de Cadastro -->
                            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">
                                <label for="token_empresa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Código de Cadastro <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="token_empresa" name="token_empresa" value="{{ old('token_empresa') }}" required class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm uppercase" placeholder="Digite o código fornecido pela empresa">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Informe o código de cadastro fornecido pela empresa administradora</p>
                                <x-input-error :messages="$errors->get('token_empresa')" class="mt-2" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome/Razão Social -->
                            <div class="md:col-span-2">
                                <label for="nome_razao_social" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nome / Razão Social <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nome_razao_social" name="nome_razao_social" value="{{ old('nome_razao_social') }}" required class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('nome_razao_social')" class="mt-2" />
                            </div>

                            <!-- Tipo -->
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tipo <span class="text-red-500">*</span>
                                </label>
                                <select id="tipo" name="tipo" required class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="juridica" {{ old('tipo') == 'juridica' ? 'selected' : '' }}>Pessoa Jurídica</option>
                                    <option value="fisica" {{ old('tipo') == 'fisica' ? 'selected' : '' }}>Pessoa Física</option>
                                </select>
                                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                            </div>

                            <!-- CPF/CNPJ -->
                            <div>
                                <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    CPF / CNPJ
                                </label>
                                <input type="text" id="cpf_cnpj" name="cpf_cnpj" value="{{ old('cpf_cnpj') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Digite o CPF ou CNPJ">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Para CNPJ, os dados serão preenchidos automaticamente</p>
                                <x-input-error :messages="$errors->get('cpf_cnpj')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    E-mail <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Telefone
                                </label>
                                <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                            </div>

                            <!-- Celular -->
                            <div>
                                <label for="celular" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Celular
                                </label>
                                <input type="text" id="celular" name="celular" value="{{ old('celular') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('celular')" class="mt-2" />
                            </div>

                            <!-- Endereço -->
                            <div class="md:col-span-2">
                                <label for="endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Endereço
                                </label>
                                <input type="text" id="endereco" name="endereco" value="{{ old('endereco') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
                            </div>

                            <!-- Bairro -->
                            <div>
                                <label for="bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Bairro
                                </label>
                                <input type="text" id="bairro" name="bairro" value="{{ old('bairro') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
                            </div>

                            <!-- Cidade -->
                            <div>
                                <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Cidade
                                </label>
                                <input type="text" id="cidade" name="cidade" value="{{ old('cidade') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('cidade')" class="mt-2" />
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Estado (UF)
                                </label>
                                <input type="text" id="estado" name="estado" maxlength="2" value="{{ old('estado') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                            </div>

                            <!-- CEP -->
                            <div>
                                <label for="cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    CEP
                                </label>
                                <input type="text" id="cep" name="cep" value="{{ old('cep') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('cep')" class="mt-2" />
                            </div>

                            <!-- Áreas de Atuação -->
                            <div class="md:col-span-2">
                                <label for="areas_atuacao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Áreas de Atuação
                                </label>
                                <textarea id="areas_atuacao" name="areas_atuacao" rows="3" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Ex: Elétrica, Hidráulica, Pintura, Reformas...">{{ old('areas_atuacao') }}</textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Descreva as áreas de atuação do prestador, separadas por vírgula ou uma por linha</p>
                                <x-input-error :messages="$errors->get('areas_atuacao')" class="mt-2" />
                            </div>

                            <!-- Observações -->
                            <div class="md:col-span-2">
                                <label for="observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Observações
                                </label>
                                <textarea id="observacoes" name="observacoes" rows="3" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('observacoes') }}</textarea>
                                <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cadastrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/cnpj-cpf-autocomplete.js')
</x-layouts.public-layout>
