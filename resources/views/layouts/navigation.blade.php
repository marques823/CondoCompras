{{-- Sidebar fixo lateral esquerdo - Tema Claro Profissional --}}
<aside class="sidebar flex flex-col" style="background: #1e3a5f; border-right: 1px solid #16304f;">

    {{-- Logo / Nome do sistema --}}
    <div style="display: flex; align-items: center; height: 64px; padding: 0 24px; border-bottom: 1px solid #16304f;">
        <a href="@if(Auth::user()->isZelador()){{ route('zelador.dashboard') }}@elseif(Auth::user()->isGerente()){{ route('gerente.dashboard') }}@elseif(Auth::user()->isAdministradora()){{ route('administradora.dashboard') }}@else{{ route('dashboard') }}@endif"
           style="color: #ffffff; font-weight: 700; font-size: 1.1rem; text-decoration: none; white-space: nowrap; letter-spacing: 0.02em;">
            {{ config('app.name', 'CondoCompras') }}
        </a>
    </div>

    {{-- Links de navegação --}}
    <nav style="flex: 1; padding: 12px 12px; overflow-y: auto;">
        <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 2px;">
            @include('layouts.nav-links')
        </ul>
    </nav>

    {{-- Rodapé do sidebar --}}
    <div style="padding: 16px; border-top: 1px solid #16304f; font-size: 0.7rem; color: #7fa8c9; text-align: center;">
        {{ config('app.name') }} &copy; {{ date('Y') }}
    </div>

</aside>
