<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalhes do Orçamento') }}
            </h2>
            <a href="{{ route('orcamentos.index') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Informações do Orçamento -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Informações do Orçamento</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Demanda</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('demandas.show', $orcamento->demanda) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $orcamento->demanda->titulo }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Condomínio</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $orcamento->demanda->condominio->nome }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prestador</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $orcamento->prestador->nome_razao_social }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    R$ {{ number_format($orcamento->valor, 2, ',', '.') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <p class="mt-1">
                                    @php
                                        $statusColors = [
                                            'recebido' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            'aprovado' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'rejeitado' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        ];
                                        $statusLabels = [
                                            'recebido' => 'Recebido',
                                            'aprovado' => 'Aprovado',
                                            'rejeitado' => 'Rejeitado',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$orcamento->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$orcamento->status] ?? ucfirst($orcamento->status) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de Envio</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $orcamento->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @if($orcamento->validade)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Validade</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $orcamento->validade->format('d/m/Y') }}</p>
                            </div>
                            @endif
                            @if($orcamento->status === 'aprovado' && $orcamento->aprovado_em)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aprovado em</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $orcamento->aprovado_em->format('d/m/Y H:i') }}</p>
                            </div>
                            @endif
                        </div>
                        
                        @if($orcamento->descricao)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrição do Orçamento</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $orcamento->descricao }}</p>
                        </div>
                        @endif

                        @if($orcamento->observacoes)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $orcamento->observacoes }}</p>
                        </div>
                        @endif

                        @if($orcamento->status === 'rejeitado' && $orcamento->motivo_rejeicao)
                        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded">
                            <label class="block text-sm font-medium text-red-800 dark:text-red-200 mb-1">Motivo da Rejeição</label>
                            <p class="text-sm text-red-700 dark:text-red-300">{{ $orcamento->motivo_rejeicao }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Documentos Anexados -->
                    @if($orcamento->documentos->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Documentos Anexados</h3>
                        <div class="space-y-2">
                            @foreach($orcamento->documentos as $documento)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center flex-1">
                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 block">{{ $documento->nome_original }}</span>
                                            @if($documento->tamanho)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ number_format($documento->tamanho / 1024, 2) }} KB
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex gap-2 ml-4">
                                        <a href="{{ route('documentos.visualizar', $documento) }}" 
                                           target="_blank" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 dark:bg-blue-900 dark:text-blue-300 border border-blue-300 dark:border-blue-700 rounded hover:bg-blue-100 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Visualizar
                                        </a>
                                        <a href="{{ route('documentos.download', $documento) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 dark:bg-green-900 dark:text-green-300 border border-green-300 dark:border-green-700 rounded hover:bg-green-100 dark:hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Ações -->
                    @if($orcamento->status === 'recebido')
                    <div class="mb-6 flex gap-2">
                        <button type="button" onclick="abrirModalAprovarOrcamento()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md">
                            Aprovar Orçamento
                        </button>
                        <button type="button" onclick="abrirModalRejeitarOrcamento()" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md">
                            Rejeitar Orçamento
                        </button>
                        <button type="button" onclick="abrirModalNegociacao()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                            Negociar
                        </button>
                    </div>
                    @endif

                    <!-- Negociações -->
                    @if($orcamento->negociacoes->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4">Negociações</h3>
                        <div class="space-y-4">
                            @foreach($orcamento->negociacoes as $negociacao)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                    @if($negociacao->status === 'aceita') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($negociacao->status === 'recusada') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @endif">
                                                    @if($negociacao->status === 'aceita') Aceita
                                                    @elseif($negociacao->status === 'recusada') Recusada
                                                    @else Pendente
                                                    @endif
                                                </span>
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    @if($negociacao->tipo === 'desconto') Desconto
                                                    @elseif($negociacao->tipo === 'parcelamento') Parcelamento
                                                    @else Contraproposta
                                                    @endif
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $negociacao->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            
                                            <div class="mt-2 space-y-1">
                                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                                    <strong>Valor Original:</strong> R$ {{ number_format($negociacao->valor_original, 2, ',', '.') }}
                                                </p>
                                                
                                                @if($negociacao->status === 'aceita' && $negociacao->valor_solicitado)
                                                    @if($negociacao->tipo === 'desconto')
                                                        @php
                                                            $valorDesconto = $negociacao->valor_original - $negociacao->valor_solicitado;
                                                            $percentualDesconto = ($valorDesconto / $negociacao->valor_original) * 100;
                                                        @endphp
                                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                                            <strong>Desconto Aplicado:</strong> R$ {{ number_format($valorDesconto, 2, ',', '.') }} ({{ number_format($percentualDesconto, 2, ',', '.') }}%)
                                                        </p>
                                                        <p class="text-sm font-semibold text-green-700 dark:text-green-400">
                                                            <strong>Valor Final:</strong> R$ {{ number_format($negociacao->valor_solicitado, 2, ',', '.') }}
                                                        </p>
                                                    @elseif($negociacao->tipo === 'parcelamento')
                                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                                            <strong>Valor por Parcela:</strong> R$ {{ number_format($negociacao->valor_solicitado, 2, ',', '.') }}
                                                        </p>
                                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                                            <strong>Número de Parcelas:</strong> {{ $negociacao->parcelas }}x
                                                        </p>
                                                        <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">
                                                            <strong>Valor Total:</strong> R$ {{ number_format($negociacao->valor_solicitado * $negociacao->parcelas, 2, ',', '.') }}
                                                        </p>
                                                    @else
                                                        <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">
                                                            <strong>Valor Proposto (Aceito):</strong> R$ {{ number_format($negociacao->valor_solicitado, 2, ',', '.') }}
                                                        </p>
                                                    @endif
                                                @elseif($negociacao->status === 'pendente')
                                                    <p class="text-sm text-yellow-700 dark:text-yellow-400 italic">
                                                        Aguardando resposta do prestador...
                                                    </p>
                                                @endif
                                            </div>
                                            
                                            @if($negociacao->mensagem_solicitacao)
                                                <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded">
                                                    <p class="text-xs font-medium text-gray-800 dark:text-gray-200 mb-1">Solicitação:</p>
                                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $negociacao->mensagem_solicitacao }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($negociacao->mensagem_resposta)
                                                <div class="mt-3 p-3 
                                                    @if($negociacao->status === 'aceita') bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700
                                                    @else bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700
                                                    @endif rounded">
                                                    <p class="text-xs font-medium 
                                                        @if($negociacao->status === 'aceita') text-green-800 dark:text-green-200
                                                        @else text-red-800 dark:text-red-200
                                                        @endif mb-1">Resposta do Prestador:</p>
                                                    <p class="text-sm 
                                                        @if($negociacao->status === 'aceita') text-green-700 dark:text-green-300
                                                        @else text-red-700 dark:text-red-300
                                                        @endif">{{ $negociacao->mensagem_resposta }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($negociacao->respondido_em)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                    <strong>Respondido em:</strong> {{ $negociacao->respondido_em->format('d/m/Y H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Aprovar Orçamento -->
    <div id="modal-aprovar-orcamento" class="hidden fixed inset-0 z-50 overflow-y-auto modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('demandas.aprovar-orcamento', [$orcamento->demanda, $orcamento]) }}">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Aprovar Orçamento
                        </h3>
                        <div class="mb-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                <strong>Prestador:</strong> {{ $orcamento->prestador->nome_razao_social }}
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                <strong>Valor:</strong> R$ {{ number_format($orcamento->valor, 2, ',', '.') }}
                            </p>
                            <label for="observacoes_aprovar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Observações (opcional)
                            </label>
                            <textarea id="observacoes_aprovar" name="observacoes" rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Aprovar
                        </button>
                        <button type="button" onclick="fecharModalAprovarOrcamento()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Rejeitar Orçamento -->
    <div id="modal-rejeitar-orcamento" class="hidden fixed inset-0 z-50 overflow-y-auto modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('demandas.rejeitar-orcamento', [$orcamento->demanda, $orcamento]) }}">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Rejeitar Orçamento
                        </h3>
                        <div class="mb-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                <strong>Prestador:</strong> {{ $orcamento->prestador->nome_razao_social }}
                            </p>
                            <label for="motivo_rejeicao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Motivo da Rejeição <span class="text-red-500">*</span>
                            </label>
                            <textarea id="motivo_rejeicao" name="motivo_rejeicao" rows="4" required class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Rejeitar
                        </button>
                        <button type="button" onclick="fecharModalRejeitarOrcamento()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Negociação -->
    <div id="modal-negociacao" class="hidden fixed inset-0 z-50 overflow-y-auto modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('demandas.criar-negociacao', [$orcamento->demanda, $orcamento]) }}">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Solicitar Negociação
                        </h3>
                        <div class="mb-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                                <strong>Prestador:</strong> {{ $orcamento->prestador->nome_razao_social }}
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                <strong>Valor Original:</strong> R$ {{ number_format($orcamento->valor, 2, ',', '.') }}
                            </p>
                            <div class="mb-4">
                                <label for="tipo_negociacao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tipo de Negociação <span class="text-red-500">*</span>
                                </label>
                                <select id="tipo_negociacao" name="tipo" required onchange="atualizarCamposNegociacao()" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="desconto">Solicitar Desconto</option>
                                    <option value="parcelamento">Solicitar Parcelamento</option>
                                    <option value="contraproposta">Enviar Contraproposta</option>
                                </select>
                            </div>
                            <div id="contraproposta_container" class="mb-4 hidden">
                                <label for="valor_solicitado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Valor da Contraproposta (R$) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="valor_solicitado" name="valor_solicitado" step="0.01" min="0.01" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="mensagem_solicitacao" id="label_mensagem" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Observações / Solicitação <span id="mensagem_required" class="text-red-500 hidden">*</span>
                                </label>
                                <textarea id="mensagem_solicitacao" name="mensagem_solicitacao" rows="4" maxlength="1000" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Descreva sua solicitação..."></textarea>
                                <p id="info_mensagem" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Enviar Negociação
                        </button>
                        <button type="button" onclick="fecharModalNegociacao()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalAprovarOrcamento() {
            document.getElementById('modal-aprovar-orcamento').classList.remove('hidden');
        }

        function fecharModalAprovarOrcamento() {
            document.getElementById('modal-aprovar-orcamento').classList.add('hidden');
            document.getElementById('observacoes_aprovar').value = '';
        }

        function abrirModalRejeitarOrcamento() {
            document.getElementById('modal-rejeitar-orcamento').classList.remove('hidden');
        }

        function fecharModalRejeitarOrcamento() {
            document.getElementById('modal-rejeitar-orcamento').classList.add('hidden');
            document.getElementById('motivo_rejeicao').value = '';
        }

        function abrirModalNegociacao() {
            document.getElementById('modal-negociacao').classList.remove('hidden');
        }

        function fecharModalNegociacao() {
            document.getElementById('modal-negociacao').classList.add('hidden');
            document.getElementById('mensagem_solicitacao').value = '';
            document.getElementById('valor_solicitado').value = '';
        }

        function atualizarCamposNegociacao() {
            const tipo = document.getElementById('tipo_negociacao').value;
            const contrapropostaContainer = document.getElementById('contraproposta_container');
            const valorSolicitado = document.getElementById('valor_solicitado');
            const labelMensagem = document.getElementById('label_mensagem');
            const infoMensagem = document.getElementById('info_mensagem');
            const mensagemRequired = document.getElementById('mensagem_required');
            
            if (tipo === 'contraproposta') {
                contrapropostaContainer.classList.remove('hidden');
                valorSolicitado.required = true;
                labelMensagem.innerHTML = 'Observações (opcional)';
                infoMensagem.textContent = '';
                mensagemRequired.classList.add('hidden');
            } else {
                contrapropostaContainer.classList.add('hidden');
                valorSolicitado.required = false;
                valorSolicitado.value = '';
                
                if (tipo === 'desconto') {
                    labelMensagem.innerHTML = 'Observações / Solicitação <span class="text-gray-500">(opcional)</span>';
                    infoMensagem.textContent = 'O prestador escolherá o valor do desconto ao aceitar a negociação.';
                    mensagemRequired.classList.add('hidden');
                } else if (tipo === 'parcelamento') {
                    labelMensagem.innerHTML = 'Observações / Solicitação <span class="text-gray-500">(opcional)</span>';
                    infoMensagem.textContent = 'O prestador escolherá a quantidade de parcelas ao aceitar a negociação.';
                    mensagemRequired.classList.add('hidden');
                }
            }
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                fecharModalAprovarOrcamento();
                fecharModalRejeitarOrcamento();
                fecharModalNegociacao();
            }
        });
    </script>
</x-app-layout>
