<div class="p-4">
    <h2 class="text-xl font-bold mb-6">Mi App</h2>

    <nav class="space-y-2">
        @foreach($menuItems as $item)
            <x-sidebar-item
                :label="$item['label']"
                :route="$item['route']"
                :icon="$item['icon']"
            />
        @endforeach
    </nav>
</div>
