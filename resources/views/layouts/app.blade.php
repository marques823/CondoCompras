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

            /* Sidebar desktop: sempre visível, parte do fluxo */
            .sidebar-desktop {
                width: 256px; /* w-64 */
                flex-shrink: 0;
                transition: width 0.3s ease;
            }
            .sidebar-desktop.collapsed {
                width: 64px; /* w-16 */
            }

            /* Conteúdo principal: ocupa o restante */
            .main-content {
                flex: 1 1 0%;
                min-width: 0;
                overflow-x: hidden;
                overflow-y: auto;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
          x-data="{ sidebarOpen: window.innerWidth >= 768 }">

        {{-- ============================================================
             MOBILE: Sidebar Overlay + Backdrop (fixed, fora do flex)
             ============================================================ --}}
        <div
            class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-slate-900 shadow-2xl md:hidden"
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            x-cloak
        >
            @include('layouts.navigation', ['isMobile' => true])
        </div>
        <div
            class="fixed inset-0 z-40 bg-slate-900/60 md:hidden"
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-cloak
        ></div>

        {{-- ============================================================
             LAYOUT PRINCIPAL (flex row): Sidebar Desktop + Conteúdo
             ============================================================ --}}
        <div class="flex h-screen overflow-hidden">

            {{-- Sidebar Desktop: elemento Flex nativo, nunca sobrepõe --}}
            <aside
                class="hidden md:flex flex-col bg-slate-900 text-slate-300 flex-shrink-0 overflow-hidden"
                style="width: 256px; transition: width 0.3s ease;"
                :style="sidebarOpen ? 'width: 256px' : 'width: 64px'"
            >
                @include('layouts.navigation', ['isMobile' => false])
            </aside>

            {{-- Conteúdo Principal --}}
            <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
                @include('layouts.top-bar')

                <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 md:p-6 lg:p-8">
                    @isset($header)
                        <div class="mb-6">{{ $header }}</div>
                    @endisset

                    <div class="mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>

        </div>
    </body>
</html>
