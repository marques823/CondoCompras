<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link Desativado ou Expirado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-xl shadow-2xl p-8 text-center">
            <!-- Ícone -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-indigo-100 mb-6">
                <svg class="h-12 w-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>

            <!-- Título -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Link Desativado ou Expirado
            </h1>

            <!-- Mensagem -->
            <div class="space-y-4 mb-6">
                @php
                    $isLinkPrestador = isset($link->prestador_id);
                    $isDesativado = isset($link->ativo) ? !$link->ativo : (isset($link->usado) ? $link->usado : false);
                    $isExpirado = $link->expira_em && $link->expira_em->isPast();
                @endphp

                @if($isDesativado)
                    <p class="text-lg text-gray-700">
                        Este link foi <strong class="text-indigo-600">desativado</strong> pela administradora.
                    </p>
                    <p class="text-sm text-gray-600">
                        Se você acredita que isso é um erro, entre em contato com a administradora do condomínio.
                    </p>
                @elseif($isExpirado)
                    <p class="text-lg text-gray-700">
                        Este link <strong class="text-indigo-600">expirou</strong> em {{ $link->expira_em->format('d/m/Y') }}.
                    </p>
                    <p class="text-sm text-gray-600">
                        Para acessar esta demanda, solicite um novo link à administradora do condomínio.
                    </p>
                @elseif(isset($link->usado) && $link->usado)
                    <p class="text-lg text-gray-700">
                        Este link já foi <strong class="text-indigo-600">utilizado</strong>.
                    </p>
                    <p class="text-sm text-gray-600">
                        Cada link pode ser usado apenas uma vez. Solicite um novo link se necessário.
                    </p>
                @else
                    <p class="text-lg text-gray-700">
                        Este link não está mais disponível.
                    </p>
                @endif
            </div>

            <!-- Informações adicionais -->
            @php
                $demanda = $link->demanda ?? null;
            @endphp
            @if($demanda)
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <p class="text-sm font-medium text-gray-700 mb-2">Informações:</p>
                    <p class="text-sm text-gray-600">
                        <strong>Demanda:</strong> {{ $demanda->titulo }}
                    </p>
                    @if($demanda->condominio)
                        <p class="text-sm text-gray-600">
                            <strong>Condomínio:</strong> {{ $demanda->condominio->nome }}
                        </p>
                    @endif
                    @if(isset($link->nome_prestador) && $link->nome_prestador)
                        <p class="text-sm text-gray-600">
                            <strong>Prestador:</strong> {{ $link->nome_prestador }}
                        </p>
                    @elseif(isset($link->prestador) && $link->prestador)
                        <p class="text-sm text-gray-600">
                            <strong>Prestador:</strong> {{ $link->prestador->nome_razao_social }}
                        </p>
                    @endif
                </div>
            @endif

            <!-- Informação adicional -->
            <div class="mt-6">
                <p class="text-xs text-gray-500">
                    Se você precisa de ajuda, entre em contato com a administradora do condomínio.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
