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
                            @if($demanda->usuario)
                                <p class="text-sm text-gray-500 mt-1">
                                    por <span class="font-medium">{{ $demanda->usuario->name }}</span>
                                    @if($demanda->usuario->isZelador())
                                        <span class="text-xs text-blue-600">(Zelador)</span>
                                    @endif
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium cursor-pointer" onclick="return confirm('Tem certeza?') && this.nextElementSibling.submit()">
                                                Remover
                                                <form method="POST" action="{{ route('demandas.remover-prestador', [$demanda, $prestador]) }}" class="hidden">@csrf @method('DELETE')</form>
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
                                                    <button onclick="abrirModalAprovarOrcamento({{ $orcamento->id }}, '{{ number_format($orcamento->valor, 2, ',', '.') }}', '{{ $orcamento->prestador->nome_razao_social }}')" class="text-green-600 hover:text-green-900">Aprovar</button>
                                                    <button onclick="abrirModalRejeitarOrcamento({{ $orcamento->id }}, '{{ $orcamento->prestador->nome_razao_social }}')" class="text-red-600 hover:text-red-900">Rejeitar</button>
                                                    <button onclick="abrirModalNegociacao({{ $orcamento->id }}, {{ $orcamento->valor }}, '{{ $orcamento->prestador->nome_razao_social }}')" class="text-blue-600 hover:text-blue-900">Negociar</button>
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
                <form method="POST" action="{{ route('demandas.adicionar-prestadores', $demanda) }}">
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

        window.onclick = e => {
            if (e.target.classList.contains('modal-overlay')) {
                fecharModalStatus(); 
                fecharModalAdicionarPrestador(); 
                fecharModalAprovarOrcamento();
            }
        }
    </script>
</x-app-layout>
