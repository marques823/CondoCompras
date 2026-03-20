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
            
            /* Layout Fixo Robusto (256px sidebar) */
            @media (min-width: 768px) {
                #sidebar-main { width: 256px !important; transform: translateX(0) !important; position: fixed !important; height: 100vh; }
                #content-main { margin-left: 256px !important; }
                
                /* Normalizar classes de centralização das páginas internas */
                #content-main .mx-auto { margin-left: 0 !important; margin-right: 0 !important; }
                #content-main .max-w-7xl { max-width: none !important; }
            }
        </style>
    </head>

    <body class="font-sans antialiased bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">

        {{-- Barra Lateral --}}
        <aside id="sidebar-main" class="fixed inset-y-0 left-0 z-40 bg-slate-900 text-slate-300 transition-transform duration-300 -translate-x-full md:translate-x-0 overflow-hidden flex flex-col">
            @include('layouts.navigation')
        </aside>

        {{-- Backdrop Mobile --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/50 md:hidden"></div>

        {{-- Container de Conteúdo --}}
        <div id="content-main" class="min-h-screen flex flex-col">
            
            @include('layouts.top-bar')

            {{-- Área do Título (Título da Página) --}}
            @isset($header)
                <div class="bg-white border-b border-gray-100 px-6 py-4">
                    {{ $header }}
                </div>
            @endisset

            {{-- Área do Slot (Conteúdo da Página) --}}
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>

        </div>

    </body>
</html>
