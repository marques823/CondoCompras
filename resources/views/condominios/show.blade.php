<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalhes do Condomínio') }} - {{ $condominio->nome }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('condominios.edit', $condominio->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('condominios.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('link_gerado'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    <p class="font-semibold">Link gerado com sucesso!</p>
                    <p class="text-sm mt-1">Copie o link abaixo para compartilhar:</p>
                    <div class="mt-2 flex items-center space-x-2">
                        <input type="text" value="{{ session('link_gerado') }}" readonly class="flex-1 px-3 py-2 border border-blue-300 rounded bg-white text-blue-900 font-mono text-sm">
                        <button onclick="copiarLink(event, '{{ session('link_gerado') }}')" class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded text-sm">
                            Copiar
                        </button>
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                <!-- Informações do Condomínio -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Informações do Condomínio</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nome</p>
                                <p class="text-base font-medium">{{ $condominio->nome }}</p>
                            </div>
                            @if($condominio->cnpj)
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">CNPJ</p>
                                    <p class="text-base font-medium">{{ $condominio->cnpj }}</p>
                                </div>
                            @endif
                            @if($condominio->endereco)
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Endereço</p>
                                    <p class="text-base font-medium">
                                        {{ $condominio->endereco }}
                                        {{ $condominio->numero ? ', ' . $condominio->numero : '' }}
                                        {{ $condominio->complemento ? ' - ' . $condominio->complemento : '' }}
                                    </p>
                                    @if($condominio->bairro || $condominio->cidade)
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $condominio->bairro ?? '' }}{{ $condominio->bairro && $condominio->cidade ? ' - ' : '' }}{{ $condominio->cidade ?? '' }}{{ $condominio->estado ? '/' . $condominio->estado : '' }} {{ $condominio->cep ? '- CEP: ' . $condominio->cep : '' }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                            @if($condominio->sindico_nome)
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Síndico</p>
                                    <p class="text-base font-medium">{{ $condominio->sindico_nome }}</p>
                                    @if($condominio->sindico_telefone || $condominio->sindico_email)
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $condominio->sindico_telefone ?? '' }}{{ $condominio->sindico_telefone && $condominio->sindico_email ? ' | ' : '' }}{{ $condominio->sindico_email ?? '' }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                @if($condominio->ativo)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inativo
                                    </span>
                                @endif
                            </div>
                            @if($condominio->tags->count() > 0)
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Tags</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($condominio->tags as $tag)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $tag->cor }}20; color: {{ $tag->cor }}; border: 1px solid {{ $tag->cor }}40;">
                                                {{ $tag->nome }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Links Públicos para Criação de Demandas -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Links Públicos para Criação de Demandas</h3>
                            <button type="button" onclick="abrirModalGerarLink()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Gerar Novo Link
                            </button>
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Compartilhe estes links com zeladores ou responsáveis do condomínio para que possam criar demandas diretamente.
                        </p>

                        @if($condominio->links->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Título</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Link</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usos</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expira Em</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($condominio->links as $link)
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $link->titulo }}
                                                </td>
                                                <td class="px-4 py-4 text-sm">
                                                    <div class="flex items-center space-x-2">
                                                        <input type="text" value="{{ route('publico.criar-demanda', ['token' => $link->token]) }}" readonly class="flex-1 px-2 py-1 border border-gray-300 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-mono text-xs">
                                                        <button onclick="copiarLink(event, '{{ route('publico.criar-demanda', ['token' => $link->token]) }}')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs">
                                                            Copiar
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $link->usos }} {{ $link->usos == 1 ? 'uso' : 'usos' }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $link->expira_em ? $link->expira_em->format('d/m/Y H:i') : 'Sem expiração' }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                    @if($link->ativo)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Ativo
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Inativo
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                    @if($link->ativo)
                                                        <form method="POST" action="{{ route('condominios.desativar-link', ['condominio' => $condominio->id, 'link' => $link->id]) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Desativar</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum link gerado ainda. Clique em "Gerar Novo Link" para criar um.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Gerar Link -->
    <div id="modal-gerar-link" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Gerar Novo Link</h3>
                <form method="POST" action="{{ route('condominios.gerar-link', $condominio->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Título (opcional)
                        </label>
                        <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Ex: Link para Zelador">
                    </div>
                    <div class="mb-4">
                        <label for="expira_em" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Expira Em (opcional)
                        </label>
                        <input type="datetime-local" id="expira_em" name="expira_em" value="{{ old('expira_em') }}" min="{{ date('Y-m-d\TH:i') }}" class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    </div>
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="fecharModalGerarLink()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">
                            Gerar Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalGerarLink() {
            document.getElementById('modal-gerar-link').classList.remove('hidden');
        }

        function fecharModalGerarLink() {
            document.getElementById('modal-gerar-link').classList.add('hidden');
        }

        function copiarLink(event, url) {
            event.preventDefault();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    // Feedback visual
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = 'Copiado!';
                    button.classList.add('bg-green-600');
                    button.classList.remove('bg-indigo-600', 'bg-blue-600', 'hover:bg-indigo-700', 'hover:bg-blue-800');
                    
                    setTimeout(function() {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-600');
                        if (button.classList.contains('text-xs')) {
                            button.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                        } else {
                            button.classList.add('bg-blue-600', 'hover:bg-blue-800');
                        }
                    }, 2000);
                }).catch(function(err) {
                    console.error('Erro ao copiar:', err);
                    alert('Erro ao copiar link. Tente selecionar e copiar manualmente.');
                });
            } else {
                // Fallback para navegadores antigos
                const input = document.createElement('input');
                input.value = url;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                alert('Link copiado para a área de transferência!');
            }
        }
    </script>
</x-app-layout>
