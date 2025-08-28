<a href="{{ route($route) }}"
   class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ $active ? 'bg-gray-700 border-r-4 border-blue-500' : '' }}">

    <!-- Icono de ejemplo (solo uno como solicitado) -->
    @if($icon === 'dashboard')
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
        </svg>
    @else
        <!-- Icono genérico para otros items -->
        <div class="w-5 h-5 mr-3 bg-gray-400 rounded"></div>
    @endif

    <span>{{ $label }}</span>
</a>
