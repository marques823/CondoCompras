<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes da Demanda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 flex justify-between items-center">
                        <a href="{{ route('demandas.index') }}" class="text-blue-500 hover:text-blue-700">← Voltar para Demandas</a>
                        <div class="flex gap-2">
                            <a href="{{ route('demandas.edit', $demanda) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                Editar Demanda
                            </a>
                            <button type="button" onclick="abrirModalStatus()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                Alterar Status
                            </button>
                            <button type="button" onclick="abrirModalCompartilhar()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                📤 Compartilhar
                            </button>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Título</h3>
                            <p>{{ $demanda->titulo }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-2">Status</h3>
                            @php
                                $statusColors = [
                                    'aberta' => 'bg-blue-100 text-blue-800',
                                    'em_andamento' => 'bg-yellow-100 text-yellow-800',
                                    'aguardando_orcamento' => 'bg-orange-100 text-orange-800',
                                    'concluida' => 'bg-green-100 text-green-800',
                                    'cancelada' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'aberta' => 'Aberta',
                                    'em_andamento' => 'Em Andamento',
                                    'aguardando_orcamento' => 'Aguardando Orçamento',
                                    'concluida' => 'Concluída',
                                    'cancelada' => 'Cancelada',
                                ];
                            @endphp
                            <span id="status-badge" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$demanda->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$demanda->status] ?? ucfirst($demanda->status) }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-2">Condomínio</h3>
                            <p>{{ $demanda->condominio->nome }}</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-2">Criado em</h3>
                            <p>{{ $demanda->created_at->format('d/m/Y H:i') }}</p>
                            @php
                                // Verifica se foi criada via link público (observações contêm "Solicitante:")
                                $isPublico = $demanda->observacoes && str_contains($demanda->observacoes, 'Solicitante:');
                                if ($isPublico) {
                                    // Extrai o nome do solicitante das observações
                                    $linhas = explode("\n", $demanda->observacoes);
                                    $nomeSolicitante = null;
                                    foreach ($linhas as $linha) {
                                        if (str_starts_with(trim($linha), 'Solicitante:')) {
                                            $nomeSolicitante = trim(str_replace('Solicitante:', '', $linha));
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            
                            @if($isPublico && $nomeSolicitante && $nomeSolicitante !== 'Não informado')
                                <p class="text-sm text-gray-500 mt-1">
                                    por <span class="font-medium">{{ $nomeSolicitante }}</span>
                                    <span class="text-xs text-green-600">(Link Público)</span>
                                </p>
                            @elseif($demanda->usuario)
                                <p class="text-sm text-gray-500 mt-1">
                                    por <span class="font-medium">{{ $demanda->usuario->name }}</span>
                                    @if($demanda->usuario->isZelador())
                                        <span class="text-xs text-blue-600">(Zelador)</span>
                                    @elseif($demanda->usuario->isGerente())
                                        <span class="text-xs text-indigo-600">(Gerente)</span>
                                    @endif
                                </p>
                            @elseif($isPublico)
                                <p class="text-sm text-gray-500 mt-1">
                                    <span class="font-medium">Link Público</span>
                                    <span class="text-xs text-green-600">(Solicitante não informado)</span>
                                </p>
                            @endif
                        </div>

                        @if($demanda->urgencia)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Urgência</h3>
                            @php
                                $urgenciaColors = [
                                    'baixa' => 'bg-green-100 text-green-800',
                                    'media' => 'bg-yellow-100 text-yellow-800',
                                    'alta' => 'bg-orange-100 text-orange-800',
                                    'critica' => 'bg-red-100 text-red-800',
                                ];
                                $urgenciaLabels = [
                                    'baixa' => 'Baixa',
                                    'media' => 'Média',
                                    'alta' => 'Alta',
                                    'critica' => 'Crítica',
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $urgenciaColors[$demanda->urgencia] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $urgenciaLabels[$demanda->urgencia] ?? ucfirst($demanda->urgencia) }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Descrição</h3>
                        <p class="whitespace-pre-wrap">{{ $demanda->descricao }}</p>
                    </div>

                    @if($demanda->anexos->count() > 0)
                    <div class="mb-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Arquivos e Fotos Anexadas</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Arquivo</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @php $imgIdx = 0; @endphp
                                    @foreach($demanda->anexos as $anexo)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $anexo->nome_original }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ str_contains($anexo->mime_type, 'image') ? 'Imagem' : 'Documento' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium space-x-3">
                                                @if(str_starts_with($anexo->mime_type ?? '', 'image/'))
                                                    <button type="button" onclick="abrirImagem({{ $imgIdx++ }})" class="text-indigo-600 hover:text-indigo-900 font-bold underline">Visualizar</button>
                                                @else
                                                    <a href="{{ Storage::url($anexo->caminho) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-bold underline">Abrir</a>
                                                @endif
                                                <a href="{{ Storage::url($anexo->caminho) }}" download="{{ $anexo->nome_original }}" class="text-green-600 hover:text-green-900 font-bold underline">Download</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Lightbox com Zoom -->
                    <div id="modal-imagem" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-90 overflow-auto py-10" onclick="fecharImagem()">
                        <!-- Botão Fechar Fixo -->
                        <button type="button" class="fixed top-6 right-6 text-white hover:text-gray-300 focus:outline-none z-[110] bg-black bg-opacity-50 p-2 rounded-full" onclick="fecharImagem()">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <!-- Navegação -->
                        <button type="button" class="fixed left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-[110] bg-black bg-opacity-20 p-2 rounded-full" onclick="event.stopPropagation(); imagemAnterior()">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button type="button" class="fixed right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-[110] bg-black bg-opacity-20 p-2 rounded-full" onclick="event.stopPropagation(); proximaImagem()">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        <!-- Container Imagem -->
                        <div class="relative flex flex-col items-center justify-center" onclick="event.stopPropagation()">
                            <img id="imagem-modal" src="" alt="" 
                                 class="max-w-[85vw] max-h-[65vh] object-contain rounded shadow-2xl transition-transform duration-300 cursor-zoom-in"
                                 ondblclick="toggleZoom(this)">
                            <div class="mt-4 flex flex-col items-center">
                                <p id="nome-imagem-modal" class="text-white text-sm font-medium bg-black bg-opacity-60 px-4 py-1.5 rounded-full"></p>
                                <p class="text-gray-400 text-[10px] mt-2 italic">Dica: 2 cliques para zoom</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $listaImagens = $demanda->anexos->filter(fn($a) => str_starts_with($a->mime_type ?? '', 'image/'))
                            ->map(fn($a) => ['url' => Storage::url($a->caminho), 'nome' => $a->nome_original])
                            ->values();
                    @endphp

                    <script>
                        const imagens = @json($listaImagens);
                        let imagemAtual = 0;
                        let isZoomed = false;

                        function toggleZoom(img) {
                            isZoomed = !isZoomed;
                            if (isZoomed) {
                                img.style.transform = 'scale(1.5)';
                                img.classList.remove('cursor-zoom-in');
                                img.classList.add('cursor-zoom-out');
                                document.getElementById('modal-imagem').classList.remove('items-center');
                                document.getElementById('modal-imagem').classList.add('items-start');
                            } else {
                                img.style.transform = 'scale(1)';
                                img.classList.remove('cursor-zoom-out');
                                img.classList.add('cursor-zoom-in');
                                document.getElementById('modal-imagem').classList.add('items-center');
                                document.getElementById('modal-imagem').classList.remove('items-start');
                            }
                        }

                        function abrirImagem(index) {
                            imagemAtual = index;
                            const img = document.getElementById('imagem-modal');
                            img.style.transform = 'scale(1)';
                            isZoomed = false;
                            img.classList.add('cursor-zoom-in');
                            img.classList.remove('cursor-zoom-out');
                            
                            img.src = imagens[imagemAtual].url;
                            document.getElementById('nome-imagem-modal').textContent = imagens[imagemAtual].nome;
                            document.getElementById('modal-imagem').classList.remove('hidden');
                            document.body.style.overflow = 'hidden';
                        }

                        function fecharImagem() {
                            document.getElementById('modal-imagem').classList.add('hidden');
                            document.body.style.overflow = 'auto';
                        }

                        function imagemAnterior() {
                            imagemAtual = (imagemAtual > 0) ? imagemAtual - 1 : imagens.length - 1;
                            const img = document.getElementById('imagem-modal');
                            img.style.transform = 'scale(1)';
                            isZoomed = false;
                            img.src = imagens[imagemAtual].url;
                            document.getElementById('nome-imagem-modal').textContent = imagens[imagemAtual].nome;
                        }

                        function proximaImagem() {
                            imagemAtual = (imagemAtual < imagens.length - 1) ? imagemAtual + 1 : 0;
                            const img = document.getElementById('imagem-modal');
                            img.style.transform = 'scale(1)';
                            isZoomed = false;
                            img.src = imagens[imagemAtual].url;
                            document.getElementById('nome-imagem-modal').textContent = imagens[imagemAtual].nome;
                        }

                        document.addEventListener('keydown', (e) => {
                            if (document.getElementById('modal-imagem').classList.contains('hidden')) return;
                            if (e.key === 'Escape') fecharImagem();
                            if (e.key === 'ArrowLeft') imagemAnterior();
                            if (e.key === 'ArrowRight') proximaImagem();
                        });
                    </script>
                    @endif

                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold">Prestadores Convidados</h3>
                            <button type="button" onclick="abrirModalAdicionarPrestador()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors text-sm">
                                + Adicionar Prestador
                            </button>
                        </div>

                    @if($demanda->prestadores->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Link</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($demanda->prestadores as $prestador)
                                        @php
                                            $link = $demanda->links->where('prestador_id', $prestador->id)->first();
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $prestador->nome_razao_social }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ ucfirst($prestador->pivot->status) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($link)
                                                    @php
                                                        $linkUrl = route('prestador.link.show', $link->token);
                                                    @endphp
                                                    <div class="flex items-center gap-2" data-link-url="{{ $linkUrl }}" data-prestador-nome="{{ $prestador->nome_razao_social }}" data-link-id="{{ $link->id }}">
                                                        <button type="button" class="copiar-link-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 transition-colors">
                                                            <span class="copiar-texto text-blue-600 font-bold">Copiar Link</span>
                                                        </button>
                                                        <button type="button" class="compartilhar-link-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded transition-colors">
                                                            Compartilhar
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <form method="POST" action="{{ route('demandas.remover-prestador', [$demanda, $prestador]) }}" onsubmit="return confirm('Tem certeza que deseja remover este prestador?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Remover</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    </div>

                    @if($demanda->orcamentos->count() > 0)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold mb-4">Orçamentos Recebidos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Prestador</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Valor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($demanda->orcamentos as $orcamento)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $orcamento->prestador->nome_razao_social }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 font-bold">R$ {{ number_format($orcamento->valor, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs font-semibold rounded-full {{ $orcamento->status === 'aprovado' ? 'bg-green-100 text-green-800' : ($orcamento->status === 'rejeitado' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                    {{ ucfirst($orcamento->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                @php
                                                    $temOrcamentoAprovado = $demanda->orcamentos->where('status', 'aprovado')->isNotEmpty();
                                                @endphp
                                                @if($orcamento->status === 'recebido' && !$temOrcamentoAprovado)
                                                    <button onclick="abrirModalAprovarOrcamento({{ $orcamento->id }}, '{{ number_format($orcamento->valor, 2, ',', '.') }}', '{{ addslashes($orcamento->prestador->nome_razao_social) }}')" class="text-green-600 hover:text-green-900">Aprovar</button>
                                                    <button onclick="abrirModalRejeitarOrcamento({{ $orcamento->id }}, '{{ number_format($orcamento->valor, 2, ',', '.') }}', '{{ addslashes($orcamento->prestador->nome_razao_social) }}')" class="text-red-600 hover:text-red-900">Rejeitar</button>
                                                    <button onclick="abrirModalNegociacao({{ $orcamento->id }}, {{ $orcamento->valor }}, '{{ addslashes($orcamento->prestador->nome_razao_social) }}')" class="text-blue-600 hover:text-blue-900">Negociar</button>
                                                @elseif($orcamento->status === 'recebido' && $temOrcamentoAprovado)
                                                    <span class="text-gray-400 text-xs">Outro orçamento já foi aprovado</span>
                                                @endif
                                                <a href="{{ route('orcamentos.show', $orcamento) }}" class="text-indigo-600 hover:text-indigo-900">Detalhes</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Orçamentos Concluídos -->
                    @php
                        $orcamentosConcluidos = $demanda->orcamentos->where('concluido', true);
                    @endphp
                    @if($orcamentosConcluidos->count() > 0)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold mb-4">Serviços Concluídos</h3>
                        <div class="space-y-6">
                            @foreach($orcamentosConcluidos as $orcamento)
                                <div class="bg-purple-50 dark:bg-purple-900/20 border-2 border-purple-200 dark:border-purple-700 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="text-lg font-bold text-purple-900 dark:text-purple-100">
                                                ✅ {{ $orcamento->prestador->nome_razao_social }}
                                            </h4>
                                            <p class="text-sm text-purple-700 dark:text-purple-300">
                                                Concluído em: {{ $orcamento->concluido_em->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                            Concluído
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor do Orçamento</label>
                                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">R$ {{ number_format($orcamento->valor, 2, ',', '.') }}</p>
                                        </div>
                                        @if($orcamento->concluidoPor)
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Concluído por</label>
                                                <p class="text-gray-900 dark:text-gray-100">{{ $orcamento->concluidoPor->nome_razao_social }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($orcamento->observacoes_conclusao)
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $orcamento->observacoes_conclusao }}</p>
                                        </div>
                                    @endif

                                    @if($orcamento->dados_bancarios)
                                        <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">💰 Dados Bancários</label>
                                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $orcamento->dados_bancarios }}</p>
                                        </div>
                                    @endif

                                    @php
                                        $notaFiscal = $orcamento->documentos->where('tipo', 'nota_fiscal')->first();
                                        $boleto = $orcamento->documentos->where('tipo', 'boleto')->first();
                                    @endphp

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @if($notaFiscal)
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">📄 Nota Fiscal</label>
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $notaFiscal->nome_original }}</span>
                                                </div>
                                                <div class="mt-2 flex gap-2">
                                                    <a href="{{ route('documentos.download', $notaFiscal) }}" 
                                                       class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        📥 Download
                                                    </a>
                                                    <a href="{{ route('documentos.visualizar', $notaFiscal) }}" 
                                                       target="_blank"
                                                       class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        👁️ Visualizar
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        @if($boleto)
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">🧾 Boleto/Comprovante</label>
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $boleto->nome_original }}</span>
                                                </div>
                                                <div class="mt-2 flex gap-2">
                                                    <a href="{{ route('documentos.download', $boleto) }}" 
                                                       class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        📥 Download
                                                    </a>
                                                    <a href="{{ route('documentos.visualizar', $boleto) }}" 
                                                       target="_blank"
                                                       class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                        👁️ Visualizar
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
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

    <!-- Modais de Status/Aprovação/Negociação -->
    <div id="modal-status" class="hidden fixed inset-0 z-50 overflow-y-auto modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:max-w-lg w-full p-6">
                <h3 class="text-lg font-medium mb-4">Alterar Status</h3>
                <form method="POST" action="{{ route('demandas.update-status', $demanda) }}">
                    @csrf
                    <select name="status" required class="block w-full rounded-md border-gray-300 dark:bg-gray-900 mb-6">
                        <option value="aberta">Aberta</option>
                        <option value="em_andamento">Em Andamento</option>
                        <option value="aguardando_orcamento">Aguardando Orçamento</option>
                        <option value="concluida">Concluída</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="fecharModalStatus()" class="px-4 py-2 border rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar Prestador -->
    <div id="modal-adicionar-prestador" class="hidden fixed inset-0 z-50 modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:max-w-md w-full p-6">
                <h3 class="text-lg font-medium mb-4">Adicionar Prestadores</h3>
                <form method="POST" action="{{ route('demandas.adicionar-prestador', $demanda) }}">
                    @csrf
                    <div class="max-h-60 overflow-y-auto mb-6">
                        @forelse($prestadoresDisponiveis ?? [] as $p)
                            <label class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                <input type="checkbox" name="prestadores[]" value="{{ $p->id }}" class="rounded text-indigo-600">
                                <span class="ml-2 text-sm">{{ $p->nome_razao_social }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500">Nenhum prestador disponível.</p>
                        @endforelse
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="fecharModalAdicionarPrestador()" class="px-4 py-2 border rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Aprovar Orçamento -->
    <div id="modal-aprovar-orcamento" class="hidden fixed inset-0 z-50 modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:max-w-md w-full p-6">
                <h3 class="text-lg font-medium mb-4 text-green-600">Aprovar Orçamento</h3>
                <form id="form-aprovar-orcamento" method="POST">
                    @csrf
                    <input type="hidden" id="orcamento_id_aprovar">
                    <p class="text-sm mb-2"><strong>Prestador:</strong> <span id="orcamento_prestador_aprovar"></span></p>
                    <p class="text-sm mb-4 font-bold">Valor: <span id="orcamento_valor_aprovar"></span></p>
                    <textarea name="observacoes" placeholder="Observações (opcional)" class="w-full rounded-md border-gray-300 dark:bg-gray-900 mb-6"></textarea>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="fecharModalAprovarOrcamento()" class="px-4 py-2 border rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">Confirmar Aprovação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Rejeitar Orçamento -->
    <div id="modal-rejeitar-orcamento" class="hidden fixed inset-0 z-50 modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:max-w-md w-full p-6">
                <h3 class="text-lg font-medium mb-4 text-red-600">Rejeitar Orçamento</h3>
                <form id="form-rejeitar-orcamento" method="POST">
                    @csrf
                    <input type="hidden" id="orcamento_id_rejeitar">
                    <p class="text-sm mb-2"><strong>Prestador:</strong> <span id="orcamento_prestador_rejeitar"></span></p>
                    <p class="text-sm mb-4 font-bold">Valor: <span id="orcamento_valor_rejeitar"></span></p>
                    <div class="mb-4">
                        <label for="motivo_rejeicao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo da Rejeição <span class="text-red-500">*</span>
                        </label>
                        <textarea id="motivo_rejeicao" name="motivo_rejeicao" required rows="4" placeholder="Informe o motivo da rejeição..." class="w-full rounded-md border-gray-300 dark:bg-gray-900"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="fecharModalRejeitarOrcamento()" class="px-4 py-2 border rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">Confirmar Rejeição</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Negociar Orçamento -->
    <div id="modal-negociar-orcamento" class="hidden fixed inset-0 z-50 modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:max-w-lg w-full p-6">
                <h3 class="text-lg font-medium mb-4 text-blue-600">Solicitar Negociação</h3>
                <form id="form-negociar-orcamento" method="POST">
                    @csrf
                    <input type="hidden" id="orcamento_id_negociar">
                    <p class="text-sm mb-2"><strong>Prestador:</strong> <span id="orcamento_prestador_negociar"></span></p>
                    <p class="text-sm mb-4 font-bold">Valor Original: <span id="orcamento_valor_negociar"></span></p>
                    
                    <div class="mb-4">
                        <label for="tipo_negociacao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo de Negociação <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo_negociacao" name="tipo" required class="w-full rounded-md border-gray-300 dark:bg-gray-900">
                            <option value="">Selecione...</option>
                            <option value="desconto">Solicitar Desconto</option>
                            <option value="parcelamento">Parcelamento</option>
                            <option value="contraproposta">Contraproposta de Valor</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="descricao_negociacao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrição da Solicitação <span class="text-red-500">*</span>
                        </label>
                        <textarea id="descricao_negociacao" name="descricao" required rows="4" placeholder="Descreva o que você está solicitando ao prestador..." class="w-full rounded-md border-gray-300 dark:bg-gray-900"></textarea>
                        <p class="text-xs text-gray-500 mt-1">O prestador receberá esta solicitação e poderá responder com os valores e condições.</p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="fecharModalNegociarOrcamento()" class="px-4 py-2 border rounded-md">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Enviar Solicitação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalStatus() { 
            document.getElementById('modal-status').classList.remove('hidden');
            document.getElementById('modal-status').querySelector('select[name="status"]').value = '{{ $demanda->status }}';
        }
        function fecharModalStatus() { document.getElementById('modal-status').classList.add('hidden'); }
        
        function abrirModalAdicionarPrestador() { 
            document.getElementById('modal-adicionar-prestador').classList.remove('hidden');
        }
        function fecharModalAdicionarPrestador() { 
            document.getElementById('modal-adicionar-prestador').classList.add('hidden');
            // Limpa seleções ao fechar
            document.querySelectorAll('#modal-adicionar-prestador input[type="checkbox"]').forEach(cb => cb.checked = false);
        }
        
        function fecharModalAprovarOrcamento() { document.getElementById('modal-aprovar-orcamento').classList.add('hidden'); }
        function abrirModalAprovarOrcamento(id, val, nom) {
            document.getElementById('orcamento_id_aprovar').value = id;
            document.getElementById('orcamento_valor_aprovar').textContent = 'R$ ' + val;
            document.getElementById('orcamento_prestador_aprovar').textContent = nom;
            document.getElementById('modal-aprovar-orcamento').classList.remove('hidden');
            document.getElementById('form-aprovar-orcamento').action = '/demandas/{{ $demanda->id }}/orcamentos/' + id + '/aprovar';
        }

        function fecharModalRejeitarOrcamento() { 
            document.getElementById('modal-rejeitar-orcamento').classList.add('hidden');
            document.getElementById('form-rejeitar-orcamento').reset();
        }
        function abrirModalRejeitarOrcamento(id, val, nom) {
            document.getElementById('orcamento_id_rejeitar').value = id;
            document.getElementById('orcamento_prestador_rejeitar').textContent = nom;
            document.getElementById('orcamento_valor_rejeitar').textContent = 'R$ ' + val;
            document.getElementById('modal-rejeitar-orcamento').classList.remove('hidden');
            document.getElementById('form-rejeitar-orcamento').action = '/demandas/{{ $demanda->id }}/orcamentos/' + id + '/rejeitar';
        }

        function fecharModalNegociarOrcamento() { 
            document.getElementById('modal-negociar-orcamento').classList.add('hidden');
            document.getElementById('form-negociar-orcamento').reset();
            document.getElementById('campo_valor_negociacao').classList.add('hidden');
        }
        function abrirModalNegociacao(id, val, nom) {
            document.getElementById('orcamento_id_negociar').value = id;
            document.getElementById('orcamento_prestador_negociar').textContent = nom;
            document.getElementById('orcamento_valor_negociar').textContent = 'R$ ' + parseFloat(val).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('modal-negociar-orcamento').classList.remove('hidden');
            document.getElementById('form-negociar-orcamento').action = '/demandas/{{ $demanda->id }}/orcamentos/' + id + '/negociar';
            document.getElementById('campo_valor_negociacao').classList.add('hidden');
        }


        // Copiar e Compartilhar Links
        document.addEventListener('DOMContentLoaded', function() {
            // Função para copiar com fallback
            function copiarTexto(texto) {
                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(texto);
                } else {
                    // Fallback para navegadores antigos
                    const textArea = document.createElement('textarea');
                    textArea.value = texto;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    textArea.style.top = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        const successful = document.execCommand('copy');
                        document.body.removeChild(textArea);
                        return successful ? Promise.resolve() : Promise.reject();
                    } catch (err) {
                        document.body.removeChild(textArea);
                        return Promise.reject(err);
                    }
                }
            }

            document.querySelectorAll('.copiar-link-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Encontra o container com data-link-url
                    let container = this.closest('[data-link-url]');
                    if (!container) {
                        container = this.parentElement;
                    }
                    
                    const url = container.getAttribute('data-link-url') || container.dataset.linkUrl;
                    
                    if (!url) {
                        console.error('URL não encontrada');
                        return;
                    }

                    copiarTexto(url).then(() => {
                        const txt = this.querySelector('.copiar-texto');
                        if (txt) {
                            const old = txt.textContent;
                            txt.textContent = 'Copiado!';
                            txt.classList.remove('text-blue-600');
                            txt.classList.add('text-green-600', 'font-bold');
                            setTimeout(() => {
                                txt.textContent = old;
                                txt.classList.remove('text-green-600', 'font-bold');
                                txt.classList.add('text-blue-600');
                            }, 2000);
                        }
                    }).catch(err => {
                        console.error('Erro ao copiar:', err);
                        alert('Não foi possível copiar. Link: ' + url);
                    });
                });
            });
            
            document.querySelectorAll('.compartilhar-link-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    let container = this.closest('[data-link-url]');
                    if (!container) {
                        container = this.parentElement;
                    }
                    
                    const url = container.getAttribute('data-link-url') || container.dataset.linkUrl;
                    const prestadorNome = container.getAttribute('data-prestador-nome') || container.dataset.prestadorNome || 'Prestador';
                    const text = 'Olá! Você foi convidado para enviar um orçamento. Acesse: ' + url;
                    
                    if (navigator.share) {
                        navigator.share({ 
                            title: 'Orçamento - ' + prestadorNome, 
                            text: text, 
                            url: url 
                        }).catch(err => {
                            if (err.name !== 'AbortError') {
                                window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
                            }
                        });
                    } else {
                        window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
                    }
                });
            });
        });

        // Modal de Compartilhamento
        function abrirModalCompartilhar() {
            const modal = document.getElementById('modal-compartilhar');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function copiarLinkEspecifico(token, tokenAcesso) {
            const linkUrl = '{{ url("/") }}/publico/demanda-prestador/' + token + '/login';
            navigator.clipboard.writeText(linkUrl).then(() => {
                alert('Link copiado! Token: ' + tokenAcesso);
            });
        }

        function compartilharWhatsAppEspecifico(token, tokenAcesso) {
            const linkUrl = '{{ url("/") }}/publico/demanda-prestador/' + token + '/login';
            const text = 'Olá! Você foi convidado para enviar um orçamento.\n\nLink: ' + linkUrl + '\nToken de acesso: ' + tokenAcesso;
            window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
        }

        function fecharModalCompartilhar() {
            const modal = document.getElementById('modal-compartilhar');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function copiarLinkPublico() {
            const input = document.getElementById('link-publico-url');
            input.select();
            document.execCommand('copy');
            alert('Link copiado para a área de transferência!');
        }

        function compartilharWhatsApp() {
            const linkUrl = document.getElementById('link-publico-url').value;
            const text = 'Olá! Você foi convidado para enviar um orçamento para esta demanda. Acesse: ' + linkUrl;
            window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
        }

        function compartilharEmail() {
            const linkUrl = document.getElementById('link-publico-url').value;
            const subject = 'Convite para Orçamento - {{ $demanda->titulo }}';
            const body = 'Olá!\n\nVocê foi convidado para enviar um orçamento para esta demanda.\n\nAcesse o link: ' + linkUrl;
            window.location.href = 'mailto:?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
        }

        window.onclick = e => {
            if (e.target.classList.contains('modal-overlay')) {
                fecharModalStatus(); 
                fecharModalAdicionarPrestador(); 
                fecharModalAprovarOrcamento();
                fecharModalCompartilhar();
            }
        }
    </script>

    <!-- Modal de Compartilhamento -->
    <div id="modal-compartilhar" class="hidden fixed inset-0 z-50 overflow-y-auto modal-overlay" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Compartilhar Demanda com Prestadores
                    </h3>
                    
                    <!-- Lista de Links Ativos -->
                    @if($demanda->linksPublicos->where('ativo', true)->count() > 0)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Links Ativos ({{ $demanda->linksPublicos->where('ativo', true)->count() }})
                            </h4>
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($demanda->linksPublicos->where('ativo', true) as $link)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-1">
                                                    @if($link->nome_prestador)
                                                        {{ $link->nome_prestador }}
                                                    @else
                                                        Prestador
                                                    @endif
                                                </p>
                                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                    CPF/CNPJ: 
                                                    @if($link->cpf_cnpj_autorizado)
                                                        @php
                                                            $doc = preg_replace('/\D/', '', $link->cpf_cnpj_autorizado);
                                                            if(strlen($doc) == 11) {
                                                                $formatado = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
                                                            } else {
                                                                $formatado = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
                                                            }
                                                        @endphp
                                                        {{ $formatado }}
                                                    @else
                                                        Não informado
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">
                                                    Acessos: {{ $link->acessos }} | Criado em: {{ $link->created_at->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                            <form method="POST" action="{{ route('demandas.desativar-link', [$demanda, $link->id]) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Tem certeza que deseja desativar este link?')"
                                                        class="text-xs text-red-600 hover:text-red-800">
                                                    Desativar
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Link:
                                            </label>
                                            <div class="flex gap-2">
                                                <input type="text" 
                                                       value="{{ route('publico.demanda.login', $link->token) }}" 
                                                       readonly 
                                                       class="flex-1 text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-100">
                                                <button type="button" 
                                                        onclick="copiarLinkEspecifico('{{ $link->token }}', '{{ $link->token_acesso }}')" 
                                                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded-md">
                                                    Copiar
                                                </button>
                                            </div>
                                        </div>
                                        
                                        @if($link->token_acesso)
                                            <div class="mb-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded">
                                                <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200 mb-1">🔑 Token de Acesso:</p>
                                                <p class="text-lg font-bold text-yellow-900 dark:text-yellow-100 tracking-widest">{{ $link->token_acesso }}</p>
                                            </div>
                                        @endif
                                        
                                        <div class="flex gap-2">
                                            <button type="button" 
                                                    onclick="compartilharWhatsAppEspecifico('{{ $link->token }}', '{{ $link->token_acesso }}')" 
                                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-2 px-3 rounded-md">
                                                📱 WhatsApp
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            Gerar Novo Link
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Gere um novo link público seguro para compartilhar esta demanda com prestadores. 
                            O prestador precisará informar CPF/CNPJ e token de acesso para acessar.
                        </p>
                        <form method="POST" action="{{ route('demandas.gerar-link', $demanda) }}">
                            @csrf
                            
                            @if($errors->any())
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                                    <ul class="list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div class="mb-4">
                                <label for="nome_prestador" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nome do Prestador <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nome_prestador" 
                                       name="nome_prestador" 
                                       required
                                       placeholder="Nome ou Razão Social do prestador"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100 @error('nome_prestador') border-red-500 @enderror"
                                       value="{{ old('nome_prestador') }}">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Nome completo ou razão social do prestador que receberá o link.
                                </p>
                            </div>

                            <div class="mb-4">
                                <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    CPF/CNPJ do Prestador <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="cpf_cnpj" 
                                       name="cpf_cnpj" 
                                       required
                                       placeholder="000.000.000-00 ou 00.000.000/0000-00"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100 @error('cpf_cnpj') border-red-500 @enderror"
                                       value="{{ old('cpf_cnpj') }}">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Apenas este CPF/CNPJ poderá acessar o link com o token de acesso gerado.
                                </p>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                                Gerar Link Público Seguro
                            </button>
                        </form>
                    </div>

                    @if($demanda->linksPublicos->where('ativo', false)->count() > 0)
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Links Desativados:</p>
                            @foreach($demanda->linksPublicos->where('ativo', false) as $link)
                                <div class="flex items-center justify-between p-2 bg-gray-100 dark:bg-gray-700 rounded mb-2">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ route('publico.demanda.show', $link->token) }}
                                    </span>
                                    <span class="text-xs text-red-600">Desativado</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" 
                            onclick="fecharModalCompartilhar()" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
