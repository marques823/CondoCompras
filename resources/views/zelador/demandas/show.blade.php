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
                        <a href="{{ route('zelador.demandas.index') }}" class="text-blue-500 hover:text-blue-700">← Voltar para Minhas Demandas</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Título</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $demanda->titulo }}</p>
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
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$demanda->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$demanda->status] ?? ucfirst($demanda->status) }}
                            </span>
                        </div>

                        @if($demanda->urgencia)
                        <div>
                            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Urgência</h3>
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

                        <div>
                            <h3 class="text-lg font-semibold mb-2">Criado em</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $demanda->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mb-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">Descrição</h3>
                        <p class="whitespace-pre-wrap">{{ $demanda->descricao }}</p>
                    </div>

                    @if($demanda->anexos->count() > 0)
                    <div class="mb-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Arquivos e Fotos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Arquivo</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-gray-900 dark:text-gray-100">
                                    @php $imgIdxZelador = 0; @endphp
                                    @foreach($demanda->anexos as $anexo)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                {{ $anexo->nome_original }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ str_contains($anexo->mime_type, 'image') ? 'Imagem' : 'Documento' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium space-x-3">
                                                @if(str_starts_with($anexo->mime_type ?? '', 'image/'))
                                                    <button type="button" onclick="abrirImagem({{ $imgIdxZelador++ }})" class="text-indigo-600 hover:text-indigo-900 font-bold underline">Visualizar</button>
                                                @else
                                                    <a href="{{ asset('storage/' . $anexo->caminho) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-bold underline">Abrir</a>
                                                @endif
                                                <a href="{{ asset('storage/' . $anexo->caminho) }}" download="{{ $anexo->nome_original }}" class="text-green-600 hover:text-green-900 font-bold underline">Download</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Lightbox Zelador com Zoom -->
                    <div id="modal-imagem" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-90 overflow-auto py-10" onclick="fecharImagem()">
                        <button type="button" class="fixed top-6 right-6 text-white hover:text-gray-300 z-[110] bg-black bg-opacity-50 p-2 rounded-full" onclick="fecharImagem()">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <button type="button" class="fixed left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-[110] bg-black bg-opacity-20 p-2 rounded-full" onclick="event.stopPropagation(); imagemAnterior()">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button type="button" class="fixed right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 z-[110] bg-black bg-opacity-20 p-2 rounded-full" onclick="event.stopPropagation(); proximaImagem()">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        <div class="relative flex flex-col items-center justify-center" onclick="event.stopPropagation()">
                            <img id="imagem-modal" src="" alt="" 
                                 class="max-w-[85vw] max-h-[65vh] object-contain rounded shadow-2xl transition-transform duration-300 cursor-zoom-in"
                                 ondblclick="toggleZoom(this)">
                            <div class="mt-4 flex flex-col items-center">
                                <p id="nome-imagem-modal" class="text-white text-sm font-medium bg-black bg-opacity-60 px-4 py-1.5 rounded-full"></p>
                                <p class="text-gray-400 text-[10px] mt-2 italic text-gray-100">Dica: 2 cliques para zoom</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $listaImagensZelador = $demanda->anexos->filter(fn($a) => str_starts_with($a->mime_type ?? '', 'image/'))
                            ->map(fn($a) => ['url' => asset('storage/' . $a->caminho), 'nome' => $a->nome_original])
                            ->values();
                    @endphp

                    <script>
                        const imagens = @json($listaImagensZelador);
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

                    @if($demanda->orcamentos->count() > 0)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200 font-semibold text-gray-900 dark:text-gray-100">Orçamentos Recebidos</h3>
                        <div class="space-y-4">
                            @foreach($demanda->orcamentos as $orcamento)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-gray-100">{{ $orcamento->prestador->nome_razao_social }}</p>
                                            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">R$ {{ number_format($orcamento->valor, 2, ',', '.') }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $orcamento->status === 'aprovado' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
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
        </div>
    </div>
</x-app-layout>
