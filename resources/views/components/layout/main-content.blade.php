<main class="py-10">
    <div class="{{ $containerClass }}">
        @if($showHeader && ($title || $subtitle))
            <div class="mb-8">
                @if($showBreadcrumbs && !empty($breadcrumbs))
                    <nav aria-label="Breadcrumb" class="flex mb-4">
                        <ol role="list" class="flex items-center space-x-4">
                            @foreach($breadcrumbs as $index => $breadcrumb)
                                <li>
                                    <div class="flex items-center">
                                        @if($index > 0)
                                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="mr-4 size-5 flex-shrink-0 text-gray-300">
                                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        @if(isset($breadcrumb['href']) && !$loop->last)
                                            <a href="{{ $breadcrumb['href'] }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                                {{ $breadcrumb['name'] }}
                                            </a>
                                        @else
                                            <span class="text-sm font-medium text-gray-500" @if($loop->last) aria-current="page" @endif>
                                                {{ $breadcrumb['name'] }}
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif

                <div class="flex items-center justify-between">
                    <div>
                        @if($title)
                            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                                {{ $title }}
                            </h1>
                        @endif
                        @if($subtitle)
                            <p class="mt-2 text-sm text-gray-700">
                                {{ $subtitle }}
                            </p>
                        @endif
                    </div>

                    @if($headerActions)
                        <div class="flex items-center gap-x-3">
                            {!! $headerActions !!}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="{{ $contentClass }}">
            {{ $slot }}
        </div>
    </div>
</main>
