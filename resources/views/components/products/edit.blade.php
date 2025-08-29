{{-- resources/views/products/edit.blade.php --}}
<x-layout.app-layout title="Editar Producto">
    <x-layout.main-content
        title="Editar Producto"
        subtitle="Actualiza la información del producto '{{ $product->name }}'."
        :show-breadcrumbs="true"
        :breadcrumbs="[
            ['name' => 'Productos', 'href' => route('products.index')],
            ['name' => 'Editar']
        ]"
    >
        <form action="{{ route('products.update', $product) }}" method="POST">
            @method('PUT')
            @include('products._form', ['product' => $product])
        </form>
    </x-layout.main-content>
</x-layout.app-layout>
