<header class="sticky top-0 z-30 h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center px-6">
    
    {{-- Lado Esquerdo: Toggle Mobile + Breadcrumbs --}}
    <div class="flex items-center gap-4 flex-1 overflow-hidden">
        {{-- Botão Mobile --}}
        <button @click="sidebarOpen = true" class="md:hidden p-2 -ml-2 text-gray-400 hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Breadcrumbs Simples --}}
        <nav class="flex text-sm font-medium text-gray-500 whitespace-nowrap overflow-hidden">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Home</a>
            @foreach(request()->segments() as $segment)
                <span class="mx-2 text-gray-300">/</span>
                <span class="capitalize truncate">{{ str_replace('-', ' ', $segment) }}</span>
            @endforeach
        </nav>
    </div>

    {{-- Lado Direito: Perfil --}}
    <div class="flex items-center gap-3">
        <div class="hidden sm:flex flex-col items-end text-right">
            <span class="text-sm font-semibold text-gray-700 leading-none">{{ auth()->user()->name }}</span>
            <span class="text-xs text-gray-400 mt-1 uppercase tracking-tighter">{{ auth()->user()->getRoleNames()->first() ?? 'Usuário' }}</span>
        </div>
        
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm hover:ring-4 hover:ring-blue-50 transition-all">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">Meu Perfil</x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Sair</x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>

</header>
