<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalhes da Administradora') }}
            </h2>
            <div>
                <a href="{{ route('administradoras.edit', $empresa->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Editar
                </a>
                <a href="{{ route('administradoras.confirm-destroy', $empresa->id) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Excluir
                </a>
                <a href="{{ route('administradoras.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nome -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Nome Fantasia</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $empresa->nome }}</p>
                        </div>

                        <!-- Razão Social -->
                        @if($empresa->razao_social)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Razão Social</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $empresa->razao_social }}</p>
                        </div>
                        @endif

                        <!-- CNPJ -->
                        @if($empresa->cnpj)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">CNPJ</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $empresa->cnpj }}</p>
                        </div>
                        @endif

                        <!-- E-mail -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2">E-mail</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $empresa->email }}</p>
                        </div>

                        <!-- Telefone -->
                        @if($empresa->telefone)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Telefone</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $empresa->telefone }}</p>
                        </div>
                        @endif

                        <!-- Endereço -->
                        @if($empresa->endereco)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Endereço</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $empresa->endereco }}</p>
                        </div>
                        @endif

                        <!-- Status -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Status</h3>
                            @if($empresa->ativo)
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Ativa
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Inativa
                                </span>
                            @endif
                        </div>

                        <!-- Data de Criação -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Cadastrada em</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $empresa->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Usuários</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $empresa->usuarios_count }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Condomínios</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $empresa->condominios_count }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Prestadores</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $empresa->prestadores_count }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Demandas</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $empresa->demandas_count }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
