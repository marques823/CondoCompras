<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* ── Layout estrutural ── */
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 256px;
                overflow-y: auto;
                z-index: 50;
            }
            .main-content {
                margin-left: 256px;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            /* ── Área de conteúdo das páginas ── */
            .py-12 { padding-top: 24px !important; padding-bottom: 24px !important; }
            .max-w-7xl { max-width: 100% !important; }

            /* ── Card / container das páginas ── */
            .bg-white.overflow-hidden.shadow-sm.sm\:rounded-lg,
            .bg-white.overflow-hidden.shadow-sm.rounded-lg,
            .bg-white.shadow-sm.sm\:rounded-lg,
            .bg-white.shadow-sm {
                border-radius: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-top: 2px solid #e2e8f0 !important;
            }

            /* ── Cabeçalho da página (header slot) ── */
            header.bg-white.shadow {
                box-shadow: none !important;
                border-bottom: 1px solid #e2e8f0 !important;
                background: #ffffff !important;
            }
            header.bg-white.shadow h2 {
                font-size: 1rem !important;
                font-weight: 600 !important;
                color: #1e3a5f !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
            }

            /* ── Tabelas ── */
            table {
                width: 100%;
                border-collapse: collapse !important;
                font-size: 0.875rem !important;
            }
            thead {
                background: #f8fafc !important;
                border-bottom: 2px solid #cbd5e1 !important;
            }
            thead th {
                padding: 10px 16px !important;
                font-size: 0.7rem !important;
                font-weight: 700 !important;
                color: #475569 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.06em !important;
                background: transparent !important;
                border: none !important;
            }
            tbody tr {
                border-bottom: 1px solid #e2e8f0 !important;
                transition: background 0.1s;
            }
            tbody tr:hover {
                background: #f1f5f9 !important;
            }
            tbody tr:last-child {
                border-bottom: none !important;
            }
            tbody td {
                padding: 10px 16px !important;
                color: #334155 !important;
                background: transparent !important;
                border: none !important;
            }

            /* ── Badges de status ── */
            .rounded-full {
                border-radius: 4px !important;
            }

            /* ── Botões de ação nas tabelas ── */
            .bg-blue-500, .bg-blue-600 {
                border-radius: 4px !important;
            }

            /* ── Alertas / flash messages ── */
            .bg-green-100 { border-radius: 0 !important; border-left: 4px solid #16a34a !important; }
            .bg-red-100    { border-radius: 0 !important; border-left: 4px solid #dc2626 !important; }
            .bg-yellow-100 { border-radius: 0 !important; border-left: 4px solid #ca8a04 !important; }

            /* ── Paginação ── */
            nav[aria-label="Pagination"] span, nav[role="navigation"] span,
            nav[aria-label="Pagination"] a, nav[role="navigation"] a {
                border-radius: 4px !important;
            }

            /* ── Contraste de textos globais ── */
            /* Forçar textos secundários a serem legíveis em fundo branco */
            .text-gray-100 { color: #1e293b !important; }
            .text-gray-200 { color: #1e293b !important; }
            .text-gray-300 { color: #334155 !important; }
            .text-gray-400 { color: #475569 !important; }
            .text-gray-500 { color: #475569 !important; }
            .text-gray-600 { color: #374151 !important; }

            /* Labels de formulário */
            label, .block.font-medium.text-sm {
                color: #1e293b !important;
                font-weight: 500 !important;
            }

            /* Textos de descrição / subtítulo */
            .text-sm.text-gray-600,
            .text-sm.text-gray-500,
            .text-sm.text-gray-400,
            p.text-gray-400,
            p.text-gray-500 {
                color: #475569 !important;
            }

            /* Títulos e textos de destaque */
            .text-gray-800, .text-gray-900 {
                color: #0f172a !important;
            }

            /* Inputs e selects */
            input, select, textarea {
                color: #0f172a !important;
                background-color: #ffffff !important;
                border-color: #cbd5e1 !important;
            }
            input::placeholder, textarea::placeholder {
                color: #94a3b8 !important;
            }

            /* Links de ação nas tabelas */
            .text-blue-600 { color: #1d4ed8 !important; }
            .text-indigo-600 { color: #4338ca !important; }
            .text-red-600   { color: #dc2626 !important; }
        </style>
    </head>
    <body class="font-sans antialiased" style="background: #f1f5f9; margin: 0; padding: 0;">

        {{-- Sidebar fixo --}}
        @include('layouts.navigation')

        {{-- Conteúdo principal deslocado --}}
        <div class="main-content">
            @include('layouts.top-bar')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

    </body>
</html>
