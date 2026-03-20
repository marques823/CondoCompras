{{-- Top Bar - Tema Claro Profissional --}}
<div style="
    position: sticky;
    top: 0;
    z-index: 40;
    display: flex;
    align-items: center;
    height: 64px;
    padding: 0 24px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    gap: 16px;
">
    <div style="flex: 1;"></div>

    @php $user = Auth::user(); @endphp

    {{-- Informação do usuário e dropdown --}}
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button style="
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 14px;
                background: #f1f5f9;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                font-size: 0.875rem;
                color: #334155;
                cursor: pointer;
                transition: background 0.15s;
            " onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                <svg style="width:16px;height:16px;color:#64748b;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <span>{{ $user->name }}</span>
                <span style="background:#dbeafe;color:#1d4ed8;font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:999px;">
                    @if($user->isAdmin()) Admin
                    @elseif($user->isAdministradora()) Adm. Empresa
                    @elseif($user->isGerente()) Gerente
                    @else Zelador
                    @endif
                </span>
                <svg style="width:14px;height:14px;color:#94a3b8;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')">
                {{ __('Perfil') }}
            </x-dropdown-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Sair') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>
