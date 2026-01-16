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
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $demanda->titulo }}</h1>
                <p class="text-gray-600">Condomínio: <strong>{{ $demanda->condominio->nome }}</strong></p>
            </div>

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

            <!-- Orçamentos já enviados -->
            @if($demanda->orcamentos->where('prestador_id', $prestador->id)->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Orçamentos Enviados</h2>
                <div class="space-y-3">
                    @foreach($demanda->orcamentos->where('prestador_id', $prestador->id) as $orcamento)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-900">R$ {{ number_format($orcamento->valor, 2, ',', '.') }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $orcamento->descricao }}</p>
                                    <p class="text-xs text-gray-500 mt-2">Enviado em: {{ $orcamento->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($orcamento->status == 'aprovado') bg-green-100 text-green-800
                                    @elseif($orcamento->status == 'rejeitado') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($orcamento->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
