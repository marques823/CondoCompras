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
        LAYOUT STRATEGY (prova de erro):
        ─────────────────────────────────
        • Sidebar: SEMPRE fixed. No mobile: oculto por padrão, aparece como overlay.
          No desktop: sempre visível, largura controlada via style binding do Alpine.
        • Conteúdo: margin-left via style binding do Alpine (0 no mobile, 256px/64px no desktop).
          Não depende de flex, de classes Tailwind dinâmicas, nem de z-index.
    --}}
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
          x-data="{ sidebarOpen: window.innerWidth >= 768 }">

        {{-- ===========================
             SIDEBAR (sempre fixed)
             =========================== --}}
        <aside
            id="app-sidebar"
            class="fixed inset-y-0 left-0 z-40 flex flex-col bg-slate-900 text-slate-300 overflow-hidden"
            style="transition: width 0.3s ease, transform 0.3s ease;"
            x-bind:style="
                window.innerWidth >= 768
                    ? (sidebarOpen ? 'width:256px; transform:translateX(0)' : 'width:64px; transform:translateX(0)')
                    : (sidebarOpen ? 'width:256px; transform:translateX(0)' : 'width:256px; transform:translateX(-100%)')
            "
        >
            @include('layouts.navigation')
        </aside>

        {{-- Mobile backdrop --}}
        <div
            class="fixed inset-0 z-30 bg-slate-900/60 md:hidden"
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-cloak
        ></div>

        {{-- ===========================
             CONTEÚDO PRINCIPAL
             =========================== --}}
        <div
            id="app-content"
            class="flex flex-col min-h-screen"
            style="transition: margin-left 0.3s ease;"
            x-bind:style="window.innerWidth >= 768 ? (sidebarOpen ? 'margin-left:256px' : 'margin-left:64px') : 'margin-left:0'"
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
