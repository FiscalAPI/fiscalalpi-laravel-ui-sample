@if($type === 'button')
    <form method="POST" action="{{ route($route) }}" class="inline">
        @csrf
        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
            {{ $label }}
        </button>
    </form>
@else
    <a href="{{ route($route) }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
        {{ $label }}
    </a>
@endif
