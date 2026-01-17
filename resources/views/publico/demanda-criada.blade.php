<x-layouts.public-layout title="Demanda Criada">
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
                            Demanda Criada com Sucesso!
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Sua demanda foi registrada e nossa equipe entrará em contato em breve.
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6 text-left">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Detalhes da Demanda:</h3>
                        <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <p><span class="font-medium">Título:</span> {{ $demanda->titulo }}</p>
                            <p><span class="font-medium">Condomínio:</span> {{ $condominio->nome }}</p>
                            <p><span class="font-medium">Status:</span> <span class="px-2 py-1 rounded bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">Aberta</span></p>
                            @if($demanda->prazo_limite)
                                <p><span class="font-medium">Prazo Limite:</span> {{ \Carbon\Carbon::parse($demanda->prazo_limite)->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Você pode fechar esta página. Em breve nossa equipe analisará sua solicitação.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public-layout>
