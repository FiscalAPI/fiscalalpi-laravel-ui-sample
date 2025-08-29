<nav class="relative flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                @foreach($items as $item)
                    <li>
                        <a href="{{ $item['href'] }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold
                                  {{ $item['active']
                                     ? 'bg-white/5 text-white'
                                     : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            @if($item['icon'] === 'dashboard')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                                    <path d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @elseif($item['icon'] === 'team')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                                    <path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @elseif($item['icon'] === 'box')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M3.27 6.96L12 12.01l8.73-5.05" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M12 22.08V12" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endif
                            {{ $item['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>

        <li class="mt-auto">
            <a href="#" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold text-gray-400 hover:bg-white/5 hover:text-white">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 shrink-0">
                    <path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.759 6.759 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Settings
            </a>
        </li>
    </ul>
</nav>
