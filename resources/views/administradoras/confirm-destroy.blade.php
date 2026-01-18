<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Confirmar Exclusão de Administradora') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($temDadosCriticos)
                <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                <strong>Atenção!</strong> Esta administradora possui dados relacionados. A exclusão será convertida em <strong>desativação</strong> para preservar o histórico.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-red-50 dark:bg-red-900 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 dark:text-red-300">
                                <strong>Atenção!</strong> Esta ação não pode ser desfeita. A administradora será excluída permanentemente.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ $administradora->nome }}</h3>
                    
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Dados que serão afetados:</h4>
                        <ul class="list-disc list-inside space-y-2 text-sm">
                            <li><strong>{{ $administradora->usuarios_count }}</strong> usuário(s) {{ $temDadosCriticos ? '(serão desativados)' : '(serão removidos)' }}</li>
                            <li><strong>{{ $administradora->condominios_count }}</strong> condomínio(s) {{ $temDadosCriticos ? '(serão desativados)' : '(serão removidos)' }}</li>
                            <li><strong>{{ $administradora->prestadores_count }}</strong> prestador(es) (serão removidos)</li>
                            <li><strong>{{ $administradora->demandas_count }}</strong> demanda(s) {{ $temDadosCriticos ? '(serão preservadas)' : '(serão removidas)' }}</li>
                            <li><strong>{{ $administradora->documentos_count }}</strong> documento(s) (serão removidos)</li>
                            <li><strong>{{ $administradora->zeladores_count }}</strong> zelador(es) (serão removidos)</li>
                            @if($orcamentosCount > 0)
                            <li><strong>{{ $orcamentosCount }}</strong> orçamento(s) {{ $temDadosCriticos ? '(serão preservados)' : '(serão removidos)' }}</li>
                            @endif
                        </ul>
                    </div>

                    @if($temDadosCriticos)
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded mb-6">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                <strong>O que acontecerá:</strong> Como há dados críticos (usuários, condomínios ou demandas), a administradora será <strong>desativada</strong> ao invés de excluída. Isso preserva todo o histórico e permite reativar no futuro se necessário.
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('administradoras.destroy', $administradora->id) }}" class="space-y-6">
                        @csrf
                        @method('DELETE')

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                            <label for="confirmacao" class="flex items-center mb-4">
                                <input type="checkbox" id="confirmacao" name="confirmacao" value="1" required class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Eu entendo as consequências e desejo prosseguir
                                </span>
                            </label>

                            <div>
                                <label for="confirmacao_texto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Digite <strong>EXCLUIR</strong> para confirmar:
                                </label>
                                <input type="text" id="confirmacao_texto" name="confirmacao_texto" required 
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="Digite EXCLUIR">
                                <x-input-error :messages="$errors->get('confirmacao_texto')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('administradoras.show', $administradora->id) }}" 
                               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                                {{ $temDadosCriticos ? 'Desativar Administradora' : 'Excluir Administradora' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
