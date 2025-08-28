<header class="bg-white shadow-sm border-b">
    <div class="flex items-center justify-between px-6 py-4">
        <h1 class="text-xl font-semibold text-gray-800">
            {{ $title ?: 'Panel de Control' }}
        </h1>

        <nav class="flex items-center space-x-4">
            @foreach($navigationItems as $item)
                <x-top-navigation-item
                    :label="$item['label']"
                    :route="$item['route']"
                    :type="$item['type']"
                />
            @endforeach
        </nav>
    </div>
</header>
