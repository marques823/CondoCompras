@props(['active', 'icon'])

@php
$linkStyle = ($active ?? false)
    ? 'display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:6px;font-size:0.875rem;font-weight:600;color:#ffffff;background:#2563eb;text-decoration:none;transition:all 0.15s;'
    : 'display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:6px;font-size:0.875rem;font-weight:500;color:#94c3e8;background:transparent;text-decoration:none;transition:all 0.15s;';

$iconStyle = ($active ?? false)
    ? 'width:18px;height:18px;flex-shrink:0;color:#ffffff;'
    : 'width:18px;height:18px;flex-shrink:0;color:#7fa8c9;';
@endphp

<a {{ $attributes->merge(['style' => $linkStyle]) }}
   onmouseover="if(!this.classList.contains('active-link')) { this.style.background='rgba(255,255,255,0.1)'; this.style.color='#ffffff'; }"
   onmouseout="if(!this.classList.contains('active-link')) { this.style.background='transparent'; this.style.color='#94c3e8'; }">
    @if(isset($icon))
        @switch($icon)
            @case('home')
                <svg style="{{ $iconStyle }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                @break
            @case('office-building')
                <svg style="{{ $iconStyle }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
                @break
            @case('clipboard')
                <svg style="{{ $iconStyle }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0018 4.5h-2.25a2.25 2.25 0 00-2.25-2.25H10.5a2.25 2.25 0 00-2.25 2.25H6a2.25 2.25 0 00-2.25 2.25v12.75A2.25 2.25 0 006 18.75h3" />
                </svg>
                @break
            @case('truck')
                <svg style="{{ $iconStyle }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125a1.125 1.125 0 001.125-1.125V11.022a2.25 2.25 0 00-.512-1.43l-2.233-2.814a2.25 2.25 0 00-1.761-.851H12.375c-.621 0-1.125.504-1.125 1.125V15.75m9.75-3H12" />
                </svg>
                @break
            @case('users')
                <svg style="{{ $iconStyle }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-3.833-6.248 10.334 10.334 0 01-5.342 1.411 10.334 10.334 0 01-5.342-1.411 4.125 4.125 0 00-3.833 6.248 9.337 9.337 0 004.121.952 9.38 9.38 0 002.625-.372M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                @break
            @case('cog')
                <svg style="{{ $iconStyle }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                @break
            @default
                <svg style="{{ $iconStyle }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
        @endswitch
    @endif

    <span>{{ $slot }}</span>
</a>
