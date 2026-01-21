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
                    <!-- Mensagem de Orçamento Já Enviado -->
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <h3 class="text-xl font-bold text-green-900">Orçamento Enviado com Sucesso!</h3>
                        </div>
                        <p class="text-green-800 mb-4">Seu orçamento foi enviado e está sendo analisado pela administradora.</p>
                        
                        @if($orcamentoEnviado)
                            <div class="bg-white rounded-lg p-4 border border-green-200">
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
                                        <span class="mt-1 inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
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
                @else
                    <!-- Formulário Desabilitado -->
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-lg p-6 opacity-75">
                        <div class="space-y-4">
                            <div class="bg-gradient-to-r from-gray-100 to-gray-200 border-2 border-gray-300 rounded-lg p-6 shadow-sm">
                                <label class="block text-lg font-bold text-gray-500 mb-3">
                                    💰 Valor do Orçamento
                                </label>
                                <div class="flex items-center space-x-3">
                                    <span class="text-3xl font-bold text-gray-400">R$</span>
                                    <input type="text" 
                                           value="Formulário desabilitado" 
                                           disabled
                                           class="flex-1 text-4xl font-bold text-gray-400 bg-transparent border-0 border-b-3 border-gray-300 py-3"
                                           style="border-bottom-width: 3px;">
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-gray-100 to-gray-200 border-2 border-gray-300 rounded-lg p-6 shadow-sm">
                                <label class="block text-lg font-bold text-gray-500 mb-3">
                                    📝 Observações
                                </label>
                                <textarea disabled
                                          rows="4"
                                          class="w-full text-base font-semibold text-gray-400 bg-transparent border-0 border-b-2 border-gray-300 py-2 resize-none"
                                          placeholder="Formulário desabilitado"></textarea>
                            </div>

                            <div class="bg-gradient-to-r from-gray-100 to-gray-200 border-2 border-gray-300 rounded-lg p-6 shadow-sm">
                                <label class="block text-lg font-bold text-gray-500 mb-3">
                                    ⏰ Validade do Orçamento
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text" 
                                           value="Formulário desabilitado" 
                                           disabled
                                           class="w-32 text-2xl font-bold text-gray-400 bg-transparent border-0 border-b-3 border-gray-300 py-2"
                                           style="border-bottom-width: 3px;">
                                    <span class="text-xl font-semibold text-gray-400">dias</span>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-gray-100 to-gray-200 border-2 border-gray-300 rounded-lg p-6 shadow-sm">
                                <label class="block text-lg font-bold text-gray-500 mb-3">
                                    📎 Anexar PDF
                                </label>
                                <input type="file" 
                                       disabled
                                       class="block w-full text-base text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-gray-400 file:text-gray-600 cursor-not-allowed">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="button" 
                                    disabled
                                    class="bg-gray-400 text-gray-600 font-bold py-2 px-6 rounded-md cursor-not-allowed">
                                Orçamento Já Enviado
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
