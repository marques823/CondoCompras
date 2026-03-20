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
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
          x-data="{ sidebarOpen: window.innerWidth >= 768 }">

        {{-- =====================================================
             MOBILE OVERLAY SIDEBAR (fixed, outside flex layout)
             ===================================================== --}}
        <div
            class="md:hidden fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-slate-900 shadow-2xl"
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

        {{-- Mobile Overlay Backdrop --}}
        <div
            class="md:hidden fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm"
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
        ></div>

        {{-- =====================================================
             MAIN LAYOUT: Desktop sidebar + content (flex row)
             ===================================================== --}}
        <div class="flex h-screen overflow-hidden">

            {{-- Desktop Sidebar (always in flex flow, never overlaps) --}}
            <aside
                class="hidden md:flex flex-col flex-shrink-0 bg-slate-900 text-slate-300 h-screen transition-all duration-300 ease-in-out overflow-hidden"
                :class="sidebarOpen ? 'w-64' : 'w-16'"
            >
                @include('layouts.navigation', ['isMobile' => false])
            </aside>

            {{-- Main Content Area --}}
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden bg-gray-50 dark:bg-gray-900 min-w-0">
                @include('layouts.top-bar')

                <main class="p-4 md:p-6 lg:p-8">
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
