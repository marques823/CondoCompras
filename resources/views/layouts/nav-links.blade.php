@php $user = Auth::user(); @endphp

@if($user->isZelador())
    <li>
        <x-nav-link-sidebar :href="route('zelador.dashboard')" :active="request()->routeIs('zelador.dashboard')" icon="home">
            {{ __('Dashboard') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-link-sidebar :href="route('demandas.index')" :active="request()->routeIs('demandas.*')" icon="clipboard">
            {{ __('Minhas Demandas') }}
        </x-nav-link-sidebar>
    </li>

@elseif($user->isAdministradora())
    <li>
        <x-nav-link-sidebar :href="route('administradora.dashboard')" :active="request()->routeIs('administradora.dashboard')" icon="home">
            {{ __('Dashboard') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-link-sidebar :href="route('condominios.index')" :active="request()->routeIs('condominios.*')" icon="office-building">
            {{ __('Condomínios') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-link-sidebar :href="route('demandas.index')" :active="request()->routeIs('demandas.*')" icon="clipboard">
            {{ __('Demandas') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-dropdown-sidebar :label="__('Configurações')" :active="request()->routeIs('notifications.settings*')">
            <x-nav-link-sidebar :href="route('notifications.settings')" :active="request()->routeIs('notifications.settings*')" :submenu="true">
                {{ __('Notificações') }}
            </x-nav-link-sidebar>
        </x-nav-dropdown-sidebar>
    </li>

@elseif($user->isGerente())
    <li>
        <x-nav-link-sidebar :href="route('gerente.dashboard')" :active="request()->routeIs('gerente.dashboard')" icon="home">
            {{ __('Dashboard') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-link-sidebar :href="route('condominios.index')" :active="request()->routeIs('condominios.*')" icon="office-building">
            {{ __('Condomínios') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-link-sidebar :href="route('prestadores.index')" :active="request()->routeIs('prestadores.*')" icon="truck">
            {{ __('Prestadores') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-link-sidebar :href="route('demandas.index')" :active="request()->routeIs('demandas.*')" icon="clipboard">
            {{ __('Demandas') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-link-sidebar :href="route('users.index')" :active="request()->routeIs('users.*')" icon="users">
            {{ __('Zeladores') }}
        </x-nav-link-sidebar>
    </li>

@elseif($user->isAdmin())
    <li>
        <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
            {{ __('Dashboard') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-link-sidebar :href="route('administradoras.index')" :active="request()->routeIs('administradoras.*')" icon="office-building">
            {{ __('Administradoras') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-link-sidebar :href="route('users.index')" :active="request()->routeIs('users.*')" icon="users">
            {{ __('Usuários Global') }}
        </x-nav-link-sidebar>
    </li>
    <li>
        <x-nav-dropdown-sidebar :label="__('Configurações')" :active="request()->routeIs('notifications.settings*')">
            <x-nav-link-sidebar :href="route('notifications.settings')" :active="request()->routeIs('notifications.settings*')" :submenu="true">
                {{ __('Notificações') }}
            </x-nav-link-sidebar>
        </x-nav-dropdown-sidebar>
    </li>
@endif
