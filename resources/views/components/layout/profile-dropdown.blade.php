<el-dropdown class="relative">
    <button class="relative flex items-center">
        <span class="absolute -inset-1.5"></span>
        <span class="sr-only">Open user menu</span>
        <img src="{{ $userAvatar }}" alt="{{ $userName }}" class="size-8 rounded-full bg-gray-50 outline -outline-offset-1 outline-black/5" />
        <span class="hidden lg:flex lg:items-center">
            <span aria-hidden="true" class="ml-4 text-sm/6 font-semibold text-gray-900">{{ $userName }}</span>
            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="ml-2 size-5 text-gray-400">
                <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
            </svg>
        </span>
    </button>
    <el-menu anchor="bottom end" popover class="w-32 origin-top-right rounded-md bg-white py-2 shadow-lg outline outline-gray-900/5 transition transition-discrete [--anchor-gap:--spacing(2.5)] data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in">
        @foreach($menuItems as $item)
            <a href="{{ $item['href'] }}" class="block px-3 py-1 text-sm/6 text-gray-900 focus:bg-gray-50 focus:outline-hidden">
                @if($item['icon'])
                    {!! $item['icon'] !!}
                @endif
                {{ $item['label'] }}
            </a>
        @endforeach
    </el-menu>
</el-dropdown>
