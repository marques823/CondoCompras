@php $user = Auth::user(); @endphp

{{-- Logo --}}
<div class="flex h-16 flex-shrink-0 items-center justify-between px-4 bg-slate-950/50 border-b border-slate-800">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        <x-application-logo class="h-8 w-8 flex-shrink-0 fill-current text-blue-500" />
        <span class="text-lg font-bold text-white whitespace-nowrap">CondoCompras</span>
    </a>
    {{-- Fechar no mobile --}}
    <button @click="sidebarOpen = false"
            class="md:hidden flex-shrink-0 p-1 rounded text-slate-400 hover:text-white hover:bg-slate-700">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

{{-- Links de navegação --}}
<nav class="flex-1 overflow-y-auto overflow-x-hidden px-2 py-4 space-y-1">

    <a href="{{ route('dashboard') }}"
       class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors 
              {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="ml-3">Dashboard</span>
    </a>

    @if($user->isZelador() || $user->isAdministradora() || $user->isGerente())
    <div class="pt-4">
        <p class="px-3 mb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">Operacional</p>

        @if($user->isZelador())
        <a href="{{ route('demandas.index') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('demandas.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="ml-3">Minhas Demandas</span>
        </a>
        @endif

        @if($user->isAdministradora() || $user->isGerente())
        <a href="{{ route('condominios.index') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('condominios.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="ml-3">Condomínios</span>
        </a>
        <a href="{{ route('demandas.index') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('demandas.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="ml-3">Demandas</span>
        </a>
        <a href="{{ route('prestadores.index') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('prestadores.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <span class="ml-3">Prestadores</span>
        </a>
        <a href="{{ route('documentos.index') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('documentos.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span class="ml-3">Documentos</span>
        </a>
        @endif
    </div>
    @endif

    @if($user->isAdmin() || $user->isAdministradora() || $user->isGerente())
    <div class="pt-4">
        <p class="px-3 mb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">Gestão</p>

        @if($user->isAdministradora())
        <a href="{{ route('users.index') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="ml-3">Gerentes</span>
        </a>
        @elseif($user->isGerente())
        <a href="{{ route('users.index') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="ml-3">Zeladores</span>
        </a>
        @elseif($user->isAdmin())
        <a href="{{ route('administradoras.index') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('administradoras.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="ml-3">Administradoras</span>
        </a>
        <a href="{{ route('users.index') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="ml-3">Usuários Global</span>
        </a>
        @endif
    </div>
    @endif

    <div class="pt-4">
        <p class="px-3 mb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sistema</p>

        @if($user->isAdministradora())
        <a href="{{ route('administradora.config') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('administradora.config') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="ml-3">Configurações</span>
        </a>
        @endif

        <a href="{{ route('profile.edit') }}"
           class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors
                  {{ request()->routeIs('profile.edit') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="ml-3">Meu Perfil</span>
        </a>
    </div>
</nav>

{{-- Logout --}}
<div class="flex-shrink-0 border-t border-slate-800 p-2">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="flex w-full items-center px-3 py-2 text-sm font-medium text-slate-400 rounded-md hover:bg-slate-800 hover:text-white transition-colors">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="ml-3">Sair</span>
        </button>
    </form>
</div>
