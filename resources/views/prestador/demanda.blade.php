<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Demanda - {{ $demanda->titulo }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $demanda->titulo }}</h1>
                <p class="text-gray-600 mb-4">Condomínio: <strong>{{ $demanda->condominio->nome }}</strong></p>
            </div>

            <!-- Informações do Condomínio -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informações do Condomínio</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome</label>
                        <p class="mt-1 text-gray-900">{{ $demanda->condominio->nome }}</p>
                    </div>

                    @if($demanda->condominio->cnpj)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                        <p class="mt-1 text-gray-900">{{ $demanda->condominio->cnpj }}</p>
                    </div>
                    @endif

                    @if($demanda->condominio->endereco)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                        <p class="mt-1 text-gray-900">
                            {{ $demanda->condominio->endereco }}
                            @if($demanda->condominio->numero), {{ $demanda->condominio->numero }}@endif
                            @if($demanda->condominio->complemento) - {{ $demanda->condominio->complemento }}@endif
                        </p>
                    </div>
                    @endif

                    @if($demanda->condominio->bairro)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bairro</label>
                        <p class="mt-1 text-gray-900">{{ $demanda->condominio->bairro }}</p>
                    </div>
                    @endif

                    @if($demanda->condominio->cidade)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cidade</label>
                        <p class="mt-1 text-gray-900">
                            {{ $demanda->condominio->cidade }}
                            @if($demanda->condominio->estado) - {{ $demanda->condominio->estado }}@endif
                        </p>
                    </div>
                    @endif

                    @if($demanda->condominio->cep)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">CEP</label>
                        <p class="mt-1 text-gray-900">{{ $demanda->condominio->cep }}</p>
                    </div>
                    @endif

                    @if($demanda->condominio->sindico_nome)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Síndico</label>
                        <p class="mt-1 text-gray-900">{{ $demanda->condominio->sindico_nome }}</p>
                        @if($demanda->condominio->sindico_telefone)
                            <p class="mt-1 text-sm text-gray-600">Tel: {{ $demanda->condominio->sindico_telefone }}</p>
                        @endif
                        @if($demanda->condominio->sindico_email)
                            <p class="mt-1 text-sm text-gray-600">Email: {{ $demanda->condominio->sindico_email }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contato do Zelador -->
            @if($zelador)
            <div class="bg-blue-50 border border-blue-200 rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-blue-900 mb-4">Contato do Zelador</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-800">Nome</label>
                        <p class="mt-1 text-blue-900 font-semibold">{{ $zelador->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-blue-800">Telefone/Celular</label>
                        <p class="mt-1 text-blue-900">
                            @if($zelador->telefone)
                                <a href="tel:{{ preg_replace('/[^0-9]/', '', $zelador->telefone) }}" class="hover:underline">{{ $zelador->telefone }}</a>
                            @else
                                <span class="text-gray-500">Não informado</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Informações da Demanda -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detalhes da Demanda</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descrição</label>
                        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $demanda->descricao }}</p>
                    </div>

                    @if($demanda->prazo_limite)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prazo Limite</label>
                        <p class="mt-1 text-gray-900">{{ $demanda->prazo_limite->format('d/m/Y') }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            @if($demanda->status == 'aberta') bg-blue-100 text-blue-800
                            @elseif($demanda->status == 'em_andamento') bg-yellow-100 text-yellow-800
                            @elseif($demanda->status == 'concluida') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $demanda->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Formulário de Orçamento ou Aviso -->
            @if(!$jaEnviouOrcamento)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Enviar Orçamento</h2>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="orcamentoForm" method="POST" action="{{ route('prestador.link.orcamento', $link->token) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        <!-- Valor -->
                        <div>
                            <label for="valor" class="block text-sm font-medium text-gray-700">Valor (R$)</label>
                            <input type="number" 
                                   id="valor" 
                                   name="valor" 
                                   step="0.01" 
                                   min="0" 
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="0.00">
                        </div>

                        <!-- Descrição -->
                        <div>
                            <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição do Orçamento</label>
                            <textarea id="descricao" 
                                      name="descricao" 
                                      rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                      placeholder="Descreva os serviços que serão realizados..."></textarea>
                        </div>

                        <!-- Validade -->
                        <div>
                            <label for="validade" class="block text-sm font-medium text-gray-700">Validade do Orçamento</label>
                            <input type="date" 
                                   id="validade" 
                                   name="validade"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <!-- Arquivo PDF (opcional) -->
                        <div>
                            <label for="arquivo" class="block text-sm font-medium text-gray-700">Anexar PDF (opcional)</label>
                            <input type="file" 
                                   id="arquivo" 
                                   name="arquivo" 
                                   accept=".pdf"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-xs text-gray-500">Apenas arquivos PDF, máximo 10MB</p>
                        </div>

                        <!-- Botão de Envio -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md">
                                Enviar Orçamento
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @else
            <!-- Aviso de que já enviou orçamento -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-blue-900">Orçamento já enviado</h3>
                        <p class="mt-2 text-sm text-blue-700">
                            Você já enviou um orçamento para esta demanda. Não é possível alterar ou enviar novos documentos. 
                            Acompanhe o status do seu orçamento abaixo.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Negociações Pendentes -->
            @if(isset($negociacoes) && $negociacoes->count() > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-yellow-900 mb-4">Negociações Pendentes</h2>
                <div class="space-y-4">
                    @foreach($negociacoes as $negociacao)
                        <div class="bg-white border border-yellow-300 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            @if($negociacao->tipo === 'desconto')
                                                Solicitação de Desconto
                                            @elseif($negociacao->tipo === 'parcelamento')
                                                Solicitação de Parcelamento
                                            @else
                                                Contraproposta
                                            @endif
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $negociacao->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    
                                    <div class="mt-2 space-y-1">
                                        <p class="text-sm text-gray-700">
                                            <strong>Valor Original:</strong> R$ {{ number_format($negociacao->valor_original, 2, ',', '.') }}
                                        </p>
                                        @if($negociacao->tipo === 'desconto')
                                            @php
                                                $valorDesconto = $negociacao->valor_original - $negociacao->valor_solicitado;
                                            @endphp
                                            <p class="text-sm text-gray-700">
                                                <strong>Desconto Solicitado:</strong> R$ {{ number_format($valorDesconto, 2, ',', '.') }}
                                            </p>
                                            <p class="text-sm font-semibold text-green-700">
                                                <strong>Novo Valor:</strong> R$ {{ number_format($negociacao->valor_solicitado, 2, ',', '.') }}
                                            </p>
                                        @elseif($negociacao->tipo === 'parcelamento')
                                            <p class="text-sm text-gray-700">
                                                <strong>Valor por Parcela:</strong> R$ {{ number_format($negociacao->valor_solicitado, 2, ',', '.') }}
                                            </p>
                                            <p class="text-sm text-gray-700">
                                                <strong>Número de Parcelas:</strong> {{ $negociacao->parcelas }}x
                                            </p>
                                            <p class="text-sm font-semibold text-gray-700">
                                                <strong>Valor Total:</strong> R$ {{ number_format($negociacao->valor_solicitado * $negociacao->parcelas, 2, ',', '.') }}
                                            </p>
                                        @else
                                            <p class="text-sm font-semibold text-blue-700">
                                                <strong>Valor Proposto:</strong> R$ {{ number_format($negociacao->valor_solicitado, 2, ',', '.') }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    @if($negociacao->mensagem_solicitacao)
                                        <div class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded">
                                            <p class="text-sm font-medium text-gray-800 mb-1">Mensagem:</p>
                                            <p class="text-sm text-gray-700">{{ $negociacao->mensagem_solicitacao }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mt-4 flex gap-2">
                                @if($negociacao->tipo === 'contraproposta')
                                    <form method="POST" action="{{ route('prestador.link.aceitar-negociacao', [$link->token, $negociacao->id]) }}" onsubmit="return confirm('Tem certeza que deseja aceitar esta contraproposta?');">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md text-sm">
                                            Aceitar
                                        </button>
                                    </form>
                                @else
                                    <button type="button" onclick="abrirModalAceitarNegociacao('{{ $link->token }}', {{ $negociacao->id }}, '{{ $negociacao->tipo }}', {{ $negociacao->valor_original }})" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md text-sm">
                                        Aceitar
                                    </button>
                                @endif
                                <button type="button" onclick="abrirModalRecusarNegociacao('{{ $link->token }}', {{ $negociacao->id }})" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md text-sm">
                                    Recusar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Orçamentos já enviados -->
            @if($demanda->orcamentos->where('prestador_id', $prestador->id)->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Status do Orçamento</h2>
                <div class="space-y-3">
                    @foreach($demanda->orcamentos->where('prestador_id', $prestador->id) as $orcamento)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <p class="text-lg font-semibold text-gray-900">R$ {{ number_format($orcamento->valor, 2, ',', '.') }}</p>
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                                            @if($orcamento->status == 'aprovado') bg-green-100 text-green-800
                                            @elseif($orcamento->status == 'rejeitado') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($orcamento->status) }}
                                        </span>
                                    </div>
                                    
                                    @if($orcamento->descricao)
                                        <p class="text-sm text-gray-600 mt-2">{{ $orcamento->descricao }}</p>
                                    @endif
                                    
                                    @if($orcamento->validade)
                                        <p class="text-xs text-gray-500 mt-2">
                                            <strong>Validade:</strong> {{ $orcamento->validade->format('d/m/Y') }}
                                        </p>
                                    @endif
                                    
                                    <p class="text-xs text-gray-500 mt-2">
                                        <strong>Enviado em:</strong> {{ $orcamento->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    
                                    @if($orcamento->status == 'rejeitado' && $orcamento->motivo_rejeicao)
                                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded">
                                            <p class="text-sm font-medium text-red-800">Motivo da Rejeição:</p>
                                            <p class="text-sm text-red-700 mt-1">{{ $orcamento->motivo_rejeicao }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($orcamento->status == 'aprovado' && $orcamento->aprovado_em)
                                        <p class="text-xs text-green-600 mt-2">
                                            <strong>Aprovado em:</strong> {{ $orcamento->aprovado_em->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                    
                                    @if($orcamento->documentos->count() > 0)
                                        <div class="mt-3">
                                            <p class="text-xs font-medium text-gray-700 mb-1">Documentos anexados:</p>
                                            <ul class="text-xs text-gray-600">
                                                @foreach($orcamento->documentos as $documento)
                                                    <li>• {{ $documento->nome_original }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
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

    <!-- Modal Aceitar Negociação -->
    <div id="modal-aceitar-negociacao" class="hidden fixed inset-0 z-50 overflow-y-auto modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <form method="POST" id="form-aceitar-negociacao">
                    @csrf
                    <input type="hidden" id="negociacao_id_aceitar" value="">
                    <input type="hidden" id="negociacao_tipo_aceitar" value="">
                    <input type="hidden" id="negociacao_valor_original" value="">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Aceitar Negociação
                        </h3>
                        <div id="campos_desconto" class="mb-4 hidden">
                            <p class="text-sm text-gray-700 mb-3">
                                <strong>Valor Original:</strong> R$ <span id="valor_original_display"></span>
                            </p>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Desconto <span class="text-red-500">*</span>
                                </label>
                                <select id="tipo_desconto" onchange="atualizarCalculoDesconto()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="percentual">Percentual (%)</option>
                                    <option value="valor">Valor Fixo (R$)</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label id="label_desconto" for="valor_desconto" class="block text-sm font-medium text-gray-700 mb-2">
                                    Valor do Desconto (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="valor_desconto" step="0.01" min="0.01" oninput="atualizarCalculoDesconto()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="p-3 bg-green-50 border border-green-200 rounded">
                                <p class="text-sm font-semibold text-green-800">
                                    <strong>Valor Final:</strong> R$ <span id="valor_final_desconto">0,00</span>
                                </p>
                                <p class="text-xs text-green-600 mt-1">
                                    Desconto: R$ <span id="valor_desconto_calculado">0,00</span>
                                </p>
                            </div>
                        </div>
                        <div id="campos_parcelamento" class="mb-4 hidden">
                            <p class="text-sm text-gray-700 mb-3">
                                <strong>Valor Original:</strong> R$ <span id="valor_original_parcela_display"></span>
                            </p>
                            <div class="mb-4">
                                <label for="numero_parcelas" class="block text-sm font-medium text-gray-700 mb-2">
                                    Número de Parcelas <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="numero_parcelas" min="2" oninput="atualizarCalculoParcelamento()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="p-3 bg-blue-50 border border-blue-200 rounded">
                                <p class="text-sm font-semibold text-blue-800">
                                    <strong>Valor por Parcela:</strong> R$ <span id="valor_por_parcela">0,00</span>
                                </p>
                                <p class="text-xs text-blue-600 mt-1">
                                    Total: R$ <span id="valor_total_parcelamento">0,00</span> em <span id="parcelas_display">0</span>x
                                </p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="mensagem_resposta_aceitar" class="block text-sm font-medium text-gray-700 mb-2">
                                Mensagem (opcional)
                            </label>
                            <textarea id="mensagem_resposta_aceitar" name="mensagem_resposta" rows="3" maxlength="1000" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="btn-confirmar-aceitar" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirmar Aceitação
                        </button>
                        <button type="button" onclick="fecharModalAceitarNegociacao()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Recusar Negociação -->
    <div id="modal-recusar-negociacao" class="hidden fixed inset-0 z-50 overflow-y-auto modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <form method="POST" id="form-recusar-negociacao">
                    @csrf
                    <input type="hidden" id="negociacao_id_recusar" value="">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Recusar Negociação
                        </h3>
                        <div class="mb-4">
                            <label for="mensagem_resposta_recusar" class="block text-sm font-medium text-gray-700 mb-2">
                                Mensagem (opcional)
                            </label>
                            <textarea id="mensagem_resposta_recusar" name="mensagem_resposta" rows="3" maxlength="1000" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Recusar
                        </button>
                        <button type="button" onclick="fecharModalRecusarNegociacao()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalAceitarNegociacao(token, negociacaoId, tipo, valorOriginal) {
            const modal = document.getElementById('modal-aceitar-negociacao');
            const form = document.getElementById('form-aceitar-negociacao');
            const inputId = document.getElementById('negociacao_id_aceitar');
            const inputTipo = document.getElementById('negociacao_tipo_aceitar');
            const inputValorOriginal = document.getElementById('negociacao_valor_original');
            const camposDesconto = document.getElementById('campos_desconto');
            const camposParcelamento = document.getElementById('campos_parcelamento');
            
            if (modal && form && inputId) {
                inputId.value = negociacaoId;
                inputTipo.value = tipo;
                inputValorOriginal.value = valorOriginal;
                form.action = '/prestador/' + token + '/negociacoes/' + negociacaoId + '/aceitar';
                
                // Limpa campos anteriores
                document.getElementById('valor_desconto').value = '';
                document.getElementById('numero_parcelas').value = '';
                document.getElementById('mensagem_resposta_aceitar').value = '';
                
                // Mostra campos apropriados
                if (tipo === 'desconto') {
                    camposDesconto.classList.remove('hidden');
                    camposParcelamento.classList.add('hidden');
                    document.getElementById('valor_original_display').textContent = parseFloat(valorOriginal).toFixed(2).replace('.', ',');
                } else if (tipo === 'parcelamento') {
                    camposDesconto.classList.add('hidden');
                    camposParcelamento.classList.remove('hidden');
                    document.getElementById('valor_original_parcela_display').textContent = parseFloat(valorOriginal).toFixed(2).replace('.', ',');
                }
                
                modal.classList.remove('hidden');
            }
        }

        function fecharModalAceitarNegociacao() {
            const modal = document.getElementById('modal-aceitar-negociacao');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function atualizarCalculoDesconto() {
            const tipoDesconto = document.getElementById('tipo_desconto').value;
            const valorDesconto = parseFloat(document.getElementById('valor_desconto').value) || 0;
            const valorOriginal = parseFloat(document.getElementById('negociacao_valor_original').value);
            const labelDesconto = document.getElementById('label_desconto');
            
            if (tipoDesconto === 'percentual') {
                labelDesconto.innerHTML = 'Valor do Desconto (%) <span class="text-red-500">*</span>';
                const descontoMax = Math.min(valorDesconto, 100);
                const valorDescontoCalculado = (valorOriginal * descontoMax) / 100;
                const valorFinal = valorOriginal - valorDescontoCalculado;
                
                document.getElementById('valor_final_desconto').textContent = valorFinal.toFixed(2).replace('.', ',');
                document.getElementById('valor_desconto_calculado').textContent = valorDescontoCalculado.toFixed(2).replace('.', ',');
            } else {
                labelDesconto.innerHTML = 'Valor do Desconto (R$) <span class="text-red-500">*</span>';
                const descontoMax = Math.min(valorDesconto, valorOriginal);
                const valorFinal = valorOriginal - descontoMax;
                
                document.getElementById('valor_final_desconto').textContent = valorFinal.toFixed(2).replace('.', ',');
                document.getElementById('valor_desconto_calculado').textContent = descontoMax.toFixed(2).replace('.', ',');
            }
        }

        function atualizarCalculoParcelamento() {
            const numeroParcelas = parseInt(document.getElementById('numero_parcelas').value) || 0;
            const valorOriginal = parseFloat(document.getElementById('negociacao_valor_original').value);
            
            if (numeroParcelas >= 2) {
                const valorPorParcela = valorOriginal / numeroParcelas;
                
                document.getElementById('valor_por_parcela').textContent = valorPorParcela.toFixed(2).replace('.', ',');
                document.getElementById('valor_total_parcelamento').textContent = valorOriginal.toFixed(2).replace('.', ',');
                document.getElementById('parcelas_display').textContent = numeroParcelas;
            } else {
                document.getElementById('valor_por_parcela').textContent = '0,00';
                document.getElementById('valor_total_parcelamento').textContent = '0,00';
                document.getElementById('parcelas_display').textContent = '0';
            }
        }

        // Atualiza ação do formulário de aceitar negociação
        document.addEventListener('DOMContentLoaded', function() {
            const formAceitar = document.getElementById('form-aceitar-negociacao');
            if (formAceitar) {
                formAceitar.addEventListener('submit', function(e) {
                    const tipo = document.getElementById('negociacao_tipo_aceitar').value;
                    const valorOriginal = parseFloat(document.getElementById('negociacao_valor_original').value);
                    
                    if (tipo === 'desconto') {
                        const tipoDesconto = document.getElementById('tipo_desconto').value;
                        const valorDesconto = parseFloat(document.getElementById('valor_desconto').value) || 0;
                        
                        if (valorDesconto <= 0) {
                            e.preventDefault();
                            alert('Por favor, informe o valor do desconto.');
                            return false;
                        }
                        
                        let valorFinal;
                        if (tipoDesconto === 'percentual') {
                            const percentual = Math.min(valorDesconto, 100);
                            valorFinal = valorOriginal - (valorOriginal * percentual / 100);
                        } else {
                            valorFinal = valorOriginal - Math.min(valorDesconto, valorOriginal);
                        }
                        
                        // Adiciona campos hidden ao formulário
                        const inputValorSolicitado = document.createElement('input');
                        inputValorSolicitado.type = 'hidden';
                        inputValorSolicitado.name = 'valor_solicitado';
                        inputValorSolicitado.value = valorFinal;
                        this.appendChild(inputValorSolicitado);
                        
                    } else if (tipo === 'parcelamento') {
                        const numeroParcelas = parseInt(document.getElementById('numero_parcelas').value) || 0;
                        
                        if (numeroParcelas < 2) {
                            e.preventDefault();
                            alert('Por favor, informe o número de parcelas (mínimo 2).');
                            return false;
                        }
                        
                        const valorPorParcela = valorOriginal / numeroParcelas;
                        
                        // Adiciona campos hidden ao formulário
                        const inputValorSolicitado = document.createElement('input');
                        inputValorSolicitado.type = 'hidden';
                        inputValorSolicitado.name = 'valor_solicitado';
                        inputValorSolicitado.value = valorPorParcela;
                        this.appendChild(inputValorSolicitado);
                        
                        const inputParcelas = document.createElement('input');
                        inputParcelas.type = 'hidden';
                        inputParcelas.name = 'parcelas';
                        inputParcelas.value = numeroParcelas;
                        this.appendChild(inputParcelas);
                    }
                });
            }
        });

        function abrirModalRecusarNegociacao(token, negociacaoId) {
            const modal = document.getElementById('modal-recusar-negociacao');
            const form = document.getElementById('form-recusar-negociacao');
            const inputId = document.getElementById('negociacao_id_recusar');
            
            if (modal && form && inputId) {
                inputId.value = negociacaoId;
                form.action = '/prestador/' + token + '/negociacoes/' + negociacaoId + '/recusar';
                modal.classList.remove('hidden');
            }
        }

        function fecharModalRecusarNegociacao() {
            const modal = document.getElementById('modal-recusar-negociacao');
            if (modal) {
                modal.classList.add('hidden');
                const textarea = document.getElementById('mensagem_resposta_recusar');
                if (textarea) {
                    textarea.value = '';
                }
            }
        }

        // Fechar modal ao clicar fora
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                fecharModalRecusarNegociacao();
            }
        });
    </script>
</body>
</html>
