@php
use App\Models\Order;
@endphp

<x-layout.app-layout title="Ventas">
    <x-layout.main-content
        title="Ventas"
        subtitle="Gestiona las ventas y genera facturas">

        <!-- Filtros y búsqueda -->
        <form method="GET" action="{{ route('sales.index') }}" class="mb-6 bg-white p-4 rounded-lg border border-gray-200">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar ventas</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Buscar por ID, cliente o RFC..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                <div class="sm:w-48">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select
                        id="status"
                        name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="{{ Order::STATUS_COMPLETED }}" {{ request('status', Order::STATUS_COMPLETED) === Order::STATUS_COMPLETED ? 'selected' : '' }}>Completada</option>
                        <option value="{{ Order::STATUS_INVOICED }}" {{ request('status') === Order::STATUS_INVOICED ? 'selected' : '' }}>Facturada</option>
                        <option value="{{ Order::STATUS_DRAFT }}" {{ request('status') === Order::STATUS_DRAFT ? 'selected' : '' }}>Borrador</option>
                        <option value="{{ Order::STATUS_CANCELLED }}" {{ request('status') === Order::STATUS_CANCELLED ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="sm:w-48">
                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Rango de fechas</label>
                    <select
                        id="date_range"
                        name="date_range"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="" {{ request('date_range') === '' ? 'selected' : '' }}>Todas las fechas</option>
                        <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Hoy</option>
                        <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>Esta semana</option>
                        <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>Este mes</option>
                        <option value="quarter" {{ request('date_range') === 'quarter' ? 'selected' : '' }}>Este trimestre</option>
                        <option value="year" {{ request('date_range') === 'year' ? 'selected' : '' }}>Este año</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrar
                    </button>
                    <a
                        href="{{ route('sales.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Limpiar
                    </a>
                </div>
            </div>
        </form>

        <!-- Indicador de filtros activos -->
        @if(request('search') || request('status') !== Order::STATUS_COMPLETED || request('date_range'))
            <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="text-sm font-medium text-blue-800">Filtros activos:</span>
                        <div class="flex items-center space-x-2 text-sm text-blue-700">
                            @if(request('search'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Búsqueda: "{{ request('search') }}"
                                </span>
                            @endif
                            @if(request('status') !== Order::STATUS_COMPLETED)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Estado: {{ ucfirst(request('status')) }}
                                </span>
                            @endif
                            @if(request('date_range'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Fecha: {{ ucfirst(request('date_range')) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <span class="text-sm text-blue-600">{{ $orders->total() }} ventas encontradas</span>
                </div>
            </div>
        @endif

        <!-- Componente de tabla de ventas -->
        @if($orders->count() > 0)
            <x-sales :orders="$orders" />
        @else
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron ventas</h3>
                <p class="text-gray-500 mb-4">
                    @if(request('search') || request('status') !== Order::STATUS_COMPLETED || request('date_range'))
                        No hay ventas que coincidan con los filtros aplicados.
                    @else
                        No hay ventas completadas disponibles para facturar.
                    @endif
                </p>
                @if(request('search') || request('status') !== Order::STATUS_COMPLETED || request('date_range'))
                    <a
                        href="{{ route('sales.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Limpiar filtros
                    </a>
                @endif
            </div>
        @endif

        <!-- Paginación -->
        @if($orders->hasPages())
            <div class="mt-6">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif

    </x-layout.main-content>
</x-layout.app-layout>
