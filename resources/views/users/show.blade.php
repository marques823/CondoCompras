<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalhes do Usuário') }}
            </h2>
            <div>
                <a href="{{ route('users.edit', $userModel->id) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Editar
                </a>
                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nome -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Nome</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $userModel->name }}</p>
                        </div>

                        <!-- Perfil -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Perfil</h3>
                            @php
                                $perfilColors = [
                                    'admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                    'administradora' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'usuario' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                    'zelador' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                ];
                                $perfilLabels = [
                                    'admin' => 'Super Admin',
                                    'administradora' => 'Administradora',
                                    'usuario' => 'Usuário',
                                    'zelador' => 'Zelador',
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $perfilColors[$userModel->perfil] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $perfilLabels[$userModel->perfil] ?? $userModel->perfil }}
                            </span>
                        </div>

                        <!-- E-mail -->
                        @if($userModel->email)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">E-mail</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $userModel->email }}</p>
                        </div>
                        @endif

                        <!-- Telefone -->
                        @if($userModel->telefone)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Telefone</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $userModel->telefone }}</p>
                        </div>
                        @endif

                        <!-- Administradora -->
                        @if($userModel->administradora)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Administradora</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $userModel->administradora->nome }}</p>
                        </div>
                        @endif

                        <!-- Condomínio -->
                        @if($userModel->condominio)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Condomínio</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $userModel->condominio->nome }}</p>
                        </div>
                        @endif

                        <!-- Data de Criação -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Cadastrado em</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $userModel->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <!-- Última Atualização -->
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Última Atualização</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $userModel->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
