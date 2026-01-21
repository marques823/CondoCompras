<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acesso Seguro - Link de Prestador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Função para formatar CPF
        function formatarCPF(input) {
            let valor = input.value.replace(/\D/g, '');
            if (valor.length <= 11) {
                valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
                valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
                valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            input.value = valor;
        }

        // Função para formatar CNPJ
        function formatarCNPJ(input) {
            let valor = input.value.replace(/\D/g, '');
            if (valor.length <= 14) {
                valor = valor.replace(/(\d{2})(\d)/, '$1.$2');
                valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
                valor = valor.replace(/(\d{3})(\d)/, '$1/$2');
                valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
            }
            input.value = valor;
        }

        // Auto-detecta CPF ou CNPJ e formata
        function formatarDocumento(input) {
            let valor = input.value.replace(/\D/g, '');
            if (valor.length <= 11) {
                formatarCPF(input);
            } else {
                formatarCNPJ(input);
            }
        }

        // Máscara para token de acesso (5 caracteres alfanuméricos maiúsculos)
        function formatarToken(input) {
            input.value = input.value.replace(/[^A-Z0-9]/g, '').toUpperCase().substring(0, 5);
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900">Acesso Seguro</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Para acessar este link, informe seu CPF/CNPJ e o token de acesso
                </p>
            </div>

            <!-- Mensagens de Erro/Sucesso -->
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulário de Login -->
            <form method="POST" action="{{ isset($link) && $link instanceof \App\Models\LinkPrestador ? route('prestador.link.login.processar', $link->token) : route('publico.demanda.login.processar', $link->token) }}" class="space-y-6">
                @csrf

                <!-- Campo CPF/CNPJ -->
                <div>
                    <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700 mb-2">
                        CPF ou CNPJ <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="cpf_cnpj" 
                        name="cpf_cnpj" 
                        required
                        autocomplete="off"
                        oninput="formatarDocumento(this)"
                        placeholder="000.000.000-00 ou 00.000.000/0000-00"
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('cpf_cnpj') border-red-500 @enderror"
                        value="{{ old('cpf_cnpj') }}">
                    @error('cpf_cnpj')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campo Token de Acesso -->
                <div>
                    <label for="token_acesso" class="block text-sm font-medium text-gray-700 mb-2">
                        Token de Acesso <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="token_acesso" 
                        name="token_acesso" 
                        required
                        autocomplete="off"
                        maxlength="5"
                        oninput="formatarToken(this)"
                        placeholder="A1B2C"
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm text-center text-2xl font-bold tracking-widest uppercase @error('token_acesso') border-red-500 @enderror"
                        value="{{ old('token_acesso') }}">
                    <p class="mt-1 text-xs text-gray-500">
                        Digite o token de 5 caracteres (letras maiúsculas e números)
                    </p>
                    @error('token_acesso')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botão de Submit -->
                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        Acessar
                    </button>
                </div>
            </form>

            <!-- Informações Adicionais -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-center text-gray-500">
                    <svg class="inline-block h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Este link é exclusivo e seguro. O token de acesso foi enviado junto com o link.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
