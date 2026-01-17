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
                    <div class="mb-6">
                        <a href="{{ route('demandas.index') }}" class="text-blue-500 hover:text-blue-700">← Voltar para Demandas</a>
                    </div>

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
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$demanda->status] ?? 'bg-gray-100 text-gray-800' }}">
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
                        </div>

                        @if($demanda->prazo_limite)
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Prazo Limite</h3>
                            <p>{{ $demanda->prazo_limite->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Descrição</h3>
                        <p class="whitespace-pre-wrap">{{ $demanda->descricao }}</p>
                    </div>

                    @if($demanda->prestadores->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Prestadores Convidados</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Link</th>
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
                                                        <button type="button" 
                                                                class="copiar-link-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                                                                title="Copiar link">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                            </svg>
                                                            <span class="copiar-texto">Copiar</span>
                                                        </button>
                                                        <button type="button" 
                                                                class="compartilhar-link-btn inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                                                                title="Compartilhar link">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                                            </svg>
                                                            Compartilhar
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($demanda->orcamentos->count() > 0)
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Orçamentos Recebidos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Prestador</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Valor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Data</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($demanda->orcamentos as $orcamento)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $orcamento->prestador->nome_razao_social }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                R$ {{ number_format($orcamento->valor, 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'recebido' => 'bg-blue-100 text-blue-800',
                                                        'aprovado' => 'bg-green-100 text-green-800',
                                                        'rejeitado' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$orcamento->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($orcamento->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $orcamento->created_at->format('d/m/Y') }}
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

    <script>
        // Configura event listeners para os botões
        document.addEventListener('DOMContentLoaded', function() {
            // Botões de copiar
            document.querySelectorAll('.copiar-link-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const container = this.closest('[data-link-url]');
                    const url = container.getAttribute('data-link-url');
                    const linkId = container.getAttribute('data-link-id');
                    copiarLink(url, linkId, this);
                });
            });

            // Botões de compartilhar
            document.querySelectorAll('.compartilhar-link-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const container = this.closest('[data-link-url]');
                    const url = container.getAttribute('data-link-url');
                    const prestadorNome = container.getAttribute('data-prestador-nome');
                    compartilharLink(url, prestadorNome);
                });
            });
        });

        function copiarLink(url, linkId, button) {
            // Usa a Clipboard API moderna
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(function() {
                    mostrarFeedbackBotao(button, 'Copiado!');
                    mostrarFeedback('Link copiado!', 'success', linkId);
                }).catch(function(err) {
                    console.error('Erro ao copiar:', err);
                    copiarFallback(url, linkId, button);
                });
            } else {
                // Fallback para navegadores antigos
                copiarFallback(url, linkId, button);
            }
        }

        function copiarFallback(url, linkId, button) {
            // Cria um elemento temporário
            const textArea = document.createElement('textarea');
            textArea.value = url;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    mostrarFeedbackBotao(button, 'Copiado!');
                    mostrarFeedback('Link copiado!', 'success', linkId);
                } else {
                    mostrarFeedback('Não foi possível copiar. Link: ' + url, 'error', linkId);
                }
            } catch (err) {
                console.error('Erro ao copiar:', err);
                mostrarFeedback('Erro ao copiar. Tente selecionar manualmente.', 'error', linkId);
            }
            
            document.body.removeChild(textArea);
        }

        function mostrarFeedbackBotao(button, texto) {
            if (!button) return;
            
            const textoElemento = button.querySelector('.copiar-texto');
            const svgElemento = button.querySelector('svg');
            if (!textoElemento) return;
            
            // Salva o texto original se ainda não foi salvo
            if (!button.dataset.originalText) {
                button.dataset.originalText = textoElemento.textContent.trim();
            }
            
            // Altera o texto do botão
            textoElemento.textContent = texto;
            
            // Altera APENAS a cor do texto e ícone para verde
            textoElemento.classList.add('text-green-600', 'dark:text-green-400', 'font-bold');
            if (svgElemento) {
                svgElemento.classList.add('text-green-600', 'dark:text-green-400');
            }
            button.disabled = true;
            
            // Restaura após 2 segundos
            setTimeout(function() {
                const originalText = button.dataset.originalText || 'Copiar';
                textoElemento.textContent = originalText;
                textoElemento.classList.remove('text-green-600', 'dark:text-green-400', 'font-bold');
                if (svgElemento) {
                    svgElemento.classList.remove('text-green-600', 'dark:text-green-400');
                }
                button.disabled = false;
            }, 2000);
        }

        function compartilharLink(url, prestadorNome) {
            const title = 'Demanda: {{ $demanda->titulo }}';
            const text = 'Olá! Você foi convidado para enviar um orçamento para a demanda: {{ $demanda->titulo }}. Acesse o link para ver os detalhes:';

            // Tenta usar a Web Share API (Mobile)
            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: text + ' ' + url,
                    url: url,
                }).then(function() {
                    mostrarFeedback('Link compartilhado!', 'success', null);
                }).catch(function(err) {
                    // Usuário cancelou ou erro ocorreu
                    if (err.name !== 'AbortError') {
                        console.error('Erro ao compartilhar:', err);
                        compartilharWhatsApp(url, text);
                    }
                });
            } else {
                // Fallback para WhatsApp (Desktop/Outros)
                compartilharWhatsApp(url, text);
            }
        }

        function compartilharWhatsApp(url, text) {
            const encodedText = encodeURIComponent(text + ' ' + url);
            window.open('https://wa.me/?text=' + encodedText, '_blank');
        }

        function mostrarFeedback(mensagem, tipo, linkId) {
            // Remove feedbacks anteriores
            const feedbacksAnteriores = document.querySelectorAll('.link-feedback');
            feedbacksAnteriores.forEach(el => el.remove());

            // Cria elemento de feedback
            const feedback = document.createElement('div');
            feedback.className = 'link-feedback fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium transition-all duration-300 ' + 
                (tipo === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white');
            feedback.textContent = mensagem;

            document.body.appendChild(feedback);

            // Remove após 3 segundos
            setTimeout(function() {
                feedback.style.opacity = '0';
                feedback.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    if (feedback.parentNode) {
                        feedback.parentNode.removeChild(feedback);
                    }
                }, 300);
            }, 3000);
        }
    </script>
</x-app-layout>
