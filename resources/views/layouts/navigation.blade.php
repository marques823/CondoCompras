{{--
    navigation.blade.php – renderiza apenas o conteúdo interno do sidebar.
    O wrapper (aside/div fixed) é fornecido pelo app.blade.php.
    $isMobile: true = mobile drawer, false = desktop sidebar
--}}
@php $user = Auth::user(); @endphp

{{-- Logo / Header --}}
<div class="flex h-16 items-center justify-between px-4 bg-slate-950/50 border-b border-slate-800 flex-shrink-0">
    <a href="{{ route('dashboard') }}" class="flex items-center min-w-0">
        <x-application-logo class="block h-8 w-8 flex-shrink-0 fill-current text-blue-500" />
        <span
            class="ml-3 text-lg font-bold text-white whitespace-nowrap"
            @if(!($isMobile ?? true))
            :class="sidebarOpen ? 'opacity-100' : 'opacity-0'"
            style="transition: opacity 0.3s"
            @endif
        >CondoCompras</span>
    </a>
    @if($isMobile ?? true)
        {{-- Mobile: botão fechar --}}
        <button @click="sidebarOpen = false" class="p-1 rounded-md text-slate-400 hover:text-white hover:bg-slate-700">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
</div>

{{-- Navigation Links --}}
<nav class="flex-1 px-2 pt-4 pb-4 space-y-1 overflow-y-auto overflow-x-hidden text-slate-300">

    {{-- Dashboard --}}
    <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
        <span
            class="ml-3 whitespace-nowrap"
            @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif
        >{{ __('Dashboard') }}</span>
    </x-nav-link-sidebar>

    {{-- Operacional --}}
    @if($user->isZelador() || $user->isAdministradora() || $user->isGerente())
    <div class="pt-4 space-y-1">
        <p
            class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 whitespace-nowrap"
            @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif
        >Operacional</p>

        @if($user->isZelador())
            <x-nav-link-sidebar :href="route('demandas.index')" :active="request()->routeIs('demandas.*')" icon="clipboard">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Minhas Demandas') }}</span>
            </x-nav-link-sidebar>
        @endif

        @if($user->isAdministradora() || $user->isGerente())
            <x-nav-link-sidebar :href="route('condominios.index')" :active="request()->routeIs('condominios.*')" icon="office-building">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Condomínios') }}</span>
            </x-nav-link-sidebar>
            <x-nav-link-sidebar :href="route('demandas.index')" :active="request()->routeIs('demandas.*')" icon="clipboard">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Demandas') }}</span>
            </x-nav-link-sidebar>
            <x-nav-link-sidebar :href="route('prestadores.index')" :active="request()->routeIs('prestadores.*')" icon="truck">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Prestadores') }}</span>
            </x-nav-link-sidebar>
            <x-nav-link-sidebar :href="route('documentos.index')" :active="request()->routeIs('documentos.*')" icon="document">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Documentos') }}</span>
            </x-nav-link-sidebar>
        @endif
    </div>
    @endif

    {{-- Gestão --}}
    @if($user->isAdmin() || $user->isAdministradora() || $user->isGerente())
    <div class="pt-4 space-y-1">
        <p
            class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 whitespace-nowrap"
            @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif
        >Gestão</p>

        @if($user->isAdministradora())
            <x-nav-link-sidebar :href="route('users.index')" :active="request()->routeIs('users.*')" icon="users">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Gerentes') }}</span>
            </x-nav-link-sidebar>
        @elseif($user->isGerente())
            <x-nav-link-sidebar :href="route('users.index')" :active="request()->routeIs('users.*')" icon="users">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Zeladores') }}</span>
            </x-nav-link-sidebar>
        @elseif($user->isAdmin())
            <x-nav-link-sidebar :href="route('administradoras.index')" :active="request()->routeIs('administradoras.*')" icon="briefcase">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Administradoras') }}</span>
            </x-nav-link-sidebar>
            <x-nav-link-sidebar :href="route('users.index')" :active="request()->routeIs('users.*')" icon="users">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Usuários Global') }}</span>
            </x-nav-link-sidebar>
        @endif
    </div>
    @endif

    {{-- Sistema --}}
    <div class="pt-4 space-y-1">
        <p
            class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 whitespace-nowrap"
            @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif
        >Sistema</p>

        @if($user->isAdministradora())
            <x-nav-link-sidebar :href="route('administradora.config')" :active="request()->routeIs('administradora.config')" icon="cog">
                <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Configurações') }}</span>
            </x-nav-link-sidebar>
        @endif

        <x-nav-link-sidebar :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" icon="user">
            <span class="ml-3 whitespace-nowrap" @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif>{{ __('Meu Perfil') }}</span>
        </x-nav-link-sidebar>
    </div>
</nav>

{{-- Logout --}}
<div class="flex-shrink-0 p-2 bg-slate-950/30 border-t border-slate-800">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
            class="flex w-full items-center px-3 py-2 text-sm font-medium text-slate-400 rounded-md hover:bg-slate-800 hover:text-white transition-colors"
        >
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span
                class="ml-3 whitespace-nowrap"
                @if(!($isMobile ?? true)) :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s" @endif
            >Sair do Sistema</span>
        </button>
    </form>
</div>

{{-- Desktop: collapse toggle --}}
@if(!($isMobile ?? true))
<button
    @click="sidebarOpen = !sidebarOpen"
    class="flex w-full items-center justify-center py-2 text-slate-500 hover:text-slate-300 hover:bg-slate-800 transition-colors border-t border-slate-800"
    :title="sidebarOpen ? 'Recolher menu' : 'Expandir menu'"
>
    <svg class="h-4 w-4" :style="sidebarOpen ? '' : 'transform: rotate(180deg)'" style="transition: transform 0.3s" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7"/>
    </svg>
    <span class="ml-2 text-xs whitespace-nowrap" :class="sidebarOpen ? 'opacity-100' : 'opacity-0'" style="transition: opacity 0.3s">Recolher</span>
</button>
@endif
