@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-3 py-2 text-sm font-medium rounded-md bg-blue-600 text-white shadow-sm ring-1 ring-inset ring-blue-500 transition-all duration-200'
            : 'flex items-center px-3 py-2 text-sm font-medium rounded-md text-slate-400 hover:text-white hover:bg-slate-800 transition-all duration-200 group';

$iconClasses = ($active ?? false)
                ? 'h-5 w-5 flex-shrink-0 text-white'
                : 'h-5 w-5 flex-shrink-0 text-slate-500 group-hover:text-blue-400 transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        @switch($icon)
            @case('home')
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                @break
            @case('office-building')
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
                @break
            @case('clipboard')
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0018 4.5h-2.25a2.25 2.25 0 00-2.25-2.25H10.5a2.25 2.25 0 00-2.25 2.25H6a2.25 2.25 0 00-2.25 2.25v12.75A2.25 2.25 0 006 18.75h3" />
                </svg>
                @break
            @case('truck')
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125a1.125 1.125 0 001.125-1.125V11.022a2.25 2.25 0 00-.512-1.43l-2.233-2.814a2.25 2.25 0 00-1.761-.851H12.375c-.621 0-1.125.504-1.125 1.125V15.75m9.75-3H12" />
                </svg>
                @break
            @case('document')
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                @break
            @case('users')
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-3.833-6.248 10.334 10.334 0 01-5.342 1.411 10.334 10.334 0 01-5.342-1.411 4.125 4.125 0 00-3.833 6.248 9.337 9.337 0 004.121.952 9.38 9.38 0 002.625-.372M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                @break
            @case('briefcase')
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 .621-.504 1.125-1.125 1.125H4.875c-.621 0-1.125-.504-1.125-1.125v-4.25m16.5 0a2.25 2.25 0 00-2.25-2.25H5.625a2.25 2.25 0 00-2.25 2.25m16.5 0V9.45c0-.621-.504-1.125-1.125-1.125h-1.32a6.003 6.003 0 00-10.61 0h-1.32c-.621 0-1.125.504-1.125 1.125v4.7m16.5 0l-1.328-1.294a2.25 2.25 0 00-2.338-.506l-1.536.568a2.25 2.25 0 01-1.548 0l-1.536-.568a2.25 2.25 0 00-2.338.506L3.75 14.15m16.5 0V11" />
                </svg>
                @break
            @case('cog')
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 1115 0 7.5 7.5 0 01-15 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9" />
                </svg>
                @break
            @case('user')
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                @break
            @default
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
        @endswitch
    @endif

    {{ $slot }}
</a>
