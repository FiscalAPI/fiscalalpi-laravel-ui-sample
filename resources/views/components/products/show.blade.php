{{-- resources/views/products/show.blade.php --}}
<x-layout.app-layout title="Detalle del Producto">
    <x-layout.main-content
        title="Detalle del Producto"
        subtitle="Detalles completos del producto registrado con información de catálogos SAT."
        :show-breadcrumbs="true"
        :breadcrumbs="[
            ['name' => 'Productos', 'href' => route('products.index')],
            ['name' => 'Ver Detalles']
        ]"
    >
        <x-slot name="headerActions">
            <a href="{{ route('products.edit', $product) }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Editar Producto
            </a>
        </x-slot>

        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $product->description }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Precio Unitario</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">${{ number_format($product->unitPrice, 6) }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Unidad de Medida SAT</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            @if($product->satUnitMeasurement)
                                <div class="font-medium">{{ $product->satUnitMeasurement->code }}</div>
                                <div class="text-gray-600">{{ $product->satUnitMeasurement->description }}</div>
                            @else
                                {{ $product->sat_unit_measurement_id }}
                            @endif
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Objeto de Impuesto SAT</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            @if($product->satTaxObject)
                                <div class="font-medium">{{ $product->satTaxObject->code }}</div>
                                <div class="text-gray-600">{{ $product->satTaxObject->description }}</div>
                            @else
                                {{ $product->sat_tax_object_id }}
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Código de Producto SAT</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            @if($product->satProductCode)
                                <div class="font-medium">{{ $product->satProductCode->code }}</div>
                                <div class="text-gray-600">{{ $product->satProductCode->description }}</div>
                            @else
                                {{ $product->sat_product_code_id }}
                            @endif
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">ID Fiscal API</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $product->fiscalapiId ?: 'N/A' }}</dd>
                    </div>
                     <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Fecha de Creación</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $product->created_at->format('d/m/Y H:i A') }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $product->updated_at->format('d/m/Y H:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('products.index') }}" class="text-sm/6 font-semibold text-indigo-600 hover:text-indigo-500">
                ← Volver a la lista de productos
            </a>
        </div>

    </x-layout.main-content>
</x-layout.app-layout>
