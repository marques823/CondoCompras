<x-layouts.public-layout title="Cadastro Realizado">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                    <div class="mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                            <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            Cadastro Realizado com Sucesso!
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Seu cadastro foi registrado e nossa equipe entrará em contato em breve.
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6 text-left">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Dados Cadastrados:</h3>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <p><span class="font-medium">Nome/Razão Social:</span> {{ $prestador->nome_razao_social }}</p>
                            <p><span class="font-medium">Empresa:</span> {{ $empresa->nome }}</p>
                            @if($prestador->cpf_cnpj)
                                <p><span class="font-medium">CPF/CNPJ:</span> {{ $prestador->cpf_cnpj }}</p>
                            @endif
                            @if($prestador->email)
                                <p><span class="font-medium">E-mail:</span> {{ $prestador->email }}</p>
                            @endif
                            <p><span class="font-medium">Status:</span> <span class="px-2 py-1 rounded bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Cadastrado</span></p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Você pode fechar esta página. Em breve nossa equipe analisará seu cadastro e entrará em contato.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public-layout>
