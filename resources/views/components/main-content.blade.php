<div class="container mx-auto px-6 py-8">
    @if($title)
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $title }}</h2>
            @if($breadcrumb)
                <p class="text-gray-600 mt-1">{{ $breadcrumb }}</p>
            @endif
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6">
        {{ $slot }}
    </div>
</div>
