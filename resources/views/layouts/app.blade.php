<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>

    {{--
        LAYOUT ESTÁTICO (Simplicidade Máxima):
        ──────────────────────────────────────
        • Desktop: Menu sempre visível (256px de largura fixa).
        • Mobile: Menu overlay toggleable via sidebarOpen.
        • Sem localStorage, sem flickering, sem classes dinâmicas complexas.
    --}}
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
          x-data="{ sidebarOpen: false }">

        {{-- ===========================
             SIDEBAR (Desktop fixo / Mobile overlay)
             =========================== --}}
        <aside
            id="app-sidebar"
            class="fixed inset-y-0 left-0 z-40 flex flex-col bg-slate-900 text-slate-300 w-64 transition-transform duration-300 ease-in-out md:translate-x-0"
            x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        >
            @include('layouts.navigation')
        </aside>

        {{-- Mobile backdrop (só aparece se sidebarOpen for true no mobile) --}}
        <div
            class="fixed inset-0 z-30 bg-slate-900/60 md:hidden"
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
        ></div>

        {{-- ===========================
             CONTEÚDO PRINCIPAL
             =========================== --}}
        {{-- ml-64 no desktop para compensar o aside fixed --}}
        <div
            id="app-content"
            class="flex flex-col min-h-screen transition-all duration-300 md:ml-64"
        >
            @include('layouts.top-bar')

            <main class="flex-1 p-4 md:p-6 lg:p-8">
                @isset($header)
                    <div class="mb-6">{{ $header }}</div>
                @endisset
                <div class="mx-auto">{{ $slot }}</div>
            </main>
        </div>

    </body>
</html>
