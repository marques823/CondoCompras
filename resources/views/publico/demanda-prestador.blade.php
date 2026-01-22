<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Demanda - {{ $demanda->titulo }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Formatação de valor monetário ao digitar
        function formatarValor(input) {
            // Remove tudo que não é número
            let valor = input.value.replace(/\D/g, '');
            
            if (valor.length === 0) {
                input.value = '';
                return;
            }
            
            // Converte centavos para reais
            valor = (parseInt(valor) / 100).toFixed(2);
            
            // Formata com separador de milhares e vírgula decimal
            valor = valor.replace('.', ',');
            valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            
            // Atualiza o input (mas mantém o valor numérico para submit)
            input.setAttribute('data-display', valor);
        }

        // Ao focar no campo, mostra o valor formatado
        document.addEventListener('DOMContentLoaded', function() {
            const valorInput = document.getElementById('valor');
            
            if (valorInput) {
                // Ao perder o foco, formata para exibição
                valorInput.addEventListener('blur', function() {
                    if (this.value) {
                        let valor = parseFloat(this.value);
                        if (!isNaN(valor)) {
                            this.value = valor.toFixed(2);
                        }
                    }
                });
                
                // Ao focar, permite edição livre
                valorInput.addEventListener('focus', function() {
                    this.select();
                });
            }
        });

        // Calcula o valor com desconto baseado na porcentagem
        function calcularValorDesconto(negociacaoId, valorOriginal) {
            const porcentagemInput = document.getElementById('porcentagem_desconto_' + negociacaoId);
            const valorInput = document.getElementById('valor_solicitado_' + negociacaoId);
            
            if (porcentagemInput && valorInput && porcentagemInput.value) {
                const porcentagem = parseFloat(porcentagemInput.value);
                if (!isNaN(porcentagem) && porcentagem >= 0 && porcentagem <= 100) {
                    const valorComDesconto = valorOriginal * (1 - porcentagem / 100);
                    valorInput.value = valorComDesconto.toFixed(2);
                }
            }
        }

        // Calcula a porcentagem de desconto baseado no valor
        function calcularPorcentagemDesconto(negociacaoId, valorOriginal) {
            const porcentagemInput = document.getElementById('porcentagem_desconto_' + negociacaoId);
            const valorInput = document.getElementById('valor_solicitado_' + negociacaoId);
            
            if (porcentagemInput && valorInput && valorInput.value) {
                const valorComDesconto = parseFloat(valorInput.value);
                if (!isNaN(valorComDesconto) && valorComDesconto >= 0 && valorComDesconto <= valorOriginal) {
                    const porcentagem = ((valorOriginal - valorComDesconto) / valorOriginal) * 100;
                    porcentagemInput.value = porcentagem.toFixed(2);
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $demanda->titulo }}</h1>
            </div>

            <!-- Informações do Condomínio -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informações do Condomínio</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

            <!-- Formulário de Orçamento -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Enviar Orçamento</h2>

                @if($jaEnviouOrcamento)
                    <!-- Notificação de Status do Orçamento -->
                    @if($orcamentoEnviado && $orcamentoEnviado->status === 'aprovado')
                        <!-- Notificação de Aprovação -->
                        <div class="bg-green-50 border-4 border-green-500 rounded-lg p-6 mb-6 shadow-lg">
                            <div class="flex items-center mb-4">
                                <svg class="w-10 h-10 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h3 class="text-2xl font-bold text-green-900">🎉 Orçamento Aprovado!</h3>
                                    <p class="text-green-700 mt-1">Parabéns! Seu orçamento foi aprovado pela administradora.</p>
                                </div>
                            </div>
                            @if($orcamentoEnviado->aprovado_em)
                                <p class="text-sm text-green-600">
                                    <strong>Aprovado em:</strong> {{ $orcamentoEnviado->aprovado_em->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>
                    @elseif($orcamentoEnviado && $orcamentoEnviado->status === 'rejeitado')
                        <!-- Notificação de Rejeição -->
                        <div class="bg-red-50 border-4 border-red-500 rounded-lg p-6 mb-6 shadow-lg">
                            <div class="flex items-center mb-4">
                                <svg class="w-10 h-10 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h3 class="text-2xl font-bold text-red-900">Orçamento Rejeitado</h3>
                                    <p class="text-red-700 mt-1">Infelizmente, seu orçamento foi rejeitado pela administradora.</p>
                                </div>
                            </div>
                            @if($orcamentoEnviado->motivo_rejeicao)
                                <div class="bg-white rounded-lg p-4 border border-red-200 mt-4">
                                    <label class="block text-sm font-medium text-red-800 mb-2">Motivo da Rejeição:</label>
                                    <p class="text-red-900 whitespace-pre-wrap">{{ $orcamentoEnviado->motivo_rejeicao }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Mensagem de Orçamento Já Enviado (Status: Recebido) -->
                        <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-6">
                            <div class="flex items-center mb-4">
                                <svg class="w-8 h-8 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <h3 class="text-xl font-bold text-green-900">Orçamento Enviado com Sucesso!</h3>
                            </div>
                            <p class="text-green-800 mb-4">Seu orçamento foi enviado e está sendo analisado pela administradora.</p>
                    @endif
                        
                    @if($orcamentoEnviado)
                        <div class="bg-white rounded-lg p-4 border 
                            @if($orcamentoEnviado->status === 'aprovado') border-green-200
                            @elseif($orcamentoEnviado->status === 'rejeitado') border-red-200
                            @else border-green-200
                            @endif">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Valor do Orçamento</label>
                                    <p class="mt-1 text-lg font-bold text-gray-900">R$ {{ number_format($orcamentoEnviado->valor, 2, ',', '.') }}</p>
                                </div>
                                @if($orcamentoEnviado->validade)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Validade</label>
                                        <p class="mt-1 text-gray-900">{{ $orcamentoEnviado->validade->format('d/m/Y') }}</p>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Data de Envio</label>
                                    <p class="mt-1 text-gray-900">{{ $orcamentoEnviado->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="mt-1 inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                        @if($orcamentoEnviado->status === 'aprovado') bg-green-100 text-green-800
                                        @elseif($orcamentoEnviado->status === 'rejeitado') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($orcamentoEnviado->status) }}
                                    </span>
                                </div>
                            </div>
                            @if($orcamentoEnviado->descricao)
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Observações</label>
                                    <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $orcamentoEnviado->descricao }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                @endif

                <!-- Formulário de Conclusão de Serviço -->
                @if($orcamentoEnviado && $orcamentoEnviado->status === 'aprovado' && !$orcamentoEnviado->concluido)
                    <div class="bg-indigo-50 border-2 border-indigo-200 rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold text-indigo-900 mb-4">Concluir Serviço</h2>
                        
                        <form method="POST" action="{{ route('publico.demanda.concluir-servico', $link->token) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="orcamento_id" value="{{ $orcamentoEnviado->id }}">
                            
                            <div class="space-y-6">
                                <!-- Checkbox para marcar como concluído -->
                                <div class="bg-white rounded-lg p-4 border-2 border-indigo-300">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="concluido" 
                                               value="1" 
                                               required
                                               class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <span class="ml-3 text-lg font-semibold text-gray-900">
                                            Marcar serviço como concluído
                                        </span>
                                    </label>
                                </div>

                                <!-- Observações -->
                                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-6 shadow-sm">
                                    <label for="observacoes_conclusao" class="block text-lg font-bold text-gray-900 mb-3">
                                        📝 Observações sobre a Conclusão
                                    </label>
                                    <textarea id="observacoes_conclusao" 
                                              name="observacoes_conclusao" 
                                              rows="4"
                                              class="w-full text-base font-semibold text-gray-900 bg-transparent border-0 border-b-2 border-indigo-400 focus:border-indigo-600 focus:ring-0 py-2 placeholder-gray-400 resize-none"
                                              placeholder="Descreva detalhes sobre a conclusão do serviço..."></textarea>
                                </div>

                                <!-- Dados Bancários -->
                                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-6 shadow-sm">
                                    <label for="dados_bancarios" class="block text-lg font-bold text-gray-900 mb-3">
                                        💰 Dados Bancários para Pagamento <span class="text-gray-500 text-sm font-normal">(opcional)</span>
                                    </label>
                                    <textarea id="dados_bancarios" 
                                              name="dados_bancarios" 
                                              rows="4"
                                              class="w-full text-base font-semibold text-gray-900 bg-transparent border-0 border-b-2 border-indigo-400 focus:border-indigo-600 focus:ring-0 py-2 placeholder-gray-400 resize-none"
                                              placeholder="Informe os dados bancários (banco, agência, conta, PIX, etc.) ou deixe em branco se o boleto substituir essas informações..."></textarea>
                                    <p class="mt-3 text-sm text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        Opcional: As administradoras já possuem essas informações. O boleto pode substituir os dados bancários.
                                    </p>
                                </div>

                                <!-- Upload de Documentos -->
                                <div class="bg-white rounded-lg p-6 border-2 border-indigo-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📎 Documentos</h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label for="nota_fiscal" class="block text-sm font-medium text-gray-700 mb-2">
                                                Nota Fiscal (PDF) <span class="text-red-500">*</span>
                                            </label>
                                            <input type="file" 
                                                   id="nota_fiscal" 
                                                   name="nota_fiscal" 
                                                   accept=".pdf"
                                                   required
                                                   class="block w-full text-base text-gray-700 file:mr-4 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer cursor-pointer">
                                            <p class="text-xs text-gray-500 mt-1">Apenas arquivos PDF, máximo 10MB</p>
                                        </div>

                                        <div>
                                            <label for="boleto" class="block text-sm font-medium text-gray-700 mb-2">
                                                Boleto/Comprovante (PDF) <span class="text-red-500">*</span>
                                            </label>
                                            <input type="file" 
                                                   id="boleto" 
                                                   name="boleto" 
                                                   accept=".pdf"
                                                   required
                                                   class="block w-full text-base text-gray-700 file:mr-4 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer cursor-pointer">
                                            <p class="text-xs text-gray-500 mt-1">Apenas arquivos PDF, máximo 10MB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botão de Envio -->
                                <div class="flex justify-end pt-4">
                                    <button type="submit" 
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-md text-lg">
                                        Concluir Serviço
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Status de Conclusão -->
                @if($orcamentoEnviado && $orcamentoEnviado->concluido)
                    <div class="bg-purple-50 border-4 border-purple-500 rounded-lg p-6 mb-6 shadow-lg">
                        <div class="flex items-center mb-4">
                            <svg class="w-10 h-10 text-purple-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3 class="text-2xl font-bold text-purple-900">✅ Serviço Concluído</h3>
                                <p class="text-purple-700 mt-1">O serviço foi marcado como concluído.</p>
                            </div>
                        </div>
                        @if($orcamentoEnviado->concluido_em)
                            <p class="text-sm text-purple-600">
                                <strong>Concluído em:</strong> {{ $orcamentoEnviado->concluido_em->format('d/m/Y H:i') }}
                            </p>
                        @endif
                        @if($orcamentoEnviado->observacoes_conclusao)
                            <div class="mt-4 bg-white rounded-lg p-4 border border-purple-200">
                                <label class="block text-sm font-medium text-purple-800 mb-2">Observações:</label>
                                <p class="text-purple-900 whitespace-pre-wrap">{{ $orcamentoEnviado->observacoes_conclusao }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Negociações -->
                @if($jaEnviouOrcamento && $negociacoes && $negociacoes->count() > 0)
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold text-yellow-900 mb-4">Negociações Recebidas</h2>
                        <div class="space-y-4">
                            @foreach($negociacoes as $negociacao)
                                <div class="bg-white border-2 border-yellow-300 rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-3">
                                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    @if($negociacao->tipo === 'desconto')
                                                        Solicitação de Desconto
                                                    @elseif($negociacao->tipo === 'parcelamento')
                                                        Solicitação de Parcelamento
                                                    @else
                                                        Contraproposta de Valor
                                                    @endif
                                                </span>
                                                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                                    @if($negociacao->status === 'aceita') bg-green-100 text-green-800
                                                    @elseif($negociacao->status === 'recusada') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800
                                                    @endif">
                                                    @if($negociacao->status === 'aceita')
                                                        Aceita
                                                    @elseif($negociacao->status === 'recusada')
                                                        Recusada
                                                    @else
                                                        Pendente
                                                    @endif
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $negociacao->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            
                                            <div class="mt-3 space-y-2">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Valor Original do Orçamento</label>
                                                    <p class="mt-1 text-lg font-bold text-gray-900">R$ {{ number_format($negociacao->valor_original, 2, ',', '.') }}</p>
                                                </div>
                                                
                                                @if($negociacao->mensagem_solicitacao)
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Solicitação da Administradora</label>
                                                        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $negociacao->mensagem_solicitacao }}</p>
                                                    </div>
                                                @endif
                                                
                                                @if($negociacao->valor_solicitado)
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Valor Solicitado</label>
                                                        <p class="mt-1 text-lg font-bold text-indigo-600">R$ {{ number_format($negociacao->valor_solicitado, 2, ',', '.') }}</p>
                                                    </div>
                                                @endif
                                                
                                                @if($negociacao->status === 'pendente')
                                                    <!-- Formulário de Resposta -->
                                                    <div class="mt-4 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                                        <h4 class="text-sm font-semibold text-blue-900 mb-3">Responder Negociação</h4>
                                                        
                                                        <form method="POST" action="{{ route('publico.demanda.negociacao.aceitar', [$link->token, $negociacao->id]) }}" class="mb-3">
                                                            @csrf
                                                            
                                                            @if($negociacao->tipo === 'desconto')
                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                                    <div>
                                                                        <label for="porcentagem_desconto_{{ $negociacao->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                                            Porcentagem de Desconto (%)
                                                                        </label>
                                                                        <input type="number" 
                                                                               id="porcentagem_desconto_{{ $negociacao->id }}" 
                                                                               name="porcentagem_desconto" 
                                                                               step="0.01" 
                                                                               min="0" 
                                                                               max="100"
                                                                               class="w-full rounded-md border-gray-300"
                                                                               placeholder="Ex: 10"
                                                                               oninput="calcularValorDesconto({{ $negociacao->id }}, {{ $negociacao->valor_original }})">
                                                                        <p class="text-xs text-gray-500 mt-1">Informe a porcentagem de desconto</p>
                                                                    </div>
                                                                    <div>
                                                                        <label for="valor_solicitado_{{ $negociacao->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                                            Valor com Desconto (R$) <span class="text-red-500">*</span>
                                                                        </label>
                                                                        <input type="number" 
                                                                               id="valor_solicitado_{{ $negociacao->id }}" 
                                                                               name="valor_solicitado" 
                                                                               step="0.01" 
                                                                               min="0.01" 
                                                                               max="{{ $negociacao->valor_original }}"
                                                                               required
                                                                               class="w-full rounded-md border-gray-300"
                                                                               placeholder="0.00"
                                                                               oninput="calcularPorcentagemDesconto({{ $negociacao->id }}, {{ $negociacao->valor_original }})">
                                                                        <p class="text-xs text-gray-500 mt-1">Informe o valor final com desconto aplicado</p>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3 p-2 bg-gray-50 rounded">
                                                                    <p class="text-xs text-gray-600">
                                                                        <strong>Valor Original:</strong> R$ {{ number_format($negociacao->valor_original, 2, ',', '.') }}
                                                                    </p>
                                                                </div>
                                                            @elseif($negociacao->tipo === 'parcelamento')
                                                                <div class="mb-3">
                                                                    <label for="parcelas_{{ $negociacao->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                                        Número de Parcelas <span class="text-red-500">*</span>
                                                                    </label>
                                                                    <input type="number" 
                                                                           id="parcelas_{{ $negociacao->id }}" 
                                                                           name="parcelas" 
                                                                           min="2" 
                                                                           max="12"
                                                                           required
                                                                           class="w-full rounded-md border-gray-300"
                                                                           placeholder="Ex: 3">
                                                                </div>
                                                            @endif
                                                            
                                                            <div class="mb-3">
                                                                <label for="mensagem_resposta_{{ $negociacao->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                                    Mensagem (opcional)
                                                                </label>
                                                                <textarea id="mensagem_resposta_{{ $negociacao->id }}" 
                                                                          name="mensagem_resposta" 
                                                                          rows="3"
                                                                          class="w-full rounded-md border-gray-300"
                                                                          placeholder="Adicione uma mensagem sobre sua resposta..."></textarea>
                                                            </div>
                                                            
                                                            <button type="submit" 
                                                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">
                                                                Aceitar Negociação
                                                            </button>
                                                        </form>
                                                        
                                                        <form method="POST" action="{{ route('publico.demanda.negociacao.recusar', [$link->token, $negociacao->id]) }}">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="mensagem_recusa_{{ $negociacao->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                                    Motivo da Recusa (opcional)
                                                                </label>
                                                                <textarea id="mensagem_recusa_{{ $negociacao->id }}" 
                                                                          name="mensagem_resposta" 
                                                                          rows="2"
                                                                          class="w-full rounded-md border-gray-300"
                                                                          placeholder="Informe o motivo da recusa..."></textarea>
                                                            </div>
                                                            <button type="submit" 
                                                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md">
                                                                Recusar Negociação
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    @if($negociacao->mensagem_resposta)
                                                        <div class="mt-3 p-3 
                                                            @if($negociacao->status === 'aceita') bg-green-50 border border-green-200
                                                            @else bg-red-50 border border-red-200
                                                            @endif rounded">
                                                            <p class="text-xs font-medium 
                                                                @if($negociacao->status === 'aceita') text-green-800
                                                                @else text-red-800
                                                                @endif mb-1">Sua Resposta:</p>
                                                            <p class="text-sm 
                                                                @if($negociacao->status === 'aceita') text-green-700
                                                                @else text-red-700
                                                                @endif">{{ $negociacao->mensagem_resposta }}</p>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($negociacao->respondido_em)
                                                        <p class="text-xs text-gray-500 mt-2">
                                                            <strong>Respondido em:</strong> {{ $negociacao->respondido_em->format('d/m/Y H:i') }}
                                                        </p>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

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

                @if(!$jaEnviouOrcamento)
                    <form method="POST" action="{{ route('publico.demanda.orcamento', $link->token) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <!-- Dados do Orçamento -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Orçamento</h3>
                                
                                <div class="space-y-4">
                                    <!-- Valor -->
                                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-6 shadow-sm">
                                        <label for="valor" class="block text-lg font-bold text-gray-900 mb-3">
                                            💰 Valor do Orçamento <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex items-center space-x-3">
                                            <span class="text-3xl font-bold text-indigo-600">R$</span>
                                            <input type="number" 
                                                   id="valor" 
                                                   name="valor" 
                                                   step="0.01" 
                                                   min="0" 
                                                   required
                                                   class="flex-1 text-4xl font-bold text-gray-900 bg-transparent border-0 border-b-3 border-indigo-400 focus:border-indigo-600 focus:ring-0 py-3 placeholder-gray-400"
                                                   placeholder="0,00"
                                                   style="border-bottom-width: 3px;">
                                        </div>
                                        <p class="mt-3 text-sm text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Informe o valor total do orçamento em reais
                                        </p>
                                    </div>

                                    <!-- Observações -->
                                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-6 shadow-sm">
                                        <label for="descricao" class="block text-lg font-bold text-gray-900 mb-3">
                                            📝 Observações
                                        </label>
                                        <textarea id="descricao" 
                                                  name="descricao" 
                                                  rows="4"
                                                  class="w-full text-base font-semibold text-gray-900 bg-transparent border-0 border-b-2 border-indigo-400 focus:border-indigo-600 focus:ring-0 py-2 placeholder-gray-400 resize-none"
                                                  placeholder="Adicione observações sobre o orçamento..."></textarea>
                                        <p class="mt-3 text-sm text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Informações adicionais sobre o orçamento (opcional)
                                        </p>
                                    </div>

                                    <!-- Validade em dias -->
                                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-6 shadow-sm">
                                        <label for="validade_dias" class="block text-lg font-bold text-gray-900 mb-3">
                                            ⏰ Validade do Orçamento <span class="text-gray-500 text-sm font-normal">(em dias)</span>
                                        </label>
                                        <div class="flex items-center space-x-3">
                                            <input type="number" 
                                                   id="validade_dias" 
                                                   name="validade_dias"
                                                   min="1"
                                                   max="365"
                                                   class="w-32 text-2xl font-bold text-gray-900 bg-transparent border-0 border-b-3 border-indigo-400 focus:border-indigo-600 focus:ring-0 py-2 placeholder-gray-400"
                                                   placeholder="30"
                                                   style="border-bottom-width: 3px;">
                                            <span class="text-xl font-semibold text-gray-700">dias</span>
                                        </div>
                                        <p class="mt-3 text-sm text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Quantos dias o orçamento permanece válido (opcional)
                                        </p>
                                    </div>

                                    <!-- Arquivo PDF (opcional) -->
                                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-6 shadow-sm">
                                        <label for="arquivo" class="block text-lg font-bold text-gray-900 mb-3">
                                            📎 Anexar PDF <span class="text-gray-500 text-sm font-normal">(opcional)</span>
                                        </label>
                                        <input type="file" 
                                               id="arquivo" 
                                               name="arquivo" 
                                               accept=".pdf"
                                               class="block w-full text-base text-gray-700 file:mr-4 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 file:cursor-pointer cursor-pointer">
                                        <p class="mt-3 text-sm text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Apenas arquivos PDF, máximo 10MB
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botão de Envio -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" 
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md">
                                    Enviar Orçamento
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
